<?php

use Illuminate\Http\UploadedFile;

if (!function_exists('fileUpload')) {

    /**
     * @param UploadedFile $file
     * @param string $folder
     * @return string
     */
    function fileUpload(UploadedFile $file, string $folder): string
    {
        $name = uniqid(date('HisYmd'));

        $ext = $file->extension();

        $full_name = "{$name}.{$ext}";

        $file->storeAs($folder, $full_name);

        return env('APP_URL') . "/storage/$folder/$full_name";
    }

}

if (!function_exists('generateCode')) {

    /**
     * @return string
     */
    function generateCode($length = 5): string
    {
        $array = [];

        while (count($array) < $length) {

            $n = rand(0, 9);

            if (count($array) == 0 || in_array($n, $array) == false) {

                $array[] = $n;

            }

        }

        return "{$array[0]}{$array[1]}{$array[2]}{$array[3]}{$array[4]}";
    }

}
