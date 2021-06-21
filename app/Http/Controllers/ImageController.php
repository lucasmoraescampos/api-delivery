<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;

class ImageController extends Controller
{
    public function show(Request $request, $image)
    {
        try {

            $request->validate([
                'width' => 'nullable|numeric',
                'height' => 'nullable|numeric'
            ]);

            $img = Image::make(storage_path('app/public/images/' . $image));

            if ($request->width || $request->height) {

                $img->resize($request->width, $request->height, function ($constraint) {
                    $constraint->aspectRatio();
                });

            }

            $parts = explode('.', $image);
    
            return $img->response(end($parts));

        } catch (Exception $e) {

            abort(404);
            
        }
    }
}
