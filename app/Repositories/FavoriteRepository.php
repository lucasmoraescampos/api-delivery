<?php

namespace App\Repositories;

use App\Exceptions\CustomException;
use App\Models\Company;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class FavoriteRepository extends BaseRepository implements FavoriteRepositoryInterface
{
    /**
     * FavoriteRepository constructor.
     *
     * @param Favorite $model
     */
    public function __construct(Favorite $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $attributes
     * @return Favorite
     */
    public function create(array $attributes): Favorite
    {
        $this->validateCreate($attributes);

        if (User::where('id', $attributes['user_id'])->count() == 0) {
            throw new CustomException('User id not found.', 404);
        }

        if (Company::where('id', $attributes['company_id'])->count() == 0) {
            throw new CustomException('Company id not found.', 404);
        }

        if (Favorite::where('user_id', $attributes['user_id'])
            ->where('company_id', $attributes['company_id'])
            ->count() > 0) {
                throw new CustomException('Company is already a favorite.', 400);
            }

        $favorite = Favorite::create([
            'user_id'    => $attributes['user_id'],
            'company_id' => $attributes['company_id']
        ]);

        return $favorite;
    }

    /**
     * @param array $attributes
     * @return void
     */
    private function validateCreate(array $attributes): void 
    {
        $validator = Validator::make($attributes, [
            'user_id' => 'required|numeric',
            'company_id' => 'required|numeric',
        ]);

        $validator->validate();
    }
}