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

        return env('IMAGES_URL') . "/$folder/$full_name";
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


if (!function_exists('validateDocumentNumber')) {

    /**
     * @return string
     */
    function validateDocumentNumber($document_number) {

        if(empty($document_number))
            return false;

        $document_number = preg_replace('/[^0-9]/', '', $document_number);

        if (strlen($document_number) == 11) {

            if (preg_match('/(\d)\1{10}/', $document_number))
                return false;

            for ($t = 9; $t < 11; $t++) {

                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $document_number[$c] * (($t + 1) - $c);
                }

                $d = ((10 * $d) % 11) % 10;

                if ($document_number[$c] != $d) {
                    return false;
                }
            }

            return true;

        }

        elseif (strlen($document_number) == 14) {

            if (preg_match('/(\d)\1{13}/', $document_number))
                return false;

            $b = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

            for ($i = 0, $n = 0; $i < 12; $n += $document_number[$i] * $b[++$i]);

            if ($document_number[12] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
                return false;
            }

            for ($i = 0, $n = 0; $i <= 12; $n += $document_number[$i] * $b[$i++]);

            if ($document_number[13] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
                return false;
            }

            return true;

        }

        else {
            return false;
        }
    }
}