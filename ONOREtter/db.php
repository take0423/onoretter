<?php

function dbc(){
    //データベース接続
    $dsn = "mysql:dbname=tb220389db;host=localhost";
    $user = "tb-220389";
    $password = "E2Tr9Nju3c";
    //データベースにアクセス・エラーモードを設定
    $dbh = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    $host = "localhost";
    $dbname ="tb220389db";
    $user = "tb-220389";
    $password= "E2Tr9Nju3c";

    $dns = "mysql:dbname=$dbname;host=$host;charset=utf8";

    try{
        //pdoを使ってデータの取得をする
        $pdo = new PDO($dns, $user, $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return $pdo;//呼び出し先でpdoを使えるようにする
    }catch(PDOException $e){
        //接続失敗したとき
        exit($e->getMessage());//処理を止めてエラー内容を出力
    }
}
    // テーブルが存在しない時、テーブルを作成
    $sql = "CREATE TABLE IF NOT EXISTS onoretter_nikki"
    ."("
    ."id INT AUTO_INCREMENT PRIMARY KEY,"
    ."name varchar(255),"
    ."title char(32),"
    ."comment TEXT,"
    ."feel varchar(10),"
    ."file_name varchar(255) null,"
    ."file_path varchar(255) null,"
    ."date DATETIME,"
    ."pass varchar(10)"
    .");";
    $stmt = dbc() -> query($sql);

    //カキコミ内容表示
    // $sql = "SELECT * FROM onoretter_nikki";
    // $stmt= dbc() -> query($sql);
    // $results = $stmt -> fetchAll();
    // foreach($results as $result){
    //     echo $result["name"].",";
    //     echo $result["id"].",";
    //     echo $result["title"].",";
    //     echo $result["comment"]."<br>";
    //     echo "<hr>";
    // }

    // // データベースの削除
    // $sql = "DROP TABLE onoretter_nikki";
    // $stmt = dbc() -> query($sql);

    
function fileSave($postName, $postTitle, $postComment, $feel,$filename, $save_path, $date, $postPass){
    $result = False; //初期値をfalseで返す
    $sql = "INSERT INTO onoretter_nikki (name, title, comment, feel, file_name, file_path, date, pass) VALUES (:name, :title, :comment, :feel, :file_name, :file_path, :date, :pass)";
    $stmt = dbc() -> prepare($sql); //pdoを使用、sql文の準備
    try{
        //?に値を入れる
        $stmt -> bindParam(":name", $postName, PDO::PARAM_STR);
        $stmt -> bindParam(":title", $postTitle, PDO::PARAM_STR);
        $stmt -> bindParam(":comment", $postComment, PDO::PARAM_STR);
        $stmt -> bindParam(":feel", $feel, PDO::PARAM_STR);
        $stmt -> bindParam(":file_name", $filename, PDO::PARAM_STR);
        $stmt -> bindParam(":file_path", $save_path, PDO::PARAM_STR);
        $stmt -> bindParam(":date", $date, PDO::PARAM_STR);
        $stmt -> bindParam(":pass", $postPass, PDO::PARAM_STR);
        $result = $stmt -> execute();
        return $result;
    }catch(\Exeption $e){
        echo$e->getMessage();//処理を止めてエラー内容を出力
        return $result;
    }

}

function tweetSave($postName, $postTitle, $postComment, $feel, $date, $postPass){
    $result = False;
    $sql ="INSERT INTO onoretter_nikki (name, title, comment, feel, date, pass) VALUES (:name, :title, :comment, :feel, :date, :pass)";
    $stmt = dbc() -> prepare($sql);
    $stmt -> bindParam(":name", $postName, PDO::PARAM_STR);
    $stmt -> bindParam(":title", $postTitle, PDO::PARAM_STR);
    $stmt -> bindParam(":comment", $postComment, PDO::PARAM_STR);
    $stmt -> bindParam(":feel", $feel, PDO::PARAM_STR);
    $stmt -> bindParam(":date", $date, PDO::PARAM_STR);
    $stmt -> bindParam(":pass", $postPass, PDO::PARAM_STR);
    $result = $stmt -> execute();
    return $result;
}


function delete($delNo, $delPass){
    $delete_result = False;
    $sql = "SELECT * FROM onoretter_nikki";
    $stmt = dbc() -> query($sql);
    $results = $stmt -> fetchAll();
    foreach($results as $result){
        if($_SESSION["username"] == $result["name"]){
            if($delNo == $result["id"] && $delPass == $result["pass"]){
                $sql = "delete from onoretter_nikki where id=:id";
                $stmt = dbc() -> prepare($sql);
                $stmt -> bindParam(":id", $delNo, PDO::PARAM_INT);
                $delete_result = $stmt -> execute();
                return $delete_result;
            }
        }
    }
}

