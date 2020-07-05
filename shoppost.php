<?php
// セッション開始
session_start();

// ログイン状態チェック
if (isset($_SESSION["NAME"])) {
  $loginstatus = "login";
  $login_display = "ようこそ、".$_SESSION["NAME"]."さん";
  
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
$shopPostMessage = "";

// レビュー投稿ボタンが押された場合
if (isset($_POST["shoppost"])) {
    // 1. レビュータイトルの入力チェック
    if (empty($_POST["shopname"])) {  // 値が空のとき
        $errorMessage = '店名が未入力です。';
    } else if (empty($_POST["shopprefecture"])) {
      $errorMessage = '都道府県が未選択です。';
    } else if (empty($_POST["shopneareststation"])) {
      $errorMessage = '最寄駅が未入力です。';
    } else if (empty($_POST["shopopeninghours"])) {
      $errorMessage = '開店時間が未入力です。';
    } else if (empty($_POST["shopclosingtime"])) {
      $errorMessage = '閉店時間が未入力です。';
    } else if (empty($_POST["shopadress"])) {
      $errorMessage = '住所が未入力です。';
    } else if (empty($_POST["shoppowersupply"])) {
      $errorMessage = '電源（コンセント）の有無を選択してください。';
    } else if (empty($_POST["shopfreewifi"])) {
      $errorMessage = 'FreeWi-Fiの有無を選択してください。';
    }

    if (!empty($_POST["shopname"]) && !empty($_POST["shopprefecture"]) && !empty($_POST["shopneareststation"]) && !empty($_POST["shopopeninghours"]) && !empty($_POST["shopclosingtime"]) && !empty($_POST["shopadress"]) && !empty($_POST["shoppowersupply"]) && !empty($_POST["shopfreewifi"])) {
        // 入力したユーザ名とメールアドレスとパスワードを格納
        $shopname = $_POST["shopname"];
        $shopprefecture = $_POST["shopprefecture"];
        $shopneareststation = $_POST["shopneareststation"];
        $shopopeninghours = $_POST["shopopeninghours"];
        $shopclosingtime = $_POST["shopclosingtime"];
        $shopmorninghoursclose = $_POST["shopmorninghoursclose"];
        $shopadress = $_POST["shopadress"];
        $shoppowersupply = $_POST["shoppowersupply"];
        $shopfreewifi = $_POST["shopfreewifi"];

      
        $image1 = uniqid(mt_rand(), true);//ファイル名をユニーク化
        $image1 .= '.' . substr(strrchr($_FILES['image1']['name'], '.'), 1);//アップロードされたファイルの拡張子を取得
        $file = "shopimages/$image1";

        $image2 = uniqid(mt_rand(), true);//ファイル名をユニーク化
        $image2 .= '.' . substr(strrchr($_FILES['image2']['name'], '.'), 1);//アップロードされたファイルの拡張子を取得
        $file = "shopimages/$image2";

        $image3 = uniqid(mt_rand(), true);//ファイル名をユニーク化
        $image3 .= '.' . substr(strrchr($_FILES['image3']['name'], '.'), 1);//アップロードされたファイルの拡張子を取得
        $file = "shopimages/$image3";
    
        // 2. ユーザ名とメールアドレスとパスワードが入力されていたら認証する
        $dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);

        // 3. エラー処理
        try {
          $pdo = new PDO( 'mysql:host=mysql10048.xserver.jp;dbname=xs836976_mornicafedb;charset=utf8','xs836976_user', 'shun0505');

            $stmt = $pdo->prepare("INSERT INTO SHOP(`shop_name`, `shop_prefecture`, `shop_neareststation`, `shop_adress`, `shop_openinghours`, `shop_closingtime`, `shop_morninghoursclose`, `shop_powersupply`, `shop_freewi-fi`, `shop_photo1`, `shop_photo2`, `shop_photo3`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute(array($shopname, $shopprefecture, $shopneareststation, $shopadress, $shopopeninghours, $shopclosingtime, $shopmorninghoursclose, $shoppowersupply, $shopfreewifi, $image1, $image2, $image3));  // パスワードのハッシュ化を行う（今回は文字列のみなのでbindValue(変数の内容が変わらない)を使用せず、直接excuteに渡しても問題ない）
            $userid = $pdo->lastinsertid();  // 登録した(DB側でauto_incrementした)IDを$useridに入れる

            move_uploaded_file($_FILES['image1']['tmp_name'], './shopimages/' . $image1);//imagesディレクトリにファイル保存
            move_uploaded_file($_FILES['image2']['tmp_name'], './shopimages/' . $image2);//imagesディレクトリにファイル保存
            move_uploaded_file($_FILES['image3']['tmp_name'], './shopimages/' . $image3);//imagesディレクトリにファイル保存

            $shopPostMessage = 'お店の投稿が完了しました。';  // ログイン時に使用するIDとパスワード
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
  <title>モニカフェ</title>
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="shoppost-stylesheet.css?12345">
  <link rel="stylesheet" href="responsive.css?20200302">
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <script　type="text/javascript" src="script.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
</head>
<body>
  <header>
    <div class="container">
      <div class="header-left">
        <p class="header-btn" onclick="transition(toppage)">モニカフェ</p>
      </div>
      <div class="header-right">
      <p class="login-btn" onclick="transition(userloginUrl)"><?php echo $login_display; ?></p>
      </div>
      <div class="clear"></div>
    </div>
  </header>

  <div class="shoppostform-wrapper">
    <div class="container">
      <h2 class="heading">お店の投稿</h2>
      <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
      <div><font color="#0000ff"><?php echo htmlspecialchars($shopPostMessage, ENT_QUOTES); ?></font></div>
      <form id="shoppostForm" name="shoppostForm" action="" method="POST" enctype="multipart/form-data">

        <div class="item first-line">
          <label class="label-first" for="shopname">店名</label>
          <input class="shoppostform" type="text" id="shopname" name="shopname" value="" placeholder="店名を入力">
        </div>
      
        <div class="item second-line">
          <div class="second second-left">
            <label class="label-second" for="shopprefecture">都道府県</label>
            <select class="shoppostform-second" id="shopprefecture" name="shopprefecture">
              <option value="" selected>都道府県</option>
              <option value="北海道">北海道</option>
              <option value="青森県">青森県</option>
              <option value="岩手県">岩手県</option>
              <option value="宮城県">宮城県</option>
              <option value="秋田県">秋田県</option>
              <option value="山形県">山形県</option>
              <option value="福島県">福島県</option>
              <option value="茨城県">茨城県</option>
              <option value="栃木県">栃木県</option>
              <option value="群馬県">群馬県</option>
              <option value="埼玉県">埼玉県</option>
              <option value="千葉県">千葉県</option>
              <option value="東京都">東京都</option>
              <option value="神奈川県">神奈川県</option>
              <option value="新潟県">新潟県</option>
              <option value="富山県">富山県</option>
              <option value="石川県">石川県</option>
              <option value="福井県">福井県</option>
              <option value="山梨県">山梨県</option>
              <option value="長野県">長野県</option>
              <option value="岐阜県">岐阜県</option>
              <option value="静岡県">静岡県</option>
              <option value="愛知県">愛知県</option>
              <option value="三重県">三重県</option>
              <option value="滋賀県">滋賀県</option>
              <option value="京都府">京都府</option>
              <option value="大阪府">大阪府</option>
              <option value="兵庫県">兵庫県</option>
              <option value="奈良県">奈良県</option>
              <option value="和歌山県">和歌山県</option>
              <option value="鳥取県">鳥取県</option>
              <option value="島根県">島根県</option>
              <option value="岡山県">岡山県</option>
              <option value="広島県">広島県</option>
              <option value="山口県">山口県</option>
              <option value="徳島県">徳島県</option>
              <option value="香川県">香川県</option>
              <option value="愛媛県">愛媛県</option>
              <option value="高知県">高知県</option>
              <option value="福岡県">福岡県</option>
              <option value="佐賀県">佐賀県</option>
              <option value="長崎県">長崎県</option>
              <option value="熊本県">熊本県</option>
              <option value="大分県">大分県</option>
              <option value="宮崎県">宮崎県</option>
              <option value="鹿児島県">鹿児島県</option>
              <option value="沖縄県">沖縄県</option>
              <option value="ハワイ">ハワイ</option>
            </select>
          </div>
          <div class="second second-right">
            <label class="label-second" for="shopneareststation">最寄駅</label>
            <input class="shoppostform-second" type="text" id="shopneareststation" name="shopneareststation" value="" placeholder="最寄駅を入力">
          </div>
          <div class="clear"></div>
        </div>


        <div class="item third-line">
          <div class="third third-left">
            <label class="label-third" for="shopopeninghours">開店時間</label>
            <input class="shoppostform-third" type="time" id="shopopeninghours" name="shopopeninghours" value="" placeholder="開店時間を入力">
          </div>
          <div class="third third-right">
            <label class="label-third" for="shopclosingtime">閉店時間</label>
            <input class="shoppostform-third" type="time" id="shopclosingtime" name="shopclosingtime" value="" placeholder="閉店時間を入力">
          </div>
          <div class="clear"></div>
        </div>

        <div class="item fourth-line">
          <label class="label-fourth" for="shopmorninghoursclose">モーニングサービス終了時間</label>
          <input class="shoppostform" type="time" id="shopmorninghoursclose" name="shopmorninghoursclose" value="" placeholder="モーニングサービス終了時間を入力">
        </div>

        <div class="item fourth-line">
          <label class="label-fourth" for="shopadress">住所</label>
          <input class="shoppostform" type="text" id="shopadress" name="shopadress" value="" placeholder="住所を入力">
        </div>

        <div class="item fifth-line">
          <div class="fifth fifth-left">
            <label class="label-fifth" for="shoppowersupply">電源（コンセント）</label><br>
            <label><input type="radio" id="shoppowersupply" name="shoppowersupply" value="○">有り</label>
            <label><input type="radio" id="shoppowersupply" name="shoppowersupply" value="×">無し</label>
          </div>
          <div class="fifth fifth-right">
            <label class="label-fifth" for="shopfreewifi">Free Wi-Fi</label><br>
            <label><input type="radio" id="shopfreewifi" name="shopfreewifi" value="○">有り</label>
            <label><input type="radio" id="shopfreewifi" name="shopfreewifi" value="×">無し</label>
          </div>
  
          <label class="shopphoto1" for="shopphoto1">写真１</label>
          <input class="shopphoto1" type="file" name="image1"><br>

          <label class="shopphoto2" for="shopphoto2">写真２</label>
          <input class="shopphoto2" type="file" name="image2"><br>

          <label class="shopphoto3" for="shopphoto3">写真３</label>
          <input class="shopphoto3" type="file" name="image3">

          <div class="clear"></div>
        </div>

        <input class="btn shoppost" type="submit" id="shoppost" name="shoppost" value="投稿する">


      </form>
      
    </div>
  </div>

  

  <footer>
    <div class="container">
      <p>Copyright©︎SHUNICHI HATAEKYAMA. All Rights Reserved.</p>
    </div>

  </footer>

  
  <script src="shoppostscript.js?12">
  </script>
</body>
</html>
