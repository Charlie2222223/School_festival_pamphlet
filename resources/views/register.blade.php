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

        <button id="login_button" class="login" type="submit">ログイン</button>
    </form>

    <button class="swich_development" id="development_button">パンフレット一覧</button>
    </div>

    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const loginForm = document.getElementById("loginForm");
        const classNameError = document.getElementById("class_name_error");
        const passwordError = document.getElementById("password_error");

        loginForm.addEventListener("submit", async function (e) {
            e.preventDefault(); // フォームのデフォルト動作を無効化

            const className = document.getElementById("class_name").value;
            const password = document.getElementById("password").value;

            // エラーメッセージをリセット
            classNameError.style.display = "none";
            passwordError.style.display = "none";

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
                const response = await fetch("{{ route('login.submit') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({ class_name: className, password: password }),
                });

                const data = await response.json();

                if (response.ok && data.redirect_url) {
                    // 成功時にリダイレクト
                    window.location.href = data.redirect_url;
                } else {
                    // サーバーからのエラーを表示
                    if (data.error_type === "class_name") {
                        classNameError.textContent = data.message;
                        classNameError.style.display = "block";
                    } else if (data.error_type === "password") {
                        passwordError.textContent = data.message;
                        passwordError.style.display = "block";
                    }
                }
            } catch (error) {
                console.error("エラー:", error);
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