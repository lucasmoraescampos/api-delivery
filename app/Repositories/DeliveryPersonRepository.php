<?php

namespace App\Repositories;

use App\Models\DeliveryPerson;
use App\Models\Order;
use App\Exceptions\CustomException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class DeliveryPersonRepository extends BaseRepository implements DeliveryPersonRepositoryInterface
{
    /**
     * DeliveryPersonRepository constructor.
     *
     * @param DeliveryPerson $model
     */
    public function __construct(DeliveryPerson $model)
    {
        parent::__construct($model);
    }

    /**
     * @param mixed $company_id
     * @return Collection
     */
    public function getByCompany($company_id): Collection
    {
        if (!CompanyRepository::checkAuth($company_id)) {
            throw new CustomException('Empresa não autorizada.', 422);
        }

        return DeliveryPerson::where('company_id', $company_id)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * @param array $attributes
     * @return DeliveryPerson
     */
    public function create(array $attributes): DeliveryPerson
    {
        $this->validateCreate($attributes);

        $deliveryPerson = new DeliveryPerson(Arr::only($attributes, [
            'company_id',
            'name',
            'additional_information'
        ]));

        if (isset($attributes['image'])) {

            $deliveryPerson->image = fileUpload($attributes['image'], 'delivery-persons');

        }

        $deliveryPerson->save();

        return $deliveryPerson;
    }

    /**
     * @param mixed $id
     * @param array $attributes
     * @return DeliveryPerson
     */
    public function update($id, array $attributes): DeliveryPerson
    {
        $this->validateUpdate($attributes);

        $deliveryPerson = DeliveryPerson::where('id', $id)
            ->where('company_id', $attributes['company_id'])
            ->first();

        if (!$deliveryPerson) {
            throw new CustomException('Atendente não encontrado.', 422);
        }

        $deliveryPerson->fill(Arr::only($attributes, [
            'name',
            'additional_information',
            'status'
        ]));

        if (isset($attributes['image'])) {

            $deliveryPerson->image = fileUpload($attributes['image'], 'delivery-persons');

        }

        $deliveryPerson->save();

        return $deliveryPerson;
    }

    /**
     * @param mixed $id
     * @param mixed $company_id
     * @return void
     */
    public function delete($id, $company_id = null): void
    {
        if (!CompanyRepository::checkAuth($company_id)) {
            throw new CustomException('Empresa não autorizada.', 422);
        }

        $deliveryPerson = DeliveryPerson::where('id', $id)
            ->where('company_id', $company_id)
            ->first();

        if (!$deliveryPerson) {
            throw new CustomException('Atendente não encontrado.', 422);
        }

        if (Order::where('delivery_person_id', $id)->count() > 0) {
            throw new CustomException('Atendente não pode ser excluído pois possui pedidos vinculados.', 200);
        }

        $deliveryPerson->delete();
    }

    /**
     * @param array $attributes
     * @return void
     */
    private function validateCreate(array $attributes): void
    {
        $validator = Validator::make($attributes, [
            'name' => 'required|string|max:150',
            'additional_information' => 'nullable|string|max:2000',
            'image' => 'nullable|file|mimes:gif,png,jpeg,bmp,webp',
            'company_id' => [
                'required', 'numeric',
                function ($attribute, $value, $fail) {
                    if (!CompanyRepository::checkAuth($value)) {
                        $fail('Empresa não autorizada.');
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
    private function validateUpdate(array $attributes): void
    {
        $validator = Validator::make($attributes, [
            'name' => 'nullable|string|max:150',
            'additional_information' => 'nullable|string|max:2000',
            'image' => 'nullable|file|mimes:gif,png,jpeg,bmp,webp',
            'status' => 'nullable|boolean',
            'company_id' => [
                'required', 'numeric',
                function ($attribute, $value, $fail) {
                    if (!CompanyRepository::checkAuth($value)) {
                        $fail('Empresa não autorizada.');
                    }
                }
            ]
        ]);

        $validator->validate();
    }
}