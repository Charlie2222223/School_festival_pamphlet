<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classes;
use App\Models\CodeSave;
use App\Models\UploadedImage;

class AdminController extends Controller
{
    public function index()
    {
        // authority_id が 1（管理者）以外のクラスを取得
        $allClasses = Classes::where('authority_id', '!=', 1)->orWhereNull('authority_id')->get();

        // 各クラスに関連する最新の CodeSave を取得
        foreach ($allClasses as $class) {
            $latestCode = CodeSave::where('class_id', $class->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $class->html_code = $latestCode->html_code ?? null;
            $class->css_code = $latestCode->css_code ?? null;
            $class->js_code = $latestCode->js_code ?? null;
        }

        // クラス名のパターンに基づいて分類
        $rClasses = Classes::where('class_name', 'like', 'R%')->where('authority_id', '!=', 1)->orWhereNull('authority_id')->get();
        $sClasses = Classes::where('class_name', 'like', 'S%')->where('authority_id', '!=', 1)->orWhereNull('authority_id')->get();
        $jClasses = Classes::where('class_name', 'like', 'J%')->where('authority_id', '!=', 1)->orWhereNull('authority_id')->get();

        // ビューにデータを渡す
        return view('poster_admin', [
            'allClasses' => $allClasses,
            'rClasses' => $rClasses,
            'sClasses' => $sClasses,
            'jClasses' => $jClasses,
        ]);
    }
public function admin_edit()
{
    // セッションにクラスIDがない場合はログイン画面にリダイレクト
    if (!session()->has('class_id')) {
        return redirect('/login');
    }

    // アップロードされた画像を取得
    $uploadedImages = UploadedImage::where('class_id', session('class_id'))->get();

    // クラス名のパターンに基づいて分類
    $rClasses = Classes::where('class_name', 'like', 'R%')->get();
    $sClasses = Classes::where('class_name', 'like', 'S%')->get();
    $jClasses = Classes::where('class_name', 'like', 'J%')->get();

    // 各クラスに関連する最新のコードを取得
    $allClasses = Classes::all();
    foreach ($allClasses as $class) {
        $latestCode = CodeSave::where('class_id', $class->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $class->html_code = $latestCode->html_code ?? null;
        $class->css_code = $latestCode->css_code ?? null;
        $class->js_code = $latestCode->js_code ?? null;
    }

    // ビューにデータを渡す
    return view('acount', compact('uploadedImages', 'rClasses', 'sClasses', 'jClasses', 'allClasses'));
}

    public function admin_show()
{
    // authority_id が 1（管理者）以外のクラスを取得
    $allClasses = Classes::where('authority_id', '!=', 1)->orWhereNull('authority_id')->get();

    // 各クラスに関連する最新の CodeSave を取得
    foreach ($allClasses as $class) {
        $latestCode = CodeSave::where('class_id', $class->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $class->html_code = $latestCode->html_code ?? null;
        $class->css_code = $latestCode->css_code ?? null;
        $class->js_code = $latestCode->js_code ?? null;
    }

    // クラス名のパターンに基づいて分類
    $rClasses = Classes::where('class_name', 'like', 'R%')->where('authority_id', '!=', 1)->orWhereNull('authority_id')->get();
    $sClasses = Classes::where('class_name', 'like', 'S%')->where('authority_id', '!=', 1)->orWhereNull('authority_id')->get();
    $jClasses = Classes::where('class_name', 'like', 'J%')->where('authority_id', '!=', 1)->orWhereNull('authority_id')->get();

    // ビューにデータを渡す
    return view('poster_admin', [
        'allClasses' => $allClasses,
        'rClasses' => $rClasses,
        'sClasses' => $sClasses,
        'jClasses' => $jClasses,
    ]);
}

public function getClassCode($id)
{
    $class = Classes::find($id);

    if (!$class) {
        return response()->json(['success' => false, 'message' => 'クラスが見つかりません']);
    }

    $latestCode = CodeSave::where('class_id', $id)->orderBy('created_at', 'desc')->first();
    $images = UploadedImage::where('class_id', $id)->get(['filename', 'path']);

    return response()->json([
        'success' => true,
        'class_name' => $class->class_name,
        'html_code' => $latestCode->html_code ?? null,
        'css_code' => $latestCode->css_code ?? null,
        'js_code' => $latestCode->js_code ?? null,
        'images' => $images,
    ]);
}

}
