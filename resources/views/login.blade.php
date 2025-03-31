<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <title>Three.js背景 × フォーム</title>

  <link rel="stylesheet" href="{{ asset('css/main.css') }}">
  {{-- ViteでThree.js/TypeScriptを読み込む --}}
  @vite('resources/js/three-app.ts')
</head>
<body>
  <div class="background-pattern"></div>
  <canvas id="myCanvas"></canvas>
  <div class="form-container">
    <h1>JOHO祭</h1>
    <form method="POST" action="{{ route('login.submit') }}" id="login">
      @csrf
      <input type="text" name="class_name" placeholder="クラス" required />
      <input type="password" name="password" placeholder="password" required />
      <button class="login" type="submit">login</button>
    </form>
    <button class="swich_development" id="development_button">パンフレット一覧</button>
  </div>

  <div class="form-container" id="watch">
    <h1>JOHO祭</h1>
    <form method="GET" action="{{ route('poster') }}" id="login">
      @csrf
      <button class="show" type="submit">パンフレット一覧</button>
    </form>
    <button class="swich_show" id="login_button">開発者の方はこちら</button>
  </div>

  <script>

    const button = document.querySelector("#login_button");
    button.addEventListener("click", toggleDisplay);

    function toggleDisplay() {
      const demo = document.querySelector("#watch");
      demo.classList.toggle("hidden");

      const login = document.querySelector("#login");
      login.classList.toggle("hidden");
    }

    const development_button = document.querySelector("#development_button");
    development_button.addEventListener("click", toggleDisplay);

    document.addEventListener("DOMContentLoaded", function () {
      const login = document.querySelector("#login");
      login.classList.toggle("hidden");
    }); 

  </script>
</body>
</html>