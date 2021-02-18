<?php

namespace App\Repositories;

use App\Exceptions\CustomException;
use App\Models\Product;
use App\Models\Segment;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class SegmentRepository extends BaseRepository implements SegmentRepositoryInterface
{
    /**
     * SegmentRepository constructor.
     *
     * @param Segment $model
     */
    public function __construct(Segment $model)
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

        return Segment::where('company_id', $company_id)
            ->orderBy('position', 'asc')
            ->get();
    }

    /**
     * @param array $attributes
     * @return Segment
     */
    public function create(array $attributes): Segment
    {
        $this->validateCreate($attributes);

        $segment = new Segment([
            'company_id' => $attributes['company_id'],
            'name' => $attributes['name']
        ]);

        $segment->position = Segment::where('company_id', $segment->company_id)->count() + 1;

        $segment->save();

        return $segment;
    }

    /**
     * @param array $attributes
     * @return Collection
     */
    public function reorder(array $attributes): Collection
    {
        $this->validateReorder($attributes);

        foreach ($attributes['segments'] as $key => $segment) {

            Segment::where('id', $segment['id'])->update(['position' => $key + 1]);
            
        }

        return Segment::where('company_id', $attributes['company_id'])
            ->orderBy('position', 'asc')
            ->get();
    }

    /**
     * @param mixed $id
     * @param array $attributes
     * @return Segment
     */
    public function update($id, array $attributes): Segment
    {
        $this->validateUpdate($attributes);

        $segment = Segment::where('id', $id)
            ->where('company_id', $attributes['company_id'])
            ->first();

        if (!$segment) {
            throw new CustomException('Segmento não encontrado.', 422);
        }

        $segment->name = $attributes['name'];

        $segment->save();

        return $segment;
    }

    /**
     * @param mixed $id
     * @param mixed $company_id
     * @return Collection
     */
    public function delete($id, $company_id = null): void
    {
        if (!CompanyRepository::checkAuth($company_id)) {
            throw new CustomException('Empresa não autorizada.', 422);
        }

        $segment = Segment::where('id', $id)
            ->where('company_id', $company_id)
            ->first();

        if (!$segment) {
            throw new CustomException('Segmento não encontrado.', 422);
        }

        if (Product::where('segment_id', $segment->id)->count() > 0) {
            throw new CustomException('Segmento não pode ser excluído pois possui produtos vinculados.', 200);
        }

        $segment->delete();

        Segment::where('company_id', $company_id)
            ->where('position', '>', $segment->position)
            ->decrement('position');
    }

    /**
     * @param array $attributes
     * @return void
     */
    private function validateCreate(array $attributes): void
    {
        $validator = Validator::make($attributes, [
            'name' => 'required|string|max:40',
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
    private function validateReorder(array $attributes): void
    {
        $validator = Validator::make($attributes, [
            'company_id' => [
                'required', 'numeric',
                function ($attribute, $value, $fail) {
                    if (!CompanyRepository::checkAuth($value)) {
                        $fail('Empresa não autorizada.');
                    }
                }
            ],
            'segments.*.id' => [
                'required', 'numeric',
                function ($attribute, $value, $fail) use ($attributes) {
                    $segment = Segment::where('id', $value)
                        ->where('company_id', $attributes['company_id'])
                        ->count();

                    if (!$segment) {
                        $fail("Segmento id {$value} não encontrado.");
                    }
                }
            ],
            'segments' => [
                'required', 'array',
                function ($attribute, $value, $fail) use ($attributes) {
                    $segments = Segment::where('company_id', $attributes['company_id'])
                        ->whereNotIn('id', Arr::pluck($value, 'id'))
                        ->get();

                    if ($segments->count() > 0) {
                        $fail("Segmentos ausentes: {$segments->implode('id', ',')}.");
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
            'name' => 'required|string|max:40',
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