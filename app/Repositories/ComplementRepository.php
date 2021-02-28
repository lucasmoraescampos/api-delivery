<?php

namespace App\Repositories;

use App\Exceptions\CustomException;
use App\Models\Company;
use App\Models\Complement;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ComplementRepository extends BaseRepository implements ComplementRepositoryInterface
{
    /**
     * ComplementRepository constructor.
     *
     * @param Complement $model
     */
    public function __construct(Complement $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $attributes
     * @return Complement
     */
    public function create(array $attributes): Complement
    {
        $this->validateCreate($attributes);

        $complement = new Complement(Arr::only($attributes, [
            'product_id',
            'title',
            'qty_max',
            'required'
        ]));

        $complement->qty_min = $complement->required ? $attributes['qty_min'] : null;

        $complement->save();

        $complement->load('subcomplements');
        
        return $complement;
    }

    /**
     * @param mixed $id
     * @param array $attributes
     * @return Complement
     */
    public function update($id, array $attributes): Complement
    {
        $this->validateUpdate($attributes);

        $complement = Complement::where('id', $id)
            ->where('product_id', $attributes['product_id'])
            ->first();

        if (!$complement) {
            throw new CustomException('Complemento não encontrado.', 422);
        }

        $complement->fill(Arr::only($attributes, [
            'title',
            'qty_max',
            'required'
        ]));

        $complement->qty_min = $complement->required ? $attributes['qty_min'] : null;

        $complement->save();

        $complement->load('subcomplements');

        return $complement;
    }

    /**
     * @param mixed $id
     * @param mixed $company_id
     * @return void
     */
    public function delete($id, $company_id = null): void
    {
        if (Company::where('id', $company_id)->where('user_id', Auth::id())->count() == 0) {
            throw new CustomException('Empresa não encontrada.', 422);
        }

        $complement = Complement::with(['product' => function ($query) use ($company_id) {
                $query->where('company_id', $company_id);
            }])
            ->where('id', $id)
            ->first();

        if (!$complement) {
            throw new CustomException('Complemento não encontrado.', 422);
        }

        $complement->delete();
    }

    /**
     * @param array $attributes
     * @return void
     */
    private function validateCreate(array $attributes)
    {
        $validator = Validator::make($attributes, [
            'title' => 'required|string|max:100',
            'qty_min' => 'required_if:required,1|numeric|min:1',
            'qty_max' => 'required|numeric|min:1',
            'required' => 'required|boolean',
            'company_id' => [
                'required', 'numeric', function ($attribute, $value, $fail) use ($attributes) {
                    if (Company::where('id', $value)->where('user_id', Auth::id())->count() == 0) {
                        $fail('Empresa não encontrada.');
                    }
                }
            ],
            'product_id' => [
                'required', 'numeric', function ($attribute, $value, $fail) use ($attributes) {
                    if (Product::where('id', $value)->where('company_id', $attributes['company_id'])->count() == 0) {
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
            'title' => 'nullable|string|max:100',
            'qty_min' => 'required_if:required,1|numeric|min:1',
            'qty_max' => 'nullable|numeric|min:1',
            'required' => 'nullable|boolean',
            'company_id' => [
                'required', 'numeric', function ($attribute, $value, $fail) use ($attributes) {
                    if (Company::where('id', $value)->where('user_id', Auth::id())->count() == 0) {
                        $fail('Empresa não encontrada.');
                    }
                }
            ],
            'product_id' => [
                'required', 'numeric', function ($attribute, $value, $fail) use ($attributes) {
                    if (Product::where('id', $value)->where('company_id', $attributes['company_id'])->count() == 0) {
                        $fail('Produto não encontrado.');
                    }
                }
            ]
        ]);

        $validator->validate();
    }
}