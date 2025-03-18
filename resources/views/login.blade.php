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
    <form method="POST" action="{{ route('login.submit') }}">
      @csrf
      <input type="text" name="class_name" placeholder="クラス" required />
      <input type="password" name="password" placeholder="password" required />
      <button type="submit">login</button>
    </form>
  </div>
</body>
</html>