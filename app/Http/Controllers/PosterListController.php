<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classes;
use App\Models\CodeSave;

class PosterListController extends Controller
{
    public function index()
    {
        // 全てのクラスを取得
        $allClasses = Classes::all();

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
        $rClasses = Classes::where('class_name', 'like', 'R%')->get();
        $sClasses = Classes::where('class_name', 'like', 'S%')->get();
        $jClasses = Classes::where('class_name', 'like', 'J%')->get();

        // ビューにデータを渡す
        return view('poster_list', [
            'allClasses' => $allClasses,
            'rClasses' => $rClasses,
            'sClasses' => $sClasses,
            'jClasses' => $jClasses,
        ]);
    }
}