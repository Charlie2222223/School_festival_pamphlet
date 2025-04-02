<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <title>Three.js背景 × フォーム</title>
    @vite('resources/js/three-app.ts')
    <link rel="stylesheet" href="{{ asset('css/poster.css') }}">
</head>
<body>
<div class="background-pattern"></div>
<canvas id="myCanvas"></canvas>
    <div class="app">
    <div class="main">
    <div class="sidebar">
        <div class="sidebar-scrollable">
        <div class="list">
            <ul>閲覧リスト</ul>
            <details class="dropdown">
            <summary>R メニュー<span>▼</span></summary>
            <div class="dropdown-content">
                @foreach ($rClasses as $class)
                <div class="dropdown-text class-selector" data-class-id="{{ $class->id }}">{{ $class->class_name }}</div>
                @endforeach
            </div>
            </details>
            <details class="dropdown">
            <summary>S メニュー<span>▼</span></summary>
            <div class="dropdown-content">
                @foreach ($sClasses as $class)
                <div class="dropdown-text class-selector" data-class-id="{{ $class->id }}">{{ $class->class_name }}</div>
                @endforeach
            </div>
            </details>
            <details class="dropdown">
            <summary>J メニュー<span>▼</span></summary>
            <div class="dropdown-content">
                @foreach ($jClasses as $class)
                <div class="dropdown-text class-selector" data-class-id="{{ $class->id }}">{{ $class->class_name }}</div>
                @endforeach
            </div>
            </details>
        </div>
        </div>
        <button onclick="location.href='{{ route('logout') }}'" class="logout">
            <img src="image/logout.png" alt="logout">ログアウト
        </button>
    </div>

    <div class="content">
        <div style="font-size: 20px; font-weight: bold; color: black; margin: 10px 0 0 20px;">
        表示中: <span id="currentClassName">{{ $allClasses[0]->class_name }}</span>
        </div>

        <div class="center-box">
            <iframe id="previewFrame" style="width: 100%; height: 100%; border: none; overflow: auto;"></iframe>
        <div class="button-group">
            <button id="prevClass">▲</button>
            <button id="nextClass">▼</button>
        </div>
        </div>
    </div>
    </div>
</div>

<script>
const classes = @json($allClasses, JSON_UNESCAPED_UNICODE);
let currentIndex = 0;

const previewFrame = document.getElementById('previewFrame');
const classNameDisplay = document.getElementById('currentClassName');

function renderCode(index) {
    const cls = classes[index];
    const html = cls.html_code ?? "<p>HTML未保存</p>";
    const css = `<style>${cls.css_code ?? ''}</style>`;
    const js  = `<script>${cls.js_code ?? ''}<\/script>`;
    const content = `
    <!DOCTYPE html>
    <html lang="ja">
        <head><meta charset="UTF-8">${css}</head>
        <body>${html}${js}</body>
    </html>`;
    previewFrame.srcdoc = content;
    classNameDisplay.textContent = cls.class_name;
}

document.getElementById('prevClass').addEventListener('click', () => {
    currentIndex = (currentIndex - 1 + classes.length) % classes.length;
    renderCode(currentIndex);
});

document.getElementById('nextClass').addEventListener('click', () => {
    currentIndex = (currentIndex + 1) % classes.length;
    renderCode(currentIndex);
});

// ドロップダウンメニュークリックでクラス切替
document.querySelectorAll('.class-selector').forEach(el => {
    el.addEventListener('click', () => {
    const classId = parseInt(el.getAttribute('data-class-id'));
    const index = classes.findIndex(c => c.id === classId);
    if (index !== -1) {
        currentIndex = index;
        renderCode(currentIndex);
    }
    });
});

renderCode(currentIndex); // 初期表示
</script>
</body>