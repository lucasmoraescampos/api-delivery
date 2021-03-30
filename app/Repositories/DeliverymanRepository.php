<?php

namespace App\Repositories;

use App\Models\CompanyDeliveryman;
use App\Exceptions\CustomException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class DeliverymanRepository extends BaseRepository implements DeliverymanRepositoryInterface
{
    /**
     * DeliverymanRepository constructor.
     *
     * @param CompanyDeliveryman $model
     */
    public function __construct(CompanyDeliveryman $model)
    {
        parent::__construct($model);
    }

    /**
     * @param mixed $company_id
     * @return Collection
     */
    public function getByCompany($company_id): Collection
    {
        return CompanyDeliveryman::where('company_id', $company_id)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * @param array $attributes
     * @return CompanyDeliveryman
     */
    public function create(array $attributes): CompanyDeliveryman
    {
        $this->validateCreate($attributes);

        $deliveryman = CompanyDeliveryman::create(Arr::only($attributes, [
            'company_id',
            'name',
            'phone'
        ]));

        return $deliveryman;
    }

    /**
     * @param mixed $id
     * @param mixed $company_id
     * @return void
     */
    public function delete($id, $company_id = null): void
    {
        $deliveryman = CompanyDeliveryman::where('id', $id)
            ->where('company_id', $company_id)
            ->first();

        if (!$deliveryman) {
            throw new CustomException('Entregador não encontrado.', 404);
        }

        $deliveryman->delete();
    }

    /**
     * @param array $attributes
     * @return void
     */
    private function validateCreate(array $attributes): void
    {
        $validator = Validator::make($attributes, [
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:11'
        ]);

        $validator->validate();
    }
}