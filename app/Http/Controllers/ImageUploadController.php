<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UploadedImage;
use App\Models\Classes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        try {
            // ファイルがアップロードされているか確認
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                // クラスIDを取得
                $classId = $request->input('class_id');
                $class = Classes::find($classId);

                if (!$class) {
                    return response()->json(['success' => false, 'message' => '指定されたクラスが存在しません'], 400);
                }

                // 保存先ディレクトリをクラス名で作成
                $directory = 'uploads/' . $class->class_name;

                // オリジナルのファイル名で保存
                $filename = $file->getClientOriginalName();
                $path = $file->storeAs($directory, $filename, 'public');

                // DB保存処理
                $uploadedImage = new UploadedImage();
                $uploadedImage->filename = $filename;
                $uploadedImage->path = $path;
                $uploadedImage->class_id = $classId; // クラスIDを保存
                $uploadedImage->save();

                return response()->json([
                    'success' => true,
                    'image_url' => asset('storage/' . $path)
                ], 200);
            }

            return response()->json(['success' => false, 'message' => 'ファイルがアップロードされていません'], 400);
        } catch (\Exception $e) {
            // エラーをログに記録
            Log::error('Image upload failed: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => '画像のアップロード中にエラーが発生しました'], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
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
        } catch (\Exception $e) {
            // エラーをログに記録
            Log::error('Image delete failed: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => '画像の削除中にエラーが発生しました'], 500);
        }
    }
}