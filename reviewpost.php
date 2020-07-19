<?php
// セッション開始
session_start();
header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

// ログイン状態チェック
if (isset($_SESSION["NAME"])) {
  $loginstatus = "login";
  $login_display = "ようこそ、".$_SESSION["NAME"];
  
}else {
  $loginstatus = "logout";
  $login_display = "ログイン";
}

$db['host'] = "mysql10048.xserver.jp";  // DBサーバのURL
$db['user'] = "xs836976_user";  // ユーザー名
$db['pass'] = "shun0505";  // ユーザー名のパスワード
$db['dbname'] = "xs836976_mornicafedb";  // データベース名

// エラーメッセージ、登録完了メッセージの初期化
$errorMessage = "";
$reviewPostMessage = "";

if (isset($_POST["shopid"])) {
  $shopid = $_POST["shopid"];
}

$dsn1 = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);

try {
  $pdo1 = new PDO( 'mysql:host=mysql10048.xserver.jp;dbname=xs836976_mornicafedb;charset=utf8','xs836976_user', 'shun0505');

  $sql1 = "SELECT * FROM SHOP where id = $shopid";
  $stmt1 = array();
  foreach ($pdo1->query($sql1) as $row) {
    array_push($stmt1,$row);
  }

} catch (PDOException $e) {
  $errorMessage = 'データベースエラー' + $e->getMessage();
  // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
  echo $e->getMessage();
}

// レビュー投稿ボタンが押された場合
if (isset($_POST["reviewpost"])) {
    // 1. レビュータイトルの入力チェック
    if (empty($_POST["reviewtitle"])) {  // 値が空のとき
        $errorMessage = 'レビューのタイトルが未入力です。';
    } else if (empty($_POST["reviewrank"])) {
      $errorMessage = 'レビュー評価を選択してください。';
    } else if (empty($_POST["reviewtext"])) {
      $errorMessage = 'レビューの本文が未入力です。';
    }

    if (!empty($_POST["reviewtitle"]) && !empty($_POST["reviewrank"]) && !empty($_POST["reviewtext"])) {
        // 入力したユーザ名とメールアドレスとパスワードを格納
        $reviewshopid = $_POST["reviewshopid"];
        $reviewuser = $_POST["reviewuser"];
        $reviewdate = $_POST["reviewdate"];
        $reviewrank = $_POST["reviewrank"];

        $reviewtitle = $_POST["reviewtitle"];
        $reviewtext = $_POST["reviewtext"];

        $image1 = uniqid(mt_rand(), true);//ファイル名をユニーク化
        $image1 .= '.' . substr(strrchr($_FILES['image1']['name'], '.'), 1);//アップロードされたファイルの拡張子を取得
        $file = "images/$image1";

        $image2 = uniqid(mt_rand(), true);//ファイル名をユニーク化
        $image2 .= '.' . substr(strrchr($_FILES['image2']['name'], '.'), 1);//アップロードされたファイルの拡張子を取得
        $file = "images/$image2";

        $image3 = uniqid(mt_rand(), true);//ファイル名をユニーク化
        $image3 .= '.' . substr(strrchr($_FILES['image3']['name'], '.'), 1);//アップロードされたファイルの拡張子を取得
        $file = "images/$image3";
    
    
        // 2. ユーザ名とメールアドレスとパスワードが入力されていたら認証する
        $dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);

        // 3. エラー処理
        try {
          $pdo = new PDO( 'mysql:host=mysql10048.xserver.jp;dbname=xs836976_mornicafedb;charset=utf8','xs836976_user', 'shun0505');

            $stmt = $pdo->prepare("INSERT INTO REVIEW(`review_shopid`, `review_user`, `review_date`, `review_rank`, `review_titlle`, `review_text`, `review_photo1`, `review_photo2`, `review_photo3`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute(array($reviewshopid, $reviewuser, $reviewdate, $reviewrank, $reviewtitle, $reviewtext, $image1, $image2, $image3));  // パスワードのハッシュ化を行う（今回は文字列のみなのでbindValue(変数の内容が変わらない)を使用せず、直接excuteに渡しても問題ない）
            $userid = $pdo->lastinsertid();  // 登録した(DB側でauto_incrementした)IDを$useridに入れる

            move_uploaded_file($_FILES['image1']['tmp_name'], './reviewimages/' . $image1);//imagesディレクトリにファイル保存
            move_uploaded_file($_FILES['image2']['tmp_name'], './reviewimages/' . $image2);//imagesディレクトリにファイル保存
            move_uploaded_file($_FILES['image3']['tmp_name'], './reviewimages/' . $image3);//imagesディレクトリにファイル保存

            $reviewPostMessage = 'レビューの投稿が完了しました。';  // ログイン時に使用するIDとパスワード
        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー' + $e->getMessage();
            // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
            echo $e->getMessage();
        }
    } 
}
?>

<script type="text/javascript">
  var loginstatus = '<?php echo $loginstatus; ?>';
</script>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>モーニンカフェ</title>
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="reviewpost-stylesheet.css?123456789123">
  <link rel="stylesheet" href="responsive.css?20200302">
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
</head>
<body>
  <header>
    <div class="container">
      <div class="header-left">
        <p class="header-btn" onclick="transition(toppage)">モーニンカフェ</p>
      </div>
      <div class="header-right">
        <p id="loginlogout-btn" class="loginusername" onclick="transition(userloginUrl)"><?php echo $login_display; ?></p>
        <p class="loginlogoutbtn-tips">ログアウトする</p>
        <p id="san" class="san">さん</p>
      </div>
      <div class="clear"></div>
    </div>
  </header>

  <div class="reviewpostform-wrapper">
    <div class="container">
      <h2 class="heading">レビューの投稿</h2>
      <?php foreach ($stmt1 as $key): ?>
      <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
      <div><font color="#0000ff"><?php echo htmlspecialchars($reviewPostMessage, ENT_QUOTES); ?></font></div>
      <p class="shopname text">レビューを投稿するお店<br><?php echo $key['shop_name'] ?></p>
      
      
      <form id="reviewpostForm" name="reviewpostForm" action="" method="POST" enctype="multipart/form-data">
        <fieldset style="border: none;">
          <label class="reviewshopid" for="reviewshopid"></label>
          <input class="reviewpostform titleform" type="hidden" id="reviewshopid" name="reviewshopid" value="<?php echo $key['id'] ?>" placeholder="レビューする店名を入力">
          <label class="reviewuser" for="reviewuser">レビューを投稿するユーザー<br><?php echo $_SESSION["NAME"];?>さん</label><br>
          <input class="reviewpostform titleform" type="hidden" id="reviewuser" name="reviewuser" value="<?php echo $_SESSION["NAME"];?>" placeholder="レビューするユーザー名を入力">
          <label class="reviewdate" for="reviewdate"></label>
          <input class="reviewdate reviewpostform titleform" type="date" id="reviewdate" name="reviewdate" value="" placeholder="レビューした日付を入力">
          
      

          <label class="reviewtitle" for="reviewtitle">タイトル</label>
          <input class="reviewpostform titleform" type="text" id="reviewtitle" name="reviewtitle" value="<?php if( !empty($_POST['reviewtitle']) ){ echo $_POST['reviewtitle']; } ?>" placeholder="レビューのタイトルを入力">
          
          <label class="reviewrank" for="reviewrank">評価</label>
          <select class="reviewpostform titleform" id="reviewrank" name="reviewrank">
            <option value="" selected>評価を選択</option>
            <option value="5" <?php if( !empty($_POST['reviewrank']) && $_POST['reviewrank'] == "5" ){ echo 'selected'; } ?>>★★★★★</option>
            <option value="4" <?php if( !empty($_POST['reviewrank']) && $_POST['reviewrank'] == "4" ){ echo 'selected'; } ?>>★★★★</option>
            <option value="3" <?php if( !empty($_POST['reviewrank']) && $_POST['reviewrank'] == "3" ){ echo 'selected'; } ?>>★★★</option>
            <option value="2" <?php if( !empty($_POST['reviewrank']) && $_POST['reviewrank'] == "2" ){ echo 'selected'; } ?>>★★</option>
            <option value="1" <?php if( !empty($_POST['reviewrank']) && $_POST['reviewrank'] == "1" ){ echo 'selected'; } ?>>★</option>
          </select>
          
          <label class="reviewtext" for="reviewtext">レビュー</label>
          <textarea class="reviewpostform titleform" id="reviewtext"　cols="30" rows="10" name="reviewtext" placeholder="レビューの本文を入力"><?php if( !empty($_POST['reviewtext']) ){ echo $_POST['reviewtext']; } ?></textarea>

          <label class="reviewphoto1" for="reviewphoto1">写真１</label>
          <input class="reviewphoto1" type="file" name="image1"><br>

          <label class="reviewphoto2" for="reviewphoto2">写真２</label>
          <input class="reviewphoto2" type="file" name="image2"><br>

          <label class="reviewphoto3" for="reviewphoto3">写真３</label>
          <input class="reviewphoto3" type="file" name="image3"><br>

          <input class="btn reviewpost" type="submit" id="reviewpost" name="reviewpost" value="投稿する">
          <input name="shopid" type="hidden" value="<?php echo $shopid; ?>">
        </fieldset>
        <?php endforeach; ?>
      </form>
      
      
    </div>
  </div>

  

  <footer>
    <div class="container">
      <p>©︎ 2020 Shirotayama</p>
    </div>

  </footer>

  <script src="reviewpostscript.js?1234567">
  </script>

</body>
</html>
