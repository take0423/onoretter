<?php
    session_start();
    $_SESSION = array();
    if(isset($_COOKIE["session_name"]) == true){
        setcookie(session_name(), "", time() - 4200, "/");
    }
    session_destroy();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login_style.css">
    <title>ログアウト</title>
</head>
<body>
    <div class="all-wrap">
        <div class="title-wrap">
            <h1>己ったー</h1>
        </div>
        <div class="form-wrap comp-logout">
            <p class="text-m">♦ログアウトしました♦</p>
            <div class="otherBtn-logout">
                <p><a href="log_index.php">ログイン画面へ</a></p>
            </div>
        </div>
    </div>
</body>
</html>