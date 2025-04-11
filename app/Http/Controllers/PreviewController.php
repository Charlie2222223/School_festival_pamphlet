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

        $class_name = session('class_name');
        $class = Classes::find($classId);
        $uploadedImages = UploadedImage::where('class_id', $classId)->get();

        // 最新の保存済みコードを取得
        $latestCode = CodeSave::where('class_id', $classId)
            ->orderBy('created_at', 'desc')
            ->first();

        // クラス分類も渡す
        $rClasses = Classes::where('class_name', 'like', 'R%')->get();
        $sClasses = Classes::where('class_name', 'like', 'S%')->get();
        $jClasses = Classes::where('class_name', 'like', 'J%')->get();

        return view('preview', [
            'class' => $class,
            'uploadedImages' => $uploadedImages,
            'rClasses' => $rClasses,
            'sClasses' => $sClasses,
            'jClasses' => $jClasses,
            'class_name' => $class_name,
            // 保存済みコードを渡す
            'html_code' => $latestCode?->html_code,
            'css_code'  => $latestCode?->css_code,
            'js_code'   => $latestCode?->js_code,
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

        $class = Classes::find($classId);

        // 最新の保存済みコードを取得
        $latestCode = CodeSave::where('class_id', $classId)
            ->orderBy('created_at', 'desc')
            ->first();

        return view('preview', [
            'uploadedImages' => $class?->uploadedImages ?? [],
            'html_code' => $latestCode?->html_code,
            'css_code'  => $latestCode?->css_code,
            'js_code'   => $latestCode?->js_code,
            'rClasses'  => Classes::where('class_name', 'like', 'R%')->get(),
            'sClasses'  => Classes::where('class_name', 'like', 'S%')->get(),
            'jClasses'  => Classes::where('class_name', 'like', 'J%')->get(),
        ]);
    }
}