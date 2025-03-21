<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UploadedImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {

        if ($request->hasFile('image')) {
            $file = $request->file('image');
    
            // クラス名から保存先ディレクトリを作成
            $className = $request->class_name ?? 'default'; // ← 必ず送られてくるようにしておく
            $directory = 'uploads/' . $className;
    
            // 画像を指定のディレクトリに保存
            $path = $file->store($directory, 'public');
    
            // dd(Storage::disk('public')->path('uploads/' . $request->class_name . '/' . $file->getClientOriginalName()));

            // DB保存処理
            $uploadedImage = new UploadedImage();
            $uploadedImage->filename = $file->getClientOriginalName();
            $uploadedImage->path = $path;
            $uploadedImage->class_id = $request->class_id;
            $uploadedImage->save();
    
            return response()->json([
                'success' => true,
                'image_url' => asset('storage/' . $path)
            ]);
        }

        \Log::info('保存先パス：' . Storage::disk('public')->path($path));
    
        return response()->json(['success' => false], 400);
    }

    public function delete(Request $request)
{
    $imageIds = $request->input('image_ids', []);
    foreach ($imageIds as $id) {
        $image = UploadedImage::find($id);
        if ($image) {
            Storage::delete($image->path);
            $image->delete();
        }
    }

    return response()->json(['success' => true]);
}
}   