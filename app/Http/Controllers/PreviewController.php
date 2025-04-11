<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classes;
use App\Models\UploadedImage;
use App\Models\CodeSave;

class PreviewController extends Controller
{
    public function index()
    {
        // セッションの logged_in_users に class_id が存在するか確認
        $loggedInUsers = session('logged_in_users', []);
        $classId = session('class_id');

        // ログインしていない場合はリダイレクト
        if (!collect($loggedInUsers)->contains('class_id', $classId)) {
            return redirect('/login');
        }

        // クラス分類を取得
        $rClasses = Classes::where('class_name', 'like', 'R%')->get();
        $sClasses = Classes::where('class_name', 'like', 'S%')->get();
        $jClasses = Classes::where('class_name', 'like', 'J%')->get();

        return view('preview', compact('rClasses', 'sClasses', 'jClasses'));
    }

    public function previewPage()
    {
        // セッションの logged_in_users に class_id が存在するか確認
        $loggedInUsers = session('logged_in_users', []);
        $classId = session('class_id');

        // ログインしていない場合はリダイレクト
        if (!collect($loggedInUsers)->contains('class_id', $classId)) {
            return redirect('/login');
        }

        // 管理者の場合は class_id を 4 に設定
        $authorityId = session('authority_id'); // セッションから authority_id を取得
        if ($authorityId === 1) { // authority_id が 1 の場合は管理者
            $classId = 5; // 管理者用の class_id を 4 に設定
        }

        // 現在のクラス情報を取得
        $class_name = session('class_name');
        $class = Classes::find($classId);

        // 最新の保存済みコードを取得
        $latestCode = CodeSave::where('class_id', $classId)
            ->orderBy('updated_at', 'desc') // updated_at を基準に取得
            ->first();

        // アップロードされた画像を取得
        $uploadedImages = UploadedImage::where('class_id', $classId)->get();

        // クラス分類を取得
        $rClasses = Classes::where('class_name', 'like', 'R%')->get();
        $sClasses = Classes::where('class_name', 'like', 'S%')->get();
        $jClasses = Classes::where('class_name', 'like', 'J%')->get();

        return view('acount', [
            'latest_update' => $latestCode?->updated_at, // 最新の更新日時を渡す
            'uploadedImages' => $uploadedImages,
            'rClasses' => $rClasses,
            'sClasses' => $sClasses,
            'jClasses' => $jClasses,
            'html_code' => $latestCode?->html_code,
            'css_code' => $latestCode?->css_code,
            'js_code' => $latestCode?->js_code,
        ]);
    }

    public function showPreview()
    {
        // セッションの logged_in_users に class_id が存在するか確認
        $loggedInUsers = session('logged_in_users', []);
        $classId = session('class_id');

        // ログインしていない場合はリダイレクト
        if (!collect($loggedInUsers)->contains('class_id', $classId)) {
            return redirect('/login');
        }

        // 現在のクラス情報を取得
        $class = Classes::find($classId);

        // 最新の保存済みコードを取得
        $latestCode = CodeSave::where('class_id', $classId)
            ->orderBy('updated_at', 'desc') // updated_at を基準に取得
            ->first();

        // クラス分類を取得
        $rClasses = Classes::where('class_name', 'like', 'R%')->get();
        $sClasses = Classes::where('class_name', 'like', 'S%')->get();
        $jClasses = Classes::where('class_name', 'like', 'J%')->get();

        return view('preview', [
            'uploadedImages' => $class?->uploadedImages ?? [],
            'html_code' => $latestCode?->html_code,
            'css_code'  => $latestCode?->css_code,
            'js_code'   => $latestCode?->js_code,
            'rClasses'  => $rClasses,
            'sClasses'  => $sClasses,
            'jClasses'  => $jClasses,
            'latest_update' => $latestCode?->updated_at, // 最新の更新日時を渡す
        ]);
    }
}