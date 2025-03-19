<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UploadedImage;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image',
            'class_id' => 'required|exists:classes,id',
        ]);

        $path = $request->file('image')->store('public/uploads');
        $filename = basename($path);

        UploadedImage::create([
            'class_id' => $request->class_id,
            'filename' => $filename,
            'path' => $path,
        ]);

        return response()->json(['success' => true, 'message' => 'アップロード成功']);
    }
}