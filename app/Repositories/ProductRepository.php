<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Exceptions\CustomException;
use App\Models\Company;
use App\Models\Segment;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    /**
     * ProductRepository constructor.
     *
     * @param Product $model
     */
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    /**
     * @param mixed $company_id
     * @return Collection
     */
    public function getByCompany($company_id): Collection
    {
        if (Company::where('id', $company_id)->where('user_id', Auth::id())->count() == 0) {
            throw new CustomException('Empresa não autorizada.', 422);
        }

        return Product::with('segment')
            ->where('company_id', $company_id)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * @param array $attributes
     * @return Product
     */
    public function create(array $attributes): Product
    {
        $this->validateCreate($attributes);

        $data = Arr::only($attributes, [
            'company_id',
            'segment_id',
            'name',
            'description',
            'qty',
            'price',
            'cost',
            'rebate',
            'has_sunday',
            'has_monday',
            'has_tuesday',
            'has_wednesday',
            'has_thursday',
            'has_friday',
            'has_saturday',
            'start_time',
            'end_time'
        ]);

        $product = new Product($data);

        $product->image = fileUpload($attributes['image'], 'products');

        $product->save();

        $product->load('segment');

        return $product;
    }

    /**
     * @param array $attributes
     * @param mixed $id
     * @return Product
     */
    public function update($id, array $attributes): Product
    {
        $this->validateUpdate($attributes);

        $product = Product::find($id);

        $data = Arr::only($attributes, [
            'segment_id',
            'name',
            'description',
            'qty',
            'cost',
            'price',
            'rebate',
            'has_sunday',
            'has_monday',
            'has_tuesday',
            'has_wednesday',
            'has_thursday',
            'has_friday',
            'has_saturday',
            'start_time',
            'end_time',
            'status'
        ]);

        $product->fill($data);

        if (isset($attributes['image'])) {

            $product->image = fileUpload($attributes['image'], 'products');

        }

        $product->save();

        $product->load('segment');

        return $product;
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

        $product = Product::where('id', $id)
            ->where('company_id', $company_id)
            ->first();

        if (!$product) {
            throw new CustomException('Produto não encontrado.', 422);
        }

        $product->delete();
    }

    /**
     * @param mixed $attributes
     * @return void
     */
    private function validateCreate(array $attributes): void
    {
        $validator = Validator::make($attributes, [
            'image' => 'required|file|mimes:gif,png,jpeg,bmp,webp',
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:200',
            'qty' => 'nullable|numeric',
            'cost' => 'nullable|numeric',
            'price' => 'required|numeric',
            'rebate' => 'nullable|numeric',
            'has_sunday' => 'required|boolean',
            'has_monday' => 'required|boolean',
            'has_tuesday' => 'required|boolean',
            'has_wednesday' => 'required|boolean',
            'has_thursday' => 'required|boolean',
            'has_friday' => 'required|boolean',
            'has_saturday' => 'required|boolean',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'company_id' => [
                'required', 'numeric',
                function ($attribute, $value, $fail) {
                    if (Company::where('id', $value)->where('user_id', Auth::id())->count() == 0) {
                        $fail('Empresa não autorizada.');
                    }
                }
            ],
            'segment_id' => [
                'required', 'numeric',
                function ($attribute, $value, $fail) use ($attributes) {
                    if (Segment::where('id', $value)->where('company_id', $attributes['company_id'])->count() == 0) {
                        $fail('Esse segmento não pertence a este administrador.');
                    }
                }
            ]
        ]);

        $validator->validate();
    }

    /**
     * @param mixed $attributes
     * @return void
     */
    private function validateUpdate(array $attributes): void
    {
        $validator = Validator::make($attributes, [
            'image' => 'nullable|file|mimes:gif,png,jpeg,bmp,webp',
            'name' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:200',
            'qty' => 'nullable|numeric',
            'cost' => 'nullable|numeric',
            'price' => 'nullable|numeric',
            'rebate' => 'nullable|numeric',
            'has_sunday' => 'nullable|boolean',
            'has_monday' => 'nullable|boolean',
            'has_tuesday' => 'nullable|boolean',
            'has_wednesday' => 'nullable|boolean',
            'has_thursday' => 'nullable|boolean',
            'has_friday' => 'nullable|boolean',
            'has_saturday' => 'nullable|boolean',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'company_id' => [
                'required', 'numeric',
                function ($attribute, $value, $fail) {
                    if (Company::where('id', $value)->where('user_id', Auth::id())->count() == 0) {
                        $fail('Empresa não autorizada.');
                    }
                }
            ],
            'segment_id' => [
                'nullable', 'numeric',
                function ($attribute, $value, $fail) use ($attributes) {
                    if (Segment::where('id', $value)->where('company_id', $attributes['company_id'])->count() == 0) {
                        $fail('Esse segmento não pertence a este administrador.');
                    }
                }
            ]
        ]);

        $validator->validate();
    }
}
