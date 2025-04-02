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
            $className = $request->input('class_name', 'default');
            $directory = 'uploads/' . $className;

            // オリジナルのファイル名で保存
            $filename = $file->getClientOriginalName();
            $path = $file->storeAs($directory, $filename, 'public');

            // DB保存処理
            $uploadedImage = new UploadedImage();
            $uploadedImage->filename = $filename;
            $uploadedImage->path = $path;
            $uploadedImage->class_name = $className; // クラス名を保存
            $uploadedImage->class_id = $request->input('class_id'); // クラスIDを保存
            $uploadedImage->uploaded_by = $request->user()->id ?? null; // ユーザーIDを保存
            $uploadedImage->save();

            return response()->json([
                'success' => true,
                'image_url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['success' => false], 400);
    }

    public function delete(Request $request)
    {
        $imageIds = $request->input('image_ids', []);
        foreach ($imageIds as $id) {
            $image = UploadedImage::find($id);
            if ($image) {
                // ストレージからファイルを削除
                Storage::disk('public')->delete($image->path);

                // データベースからレコードを削除
                $image->delete();
            }
        }

        return response()->json(['success' => true]);
    }
}