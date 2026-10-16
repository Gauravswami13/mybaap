<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'MyBaap')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #010a0f; --card: #10181d; --line: #243038; --text: #f4f4f4; --muted: #c4c2c2; --red: #e50914; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--text); font-family: Roboto, sans-serif; }
        a { color: inherit; text-decoration: none; }
        .top { position: sticky; top: 0; z-index: 5; display: flex; align-items: center; gap: 22px; padding: 14px 28px; background: rgba(1,10,15,.92); border-bottom: 1px solid #1c262c; }
        .logo { color: var(--red); font-weight: 700; font-size: 22px; letter-spacing: .04em; }
        .top nav { display: flex; gap: 16px; color: var(--muted); font-size: 14px; }
        .top nav a:hover { color: #fff; }
        .wrap { width: min(1180px, calc(100% - 32px)); margin: 0 auto; }
        .hero { position: relative; min-height: 420px; display: flex; align-items: flex-end; background: #071014 center/cover no-repeat; }
        .hero::after { content: ""; position: absolute; inset: 0; background: linear-gradient(90deg, #010a0f 0%, rgba(1,10,15,.2) 70%); }
        .hero-copy { position: relative; z-index: 1; padding: 48px 28px; max-width: 640px; }
        .hero h1 { margin: 8px 0; font-size: 40px; }
        .muted { color: var(--muted); line-height: 1.45; }
        .btn { display: inline-flex; align-items: center; gap: 8px; background: var(--red); color: #fff; border: 0; border-radius: 8px; padding: 12px 18px; font-weight: 700; }
        .chips, .rail { display: flex; gap: 10px; overflow: auto; padding-bottom: 6px; }
        .chip, .poster { background: var(--card); border: 1px solid var(--line); border-radius: 12px; }
        .chip { padding: 8px 14px; white-space: nowrap; }
        h2 { margin: 28px 0 12px; font-size: 20px; }
        .poster { width: 150px; flex: 0 0 150px; overflow: hidden; }
        .poster .thumb, .grid .thumb, .banner-card .thumb { background: #1c262c center/cover no-repeat; }
        .poster .thumb { aspect-ratio: 2/3; }
        .poster p, .grid p { margin: 8px 10px 12px; font-size: 13px; }
        .tag { position: absolute; top: 8px; left: 8px; background: rgba(0,0,0,.72); color: #fff; font-size: 10px; font-weight: 700; padding: 3px 6px; border-radius: 6px; }
        .thumb-wrap { position: relative; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px; }
        .grid .card { background: var(--card); border-radius: 12px; overflow: hidden; border: 1px solid var(--line); }
        .grid .thumb { aspect-ratio: 2/3; }
        .banner-card { display: block; margin: 28px 0 10px; border-radius: 16px; overflow: hidden; position: relative; min-height: 180px; background: #071014; }
        .banner-card .thumb { position: absolute; inset: 0; }
        .banner-card .copy { position: relative; z-index: 1; padding: 28px; background: linear-gradient(90deg, rgba(1,10,15,.9), rgba(1,10,15,.15)); min-height: 180px; display: flex; flex-direction: column; justify-content: flex-end; align-items: flex-start; }
        .detail { display: grid; grid-template-columns: minmax(0, 420px) 1fr; gap: 28px; padding: 28px 0 10px; }
        .detail .stage { border-radius: 16px; overflow: hidden; background: #071014 center/cover no-repeat; aspect-ratio: 16/9; }
        .plans { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px; }
        .plan { background: var(--card); border: 1px solid var(--line); border-radius: 16px; padding: 18px; }
        .plan strong { font-size: 28px; }
        .page { max-width: 760px; padding: 28px 0 40px; line-height: 1.6; }
        .field { display: flex; flex-direction: column; gap: 6px; margin: 0 0 14px; }
        .field input { background: #071014; color: #fff; border: 1px solid var(--line); border-radius: 8px; padding: 12px; font: inherit; }
        .flash { background: #3a1518; color: #fff; border-radius: 10px; padding: 10px 12px; }
        .menu a { display: block; background: var(--card); border: 1px solid var(--line); border-radius: 12px; padding: 14px; margin-bottom: 8px; }
        video { width: 100%; background: #000; border-radius: 16px; }
        footer { border-top: 1px solid #1c262c; color: var(--muted); padding: 22px 28px 40px; display: flex; gap: 16px; font-size: 13px; }
        @media (max-width: 800px) { .detail { grid-template-columns: 1fr; } .hero { min-height: 320px; } .hero h1 { font-size: 30px; } }
    </style>
</head>
<body>
    <header class="top">
        <a class="logo" href="{{ url('/') }}">MYBAAP</a>
        <nav>
            <a href="{{ url('/') }}">Home</a>
            <a href="{{ route('plans') }}">Plans</a>
            <a href="{{ route('faq') }}">FAQ</a>
            @auth
                <a href="{{ route('account') }}">Account</a>
            @else
                <a href="{{ route('login') }}">Login</a>
            @endauth
        </nav>
    </header>
    <main class="wrap">
        @if (session('status'))
            <p class="flash">{{ session('status') }}</p>
        @endif
        @if (session('error'))
            <p class="flash">{{ session('error') }}</p>
        @endif
        @yield('content')
    </main>
    <footer>
        <a href="{{ url('/privacy-policy') }}">Privacy</a>
        <a href="{{ url('/terms') }}">Terms</a>
        <a href="{{ url('/delete-account') }}">Delete account</a>
    </footer>
</body>
</html>
