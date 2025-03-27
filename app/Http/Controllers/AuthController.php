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

    public function show_poster(){
        $allClasses = Classes::all();
        $rClasses = Classes::where('class_name', 'like', 'R%')->get();
        $sClasses = Classes::where('class_name', 'like', 'S%')->get();
        $jClasses = Classes::where('class_name', 'like', 'J%')->get();
    
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
