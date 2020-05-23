<?php

namespace App\Rules;

use App\Complement;
use Illuminate\Contracts\Validation\Rule;

class RequiredComplementsRule implements Rule
{
    const COMPLEMENTS_NOT_FOUND = 1;

    const QTY_MIN_ERROR = 2;

    const COMPLEMENT_REQUIRED_ERROR = 3;

    private $error_complement_id;

    private $error_code;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->products = $products;



        // $this->complements = [];

        // if ($subcomplements == null) return;

        // foreach ($subcomplements as $subcomplement) {

        //     $index = array_search($subcomplement['complement_id'], array_column($this->complements, 'id'));

        //     if ($index !== false) {

        //         $this->complements[$index]['amount']++;
        //     } else {

        //         $this->complements[] = [
        //             'id' => $subcomplement['complement_id'],
        //             'amount' => 1
        //         ];
        //     }
        // }
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        dd($attribute);
        // $requireds = Complement::where('product_id', $product_id)
        //     ->where('is_required', 1)
        //     ->get();

        // if (count($requireds) == 0) {

        //     return true;

        // }
        
        // elseif (count($this->complements) == 0) {

        //     $this->error_code = self::COMPLEMENTS_NOT_FOUND;

        //     return false;

        // }
        
        // else {

        //     return $this->checkRequireds($requireds);

        // }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        // switch ($this->error_code) {

        //     case self::COMPLEMENTS_NOT_FOUND:

        //         return 'O Array de subcomplementos ou o atributo complement_id de um subcomplemento não foi encontrado.';

        //         break;

        //     case self::QTY_MIN_ERROR:

        //         return 'O Complemento ' . $this->error_complement_id . ' não satisfaz a condição de quantidade mínima.';

        //         break;

        //     case self::COMPLEMENT_REQUIRED_ERROR:

        //         return 'O Complemento ' . $this->error_complement_id . ' é obrigatório.';

        //         break;
        // }

        return 'aaaa';
    }

    private function checkRequireds($requireds)
    {
        foreach ($requireds as $required) {

            $index = array_search($required->id, array_column($this->complements, 'id'));

            if ($index !== false) {

                if ($this->complements[$index]['amount'] < $required->qty_min) {

                    $this->error_complement_id = $required->id;

                    $this->error_code = self::QTY_MIN_ERROR;

                    return false;

                }

            }
            
            else {

                $this->error_complement_id = $required->id;

                $this->error_code = self::COMPLEMENT_REQUIRED_ERROR;

                return false;

            }

        }

        return true;
    }
}
