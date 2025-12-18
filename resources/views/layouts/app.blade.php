<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ISC - Marks mailer</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<style>
    @keyframes pulse-count {
        0% { transform: scale(1); }
        50% { transform: scale(1.15); }
        100% { transform: scale(1); }
    }

    #student-counter {
        min-width: 35px;
        animation: pulse-count 0.3s ease-out;
    }

    kbd {
        font-family: system-ui, -apple-system, sans-serif;
        font-size: 0.6rem !important;
        border-radius: 4px;
        box-shadow: 0 2px 0 #cbd5e0;
        display: inline-block;
        vertical-align: middle;
    }
</style>
<body>
    <div>
        @yield('content')
    </div>
</body>
@yield("script")
</html>
