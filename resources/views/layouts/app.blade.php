<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ISC - Marks mailer</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        (function() {
            const storedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = storedTheme || systemTheme;
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>
</head>
<style>
  * {
      box-sizing: border-box;
      transition: background-color 0.3s, color 0.3s;
  }
  @keyframes pulse-count {
      0% { transform: scale(1); }
      50% { transform: scale(1.15); }
      100% { transform: scale(1); }
  }

  #student-counter {
      min-width: 35px;
      animation: pulse-count 0.3s ease-out;
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

  .custom-file-upload {
    position: relative;
    max-width: 500px;
    margin: 0 auto;
  }

  .custom-file-upload input[type="file"] {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    border: 0;
  }

  .custom-file-upload label {
    display: block;
    padding: 40px 20px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px dashed rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    color: #a0a0a0;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
  }

  .custom-file-upload label:hover {
    background: rgba(255, 255, 255, 0.07);
    border-color: rgba(255, 255, 255, 0.5);
    color: #ffffff;
  }

  .custom-file-upload i {
    font-size: 2rem;
    opacity: 0.7;
  }

  .main-text {
    font-weight: 500;
    letter-spacing: 0.5px;
  }

  .sub-text {
    font-size: 0.8rem;
    opacity: 0.5;
    margin-top: 5px;
  }
  #mainContainer {
    background-color: #f8f9fa
  }
  [data-bs-theme="dark"] #mainContainer {
    background-color: #434343
  }

  .bg-white-prefer {
    background-color: #ffffff;
  }
  [data-bs-theme="dark"] .bg-white-prefer {
    background-color: #212529;
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@yield("script")
</html>
