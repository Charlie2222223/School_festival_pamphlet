<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>リアルタイムプレビュー（HTML / CSS / JS 切り替え）</title>
  <link rel="stylesheet" href="{{ asset('css/preview.css') }}">
  @vite('resources/js/three-app.ts')

  <!-- CodeMirror CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/theme/monokai.min.css">

  <!-- CodeMirror Core & Modes -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/htmlmixed/htmlmixed.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/xml/xml.min.js"></script> <!-- ←追加おすすめ -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/javascript/javascript.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/css/css.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/edit/closetag.min.js"></script>
</head>
<body>
  <div class="background-pattern"></div>
  <canvas id="myCanvas"></canvas>
  <div class="main">
    <!-- サイドバー -->
    <div class="sidebar">
      <div class="sidebar-icon">
        <div class="icon">
          <div class="icon_circle"></div>
          <p>{{ session('class_name') }}</p>
        </div>
      </div>
      <div class="sidebar-scrollable">
        <div class="list">
          <ul>作業リスト</ul>
          <button onclick="location.href='{{ route('login.page') }}'" class="home">
            <img src="image/home.svg" alt="画像の説明">
            <span class="home-text">HOME</span>
          </button>
          <button class="work">
            <img src="image/icon.svg" alt="画像の説明">パンフレットを作成
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

      <button class="logout">
        <img src="image/logout.png" alt="logout">ログアウト
      </button>
    </div>

    <!-- エディタとプレビューエリア -->
    <div class="preview-frame-container">
      <iframe id="previewFrame"></iframe>
    </div>

    <div class="editor">
      <div style="display: flex; justify-content: center; gap: 1rem; margin: 0.5rem;">
        <button onclick="switchEditor('html')">HTML</button>
        <button onclick="switchEditor('css')">CSS</button>
        <button onclick="switchEditor('js')">JavaScript</button>
      </div>

      <textarea id="htmlEditor"></textarea>
      <textarea id="cssEditor" style="display: none;"></textarea>
      <textarea id="jsEditor" style="display: none;"></textarea>
    </div>ss
  </div>

  <script>
    function autoCloseTag(cm, ch) {
      const cursor = cm.getCursor();
      const token = cm.getTokenAt(cursor);
      if (ch === '>') {
        const line = cm.getLine(cursor.line);
        const beforeCursor = line.slice(0, cursor.ch);
        const match = beforeCursor.match(/<([a-zA-Z0-9]+)>$/);
        if (match) {
          const tagName = match[1];
          const closeTag = `</${tagName}>`;
          cm.replaceRange(closeTag, cursor);
          cm.setCursor(cursor);
        }
      }
    }
  
    const htmlEditor = CodeMirror.fromTextArea(document.getElementById("htmlEditor"), {
      mode: "htmlmixed",
      lineNumbers: true,
      autoCloseTags: false,
      theme: "monokai", // ← ★ここ追加！
      extraKeys: {
        "'>'": function(cm) {
          cm.replaceSelection('>');
          autoCloseTag(cm, '>');
          updatePreview();
        }
      }
    });

    const cssEditor = CodeMirror.fromTextArea(document.getElementById("cssEditor"), {
      mode: "css",
      lineNumbers: true,
      theme: "monokai", // ← ★ここ追加！
    });

    const jsEditor = CodeMirror.fromTextArea(document.getElementById("jsEditor"), {
      mode: "javascript",
      lineNumbers: true,
      theme: "monokai", // ← ★ここ追加！
    });
  
    // ✅ 初期コードをそれぞれ設定
    htmlEditor.setValue(
  `<!DOCTYPE html>
  <html>
  <head>
    <title>サンプルページ</title>
  </head>
  <body>
    <h1>Hello World</h1>
    <p>これはHTMLの初期テンプレートです。</p>
  </body>
  </html>`
    );
  
    cssEditor.setValue(
  `body {
    color: #333;
    font-family: sans-serif;
  }
  
  h1 {
    color: #00cccc;
  }`
    );
  
    jsEditor.setValue(
  `console.log("JavaScriptが実行されました！");
  
  document.addEventListener("DOMContentLoaded", () => {
    console.log("ページが読み込まれました");
  });`
    );
  
    function updatePreview() {
      const html = htmlEditor.getValue();
      const css = `<style>${cssEditor.getValue()}</style>`;
      const js = `<script>${jsEditor.getValue()}<\/script>`;
      const content = html + css + js;
      document.getElementById('previewFrame').srcdoc = content;
    }
  
    htmlEditor.on("change", updatePreview);
    cssEditor.on("change", updatePreview);
    jsEditor.on("change", updatePreview);
  
    function switchEditor(type) {
      htmlEditor.getWrapperElement().style.display = type === "html" ? "block" : "none";
      cssEditor.getWrapperElement().style.display = type === "css" ? "block" : "none";
      jsEditor.getWrapperElement().style.display = type === "js" ? "block" : "none";
    }
  
    window.addEventListener('DOMContentLoaded', () => {
      switchEditor('html'); // HTMLを最初に表示
      updatePreview(); // 初期表示更新
    });
  </script>

</body>
</html>