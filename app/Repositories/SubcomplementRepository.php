<?php

namespace App\Repositories;

use App\Exceptions\CustomException;
use App\Models\Company;
use App\Models\Complement;
use App\Models\Product;
use App\Models\Subcomplement;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SubcomplementRepository extends BaseRepository implements SubcomplementRepositoryInterface
{
    /**
     * SubcomplementRepository constructor.
     *
     * @param Subcomplement $model
     */
    public function __construct(Subcomplement $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $attributes
     * @return Subcomplement
     */
    public function create(array $attributes): Subcomplement
    {
        $this->validateCreate($attributes);

        $subcomplement = Subcomplement::create(Arr::only($attributes, [
            'description',
            'price',
            'complement_id'
        ]));
        
        return $subcomplement;
    }

    /**
     * @param mixed $id
     * @param array $attributes
     * @return Subcomplement
     */
    public function update($id, array $attributes): Subcomplement
    {
        $this->validateUpdate($attributes);

        $subcomplement = Subcomplement::where('id', $id)
            ->where('complement_id', $attributes['complement_id'])
            ->first();

        if (!$subcomplement) {
            throw new CustomException('Subcomplemento não encontrado.', 422);
        }

        $subcomplement->fill(Arr::only($attributes, [
            'description',
            'price'
        ]));

        $subcomplement->save();

        return $subcomplement;
    }

    /**
     * @param mixed $id
     * @param mixed $company_id
     * @return void
     */
    public function delete($id, $company_id = null): void
    {
        if (Company::where('id', $company_id)->where('user_id', Auth::id())->count() == 0) {
            throw new CustomException('Empresa não autorizada.', 422);
        }

        $subcomplement = Subcomplement::with(['complement' => function ($query) use ($company_id) {

                $query->with(['product' => function ($query) use ($company_id) {
                    $query->where('company_id', $company_id);
                }]);

            }])
            ->where('id', $id)
            ->first();

        if (!$subcomplement) {
            throw new CustomException('Subcomplemento não encontrado.', 422);
        }

        $subcomplement->delete();
    }

    /**
     * @param array $attributes
     * @return void
     */
    private function validateCreate(array $attributes)
    {
        $validator = Validator::make($attributes, [
            'description' => 'required|string|max:100',
            'price' => 'nullable|numeric|min:0.01',
            'company_id' => [
                'required', 'numeric', function ($attribute, $value, $fail) use ($attributes) {
                    if (Company::where('id', $value)->where('user_id', Auth::id())->count() == 0) {
                        $fail('Empresa não encontrada.');
                    }
                }
            ],
            'complement_id' => [
                'required', 'numeric', function ($attribute, $value, $fail) use ($attributes) {

                    $invalid = Complement::with(['product' => function ($query) use ($attributes) {

                            $query->where('complement_id', $attributes['complement_id'])
                                ->where('company_id', $attributes['company_id']);

                        }])
                        ->where('id', $value)
                        ->count() == 0;

                    if ($invalid) {
                        $fail('Produto não encontrado.');
                    }
                    
                }
            ]
        ]);

        $validator->validate();
    }

    /**
     * @param array $attributes
     * @return void
     */
    private function validateUpdate(array $attributes)
    {
        $validator = Validator::make($attributes, [
            'description' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0.01',
            'company_id' => [
                'required', 'numeric', function ($attribute, $value, $fail) use ($attributes) {
                    if (Company::where('id', $value)->where('user_id', Auth::id())->count() == 0) {
                        $fail('Empresa não encontrada.');
                    }
                }
            ],
            'complement_id' => [
                'required', 'numeric', function ($attribute, $value, $fail) use ($attributes) {

                    $invalid = Complement::with(['product' => function ($query) use ($attributes) {

                            $query->where('complement_id', $attributes['complement_id'])
                                ->where('company_id', $attributes['company_id']);

                        }])
                        ->where('id', $value)
                        ->count() == 0;

                    if ($invalid) {
                        $fail('Produto não encontrado.');
                    }
                    
                }
            ]
        ]);

        $validator->validate();
    }
}