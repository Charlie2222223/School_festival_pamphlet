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

const imageInput = document.getElementById('imageInput');
const uploadForm = document.getElementById('uploadForm');
const progressBar = document.getElementById('uploadProgressBar');
const imageBox = document.getElementById('imageDisplayBox');
const statusText = document.getElementById('imageStatusText');

imageInput.addEventListener('change', function () {
const file = this.files[0];
if (!file) return;

const formData = new FormData(uploadForm);
formData.append('class_id', 1); // ← 適切なclass_idに動的変更してね

const xhr = new XMLHttpRequest();
xhr.open('POST', '{{ route("image.upload") }}', true);
xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

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
    }
    }
};

    xhr.send(formData);
});

function updatePreview() {
    const html = htmlEditor.getValue();
    const css = `<style>${cssEditor.getValue()}</style>`;
    const js = `<script>${jsEditor.getValue()}<\/script>`;

    // Laravelから画像パスを受け取る


    let content = html + css;

    // body閉じタグの前に画像を挿入する場合：
    if (imageUrl) {
    content = content.replace('</body>', `<img src="${imageUrl}" alt="Uploaded Image" style="max-width: 100%;"><\/body>`);
    }

    content += js;

    document.getElementById('previewFrame').srcdoc = content;
}