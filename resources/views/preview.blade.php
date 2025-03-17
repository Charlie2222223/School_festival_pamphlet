<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>リアルタイムHTMLプレビュー＋タグ補完</title>

  <!-- CodeMirror CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css">

  <!-- CodeMirror JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/htmlmixed/htmlmixed.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/xml/xml.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/javascript/javascript.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/css/css.min.js"></script>

  <!-- 必須：タグ自動閉じアドオン -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/edit/closetag.min.js"></script>

  <style>
    body {
      margin: 0;
      display: flex;
      height: 100vh;
    }

    .editor {
      width: 50%;
      height: 100vh;
    }

    .CodeMirror {
      height: 100%;
      font-size: 16px;
    }

    iframe {
      width: 50%;
      height: 100vh;
      border: none;
    }
  </style>
</head>
<body>

<div class="editor">
    <textarea id="codeArea">
        <!DOCTYPE html>
        <html>
        <head>
            <style>
            body { background: #fefefe; }
            </style>
        </head>
        <body>
            <h1>Hello World</h1>
            <p>リアルタイム表示します</p>
        </body>
        </html>
    </textarea>
</div>
<iframe id="previewFrame"></iframe>

<script>
    const editor = CodeMirror.fromTextArea(document.getElementById("codeArea"), {
        mode: "htmlmixed",
        lineNumbers: true,
        autoCloseTags: {
            whenClosing: true,
            whenOpening: true,
            indentTags: []
        },
        theme: "default"
    });

    const preview = document.getElementById('previewFrame');

    const updatePreview = () => {
      preview.srcdoc = editor.getValue();
    };

    editor.on('change', updatePreview);
    window.addEventListener('DOMContentLoaded', updatePreview);
  </script>

</body>
</html>