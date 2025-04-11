<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classes;
use App\Models\CodeSave;
use Illuminate\Support\Facades\DB;
use App\Models\UploadedImage;
use Carbon\Carbon;

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
        $loggedInUsers = session('logged_in_users', []);
        $classId = session('class_id');

        // ログインしていない場合はリダイレクト
        if (!collect($loggedInUsers)->contains('class_id', $classId)) {
            return redirect('/login');
        }

        // 管理者の場合は class_id を 4 に設定
        $authorityId = session('authority_id');
        if ($authorityId === 1) {
            $classId = 4; // 管理者用の class_id を設定
        }

        $class_name = session('class_name');
        $class = Classes::find($classId);

        $latestCode = CodeSave::where('class_id', $classId)
            ->orderBy('updated_at', 'desc')
            ->first();

        $uploadedImages = UploadedImage::where('class_id', $classId)->get();

        $rClasses = Classes::where('class_name', 'like', 'R%')->get();
        $sClasses = Classes::where('class_name', 'like', 'S%')->get();
        $jClasses = Classes::where('class_name', 'like', 'J%')->get();

        return view('acount', [
            'latest_update' => $latestCode?->updated_at,
            'uploadedImages' => $uploadedImages,
            'rClasses' => $rClasses,
            'sClasses' => $sClasses,
            'jClasses' => $jClasses,
            'html_code' => $latestCode?->html_code,
            'css_code' => $latestCode?->css_code,
            'js_code' => $latestCode?->js_code,
        ]);
    }

    public function admin_show()
    {
        // authority_id が 1（管理者）以外のクラスを取得
        $allClasses = Classes::where(function ($query) {
            $query->where('authority_id', '!=', 1)
                ->orWhereNull('authority_id');
        })->get();

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
        $rClasses = $allClasses->filter(fn($class) => str_starts_with($class->class_name, 'R'));
        $sClasses = $allClasses->filter(fn($class) => str_starts_with($class->class_name, 'S'));
        $jClasses = $allClasses->filter(fn($class) => str_starts_with($class->class_name, 'J'));

        // ビューにデータを渡す
        return view('poster_admin', compact('allClasses', 'rClasses', 'sClasses', 'jClasses'));
    }
    
    public function admin_classes()
    {
        // 全クラスを取得（authority_name をリレーションで取得）
        $classes = Classes::with('authority')->get();

        // クラス名のパターンに基づいて分類
        $rClasses = Classes::with('authority')->where('class_name', 'like', 'R%')->get();
        $sClasses = Classes::with('authority')->where('class_name', 'like', 'S%')->get();
        $jClasses = Classes::with('authority')->where('class_name', 'like', 'J%')->get();

        // セッションに保存されたログイン中のユーザー情報を取得
        $logged_in_users = session('logged_in_users', []);

        // ビューにデータを渡す
        return view('admin_user', compact('classes', 'rClasses', 'sClasses', 'jClasses', 'logged_in_users'));
    }

    public function getClassCode($id)
    {
        $class = Classes::find($id);

        if (!$class) {
            return response()->json(['success' => false, 'message' => 'クラスが見つかりません']);
        }

        // 最新のコードを取得
        $latestCode = CodeSave::where('class_id', $id)->orderBy('created_at', 'desc')->first();

        // アップロードされた画像を取得
        $images = UploadedImage::where('class_id', $id)->get(['filename', 'path']);

        return response()->json([
            'success' => true,
            'class_name' => $class->class_name,
            'html_code' => $latestCode->html_code ?? null,
            'css_code' => $latestCode->css_code ?? null,
            'js_code' => $latestCode->js_code ?? null,
            'comment' => $latestCode->comment ?? 'コメントがありません',
            'images' => $images,
        ]);
    }
}
