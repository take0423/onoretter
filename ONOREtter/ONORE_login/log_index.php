<?php
    session_start();

    $_SESSION["login"] = 1;
    $err_msg ="";
    require_once "./log_db.php";
    logdbc();

    if(isset($_POST["login"])){
        if(!empty($_POST["username"]) && !empty($_POST["password"])){
            $username = $_POST["username"];
            $_SESSION["username"] = $username;
            $password = $_POST["password"];
            $login_result = login($username, $password);
            if($login_result){
                if(isset($_SESSION["login"]) == false){
                    echo "<a href='log_index.php'>ログインしてね</a>";
                }else{
                    echo $_SESSION["username"];
                    echo "ok!";
                }
            }else{
                $err_msg = "まちがっているよ";
            }
        }else{
            $err_msg = "入力されていません";
        }
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login_style.css">
    <title>ログイン画面</title>
</head>
<body>
    <div class="all-wrap">
        <div class="title-wrap">
            <h1>己ったー</h1>
        </div>
        <div class="form-wrap form-login">
            <form action="" method="post">
                <p class="text-m">♦ログイン画面♦</p>
                <p><?php if($err_msg){ echo $err_msg;}?></p>
                <span>ユーザ名</span><input type="text" name="username" ><br>
                <span>パスワード</span><input type="password" name="password"><br>
                <input class="submitBtn loginBtn" type="submit" name="login" value="ログイン">
            </form>
            <div class="otherBtn">
                <p><a href="signin.php">新規登録</a></p>
                <p><a href="del_account.php">アカウントを消す</a></p>
            </div>
        </div>
    </div>
</body>
</html>