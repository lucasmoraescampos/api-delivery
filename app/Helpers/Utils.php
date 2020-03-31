<?php

if (!function_exists('percentValue')) {

    function percentValue($base, $value)
    {
        $base = $base;
        $value = $value;

        return $value * 100 / $base;
    }
}

if (!function_exists('validateEmail')) {

    function validateEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('generateCode')) {

    function generateCode()
    {
        $array = [];

        while (count($array) < 4) {

            $n = rand(0, 9);

            if (count($array) == 0 || in_array($n, $array) == false) {
                $array[] = $n;
            }
        }

        return "{$array[0]}{$array[1]}{$array[2]}{$array[3]}";
    }
}
