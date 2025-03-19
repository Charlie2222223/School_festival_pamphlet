<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

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
        ]);

        $class = Classes::where('class_name', $request->class_name)->first();

        if (!$class || !Hash::check($request->password, $class->password)) {
            return back()->withErrors(['login_error' => 'クラス名またはパスワードが間違っています。']);
        }

        // セッションに保存
        Session::put('class_id', $class->id);
        Session::put('class_name', $class->class_name);

        return redirect('/poster_list'); // ログイン後の遷移先
    }

    public function logout()
    {
        Session::flush(); // セッション削除
        return redirect('/');
    }
}
