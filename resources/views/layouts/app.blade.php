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
    #variable-menu .list-group-item {
        padding: 8px 12px;
        font-size: 0.9rem;
        cursor: pointer;
    }
    #textarea-mirror {
        visibility: hidden;
        position: absolute;
        top: 0;
        left: 0;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
</style>
<body>
    <div>
        @yield('content')
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
@yield("script")
</html>
