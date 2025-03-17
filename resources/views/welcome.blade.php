<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <title>Three.js背景 × フォーム</title>

  {{-- ViteでThree.js/TypeScriptを読み込む --}}
  @vite('resources/js/three-app.ts')

  <style>
    html, body {
      margin: 0;
      padding: 0;
      overflow: hidden;
      height: 100vh;
      width: 100vw;
      font-family: 'Helvetica Neue', sans-serif;
      background: #ffffff;
    }

    canvas {
      position: absolute;
      top: 0;
      left: 0;
      z-index: 1;
    }

    h1 {
      text-align: center;
      font-size: 8vb;
      margin-top: 0;       
      margin-bottom: 15%;
      color: #00cccc;
    }

    .form-container {
      position: absolute;
      height: 44vh;
      width: 33vw;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 10;
      background-color: rgba(255, 255, 255, 0.9);
      padding: 100px;
      border-radius: 16px;
      border: 8px dashed rgb(3, 255, 247);
      box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
      color: #333;
      backdrop-filter: blur(6px);
    }

    .form-container input {
      width: 67%;
      margin: 0 auto 6%;
      padding: 10px;
      border-radius: 8px;
      background-color: #f9f9f9;
      color: #00cccc;
      font-size: 16px;
      display: block;
      border: 4px solid #00cccc;
      text-align: center;
    }

    .form-container button {
      width: 40%;
      margin: 12% auto 10%;
      display: block;
      padding: 10px;
      border: 4px solid #00cccc;
      background: white;
      color: #00cccc;
      border-radius: 8px;
      font-size: 20px;
      letter-spacing: 0.4em;
      font-weight: bold;
      text-align: center;
      cursor: pointer;
    }

    .form-container button:hover {
      background: #009999;
    }

    .background-pattern {
      position: absolute;
      top: 0;
      left: 0;
      z-index: 0;
      width: 100vw;
      height: 100vh;
      background-color: white;
      background-image:
        linear-gradient(to right, #ccc 1px, transparent 1px),
        linear-gradient(to bottom, #ccc 1px, transparent 1px);
      background-size: 32px 32px;
      opacity: 0.3;
      pointer-events: none;
    }
  </style>
</head>
<body>
  <div class="background-pattern"></div>
  <canvas id="myCanvas"></canvas>
  <div class="form-container">
    <h1>JOHO祭</h1>
    <form>
      <input type="text" placeholder="クラス" required />
      <input type="password" placeholder="password" required />
      <button type="submit">login</button>
    </form>
  </div>
</body>
</html>