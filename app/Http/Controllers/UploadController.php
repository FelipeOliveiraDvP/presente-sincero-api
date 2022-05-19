<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|mimes:jpeg,png,jpg|max:4096'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro ao salvar a imagem!',
                'errors' => $validator->errors()
            ], 400);
        }

        $upload_folder = 'img';
        $image = $request->file('image');
        $upload_path = $image->store($upload_folder);

        $image->move(public_path() . '/uploads', $upload_path);

        $uploaded_image = [
            'title' => basename($upload_path),
            'path' => "/uploads/" . basename($upload_path),
        ];

        $created_image = Image::create($uploaded_image);

        return response()->json([
            'message' => 'Imagem carregada com sucesso!',
            'image'    => $created_image
        ], 201);
    }
}
