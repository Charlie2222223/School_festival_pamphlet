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

    .app {
    width: 100vw;
    height: 100vh;
    display: flex;
    flex-direction: column;
    }

    .main {
    display: flex;
    flex-direction: row;
    flex-grow: 1;
    }

    .sidebar {
    width: 20%;
    background-color: white;
    box-sizing: border-box;
    border: 3px solid black;
    z-index: 2;
    }

    .icon {
    height: 20%;
    display: flex;
    align-items: center;
    justify-content: center;
    border-bottom: 3px solid black;
    }

    .icon_circle {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    background-color: #00cccc;
    border: 2px solid black;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.3);
    flex-shrink: 0;
     margin-left: -15%;
     margin-right: 10%;
    }

    .icon p {
    font-size: 48px; /* 少し下げてバランスよく */
    color: #333;
    margin: 0;
    font-weight: bold;
    }

    .content {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    }

    .list ul{
        margin-left: -10%;
    }

    .work {
        display: flex;
        align-items: center;
        justify-content: flex-start; /* ← 左寄せ */
        gap: 8px; /* 画像とテキストの間隔 */
        width: 77%;
        margin: 1rem auto;
        margin-bottom: 20%;
        padding: 1rem;
        font-size: 20px;
        font-weight: bold;
        color: #00fe19;
        background-color: #ffffff;
        text-align: left;
        border: 2px solid #00fe19;
    }

    .work img {
        width: 24px;
        height: 24px;
        object-fit: contain;
    }

    .home {
        display: flex;
        align-items: center;
        justify-content: flex-start; /* ← 左寄せ */
        gap: 8px; /* 画像とテキストの間隔 */
        width: 77%;
        margin: 1rem auto;
        margin-bottom: 5%;
        padding: 1rem;
        font-size: 20px;
        font-weight: bold;
        color: #00cccc;
        background-color: #ffffff;
        text-align: left;
        border: 2px solid #00cccc;
    }

    .home img {
        width: 24px;
        height: 24px;
        object-fit: contain;
    }

    .home-text {
        flex: 1;
        text-align: center;
    }

    .division {
        display: block;
        width: 77%;
        margin: 1rem auto;
        padding: 0.4rem 1rem;
        font-size: 20px;
        font-weight: bold;
        color: #00fe19;
        background-color: #ffffff;
        text-align: center;
        border: 2px solid #00fe19;
    }


    .flex-content {
    flex-grow: 1;
    background-color: palegoldenrod;
    box-sizing: border-box;
    border: 8px solid green;
    }

    .fixed-content {
    height: 128px;
    background-color: thistle;
    box-sizing: border-box;
    border: 8px solid darkmagenta;
    }

    .content {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .flex-content {
    flex-grow: 1;
    background-color: palegoldenrod;
    box-sizing: border-box;
    border: 8px solid green;
    }

  .center-box {
    width: 42%;
    height: 80%;
    /* margin-left: 5%; */
    background-color: white;
    border: 2px solid black;
    border-radius: 12px;
    box-shadow: 0 0 12px rgba(0, 0, 0, 0.2);
    font-size: 24px;
    color: #00cccc;
    font-weight: bold;
    text-align: center;
    z-index: 3;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);

    display: flex;
    justify-content: center;
    align-items: center;
    position: relative; /* ★ ここを追加 */
  }

  .button-group {
    position: absolute;
    right: -40%;
    top: 60%;
    transform: translateY(-50%);
    display: block;
    flex-direction: column;
    gap: 12px;
  }

  .button-group button {
    width: 60px;
    height: 60px;
    font-size: 40px;
    background-color: #00cccc;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
    transition: background 0.3s;
  }

  .button-group button:hover {
    background-color: #009999;
  }

  .dropdown {
  width: 77%;
  margin: 1rem auto;
  padding: 0.4rem 1rem;
  font-size: 18px;
  font-weight: bold;
  color: #00fe19;
  background-color: #ffffff;
  border: 2px solid #00fe19;
  border-radius: 6px;
}

.dropdown summary {
  cursor: pointer;
  list-style: none;
  user-select: none;
}

.dropdown[open] summary {
  border-bottom: 1px solid #00fe19;
}

.dropdown-content {
  margin-top: 0.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.dropdown-btn {
  width: 100%;
  padding: 0.5rem;
  font-size: 16px;
  background-color: #f8f8f8;
  border: 1px solid #ccc;
  border-radius: 4px;
  text-align: left;
  cursor: pointer;
}
.dropdown-btn:hover {
  background-color: #e0f8f0;
}

</style>

  </style>
</head>
<body>
  <div class="background-pattern"></div>
  <canvas id="myCanvas"></canvas>
  <div class="app">
    <div class="main">
      <div class="sidebar">
        <div class="icon">
            <div class="icon_circle"></div>
            <p>R3SB</p>
        </div>
        <div class="list">
            <ul>作業リスト</ul>
            <button class="home">
                <img src="image/home.svg" alt="画像の説明">
                <span class="home-text">HOME</span>
            </button>
            <button class="work">
                <img src="image/icon.svg" alt="画像の説明">
                パンフレットを作成
            </button>
            <div class="list">
                <ul>閲覧リスト</ul>
              
                <details class="dropdown">
                  <summary>R メニュー</summary>
                  <div class="dropdown-content">
                    <button class="dropdown-btn">Rの内容1</button>
                    <button class="dropdown-btn">Rの内容2</button>
                  </div>
                </details>
              
                <details class="dropdown">
                  <summary>S メニュー</summary>
                  <div class="dropdown-content">
                    <button class="dropdown-btn">Sの内容1</button>
                    <button class="dropdown-btn">Sの内容2</button>
                  </div>
                </details>
              
                <details class="dropdown">
                  <summary>J メニュー</summary>
                  <div class="dropdown-content">
                    <button class="dropdown-btn">Jの内容1</button>
                    <button class="dropdown-btn">Jの内容2</button>
                  </div>
                </details>
              </div>
        </div>
      </div>
      <div class="content">
        <div class="center-box">
            <div class="button-group">
                <button>▲</button>
                <button>▼</button>
              </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>