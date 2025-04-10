<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>学園祭パンフレット作成システム・ログイン</title>

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
      <div style="position: relative;">
        <input type="text" name="class_name" id="class_name" placeholder="クラス" required />
        <div id="class_name_error" class="user-error-message"></div> 
      </div>
  
      <div style="position: relative;">
        <input type="password" name="password" id="password" placeholder="password" required />
        <div id="password_error" class="password-error-message"></div>
      </div>

      <div style="position: relative; display: none;" id="email_container">
        <input type="email" name="email" id="email" placeholder="メールアドレス" />
        <div id="email_error" class="email-error-message"></div>
      </div>
  
      <button class="login" type="submit">login</button>
    </form>
  
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
      const classNameError = document.getElementById("class_name_error");
      const passwordError = document.getElementById("password_error");
      const emailContainer = document.getElementById("email_container");
      const emailError = document.getElementById("email_error");

      loginForm.addEventListener("submit", async function (e) {
        e.preventDefault(); // フォームのデフォルト動作を無効化

        const className = document.getElementById("class_name").value;
        const password = document.getElementById("password").value;
        const email = document.getElementById("email")?.value;

        // エラーメッセージをリセット
        classNameError.style.display = "none";
        passwordError.style.display = "none";
        emailError.style.display = "none";

        try {
          const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
          const response = await fetch("{{ route('login.submit') }}", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({ class_name: className, password: password, email: email }),
          });

          const data = await response.json();

          if (response.ok) {
            if (data.is_first_login) {
              // 初回ログイン時の処理
              emailContainer.style.display = "block"; // メールアドレス入力欄を表示
              emailError.textContent = data.message;
              emailError.style.display = "block";
            } else if (data.redirect_url) {
              // 通常ログイン成功時の処理
              window.location.href = data.redirect_url; // リダイレクト
            }
          } else {
            // エラーメッセージを表示
            if (data.error_type === "class_name") {
              classNameError.textContent = "クラス名が間違っています。";
              classNameError.style.display = "block";
            }
            if (data.error_type === "password") {
              passwordError.textContent = "パスワードが間違っています。";
              passwordError.style.display = "block";
            }
            if (data.error_type === "email") {
              emailError.textContent = data.message; // サーバーからのエラーメッセージを表示
              emailError.style.display = "block";
              emailContainer.style.display = "block"; // メールアドレス入力欄を再表示
            }
          }
        } catch (error) {
          console.error("ログインエラー:", error);
          classNameError.textContent = "サーバーエラーが発生しました。";
          classNameError.style.display = "block";
        }
      });
    });

    document.addEventListener("DOMContentLoaded", function () {
      const loginModal = document.getElementById("login");
      const watchModal = document.getElementById("watch");
      const developmentButton = document.getElementById("development_button");
      const loginButton = document.getElementById("login_button");

      // パンフレット一覧ボタンをクリックしたとき
      developmentButton.addEventListener("click", function () {
        loginModal.classList.add("hidden");
        watchModal.classList.remove("hidden");
      });

      // 開発者の方はこちらボタンをクリックしたとき
      loginButton.addEventListener("click", function () {
        watchModal.classList.add("hidden");
        loginModal.classList.remove("hidden");
      });
    });
  </script>
</body>
</html>