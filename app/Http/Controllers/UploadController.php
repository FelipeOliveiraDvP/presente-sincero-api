<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'   => 'required',
            'image.*' => 'mimes:jpeg,png,jpg|max:4096'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro ao salvar a imagens!',
                'errors' => $validator->errors()
            ], 400);
        }

        $created_images = [];
        foreach ($request->file('image') as $image) {
            $upload_folder = 'img';
            $upload_path = $image->store($upload_folder);

            $image->move(public_path() . '/uploads', $upload_path);

            $uploaded_image = [
                'title' => basename($upload_path),
                'path' => "/uploads/" . basename($upload_path),
            ];

            $created_image = Image::create($uploaded_image);
            $created_images[] = $created_image;
        }

        return response()->json([
            'message' => 'Imagens carregadas com sucesso!',
            'images'    => $created_images
        ], 201);
    }
}
