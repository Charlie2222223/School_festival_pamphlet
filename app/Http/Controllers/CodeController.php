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
        $comment = $request->input('comment'); // コメントを取得
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
            'comment' => $comment, // コメントを保存
            'main_save_date' => $request->input('main_save_date', false), // デフォルトで false
        ]);
        return response()->json(['success' => true, 'code_save_id' => $codeSave->id]);
    }

    // public function update(Request $request, $id)
    // {
    //     $html = $request->input('html');
    //     $css  = $request->input('css');
    //     $js   = $request->input('js');
    //     $classId = session('class_id');

    //     if (!$classId) {
    //         return response()->json(['success' => false, 'message' => 'クラスIDがセッションに存在しません'], 400);
    //     }

    //     // 新しい履歴として保存
    //     $codeSave = CodeSave::create([
    //         'class_id' => $classId,
    //         'save_number' => $request->input('save_number', 1), // デフォルトで 1 を設定
    //         'html_code' => $html,
    //         'css_code' => $css,
    //         'js_code' => $js,
    //         'main_save_date' => $request->input('main_save_date', false), // デフォルトで false
    //     ]);

    //     return response()->json(['success' => true, 'code_save_id' => $codeSave->id]);
    // }

    public function getHistory($classId)
    {
        $history = CodeSave::where('class_id', $classId)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json(['success' => true, 'history' => $history]);
    }

    public function delete(Request $request)
    {
        $historyIds = $request->input('history_ids', []);

        if (empty($historyIds)) {
            return response()->json(['success' => false, 'message' => '削除する履歴が選択されていません。']);
        }

        CodeSave::whereIn('id', $historyIds)->delete();

        return response()->json(['success' => true, 'message' => '選択した履歴を削除しました。']);
    }
}