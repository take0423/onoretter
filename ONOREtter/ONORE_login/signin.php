<?php
    require_once "./log_db.php";
    logdbc();
    $err_msg ="";
    if(isset($_POST["signin"])){
        if(!empty($_POST["username"]) && !empty($_POST["password"])){
            $username = $_POST["username"];
            $password = $_POST["password"];
            $signin = signin($username, $password);
            if($signin){
                header("location: log_index.php");
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
    <title>新規登録画面</title>
</head>
<body>
    <div class="all-wrap">
        <div class="title-wrap">
            <h1>己ったー</h1>
        </div>
        <div class="form-wrap form-signin">
            <form action="" method="post">
                <p class="text-m">♦アカウント登録♦</p>
                <p><?php if($err_msg){ echo $err_msg;}?></p>
                <span>ユーザ名</span><input type="text" name="username" ><br>
                <span>パスワード</span><input type="password" name="password"><br>
                <input class="submitBtn signinBtn" type="submit" name="signin" value="登録" id="">
            </form>
            <div class="otherBtn">
                <p class="otherBtn-s"><a href="log_index.php">戻る</a></p>
            </div>
        </div>
    </div>
</body>
</html>