<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CodeSave;

class CodeController extends Controller
{
    public function save(Request $request)
    {
        $html = $request->input('html');
        $css  = $request->input('css');
        $js   = $request->input('js');
        $classId = session('class_id');

        if (!$classId) {
            return response()->json(['success' => false, 'message' => 'クラスIDがセッションに存在しません'], 400);
        }

        // クラスに関連付けられたコードを保存
        $codeSave = CodeSave::create([
            'class_id' => $classId,
            'save_number' => $request->input('save_number', 1), // デフォルトで 1 を設定
            'html_code' => $html,
            'css_code' => $css,
            'js_code' => $js,
            'main_save_date' => $request->input('main_save_date', false), // デフォルトで false
        ]);

        return response()->json(['success' => true, 'code_save_id' => $codeSave->id]);
    }

    public function update(Request $request, $id)
    {
        $html = $request->input('html');
        $css  = $request->input('css');
        $js   = $request->input('js');

        // 指定された ID のコードを取得
        $codeSave = CodeSave::find($id);

        if ($codeSave) {
            $codeSave->html_code = $html;
            $codeSave->css_code = $css;
            $codeSave->js_code = $js;
            $codeSave->main_save_date = $request->input('main_save_date', $codeSave->main_save_date);
            $codeSave->save();

            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'コードが見つかりません'], 404);
        }
    }
}