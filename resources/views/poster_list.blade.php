<!DOCTYPE html>
<html lang="ja">
<head>
        <meta charset="UTF-8" />
    <title>Three.js背景 × フォーム</title>
    {{-- ViteでThree.js/TypeScriptを読み込む --}}
    @vite('resources/js/three-app.ts')
    <link rel="stylesheet" href="{{ asset('css/poster.css') }}">
</head>
<body>
    <div class="background-pattern"></div>
    <canvas id="myCanvas"></canvas>
    <div class="app">
        <div class="main">
        <div class="sidebar">
            <!-- 固定アイコン部分 -->
            <div class="sidebar-icon">
            <div class="icon">
                <div class="icon_circle"></div>
                <p>{{ session('class_name') }}</p>
            </div>
            </div>

            <!-- スクロール可能なリスト部分 -->
            <div class="sidebar-scrollable">
            <div class="list">
                <ul>作業リスト</ul>
                <button onclick="location.href='{{ route('login.page') }}'" class="home">
                <img src="image/home.svg" alt="画像の説明">
                <span class="home-text">HOME</span>
                </button>
                <button onclick="location.href='{{ route('preview.page') }}'" class="work">
                <img src="image/icon.svg" alt="画像の説明">
                パンフレットを作成
                </button>

                <ul>閲覧リスト</ul>

                <details class="dropdown">
                <summary>R メニュー<span>▼</span></summary>
                <div class="dropdown-content">
                    @foreach ($rClasses as $class)
                    <div class="dropdown-text" onclick="alert('{{ $class->class_name }}')">
                    {{ $class->class_name }}
                    </div>
                    @endforeach
                </div>
                </details>

                <details class="dropdown">
                <summary>S メニュー<span>▼</span></summary>
                <div class="dropdown-content">
                    @foreach ($sClasses as $class)
                    <div class="dropdown-text" onclick="alert('{{ $class->class_name }}')">
                    {{ $class->class_name }}
                    </div>
                    @endforeach
                </div>
                </details>

                <details class="dropdown">
                <summary>J メニュー<span>▼</span></summary>
                <div class="dropdown-content">
                    @foreach ($jClasses as $class)
                    <div class="dropdown-text" onclick="alert('{{ $class->class_name }}')">
                    {{ $class->class_name }}
                    </div>
                    @endforeach
                </div>
                </details>
            </div>
            </div>

            <!-- ログアウトボタン -->
            <button class="logout">
            <img src="image/logout.png" alt="logout">
            ログアウト
            </button>
        </div>

        <!-- メインコンテンツ -->
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