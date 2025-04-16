<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Classes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\CodeSave;

use function Laravel\Prompts\form;

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

        // クラス名を検索
        $class = Classes::where('class_name', $request->class_name)->first();

        if (!$class) {
            return response()->json(['error_type' => 'class_name', 'message' => 'クラス名が間違っています。'], 422);
        }

        // パスワードを検証
        if (!Hash::check($request->password, $class->password)) {
            return response()->json(['error_type' => 'password', 'message' => 'パスワードが間違っています。'], 422);
        }

        if (session('microsoft_user_id')) {
                // セッションに microsoft_user_id が存在する場合の処理
            $microsoftUserId = session('microsoft_user_id');
            $user = User::find($microsoftUserId);

            if ($user) {
                // ユーザーにクラス ID を設定
                Log::info('セッション microsoft_user_id に一致するユーザーが見つかりました:', ['user_id' => $user->id]);
                $user->class_id = $class->id;
                $user->save();
                Log::info('クラス ID を更新しました:', ['class_id' => $class->id]);
            } else {
                Log::warning('セッション microsoft_user_id に一致するユーザーが見つかりませんでした:', ['microsoft_user_id' => $microsoftUserId]);
            }
            // ユーザーにクラス ID を設定
            Log::info('Class ID:', ['class_id' => $class->id]);
            Log::info('User before save:', $user->toArray());
            $user->class_id = $class->id;
            $user->save();
            Log::info('User after save:', $user->toArray());

            // ユーザーをログイン状態にする
            Auth::login($user);

            if ($class->authority_id === 1) {
                // 管理者クラスの場合
                $loggedInUsers = session('logged_in_users', []);
                $loggedInUsers[] = [
                    'class_id' => $class->id,
                    'class_name' => $class->class_name,
                    'authority_id' => $class->authority_id,
                    'user_name' => $user->name, // ユーザー名を追加
                ];
                session(['class_id' => $class->id]);
                session(['authority_id' => $class->authority_id]);
                session(['logged_in_users' => $loggedInUsers]);
                session(['user_name' => $user->name]); // ユーザー名をセッションに保存

                $redirectUrl = route('poster_admin');
            } else {
                // 通常のクラスの場合
                $loggedInUsers = session('logged_in_users', []);
                $loggedInUsers[] = [
                    'class_id' => $class->id,
                    'class_name' => $class->class_name,
                    'authority_id' => $class->authority_id,
                    'user_name' => $user->name, // ユーザー名を追加
                ];
                session(['class_id' => $class->id]);
                session(['logged_in_users' => $loggedInUsers]);
                session(['user_name' => $user->name]); // ユーザー名をセッションに保存
                Log::info('セッション全体:', session()->all());


                $redirectUrl = route('poster.page');
            }
            return response()->json([
                'redirect_url' => $redirectUrl,
            ]);
        }
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

    /**
     * Microsoftの認証ページヘユーザーをリダイレクト
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToProvider()
    {
        return Socialite::driver('graph')->redirect();
    }

    /**
     * Microsoftからユーザー情報を取得
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        try {
            // Microsoft アカウントからユーザー情報を取得
            $microsoftUser = Socialite::driver('graph')->user();

            // ユーザーをデータベースに保存または取得
            $user = User::firstOrCreate(
                ['email' => $microsoftUser->getEmail()],
                [
                    'name' => $microsoftUser->getName(),
                    'microsoft_id' => $microsoftUser->getId(),
                    'password' => bcrypt(Str::random(16)), // ランダムなパスワードを生成
                ]
            );

            // クラス情報がない場合、クラス登録ページへリダイレクト
            if (is_null($user->class_id)) {
                // クラス ID を設定するためのセッションを保存
                session(['microsoft_user_id' => $user->id]);

                return redirect()->route('class.registration.page'); // クラス登録ページへリダイレクト
            }

            $class = Classes::where('id', $user->class_id)->first();

            $loggedInUsers[] = [
                'class_id' => $class->id,
                'class_name' => $class->class_name,
                'authority_id' => $class->authority_id,
                'user_name' => $user->name, // ユーザー名を追加
            ];
            session(['class_id' => $class->id]);
            session(['logged_in_users' => $loggedInUsers]);
            session(['user_name' => $user->name]); // ユーザー名をセッションに保存
            Log::info('セッション全体:', session()->all());

            $redirectUrl = route('poster.page');

            // クラス情報がある場合、ポスター一覧ページへリダイレクト
            return redirect()->route('poster.page');
        } catch (\Exception $e) {
            // エラー内容をログに記録
            Log::error('Microsoftログインエラー: ' . $e->getMessage());

            // エラー内容を画面に表示（デバッグ用）
            return redirect()->route('login')->withErrors(['error' => 'Microsoftログインに失敗しました。']);
        }
    }
}