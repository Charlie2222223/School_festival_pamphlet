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
            <div class="user-info">
                <p>{{ session('class_name', 'ゲスト') }}</p>
                <p>{{ session('user_name', 'ゲスト') }}</p>
            </div>  
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
    <div class="class-list">
        <h2>クラス一覧</h2>
        <table>
            <thead>
                <tr>
                    <th>クラス名</th>
                    <th>ログイン状態</th>
                    <th>登録ユーザー</th>
                    <th>ログイン中のユーザー</th> <!-- ログイン中のユーザー列を追加 -->
                </tr>
            </thead>
            <tbody>
                @foreach ($classes as $class)
                <tr>
                    <td>{{ $class->class_name }}</td>
                    <td>
                        @if (collect($logged_in_users)->contains('class_id', $class->id))
                            <span style="color: green;">● ログイン中</span>
                        @else
                            <span style="color: red;">● オフライン</span>
                        @endif
                    </td>
                    <td>
                        <details>
                            <summary>ユーザー一覧</summary>
                            <ul>
                                @foreach ($class->users as $user)
                                <li>{{ $user->name }} ({{ $user->email }})</li>
                                @endforeach
                            </ul>
                        </details>
                    </td>
                    <td>
                        <details>
                            <summary>ログイン中のユーザー</summary>
                            <ul>
                                @foreach ($logged_in_users as $loggedInUser)
                                    @if ($loggedInUser['class_id'] === $class->id)
                                        <li>{{ $loggedInUser['user_name'] }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </details>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
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