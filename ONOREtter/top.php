<?php

    session_start();

    require_once "./db.php";
    dbc();
    //未入力の項目があります
    $kara_post = "";
    $kara_edit = "";
    $kara_del = "";
    //○○完了！
    $post_comp = "";
    $edit_comp = "";
    $del_comp = "";
    //投稿できませんでした
    $mistake_msg = "";
    $errMsg = "";
//・・・・・・・・・・投稿ボタンを押したとき
if(isset($_POST["postBtn"])){
    //・・・・・・・・・・タイトル・コメント・顔文字・パスが埋まっていたら
    if(!empty($_POST["postTitle"]) && !empty($_POST["postComment"]) && !empty($_POST["feel"]) && !empty($_POST["postPass"])){
        
        //投稿内容を取得
        $postName = $_SESSION["username"];
        $postTitle = $_POST["postTitle"];
        $postComment = $_POST["postComment"];
        $feel = $_POST["feel"];
        $date = date("Y/m/d h:i:s");
        $postPass = $_POST["postPass"];

        //編集指定番号が空だったら→新規投稿
        if(empty($_POST["hdEdNo"])){
            //ファイル関連の取得
            $file = $_FILES["img"];
            $filename = basename($file["name"]);
            $tmp_path = $file["tmp_name"];
            $file_err = $file["error"];
            $fielsize = $file["size"];
            $upload_dir = "./upload_img/";
            $save_filename = date("YmdHis").$filename;
            $save_path = $upload_dir.$save_filename;
            //バリデーション
            //ファイルサイズが1MB未満か
            if($fielsize > 1048576 || $file_err == 2){
                echo "ファイルサイズは１MG未満にしてね";
            }
            //拡張は画像形式か
            $allow_ext = array("jpg", "jpeg", "png"); //許可する拡張子
            $file_ext = pathinfo($filename, PATHINFO_EXTENSION); //ファイル名から拡張子を取得
            
            //画像があるかどうか
            if(is_uploaded_file($tmp_path)){
                //小文字に直した拡張子が配列に含まれているか
                if(!in_array(strtolower($file_ext), $allow_ext)){
                    echo "画像ファイルを添付してね";
                }
                //・・・・・・・・・・画像がアップロードされた場合の表示
                if(move_uploaded_file($tmp_path, $upload_dir.$save_filename)){
                    $result = fileSave($postName, $postTitle, $postComment, $feel, $filename, $save_path, $date, $postPass);
                    if($result){
                        $post_comp = "投稿完了！";
                    }else{
                        $mistake_msg = "投稿できませんでした";
                    }
                }

            }else{//・・・・・・・・・・画像なしの場合の表示
                $result = tweetSave($postName, $postTitle, $postComment, $feel, $date, $postPass);
                if($result){
                    $post_comp = "投稿完了！";
                }else{
                    $mistake_msg = "投稿できませんでした";
                }
                    
            }
                
        }else{//編集番号があったら→編集
            $edit_comp = "編集完了！";
            $edNo = $_POST["hdEdNo"];
            $sql = "SELECT * FROM onoretter_nikki";
            $stmt = dbc() -> query($sql);
            $results = $stmt -> fetchAll();
            foreach($results as $result){
                $sql = "UPDATE onoretter_nikki SET name=:name, title=:title, comment=:comment, feel=:feel, date=:date, pass=:pass WHERE id=:id";
                $stmt = dbc() -> prepare($sql);
                $stmt -> bindParam(':id', $edNo, PDO::PARAM_INT);
                $stmt -> bindParam(':name', $postName, PDO::PARAM_STR);
                $stmt -> bindParam(":title", $postTitle, PDO::PARAM_STR);
                $stmt -> bindParam(":comment", $postComment, PDO::PARAM_STR);
                $stmt -> bindParam(":feel", $feel, PDO::PARAM_STR);
                $stmt -> bindParam(":date", $date, PDO::PARAM_STR);
                $stmt -> bindParam(":pass", $postPass, PDO::PARAM_STR);
                $stmt -> execute();            
            }
        }
    }else{
        $kara_post = "未入力の項目があるよ";
    }
}
//・・・・・・・・・・編集ボタンを押したとき
if(isset($_POST["editBtn"])){
    if(!empty($_POST["edNo"]) && !empty($_POST["edPass"])){
        $edNo = $_POST["edNo"];
        $hdUserName = $_SESSION["username"];
        $edPass = $_POST["edPass"];
        $sql = "SELECT * FROM onoretter_nikki";//データベースからデータを取り出す
        $stmt = dbc() -> query($sql);
        $results = $stmt->fetchAll();
        foreach( $results as $result ){
            if($_SESSION["username"] == $result["name"]){
                //番号とパスワードが一致しているか
                if( $edNo == $result['id'] && $edPass == $result['pass']){
                    //フォームに表示させる内容
                    $hdEdNo = $result['id'];
                    $editName = $result['name'];
                    $editTitle = $result['title'];
                    $editComment = $result['comment'];
                }
            }
        } 
    }else{
        $kara_edit = "未入力の項目があるよ";
    }
}
// ・・・・・・・・・・削除ボタンを押したとき
if(isset($_POST["delBtn"])){
    if(!empty($_POST["delNo"]) && !empty($_POST["delPass"])){
        $delNo = $_POST["delNo"];
        $delPass = $_POST["delPass"];

        $delete_result = delete($delNo, $delPass);
        if($delete_result){
            $del_comp = "削除完了！";
        }

    }else{
        $kara_del = "未入力の項目があるよ";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/top-style.css">
    <title>己ったー</title>
</head>
<body>
    <div class="all-wrap">
        <div class="title-wrap">
            <h1>己ったー</h1>
            <p>一日を振り返って己と向き合おう！</p>
            <p class="logout"><?php echo $_SESSION["username"]."さんようこそ"?></p>
            <p class="logout"><a href="ONORE_login/logout.php">ログアウト</a></p>
        </div>
        <div class="nikki-wrap">
            <div class="form-wrap">
                <!-- --------------------------------------------------------新規投稿 -->
                <div class="toukou-wrap">
                <?php if($kara_post){ echo $kara_post;}?>
                <?php if($mistake_msg){ echo $mistake_msg;}?>
                <p class="text-m">♦投稿フォーム♦</p>
                    <form enctype="multipart/form-data" action="" method="POST" class="form post-form">
                        <input type="hidden" name="hdEdNo" id="" value="<?php if(!empty($hdEdNo)){ echo $hdEdNo; }?>">
                        <input type="hidden" name="hdUserName" id="" value="<?php if(!empty($hdUserName)){ echo $hdUserName; }?>">
                        <input type="text" name="postTitle" placeholder="タイトル" id="" value="<?php if(isset($editTitle)){ echo $editTitle; } ?>">
                        <textarea name="postComment" id="" placeholder="自由にかいてね" value="<?php if(isset($editComment)){ echo $editComment; } ?>"></textarea>
                        <div class="feel-check">
                            <p style="font-size:13px; margin-left:10px;">気分は？</p>
                            <label><input class="feel" type="radio" name="feel" value="excellent" id=""><img src="img/excellent.png" alt="" width="30" height="30"></label>
                            <label><input class="feel" type="radio" name="feel" value="good" id=""><img src="img/good.png" alt="" width="30" height="30"></label>
                            <label><input class="feel" type="radio" name="feel" value="nomal" id=""><img src="img/nomal.png" alt="" width="30" height="30"></label>
                            <label><input class="feel" type="radio" name="feel" value="notgood" id=""><img src="img/not_good.png" alt="" width="30" height="30"></label>
                            <label><input class="feel" type="radio" name="feel" value="bad" id=""><img src="img/bad.png" alt="" width="30" height="30"></label>
                        </div>
                        <div class="file-up">
                            <!-- ファイルの最大サイズを指定 -->
                            <input type="hidden" name="MAX_FILE_SIZE" value="1048576" id="">
                            <input type="file" name="img" accept="image/*" id="">
                        </div>
                        <input type="text" name="postPass" placeholder="パスワード" id="">
                        <input class="submitBtn postBtn" type="submit" name="postBtn" value="カキコミ" id="">
                    </form>
                </div>
                <!-- --------------------------------------------------------編集 -->
                <div class="ED-flex">
                    <div class="hensyu-wrap">
                    <?php if($kara_edit){ echo $kara_edit;}?>
                    <?php if($errMsg){ echo $errMsg;}?>
                    <p class="text-m">♦編集フォーム♦</p>
                        <form action="" method="POST" class="form edit-form">
                            <input type="num" name="edNo" placeholder="編集対象番号" id="">
                            <input type="text" name="edPass" placeholder="パスワード" id="">
                            <input class="submitBtn editBtn" type="submit" name="editBtn" value="ヘンシュウ" id="">
                        </form>
                    </div>
                    <!-- --------------------------------------------------------削除 -->
                    <div class="sakujo-wrap">
                        <?php if($kara_del){ echo $kara_del;}?>
                        <p class="text-m">♦削除フォーム♦</p>
                        <form action="" method="POST" class="form delete-form">
                            <input type="num" name="delNo" placeholder="削除対象番号" id="">
                            <input type="text" name="delPass" placeholder="パスワード" id="">
                            <input class="submitBtn delBtn" type="submit" name="delBtn" value="サクジョ" id="">
                        </form>
                    </div>                    
                </div>
            </div>
            <div class="list-wrap">
                <h2>♦投稿一覧♦</h2>
                <div class="comp-msg">
                <p class="post_comp"><?php if($post_comp){ echo $post_comp; }?></p>
                <p class="edit_comp"><?php if($edit_comp){ echo $edit_comp; }?></p>
                <p class="del_comp"><?php if($del_comp){ echo $del_comp; }?></p>
                </div>
                <div class="lists">
                    <?php
                        $sql = "SELECT * FROM onoretter_nikki";
                        $stmt = dbc() -> query($sql);//実行して結果を返す
                        $results = $stmt -> fetchAll();                        
                    ?>
                    <?php foreach($results as $result){ ?>
                        <div class="list">
                            <div class="listNo-ciecle">
                                <p class="list-no"><?php echo $result["id"]; ?></p>
                            </div>
                            <div class="feel-mark">
                                <?php
                                    if($result["feel"] == "excellent"){
                                        echo "<img src='img/excellent.png' alt='' width='35' height='35'>";
                                    }elseif($result["feel"] == "good"){
                                        echo "<img src='img/good.png' alt='' width='35' height='35'>";
                                    }elseif($result["feel"] == "nomal"){
                                        echo "<img src='img/nomal.png' alt='' width='35' height='35'>";
                                    }elseif($result["feel"] == "notgood"){
                                        echo "<img src='img/not_good.png' alt='' width='35' height='35'>";
                                    }elseif($result["feel"] == "bad"){
                                        echo "<img src='img/bad.png' alt='' width='35' height='35'>";
                                    }
                                ?>
                            </div>
                            <p class="list-title"><?php echo $result["title"]; ?></p>
                            <div class="list-comment"><p><?php echo $result["comment"]; ?></p></div>
                            <div class="list-img">
                                <?php if(isset($result["file_path"]) ){?>
                                <img src="<?php echo $result["file_path"];?>" alt="">
                                <?php }?>
                            </div>
                            <p class="list-date">by【<?php echo $result["name"]; ?>】さん <?php echo $result["date"];?></p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>