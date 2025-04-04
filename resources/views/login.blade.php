<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Three.js背景 × フォーム</title>

  <link rel="stylesheet" href="{{ asset('css/main.css') }}">
  @vite('resources/js/three-app.ts')
</head>
<body>
  <div class="background-pattern"></div>
  <canvas id="myCanvas"></canvas>

  <div class="form-container" id="login">
    <h1>JOHO祭</h1>
    <form id="loginForm">
      @csrf
      <input type="text" name="class_name" id="class_name" placeholder="クラス" required />
      <input type="password" name="password" id="password" placeholder="password" required />
      <button class="login" type="submit">login</button>
    </form>

    {{-- エラーメッセージを表示 --}}
    <div id="error-message" class="error-message" style="color: red; margin-top: 10px; display: none;"></div>

    <button class="swich_development" id="development_button">パンフレット一覧</button>
  </div>

  <div class="form-container hidden" id="watch">
    <h1>JOHO祭</h1>
    <form method="GET" action="{{ route('poster') }}" id="posterForm">
      @csrf
      <button class="show" type="submit">パンフレット一覧</button>
    </form>
    <button class="swich_show" id="login_button">開発者の方はこちら</button>
  </div>

  <script>
        document.addEventListener("DOMContentLoaded", function () {
        const loginForm = document.getElementById("loginForm");
        const errorMessage = document.getElementById("error-message");
      
        if (!loginForm || !errorMessage) {
          console.error("必要な要素が見つかりません: #loginForm または #error-message");
          return;
        }
      
        loginForm.addEventListener("submit", async function (e) {
          e.preventDefault(); // フォームのデフォルト動作を無効化
      
          const className = document.getElementById("class_name").value;
          const password = document.getElementById("password").value;
      
          try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
              throw new Error("CSRFトークンが見つかりません");
            }
      
            const response = await fetch("{{ route('login.submit') }}", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken.getAttribute("content"),
              },
              body: JSON.stringify({ class_name: className, password: password }),
            });
      
            const data = await response.json();
      
            if (response.ok) {
              // ログイン成功時の処理
              if (data.redirect_url) {
                window.location.href = data.redirect_url; // リダイレクト
              }
            } else {
              // エラーメッセージを表示
              if (data.error_type === "class_name") {
                errorMessage.textContent = "クラス名が間違っています。";
              } else if (data.error_type === "password") {
                errorMessage.textContent = "パスワードが間違っています。";
              } else {
                errorMessage.textContent = data.message || "ログインに失敗しました。";
              }
              errorMessage.style.display = "block";
            }
          } catch (error) {
            console.error("ログインエラー:", error);
            errorMessage.textContent = "サーバーエラーが発生しました。";
            errorMessage.style.display = "block";
          }
        });
      });
  </script>
</body>
</html>