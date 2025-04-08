<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>クラス一覧</title>
    @vite('resources/js/three-app.ts')
    <link rel="stylesheet" href="{{ asset('css/admin_user.css') }}">
</head>
<body>
<div class="background-pattern"></div>
<canvas id="myCanvas"></canvas>
    <div class="app">
    <div class="main">
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
            <button onclick="location.href='{{ route('admin_show') }}'" class="home">
            <img src="image/home.svg" alt="画像の説明">
            <span class="home-text">HOME</span>
            </button>
            <button onclick="location.href='{{ route('admin_edit') }}'" class="work">
            <img src="image/icon.svg" alt="画像の説明">
            <span class="home-text">アカウント管理</span>
            </button>
            <button onclick="location.href='{{ route('admin_user') }}'" class="user">
            <img src="image/user.png" alt="画像の説明">
            <span class="home-text">ユーザー管理</span>
            </button>
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
</div>

<div class="class-list">
    <h2>クラス一覧</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>クラス名</th>
                <th>権限</th>
                <th>作成日</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($classes as $class)
            <tr>
                <td>{{ $class->id }}</td>
                <td>{{ $class->class_name }}</td>
                <td>{{ $class->authority_id ?? 'なし' }}</td>
                <td>{{ $class->created_at->format('Y-m-d') }}</td>
                <td>
                    <button onclick="editClass({{ $class->id }})">編集</button>
                    <button onclick="deleteClass({{ $class->id }})">削除</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
function editClass(classId) {
    alert(`クラスID ${classId} を編集します（実装は別途追加してください）`);
}

function deleteClass(classId) {
    if (confirm(`クラスID ${classId} を削除しますか？`)) {
        fetch(`/admin/classes/delete/${classId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('クラスを削除しました');
                location.reload();
            } else {
                alert('削除に失敗しました');
            }
        })
        .catch(error => {
            console.error('エラー:', error);
            alert('削除中にエラーが発生しました');
        });
    }
}
</script>
</body>
</html>