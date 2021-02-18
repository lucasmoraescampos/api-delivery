<?php

namespace App\Repositories;

use App\Models\Card;
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
     * @param array $attributes
     * @return Card
     */
    public function create(array $attributes): Card
    {
        $this->validateCreate($attributes);

        $card = Card::create([
            'user_id' => Auth::id(),
            'number' => $attributes['number'],
            'expiration_month' => $attributes['expiration_month'],
            'expiration_year' => $attributes['expiration_year'],
            'security_code' => $attributes['security_code'],
            'holder_name' => $attributes['holder_name'],
            'document_type' => $attributes['document_type'],
            'document_number' => $attributes['document_number']
        ]);

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
            'document_type' => 'required|string|max:10',
            'document_number' => 'required|string|max:20',
        ]);

        $validator->validate();
    }
}