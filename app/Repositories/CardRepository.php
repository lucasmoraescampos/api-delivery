<?php

namespace App\Repositories;

use App\Exceptions\CustomException;
use App\Models\Card;
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
            'document_number',
            'icon'
        ]));

        $card->user_id = Auth::id();

        $card->document_type = strlen($card->document_number) == 11 ? 'CPF' : 'CNPJ';

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
            'icon' => 'nullable|string',
        ]);

        $validator->validate();
    }
}