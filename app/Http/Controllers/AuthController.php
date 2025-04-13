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
        $request->validate([
            'class_name' => 'required|string',
            'password' => 'required|string',
            'email' => 'nullable|email',
        ]);

        $class = Classes::where('class_name', $request->class_name)->first();

        if (!$class) {
            return response()->json(['error_type' => 'class_name', 'message' => 'クラス名が間違っています。'], 422);
        }

        if (!Hash::check($request->password, $class->password)) {
            return response()->json(['error_type' => 'password', 'message' => 'パスワードが間違っています。'], 422);
        }

        // 管理者クラスの場合
        if ($class->authority_id === 1) {
            // セッションにクラス情報を追加
            $loggedInUsers = session('logged_in_users', []);
            $loggedInUsers[] = [
                'class_id' => $class->id,
                'class_name' => $class->class_name,
                'authority_id' => $class->authority_id,
            ];
            session(['class_id' => $class->id]);
            session(['authority_id' => $class->authority_id]);
            session(['logged_in_users' => $loggedInUsers]);

            // JSON レスポンスを返す
            return response()->json([
                'redirect_url' => route('poster_admin'),
            ]);
        }

        // 初回ログインの場合
        if ($class->is_first_login) {
            if (!$request->email) {
                return response()->json([
                    'is_first_login' => true,
                    'message' => '初回ログインです。メールアドレスを入力してください。',
                ]);
            }

            $emailDomain = substr(strrchr($request->email, "@"), 1);
            if ($emailDomain !== 'ocsjoho.onmicrosoft.com') {
                return response()->json([
                    'error_type' => 'email',
                    'message' => 'ocsjoho.onmicrosoft.comのメールアドレスを使用してください。',
                ], 422);
            }

            $class->mail = $request->email;
            $class->is_first_login = false;
            $class->save();
        }

        // セッションにクラス情報を追加
        $loggedInUsers = session('logged_in_users', []);
        $loggedInUsers[] = [
            'class_id' => $class->id,
            'class_name' => $class->class_name,
            'authority_id' => $class->authority_id,
        ];
        session(['class_id' => $class->id]);
        session(['logged_in_users' => $loggedInUsers]);

        return response()->json([
            'redirect_url' => route('poster.page'),
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
        $classId = session('class_id');

        if ($classId) {
            $loggedInUsers = session('logged_in_users', []);
            $updatedUsers = array_filter($loggedInUsers, function ($user) use ($classId) {
                return $user['class_id'] !== $classId;
            });

            session(['logged_in_users' => $updatedUsers]); // 更新されたログインユーザーリストを保存
        }

        $request->session()->flush(); // セッションを全て削除
        return redirect()->route('login.page'); // ログインページへリダイレクト
    }
}