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
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        else {
            return false;
        }
    }
}
