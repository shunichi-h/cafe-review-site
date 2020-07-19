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
$shopPostMessage = "";

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

        if(!empty($_FILES['image1'])){
          $image1 = uniqid(mt_rand(), true);//ファイル名をユニーク化
          $image1 .= '.' . substr(strrchr($_FILES['image1']['name'], '.'), 1);//アップロードされたファイルの拡張子を取得
          $file = "shopimages/$image1";
        }else {
          foreach ($stmt1 as $key1){
            $image1 = $key1['shop_photo1'];
          }
        }
        
        if(!empty($_FILES['image2'])){
          $image2 = uniqid(mt_rand(), true);//ファイル名をユニーク化
          $image2 .= '.' . substr(strrchr($_FILES['image2']['name'], '.'), 1);//アップロードされたファイルの拡張子を取得
          $file = "shopimages/$image2";
        }else {
          foreach ($stmt1 as $key1){
            $image1 = $key1['shop_photo2'];
          }
        }
        
        if(!empty($_FILES['image3'])){
          $image3 = uniqid(mt_rand(), true);//ファイル名をユニーク化
          $image3 .= '.' . substr(strrchr($_FILES['image3']['name'], '.'), 1);//アップロードされたファイルの拡張子を取得
          $file = "shopimages/$image3";
        }else {
          foreach ($stmt1 as $key1){
            $image1 = $key1['shop_photo3'];
          }
        }
    
        // 2. ユーザ名とメールアドレスとパスワードが入力されていたら認証する
        $dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);

        // 3. エラー処理
        try {
          $pdo = new PDO( 'mysql:host=mysql10048.xserver.jp;dbname=xs836976_mornicafedb;charset=utf8','xs836976_user', 'shun0505');

            $stmt = $pdo->prepare("UPDATE SHOP SET `shop_name` = ?, `shop_prefecture` = ?, `shop_neareststation` = ?, `shop_adress` = ?, `shop_openinghours` = ?, `shop_closingtime` = ?, `shop_morninghoursclose` = ?, `shop_powersupply` = ?, `shop_freewi-fi` = ?, `shop_photo1` = ?, `shop_photo2` = ?, `shop_photo3` = ? WHERE `id` = $shopid");

            $stmt->execute(array($shopname, $shopprefecture, $shopneareststation, $shopadress, $shopopeninghours, $shopclosingtime, $shopmorninghoursclose, $shoppowersupply, $shopfreewifi, $image1, $image2, $image3));  // パスワードのハッシュ化を行う（今回は文字列のみなのでbindValue(変数の内容が変わらない)を使用せず、直接excuteに渡しても問題ない）
            
            if(!empty($_FILES['image1'])){
              move_uploaded_file($_FILES['image1']['tmp_name'], './shopimages/' . $image1);//imagesディレクトリにファイル保存
            }

            if(!empty($_FILES['image2'])){
              move_uploaded_file($_FILES['image2']['tmp_name'], './shopimages/' . $image2);//imagesディレクトリにファイル保存
            }

            if(!empty($_FILES['image3'])){
              move_uploaded_file($_FILES['image3']['tmp_name'], './shopimages/' . $image3);//imagesディレクトリにファイル保存
            }

            $shopPostMessage = '店情報の更新が完了しました。';  // ログイン時に使用するIDとパスワード
        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー' + $e->getMessage();
            // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
            echo $e->getMessage();
        }
    } 
}

$prefecture_array = array(
  '北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県',
  '埼玉県','千葉県','東京都','神奈川県','新潟県','富山県','石川県','福井県','山梨県','長野県',
  '岐阜県','静岡県','愛知県','三重県','滋賀県','京都府','大阪府','兵庫県','奈良県','和歌山県',
  '鳥取県','島根県','岡山県','広島県','山口県','徳島県','香川県','愛媛県','高知県','福岡県',
  '佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県','沖縄県','ハワイ'
);

$time_array = array(
  '05:00','05:15','05:30','05:45',
  '06:00','06:15','06:30','06:45',
  '07:00','07:15','07:30','07:45',
  '08:00','08:15','08:30','08:45',
  '09:00','09:15','09:30','09:45',
  '10:00','10:15','10:30','10:45',
  '11:00','11:15','11:30','11:45',
  '12:00','12:15','12:30','12:45',
  '13:00','13:15','13:30','13:45',
  '14:00','14:15','14:30','14:45',
  '15:00','15:15','15:30','15:45',
  '16:00','16:15','16:30','16:45',
  '17:00','17:15','17:30','17:45',
  '18:00','18:15','18:30','18:45',
  '19:00','19:15','19:30','19:45',
  '20:00','20:15','20:30','20:45',
  '21:00','21:15','21:30','21:45',
  '22:00','22:15','22:30','22:45',
  '23:00','23:15','23:30','23:45',
  '24:00',
);

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
  <link rel="stylesheet" type="text/css" href="shoppost-stylesheet.css?1234567">
  <link rel="stylesheet" href="responsive.css?20200302">
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <script　type="text/javascript" src="script.js"></script>
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

  <div class="shoppostform-wrapper">
    <div class="container">
      <h2 class="heading">店情報の編集</h2>
      <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
      <div><font color="#0000ff"><?php echo htmlspecialchars($shopPostMessage, ENT_QUOTES); ?></font></div>
      <form id="shoppostForm" name="shoppostForm" action="" method="POST" enctype="multipart/form-data">
      <?php foreach ($stmt1 as $key1): ?>

        <div class="item first-line">
          <label class="label-first" for="shopname">店名</label>
          <input class="shoppostform" type="text" id="shopname" name="shopname" value="<?php echo $key1['shop_name'] ?>" placeholder="店名を入力">
        </div>
      
        <div class="item second-line">
          <div class="second second-left">
            <label class="label-second" for="shopprefecture">都道府県</label>
            <select class="shoppostform-second" id="shopprefecture" name="shopprefecture">
              <option value="<?php echo $key1['shop_prefecture'] ?>" selected><?php echo $key1['shop_prefecture'] ?></option>
              <?php foreach ($prefecture_array as $key_prefecture): ?>
                <option value="<?php echo $key_prefecture ?>" <?php if( !empty($_POST['shopprefecture']) && $_POST['shopprefecture'] == $key_prefecture ){ echo 'selected'; } ?>><?php echo $key_prefecture ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="second second-right">
            <label class="label-second" for="shopneareststation">最寄駅</label>
            <input class="shoppostform-second" type="text" id="shopneareststation" name="shopneareststation" value="<?php echo $key1['shop_neareststation'] ?>" placeholder="最寄駅を入力">
          </div>
          <div class="clear"></div>
        </div>


        <div class="item third-line">
          <div class="third third-left">
            <label class="label-third" for="shopopeninghours">開店時間</label>
            <select class="shoppostform-third" id="shopopeninghours" name="shopopeninghours">
              <option value="<?php echo $key1['shop_openinghours'] ?>" selected><?php echo $key1['shop_openinghours'] ?></option>
              <?php foreach ($time_array as $key_time1): ?>
                <option value="<?php echo $key_time1 ?>" <?php if( !empty($_POST['shopopeninghours']) && $_POST['shopopeninghours'] == $key_time1 ){ echo 'selected'; } ?>><?php echo $key_time1 ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="third third-right">
            <label class="label-third" for="shopclosingtime">閉店時間</label>
            <select class="shoppostform-third" id="shopclosingtime" name="shopclosingtime">
              <option value="<?php echo $key1['shop_closingtime'] ?>" selected><?php echo $key1['shop_closingtime'] ?></option>
              <?php foreach ($time_array as $key_time2): ?>
                <option value="<?php echo $key_time2 ?>" <?php if( !empty($_POST['shopclosingtime']) && $_POST['shopclosingtime'] == $key_time2 ){ echo 'selected'; } ?>><?php echo $key_time2 ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="clear"></div>
        </div>

        <div class="item fourth-line">
          <label class="label-fourth" for="shopmorninghoursclose">モーニングサービス終了時間</label>
          <select class="shoppostform" id="shopmorninghoursclose" name="shopmorninghoursclose">
              <option value="<?php echo $key1['shop_morninghoursclose'] ?>" selected><?php echo $key1['shop_morninghoursclose'] ?></option>
              <?php foreach ($time_array as $key_time3): ?>
                <option value="<?php echo $key_time3 ?>" <?php if( !empty($_POST['shopmorninghoursclose']) && $_POST['shopmorninghoursclose'] == $key_time3 ){ echo 'selected'; } ?>><?php echo $key_time3 ?></option>
              <?php endforeach; ?>
            </select>
        </div>

        <div class="item fourth-line">
          <label class="label-fourth" for="shopadress">住所</label>
          <input class="shoppostform" type="text" id="shopadress" name="shopadress" value="<?php echo $key1['shop_adress'] ?>" placeholder="住所を入力">
        </div>

        <div class="item fifth-line">
          <div class="fifth fifth-left">
            <label class="label-fifth" for="shoppowersupply">電源（コンセント）</label><br>
            <label><input type="radio" id="shoppowersupply" name="shoppowersupply" value="○" <?php if( !empty($key1['shop_powersupply']) && $key1['shop_powersupply'] === "○" ){ echo 'checked'; } ?>>有り</label>
            <label><input type="radio" id="shoppowersupply" name="shoppowersupply" value="×" <?php if( !empty($key1['shop_powersupply']) && $key1['shop_powersupply'] === "×" ){ echo 'checked'; } ?>>無し</label>
          </div>
          <div class="fifth fifth-right">
            <label class="label-fifth" for="shopfreewifi">Free Wi-Fi</label><br>
            <label><input type="radio" id="shopfreewifi" name="shopfreewifi" value="○" <?php if( !empty($key1['shop_freewi-fi']) && $key1['shop_freewi-fi'] === "○" ){ echo 'checked'; } ?>>有り</label>
            <label><input type="radio" id="shopfreewifi" name="shopfreewifi" value="×" <?php if( !empty($key1['shop_freewi-fi']) && $key1['shop_freewi-fi'] === "×" ){ echo 'checked'; } ?>>無し</label>
          </div>
  
          <label class="shopphoto1" for="shopphoto1">写真１</label>
          <input class="shopphoto1" type="file" name="image1"><br>

          <label class="shopphoto2" for="shopphoto2">写真２</label>
          <input class="shopphoto2" type="file" name="image2"><br>

          <label class="shopphoto3" for="shopphoto3">写真３</label>
          <input class="shopphoto3" type="file" name="image3">

          <div class="clear"></div>
        </div>
        <input name="shopid" type="hidden" value="<?php echo $shopid; ?>">
        <input class="btn shoppost" type="submit" id="shoppost" name="shoppost" value="店情報を更新する">

      <?php endforeach; ?>
      </form>
      
    </div>
  </div>

  

  <footer>
    <div class="container">
      <p>©︎ 2020 Shirotayama</p>
    </div>

  </footer>

  
  <script src="shoppostscript.js?1234">
  </script>
</body>
</html>
