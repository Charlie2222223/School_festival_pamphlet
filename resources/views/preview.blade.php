<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>リアルタイムプレビュー（HTML / CSS / JS 切り替え）</title>
  <link rel="stylesheet" href="{{ asset('css/preview.css') }}">
  @vite('resources/js/three-app.ts')

  <!-- CodeMirror CSS -->
  <!-- CodeMirror CSS（順序：Core → Theme → Addon） -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/theme/monokai.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/hint/show-hint.min.css">

<!-- CodeMirror Core（最初に必ず） -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.js"></script>

<!-- CodeMirror Modes（Coreの次に） -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/css/css.min.js"></script>

<!-- CodeMirror Addons（最後に） -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/edit/closetag.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/hint/show-hint.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/hint/css-hint.min.js"></script>

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
          <p>{{ session('user_name', 'ゲスト') }}</p>
        </div>
      </div>
      <div class="sidebar-scrollable">
        <div class="list">
          <ul>作業リスト</ul>
          <button onclick="location.href='{{ route('poster.page') }}'" class="home">
            <img src="image/home.svg" alt="画像の説明">
            <span class="home-text">HOME</span>
          </button>
          <button onclick="location.href='{{ route('preview.page') }}'" class="work">
            <img src="image/icon.svg" alt="画像の説明">パンフレットを作成
          </button>

          <ul>閲覧リスト</ul>
          <button class="menu-button">セーブデータ１</button>
          <button class="menu-button">セーブデータ２</button>
          <button class="menu-button">セーブデータ３</button>
        </div>
      </div>

      <button onclick="location.href='{{ route('logout') }}'" class="logout">
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
              <button type="button" id="deleteSelectedImagesBtn" class="delete-button">取り消し</button>
            </form>
          @else
            <span id="imageStatusText">画像はありません</span>
          @endif
        </div>
      
        <form id="uploadForm" class="upload-form" enctype="multipart/form-data">
          <div class="history-button-wrapper">
            <button class="history-button" id="openHistoryModal">履歴</button>
          </div>
          <label class="upload-btn">
            <span class="upload-text">アップロードフォーム</span>
            <input type="file" accept="image/*" name="image" id="imageInput">
            <div class="upload-progress-container inside-btn">
              <div class="upload-progress-bar" id="uploadProgressBar"></div>
            </div>
          </label>
          <div class="save-button-wrapper">
            <textarea id="commentInput" name="comment" placeholder="コメントを入力してください" rows="3" style="width: 100%; margin-bottom: 10px;"></textarea>
            <button id="saveCodeButton" class="save-button">コードを保存</button>
        </div>
        </form>
      </div>

      <!-- モーダルのHTML構造 -->
      <div id="historyModal" class="modal">
        <div class="modal-content">
          <span id="closeModal" class="close">&times;</span>
          <h2>コード履歴</h2>
          <form id="deleteHistoryForm">
            <div id="historyList">
                <!-- 履歴がここに表示されます -->
            </div>
            <button type="button" id="deleteSelectedHistoryBtn" class="delete-button">選択した履歴を削除</button>
          </form>
        </div>
      </div>
    </div>
  </div>


  <script>
    const baseUrl = "{{ asset('storage/uploads/' . session('class_name')) }}/";

    document.addEventListener("DOMContentLoaded", function () {
      // CodeMirror 初期化
      window.htmlEditor = CodeMirror.fromTextArea(document.getElementById("htmlEditor"), {
        mode: "htmlmixed",
        lineNumbers: true,
        autoCloseTags: false,
        sourcemap: true,
        theme: "monokai",
        extraKeys: {
          "'>'": function (cm) {
            cm.replaceSelection('>');
            autoCloseTag(cm, '>');
            updatePreview();
          }
        }
      });
  
      window.cssEditor = CodeMirror.fromTextArea(document.getElementById("cssEditor"), {
        mode: "css",
        lineNumbers: true,
        sourcemap: true,
        theme: "monokai",
        extraKeys: { "Ctrl-Space": "autocomplete" } // Ctrl+Space で補完
      });

      // CodeMirrorのヒント機能を有効にする
      CodeMirror.registerHelper("hint", "css", CodeMirror.hint.css);
      window.cssEditor.on("inputRead", function (editor, event) {
        if (!editor.state.completionActive && event.text[0].match(/[a-zA-Z]/)) {
          CodeMirror.commands.autocomplete(editor);
        }
      });
        
      window.jsEditor = CodeMirror.fromTextArea(document.getElementById("jsEditor"), {
        mode: "javascript",  
        lineNumbers: true,
        sourcemap: true,
        theme: "monokai"
      });
  
      htmlEditor.on('change', updatePreview);
      cssEditor.on('change', updatePreview);
      jsEditor.on('change', updatePreview);
  
      // 保存済みのコードをセット（なければサンプル）
      const htmlCode = `{!! addslashes($html_code ?? '') !!}`;
      const cssCode  = `{!! addslashes($css_code ?? '') !!}`;
      const jsCode   = `{!! addslashes($js_code ?? '') !!}`;
  
      htmlEditor.setValue(htmlCode.trim() !== '' ? htmlCode : `<!DOCTYPE html>
    <html>
    <head>
      <base href="${baseUrl}">
      <meta charset="UTF-8">
      <title>サンプルページ</title>
    </head>
    <body>
      <h1>Hello World</h1>
      <p>これはHTMLの初期テンプレートです。</p>
    </body>
    </html>`);
  
      cssEditor.setValue(cssCode.trim() !== '' ? cssCode : `body {
    color: #333;
    font-family: sans-serif;
  }
  h1 {
    color: #00cccc;
  }`);
  
      jsEditor.setValue(jsCode.trim() !== '' ? jsCode : `console.log("JavaScriptが実行されました！");
  document.addEventListener("DOMContentLoaded", () => {
    console.log("ページが読み込まれました");
  });`);
  
      switchEditor('html');
      updatePreview();
  
      // 保存ボタン処理
      const saveButton = document.getElementById("saveCodeButton");
      if (saveButton) {
          saveButton.addEventListener("click", function (e) {
              e.preventDefault();
      
              const html = htmlEditor.getValue();
              const css = cssEditor.getValue();
              const js = jsEditor.getValue();
              const comment = document.getElementById("commentInput").value; // コメントを取得
      
              const formData = new FormData();
              formData.append('_token', csrfToken);
              formData.append('html', html);
              formData.append('css', css);
              formData.append('js', js);
              formData.append('comment', comment); // コメントを追加
      
              fetch("{{ route('code.save') }}", {
                  method: 'POST',
                  body: formData
              })
              .then(res => res.json())
              .then(data => {
                  if (data.success) {
                      alert("コードが保存されました！");
                  } else {
                      alert("保存に失敗しました：" + (data.message || ''));
                  }
              })
              .catch(error => {
                  console.error("保存エラー:", error);
                  alert("保存中にエラーが発生しました。");
              });
          });
      }
  
      // 画像アップロード処理
      const imageInput = document.getElementById('imageInput');
      const uploadForm = document.getElementById('uploadForm');
      const progressBar = document.getElementById('uploadProgressBar');
      const imageBox = document.getElementById('imageDisplayBox');
      const statusText = document.getElementById('imageStatusText');
  
      if (imageInput) {
        imageInput.addEventListener('change', function () {
          const file = this.files[0];
          if (!file) return;
  
          const formData = new FormData(uploadForm);
          formData.append('class_name', document.body.dataset.className);
          formData.append('class_id', document.body.dataset.classId);
  
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
      }
  
      // 削除ボタン処理（存在確認あり）
      const deleteButton = document.getElementById('deleteSelectedImagesBtn');
      if (deleteButton) {
        deleteButton.addEventListener('click', function () {
          const checkedBoxes = document.querySelectorAll('input[name="image_ids[]"]:checked');
          if (checkedBoxes.length === 0) {
            alert('削除したい画像を選択してください。');
            return;
          }
  
          if (!confirm('選択した画像を削除しますか？')) return;
  
          const formData = new FormData();
          checkedBoxes.forEach(box => formData.append('image_ids[]', box.value));
          formData.append('_token', csrfToken);
  
          fetch('{{ route('image.delete') }}', {
            method: 'POST',
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              checkedBoxes.forEach(box => {
                box.closest('li').remove();
              });
  
              if (document.querySelectorAll('#imageDisplayBox li').length === 0) {
                imageBox.innerHTML = '<span id="imageStatusText">画像はありません</span>';
              }
            } else {
              alert('削除に失敗しました');
            }
          });
        });
      }
  
      // エディタ切り替え関数
      function switchEditor(type) {
        htmlEditor.getWrapperElement().style.display = type === "html" ? "block" : "none";
        cssEditor.getWrapperElement().style.display = type === "css" ? "block" : "none";
        jsEditor.getWrapperElement().style.display = type === "js" ? "block" : "none";
      }
      window.switchEditor = switchEditor;
  
      // プレビュー更新関数
      function updatePreview(imageTag = '') {
        const html = htmlEditor.getValue();
        const css = `<style>${cssEditor.getValue()}</style>`;
        const js = `<script>${jsEditor.getValue()}<\/script>`;
    
        let finalHtml = html.replace(/<img .*?>/, '');
        const content = `${finalHtml}${css}${js}`;
        document.getElementById('previewFrame').srcdoc = content;
      }
    
      // グローバルスコープに関数を登録
      window.updatePreview = updatePreview;
  
      // タグ自動閉じ補助関数
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

      const historyButton = document.getElementById("openHistoryModal");
      historyButton.addEventListener("click", function (e) {
          e.preventDefault();
      
          const classId = document.body.dataset.classId;
      
          // モーダルを表示
          historyModal.style.display = "block";
      
          // 履歴データを取得
          fetch(`/code-history/${classId}`)
              .then(response => response.json())
              .then(data => {
                  if (data.success) {
                      historyList.innerHTML = ""; // 既存の履歴をクリア
      
                      data.history.forEach(item => {
                          const historyItem = document.createElement("div");
                          historyItem.classList.add("history-item");
                          historyItem.innerHTML = `
                              <input type="checkbox" name="history_ids[]" value="${item.id}">
                              <p><strong>保存日時:</strong> ${item.created_at}</p>
                              <p><strong>コメント:</strong> ${item.comment || "コメントがありません"}</p>
                          `;
                          historyList.appendChild(historyItem);
                      });
                  } else {
                      historyList.innerHTML = "<p>履歴がありません。</p>";
                  }
              })
              .catch(error => {
                  console.error("履歴取得エラー:", error);
                  historyList.innerHTML = "<p>履歴の取得中にエラーが発生しました。</p>";
              });
      });

      const historyModal = document.getElementById("historyModal");
      const closeModal = document.getElementById("closeModal");

      // モーダルを閉じる
      closeModal.addEventListener("click", function () {
          historyModal.style.display = "none";
      });

      // モーダル外をクリックした場合に閉じる
      window.addEventListener("click", function (event) {
          if (event.target === historyModal) {
              historyModal.style.display = "none";
          }
      });

      const deleteHistoryButton = document.getElementById("deleteSelectedHistoryBtn");

      // 選択した履歴を削除
      deleteHistoryButton.addEventListener("click", function () {
          const checkedBoxes = document.querySelectorAll('input[name="history_ids[]"]:checked');
          if (checkedBoxes.length === 0) {
              alert('削除したい履歴を選択してください。');
              return;
          }

          if (!confirm('選択した履歴を削除しますか？')) return;

          const formData = new FormData();
          checkedBoxes.forEach(box => formData.append('history_ids[]', box.value));
          formData.append('_token', csrfToken);

          fetch('/delete-code-history', {
              method: 'POST',
              body: formData
          })
          .then(res => res.json())
          .then(data => {
              if (data.success) {
                  // 削除された履歴をリストから削除
                  checkedBoxes.forEach(box => {
                      box.closest('.history-item').remove();
                  });

                  // 履歴が空の場合のメッセージ
                  if (document.querySelectorAll('#historyList .history-item').length === 0) {
                      document.getElementById('historyList').innerHTML = "<p>履歴がありません。</p>";
                  }
              } else {
                  alert('削除に失敗しました');
              }
          })
          .catch(error => {
              console.error("削除エラー:", error);
              alert('削除中にエラーが発生しました。');
          });
      });
    });

    document.addEventListener("DOMContentLoaded", function () {
        const historyButton = document.getElementById("openHistoryModal");
        const historyModal = document.getElementById("historyModal");
        const closeModal = document.getElementById("closeModal");
        const historyList = document.getElementById("historyList");
    
        // 履歴ボタンをクリックしてモーダルを表示
        historyButton.addEventListener("click", function (e) {
            e.preventDefault();
    
            const classId = document.body.dataset.classId;
    
            // モーダルを表示
            historyModal.style.display = "block";
    
            // 履歴データを取得
            fetch(`/code-history/${classId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        historyList.innerHTML = ""; // 既存の履歴をクリア
    
                        data.history.forEach(item => {
                            const historyItem = document.createElement("div");
                            historyItem.classList.add("history-item");
                            historyItem.dataset.html = encodeURIComponent(item.html_code);
                            historyItem.dataset.css = encodeURIComponent(item.css_code);
                            historyItem.dataset.js = encodeURIComponent(item.js_code);
                            historyItem.innerHTML = `
                                <input type="checkbox" name="history_ids[]" value="${item.id}">
                                <p><strong>保存日時:</strong> ${item.created_at}</p>
                                <p><strong>コメント:</strong> ${item.comment || "コメントがありません"}</p>
                            `;
                            historyList.appendChild(historyItem);
    
                            // 履歴アイテムにクリックイベントを追加
                            historyItem.addEventListener("click", function () {
                                const htmlCode = decodeURIComponent(this.dataset.html);
                                const cssCode = decodeURIComponent(this.dataset.css);
                                const jsCode = decodeURIComponent(this.dataset.js);
    
                                // エディターに反映
                                htmlEditor.setValue(htmlCode);
                                cssEditor.setValue(cssCode);
                                jsEditor.setValue(jsCode);
    
                                // プレビューを更新
                                updatePreview();
    
                                // モーダルを閉じる
                                historyModal.style.display = "none";
                            });
                        });
                    } else {
                        historyList.innerHTML = "<p>履歴がありません。</p>";
                    }
                })
                .catch(error => {
                    console.error("履歴取得エラー:", error);
                    historyList.innerHTML = "<p>履歴の取得中にエラーが発生しました。</p>";
                });
        });
    
        // モーダルを閉じる
        closeModal.addEventListener("click", function () {
            historyModal.style.display = "none";
        });
    
        // モーダル外をクリックした場合に閉じる
        window.addEventListener("click", function (event) {
            if (event.target === historyModal) {
                historyModal.style.display = "none";
            }
        });
    });
    
  </script>

</body>
</html>