<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\AccessToken;
use App\Exceptions\CustomException;
use App\Mail\SendVerificationCode;
use App\Models\VerificationCode;
use Exception;
use MercadoPago;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;
use Firebase\Auth\Token\Exception\InvalidToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * UserRepository constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * @return User
     */
    public function getAuth(): User
    {
        $user = User::where('id', Auth::id())->first();

        $user->load(['companies.plan', 'companies.payment_methods', 'companies' => function ($query) {
            $query->where('deleted', false)->orderBy('id', 'desc');
        }]);

        return $user;
    }

    /**
     * @param array $attributes
     * @return User
     */
    public function create(array $attributes): User
    {
        $this->validateCreate($attributes);

        $user = new User([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'phone' => $attributes['phone']
        ]);

        try {

            $customer = $this->createCustomer($attributes['email']);

            $user->customer_id = $customer->id;

        } catch (Exception $e) {

            $user->customer_id = null;

        }

        $user->save();

        $user->load(['companies.plan', 'companies.payment_methods', 'companies' => function ($query) {
            $query->where('deleted', false)->orderBy('id', 'desc');
        }]);

        VerificationCode::where('email', $attributes['email'])
            ->orWhere('phone', $attributes['phone'])
            ->delete();

        return $user;
    }

    /**
     * @param array $attributes
     * @return User
     */
    public function authenticate(array $attributes): ?User
    {
        $this->validateAuthenticate($attributes);

        if (isset($attributes['email'])) {

            $user = User::where('email', $attributes['email'])->first();

            if (!$user) {
                throw new CustomException('Este e-mail não foi cadastrado', 200);
            }

            if (isset($attributes['code'])) {

                $verification = VerificationCode::where('email', $user->email)->orderBy('id', 'desc')->first();

                if (!$verification) {
                    throw new CustomException('Nenhum código de verificação enviado para este e-mail', 200);
                }

                if ($verification->code != $attributes['code']) {
                    throw new CustomException('Código inválido', 200);
                }

                $user->load(['companies.plan', 'companies.payment_methods', 'companies' => function ($query) {
                    $query->where('deleted', false)->orderBy('id', 'desc');
                }]);

                VerificationCode::where('email', $attributes['email'])->delete();
    
                return $user;

            }
    
            else {
    
                $code = generateCode();

                VerificationCode::create(['email' => $user->email, 'code' => $code]);

                Mail::to($user->email)->send(new SendVerificationCode($code));
    
                return null;
    
            }

        }

        else {

            $user = User::where('phone', $attributes['phone'])->first();

            if (!$user) {
                throw new CustomException('Este número de telefone não foi cadastrado', 200);
            }

            if (isset($attributes['code'])) {

                $verification = VerificationCode::where('phone', $user->phone)
                    ->orderBy('id', 'desc')
                    ->first();

                if (!$verification) {
                    throw new CustomException('Nenhum código de verificação enviado para este número de celular', 200);
                }

                if ($verification->code != $attributes['code']) {
                    throw new CustomException('Código inválido', 200);
                }

                $user->load(['companies.plan', 'companies.payment_methods', 'companies' => function ($query) {
                    $query->where('deleted', false)->orderBy('id', 'desc');
                }]);

                VerificationCode::where('phone', $attributes['phone'])->delete();

                return $user;
    
            }
    
            else {

                $code = generateCode();

                VerificationCode::create(['phone' => $user->phone, 'code' => $code]);

                $httpClient = new HttpClientRepository();

                $httpClient->setData([
                    'key' => env('SMS_DEV_KEY'),
                    'type' => 9,
                    'number' => $user->phone,
                    'msg' => "Seu codigo Meu Pedido: $code"
                ]);

                $httpClient->post('https://api.smsdev.com.br/v1/send');
    
                return null;
    
            }

        }
    }

    /**
     * @param array $attributes
     * @return User
     */
    public function authenticateWithProvider(array $attributes): User
    {
        Validator::make($attributes, ['token' => 'required|string' ])->validate();

        try {

            $auth = app('firebase.auth');

            $verifiedIdToken = $auth->verifyIdToken($attributes['token']);

            $uid = $verifiedIdToken->claims()->get('sub');

            $data = $auth->getUser($uid);
            
            $user = User::where('email', $data->email)->first();
        
            if ($user == null) {

                $user = new User([
                    'uid' => $data->uid,
                    'name' => $data->displayName,
                    'email' => $data->email,
                    'image' => $data->photoUrl
                ]);

                try {

                    $customer = $this->createCustomer($data->email);

                    $user->customer_id = $customer->id;

                } catch(Exception $e) {

                    $user->customer_id = null;
                    
                }

                $user->save();

            }

            else if ($user->uid == null) {

                $user->uid = $uid;

                $user->save();

            }

            $user->load(['companies.plan', 'companies.payment_methods', 'companies' => function ($query) {
                $query->where('deleted', false)->orderBy('id', 'desc');
            }]);

            return $user;

        } catch (InvalidArgumentException $e) {
    
            throw new CustomException($e->getMessage(), 422);    
        
        } catch (InvalidToken $e) {
        
            throw new CustomException($e->getMessage(), 422);
        
        }
    }

    /**
     * @param User $user
     * @return string
     */
    public function createAccessToken(User $user): string
    {
        $token = auth()->login($user);

        $agent = new Agent();

        AccessToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'ip' => request()->ip(),
            'device' => $agent->device(),
            'platform' => $agent->platform(),
            'browser' => $agent->browser()
        ]);

        return $token;
    }

    /**
     * @param array $attributes
     * @return void
     */
    public function invalidAccessToken(array $attributes): void
    {
        Validator::make($attributes, ['token' => 'required|string'])->validate();

        auth('users')->setToken($attributes['token'])->logout();

        AccessToken::where('token', $attributes['token'])->update(['status' => AccessToken::STATUS_INVALID]);
    }

    /**
     * @param mixed $email
     * @return MercadoPago\Customer
     */
    private function createCustomer($email): MercadoPago\Customer
    {
        MercadoPago\SDK::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));

        $customer = MercadoPago\Customer::search(['email' => $email]);

        if ($customer->total > 0) {

            return $customer[0];
            
        }

        $customer = new MercadoPago\Customer();

        $customer->email = $email;

        $customer->save();

        return $customer;
    }

    /**
     * @param array $attributes
     * @return void
     */
    private function validateCreate(array $attributes): void
    {
        $validator = Validator::make($attributes, [
            'name' => 'required|string|max:200',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:11'
        ]);

        $validator->validate();

        if (User::where('email', $attributes['email'])->count() > 0) {
            throw new CustomException('Este e-mail já está sendo usado.', 200);
        }

        if (User::where('phone', $attributes['phone'])->count() > 0) {
            throw new CustomException('Este número de telefone já está sendo usado.', 200);
        }
    }

    /**
     * @param array $attributes
     * @return void
     */
    private function validateAuthenticate(array $attributes): void
    {
        $validator = Validator::make($attributes, [
            'email' => 'required_without:phone|string|email|max:255',
            'phone' => 'required_without:email|string|max:11',
            'code' => 'nullable|string'
        ]);

        $validator->validate();
    }
}
