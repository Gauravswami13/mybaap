<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->title }} · MyBaap</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; font-family: Lexend, sans-serif; background: #F6FFFC; color: #1A2420; }
        header { background: #058354; color: #fff; padding: 20px 24px; }
        header a { color: #fff; text-decoration: none; font-weight: 700; font-size: 20px; }
        main { max-width: 720px; margin: 0 auto; padding: 24px; line-height: 1.6; }
        h1 { margin-top: 0; }
    </style>
</head>
<body>
    <header><a href="/">MyBaap</a></header>
    <main>
        <h1>{{ $page->title }}</h1>
        {!! $page->content !!}
    </main>
</body>
</html>
