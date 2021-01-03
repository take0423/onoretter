<?php

function logdbc(){
    //データベース接続
    $dsn = "mysql:dbname=tb220389db;host=localhost";
    $user = "tb-220389";
    $password = "E2Tr9Nju3c";
    //データベースにアクセス・エラーモードを設定
    try{
    $dbh = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    return $dbh;//呼び出し先でpdoを使えるようにする

    }catch(PDOException $e){
        //接続失敗したとき
        exit($e->getMessage());//処理を止めてエラー内容を出力
    }
}

$sql = "CREATE TABLE IF NOT EXISTS onoretter_users"
."("
."username varchar(100) not null,"
."password varchar(255) not null"
.");";
$stmt = logdbc() -> query($sql);

// $sql = "SHOW TABLES";
// $result = logdbc() -> query($sql);
// foreach($result as $row){
//     echo $row[0];
//     echo "<br>";
// }

    // // データベースの削除
    // $sql = "DROP TABLE onoretter_users";
    // $stmt = logdbc() -> query($sql);

$sql = "SELECT * FROM onoretter_users";
$stmt= logdbc() -> query($sql);
$results = $stmt -> fetchAll();
foreach($results as $result){
    echo $result["username"].",";
    echo $result["password"]."<br>";
    echo "<hr>";
}

//ログイン
function login($username, $password){
    $sql = "SELECT count(*) FROM onoretter_users where username=? and password=?";
    $stmt = logdbc() -> prepare($sql);
    $stmt -> execute( array($username, $password));
    $result = $stmt -> fetch();
    $stmt = null;
    $sql = null;
    if($result[0] != 0){
        header("location: ../top.php");
    }
}
//新規登録
function signin($username, $password){
    $sql = "SELECT count(*) FROM onoretter_users where username=?";
    $stmt = logdbc() -> prepare($sql);
    $stmt -> execute( array($username));
    $result = $stmt -> fetch();
    if($result[0] > 0){
        $signin = header("location: signin.php");
    }else{
        $sql = "INSERT INTO onoretter_users(username, password) values(?, ?)";
        $stmt = logdbc() -> prepare($sql);
        $signin = $stmt -> execute( array($username, $password));
    }
    return $signin;
    $stmt = null;
    $sql = null;
}
//アカウント削除
function delete($username, $password){
    $sql = "SELECT * FROM onoretter_users";
    $stmt = logdbc() -> query($sql);
    $results = $stmt -> fetchAll();
    foreach($results as $result){
        if($username == $result["username"] && $password == $result["password"]){
            $sql = "delete from onoretter_users where username=:username";
            $stmt = logdbc() -> prepare($sql);
            $stmt -> bindParam(":username", $username, PDO::PARAM_STR);
            $delete_result =  $stmt -> execute();
            return $delete_result;
        }
    }
}