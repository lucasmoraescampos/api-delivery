<?php

namespace App\Repositories;

use App\Exceptions\CustomException;
use App\Models\Card;
use App\Models\PaymentMethod;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CardRepository extends BaseRepository implements CardRepositoryInterface
{
    /**
     * CardRepository constructor.
     *
     * @param Card $model
     */
    public function __construct(Card $model)
    {
        parent::__construct($model);
    }

    /**
     * @param mixed $id
     * @return Card
     */
    public function getById($id): Card
    {
        $card = Card::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$card) {
            throw new CustomException('Cartão não encontrado.', 422);
        }

        return $card;
    }

    /**
     * @return Collection
     */
    public function getByAuth(): Collection
    {
        return Card::select('id', 'number', 'holder_name', 'icon')
            ->where('user_id', Auth::id())
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * @param array $attributes
     * @return Card
     */
    public function create(array $attributes): Card
    {
        $this->validateCreate($attributes);

        $card = new Card(Arr::only($attributes, [
            'number',
            'expiration_month',
            'expiration_year',
            'security_code',
            'holder_name',
            'document_number'
        ]));

        $card->user_id = Auth::id();

        $card->document_type = strlen($card->document_number) == 11 ? 'CPF' : 'CNPJ';

        $card->provider = $this->getProvider($attributes['payment_method_id']);

        $card->icon = $this->getIcon($attributes['payment_method_id']);

        $card->save();

        return $card;
    }

    /**
     * @param array $attributes
     * @return void
     */
    private function validateCreate(array $attributes): void
    {
        $validator = Validator::make($attributes, [
            'number' => 'required|string|min:15|max:16',
            'expiration_month' => 'required|string|min:2|max:2',
            'expiration_year' => 'required|string|min:4|min:4',
            'security_code' => 'required|string|min:3|max:4',
            'holder_name' => 'required|string|max:150',
            'document_number' => 'required|string|max:20',
            'payment_method_id' => 'required|string',
        ]);

        $validator->validate();
    }

    /**
     * @param mixed $payment_method_id
     * @return string
     */
    private function getProvider($payment_method_id): ?string
    {
        if ($payment_method_id == 'visa') {
            return 'Visa';
        }
        elseif ($payment_method_id == 'master') {
            return 'Mastercard';
        }
        elseif ($payment_method_id == 'hipercard') {
            return 'Hipercard';
        }
        elseif ($payment_method_id == 'amex') {
            return 'American Express';
        }
        elseif ($payment_method_id == 'elo') {
            return 'Elo';
        }
        else {
            return null;
        }
    }

    /**
     * @param mixed $payment_method_id
     * @return string
     */
    private function getIcon($payment_method_id): ?string
    {
        $path = env('IMAGES_URL') . '/payment-methods';

        if ($payment_method_id == 'visa') {
            return $path . '/visa.png';
        }
        elseif ($payment_method_id == 'master') {
            return $path . '/mastercard.png';
        }
        elseif ($payment_method_id == 'hipercard') {
            return $path . '/hipercard.png';
        }
        elseif ($payment_method_id == 'amex') {
            return $path . '/amex.png';
        }
        elseif ($payment_method_id == 'elo') {
            return $path . '/elo.png';
        }
        else {
            return null;
        }
    }
}