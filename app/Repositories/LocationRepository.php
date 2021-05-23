<?php

namespace App\Repositories;

use App\Exceptions\CustomException;
use App\Models\Location;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LocationRepository implements LocationRepositoryInterface
{
    /**
     * @return Collection
     */
    public function getByAuth(): Collection
    {
        return Location::where('user_id', Auth::id())
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * @param array $attributes
     * @return Location
     */
    public function create(array $attributes): Location
    {
        $this->validateCreate($attributes);

        $location = new Location(Arr::only($attributes, [
            'street_name',
            'street_number',
            'complement',
            'district',
            'city',
            'uf',
            'postal_code',
            'country',
            'latitude',
            'longitude',
            'type'
        ]));

        $location->user_id = Auth::id();

        $location->save();

        return $location;
    }

    /**
     * @param mixed $id
     * @param array $attributes
     * @return Location
     */
    public function update($id, array $attributes): Location
    {
        $this->validateUpdate($attributes);

        $location = Location::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$location) {
            throw new CustomException('Localização não encontrada.', 422);
        }

        $location->fill(Arr::only($attributes, [
            'street_name',
            'street_number',
            'complement',
            'district',
            'city',
            'uf',
            'postal_code',
            'country',
            'latitude',
            'longitude',
            'type'
        ]));

        $location->save();

        return $location;
    }

    /**
     * @param mixed $id
     * @return void
     */
    public function delete($id): void
    {
        $location = Location::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$location) {
            throw new CustomException('Localização não encontrada.', 422);
        }

        $location->delete();
    }

    /**
     * @param array $attributes
     * @return void
     */
    private static function validateCreate(array $attributes)
    {
        $validator = Validator::make($attributes, [
            'street_name' => 'required|string|max:255',
            'street_number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'district' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'uf' => 'required|string|max:2',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:100',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => ['nullable', 'numeric', function ($attribute, $value, $fail) {
                if ($value != 1 && $value != 2) {
                    $fail('Tipo inválido.');
                }
            }]
        ]);

        $validator->validate();
    }

    /**
     * @param array $attributes
     * @return void
     */
    private static function validateUpdate(array $attributes)
    {
        $validator = Validator::make($attributes, [
            'street_name' => 'nullable|string|max:255',
            'street_number' => 'nullable|string|max:20',
            'complement' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'type' => ['nullable', 'numeric', function ($attribute, $value, $fail) {
                if ($value != 1 && $value != 2) {
                    $fail('Tipo inválido.');
                }
            }]
        ]);

        $validator->validate();
    }
}
