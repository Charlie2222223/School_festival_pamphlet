<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\CodeSave;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login'); // login.blade.php を表示
    }

    public function login(Request $request)
    {
        // 入力値のバリデーション
        $request->validate([
            'class_name' => 'required|string',
            'password' => 'required|string',
        ]);

        // クラス名でクラスを取得
        $class = Classes::where('class_name', $request->class_name)->first();

        // クラス名が存在しない場合
        if (!$class) {
            return response()->json([
                'message' => 'クラス名が間違っています。',
                'error_type' => 'class_name',
            ], 401);
        }

        // パスワードが一致しない場合
        if (!Hash::check($request->password, $class->password)) {
            return response()->json([
                'message' => 'パスワードが間違っています。',
                'error_type' => 'password',
            ], 401);
        }

        // セッションに保存
        Session::put('class_id', $class->id);
        Session::put('class_name', $class->class_name);

        // 管理者の場合
        if ($class->authority_id == 1) {
            return response()->json([
                'redirect_url' => url('/poster_admin'),
            ]);
        }

        // 一般ユーザーの場合
        return response()->json([
            'redirect_url' => url('/poster_list'),
        ]);
    }

    public function show_poster()
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
        return view('poster', [
            'allClasses' => $allClasses,
            'rClasses' => $rClasses,
            'sClasses' => $sClasses,
            'jClasses' => $jClasses,
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->flush(); // セッションを全て削除
        return redirect()->route('login.page'); // ログインページへリダイレクト
    }
}