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

  <script>
    const imageUploadRoute = "{{ route('image.upload') }}";
    const csrfToken = "{{ csrf_token() }}";
  </script>
  
</head>
<body data-class-id="{{ session('class_id') }}" data-class-name="{{ session('class_name') }}">
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
          <button onclick="location.href='{{ route('preview.page') }}'" class="work">
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
      <div class="editor-tab-buttons">
        <button onclick="switchEditor('html')" style="margin-left: 3%;">HTML</button>
        <button onclick="switchEditor('css')">CSS</button>
        <button onclick="switchEditor('js')">JavaScript</button>
      </div>

      <div class="code_editor">
        <textarea id="htmlEditor"></textarea>
        <textarea id="cssEditor" style="display: none;"></textarea>
        <textarea id="jsEditor" style="display: none;"></textarea>
      </div>

      <div class="editor-bottom-wrapper">
        <div class="editor-bottom-box" id="imageDisplayBox">
          @if($uploadedImages->count())
            <form id="deleteImageForm">
              <ul>
                @foreach($uploadedImages as $image)
                  <li>
                    <label>
                      <input type="checkbox" name="image_ids[]" value="{{ $image->id }}">
                      {{ $image->filename }}
                    </label>
                  </li>
                @endforeach
              </ul>
                <button type="button" id="deleteSelectedImagesBtn" class="delete-button">
                  取り消し
                </button>
            </form>
          @else
            <span id="imageStatusText">画像はありません</span>
          @endif
        </div>
      
        <form id="uploadForm" class="upload-form" enctype="multipart/form-data">
          <label class="upload-btn">
            <span class="upload-text">アップロードフォーム</span>
            <input type="file" accept="image/*" name="image" id="imageInput">
            <div class="upload-progress-container inside-btn">
              <div class="upload-progress-bar" id="uploadProgressBar"></div>
            </div>
          </label>
        </form>
      </div>

      <div class="save-button-wrapper">
        <button id="saveCodeButton" class="save-button">コードを保存</button>
      </div>

  </div>

  <script>
document.addEventListener("DOMContentLoaded", function () {
  const htmlEditor = CodeMirror.fromTextArea(document.getElementById("htmlEditor"), {
    mode: "htmlmixed",
    lineNumbers: true,
    autoCloseTags: false,
    theme: "monokai",
    extraKeys: {
      "'>'": function (cm) {
        cm.replaceSelection('>');
        autoCloseTag(cm, '>');
        updatePreview();
      }
    }
  });

  const cssEditor = CodeMirror.fromTextArea(document.getElementById("cssEditor"), {
    mode: "css",
    lineNumbers: true,
    theme: "monokai"
  });

  const jsEditor = CodeMirror.fromTextArea(document.getElementById("jsEditor"), {
    mode: "javascript",
    lineNumbers: true,
    theme: "monokai"
  });

  htmlEditor.on('change', () => updatePreview());
  cssEditor.on('change', () => updatePreview());
  jsEditor.on('change', () => updatePreview());

  htmlEditor.setValue(`<!DOCTYPE html>
<html>
<head>
  <title>サンプルページ</title>
</head>
<body>
  <h1>Hello World</h1>
  <p>これはHTMLの初期テンプレートです。</p>
</body>
</html>`);

  cssEditor.setValue(`body {
  color: #333;
  font-family: sans-serif;
}

h1 {
  color: #00cccc;
}`);

  jsEditor.setValue(`console.log("JavaScriptが実行されました！");

document.addEventListener("DOMContentLoaded", () => {
  console.log("ページが読み込まれました");
});`);

  function autoCloseTag(cm, ch) {
    const cursor = cm.getCursor();
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

  function switchEditor(type) {
    htmlEditor.getWrapperElement().style.display = type === "html" ? "block" : "none";
    cssEditor.getWrapperElement().style.display = type === "css" ? "block" : "none";
    jsEditor.getWrapperElement().style.display = type === "js" ? "block" : "none";
  }

  window.switchEditor = switchEditor;

  function updatePreview(imageTag = '') {
  const html = htmlEditor.getValue();
  const css = `<style>${cssEditor.getValue()}</style>`;
  const js = `<script>${jsEditor.getValue()}<\/script>`;

  // 一時的に imageTag を body に挿入する処理を改善
  let finalHtml = html;

  // imageTag が挿入済みの場合、重複を避けてクリーンに再生成
  finalHtml = finalHtml.replace(/<img .*?>/, ''); // ← 前回の画像タグを除去（任意）

  // <body> タグ内に imageTag を挿入する方法に変更（安全）

  const content = `
    ${finalHtml}
    ${css}
    ${js}
  `;
  document.getElementById('previewFrame').srcdoc = content;
}

  // 初期表示
  switchEditor('html');
  updatePreview();

  // イメージアップロード処理
  const imageInput = document.getElementById('imageInput');
  const uploadForm = document.getElementById('uploadForm');
  const progressBar = document.getElementById('uploadProgressBar');
  const imageBox = document.getElementById('imageDisplayBox');
  const statusText = document.getElementById('imageStatusText');
  
  imageInput.addEventListener('change', function () {
  const file = this.files[0];
  if (!file) return;

  const formData = new FormData(uploadForm);
  formData.append('class_name', document.body.dataset.className); // ← class_name追加済み
  formData.append('class_id', document.body.dataset.classId);     // ← ✅ これを追加！

  const xhr = new XMLHttpRequest();
    xhr.open('POST', imageUploadRoute, true);
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

    xhr.upload.addEventListener('progress', function (e) {
      if (e.lengthComputable) {
        const percent = (e.loaded / e.total) * 100;
        progressBar.style.width = percent + '%';
      }
    });

    xhr.onload = function () {
      if (xhr.status === 200) {
        const response = JSON.parse(xhr.responseText);
        if (response.success) {
          statusText.style.display = 'none';
          const img = document.createElement('img');
          img.src = response.image_url;
          img.style.maxWidth = '100%';
          img.style.maxHeight = '100%';
          imageBox.appendChild(img);
          updatePreview(`<img src="${response.image_url}" alt="Uploaded Image" style="max-width: 100%;">`);
        }
      }
    };

    xhr.send(formData);
  });
});

document.getElementById('deleteSelectedImagesBtn').addEventListener('click', function () {
  const checkedBoxes = document.querySelectorAll('input[name="image_ids[]"]:checked');
  if (checkedBoxes.length === 0) {
    alert('削除したい画像を選択してください。');
    return;
  }

  if (!confirm('選択した画像を削除しますか？')) return;

  const formData = new FormData();
  checkedBoxes.forEach(box => {
    formData.append('image_ids[]', box.value);
  });
  formData.append('_token', csrfToken);

  fetch('{{ route('image.delete') }}', {
    method: 'POST',
    body: formData,
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      // 削除した画像のliをDOMから消す
      checkedBoxes.forEach(box => {
        box.closest('li').remove();
      });

      // 全部なくなったら「画像はありません」を表示
      if (document.querySelectorAll('#imageDisplayBox li').length === 0) {
        document.getElementById('imageDisplayBox').innerHTML = '<span id="imageStatusText">画像はありません</span>';
      }
    } else {
      alert('削除に失敗しました');
    }
  });
});
</script>

</body>
</html>