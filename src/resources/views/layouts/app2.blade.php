<!-- resources/views/layouts/app2.blade.php -->

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
        </div>
    </header>
    <main class="main-container">
        @yield('content')
    </main>
    @yield('js')
</body>
</html>
