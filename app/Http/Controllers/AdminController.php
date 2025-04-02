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
}
