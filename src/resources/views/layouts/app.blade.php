<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('css')
    <title>{{ config('app.name', 'coachtechフリマ') }}</title>

</head>
<body class="body">

    <header class="header">
        <div class="function-bar">

            {{-- 左：ロゴ --}}
            <a href="{{ url('/') }}" class="logo-image">
                <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" class="logo-png">
            </a>

            {{-- 中央：検索フォーム --}}
            <form action="{{ route('items.search') }}" method="GET" class="search-form">
                <input type="text" name="keyword" placeholder="なにをお探しですか？"
                    class="search-form__input">
            </form>

            {{-- 右：リンクセット --}}
            <div class="link-set">
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-btn">ログアウト</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="login-btn">ログイン</a>
                @endauth
                <a href="{{ route('mypage.index') }}" class="mypage-link">マイページ</a>
                <div class="sell-btn">
                    <a href="{{ route('items.create') }}" class="sell-btn__link">
                    出品
                    </a>
                </div>
            </div>
        </div>
    </header>
    <main class="main-container">
        @yield('content')
    </main>
    @yield('js')
</body>
</html>
