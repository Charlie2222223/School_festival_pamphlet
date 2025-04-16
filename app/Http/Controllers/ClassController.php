<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Classes;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    // クラス登録フォームを表示
    public function showRegistrationForm()
    {
        return view('register');
    }

    // クラスを登録
    public function registerClass(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        // セッションから Microsoft ユーザー ID を取得
        $userId = session('microsoft_user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['error' => 'セッションが切れました。もう一度ログインしてください。']);
        }

        // ユーザーにクラス ID を設定
        $user = User::find($userId);
        $user->class_id = $request->input('class_id');
        $user->save();

        // セッションをクリア
        session()->forget('microsoft_user_id');

        return redirect()->route('poster.page')->with('success', 'クラス情報が登録されました！');
    }
}
