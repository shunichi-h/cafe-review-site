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
  $btntext = "店情報を投稿する";
  
}else {
  $loginstatus = "logout";
  $login_display = "ログイン";
  $btntext = "ログインして店情報を投稿する";
}

$prefecturename = "";
$keyword = "";


if (!empty($_POST["prefecturename"])) {
  $prefecturename = $_POST["prefecturename"];
  $searchprefecture = $_POST["prefecturename"];
}else {
  $prefecturename = "全国";
  $searchprefecture = '';
}

if (!empty($_POST["keyword"])) {
  $keyword = "「".$_POST["keyword"]."」に該当する";
  $searchkeyword = htmlspecialchars($_POST["keyword"]);
}else {
  $keyword = "全ての";
  $searchkeyword = '';
}

$db['host'] = "mysql10048.xserver.jp";  // DBサーバのURL
$db['user'] = "xs836976_user";  // ユーザー名
$db['pass'] = "shun0505";  // ユーザー名のパスワード
$db['dbname'] = "xs836976_mornicafedb";  // データベース名

// エラーメッセージ、登録完了メッセージの初期化
$errorMessage = "";
$reviewPostMessage = "";
$emptyMessage = "";

$dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);

try {
  $pdo = new PDO( 'mysql:host=mysql10048.xserver.jp;dbname=xs836976_mornicafedb;charset=utf8','xs836976_user', 'shun0505');

  $sql = "SELECT * FROM SHOP where shop_prefecture LIKE '%$searchprefecture%' AND shop_name LIKE '%$searchkeyword%' ";
  $stmt = array();
  foreach ($pdo->query($sql) as $row) {
    array_push($stmt,$row);
  }

  if(empty($stmt)){
    $emptyMessage = "該当するお店は登録されていません。";
  }

} catch (PDOException $e) {
  $errorMessage = 'データベースエラー' + $e->getMessage();
  // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
  echo $e->getMessage();
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
  <link rel="stylesheet" type="text/css" href="shoplist-stylesheet.css?123456789012345">
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

  <div class="shoplist-wrapper">
    <div class="container">
      <div>
        <h2><?php echo $prefecturename."の".$keyword."お店"; ?></h2>
      </div>

      <?php foreach ($stmt as $key): ?>
        <div class="shop" onclick="submitshopform(<?php echo $key['id'] ?>)">
          <h2 class="shop-heading"><?php echo $key['shop_name'] ?></h2>
          <div class="shop-photo">
            <img class="shop-image" src="
              <?php
                if(file_exists("./shopimages/{$key['shop_photo1']}")){
                print "./shopimages/{$key['shop_photo1']}" ;
                }else{
                print "./shopimages/noimage.jpg" ;
                }
              ?>">
          </div>
          <div class="shop-text">
            
            <p class="shop-rank">
            
            <?PHP 
                  $review_shop_id = $key['id'];
                  try {
                    $pdo = new PDO( 'mysql:host=mysql10048.xserver.jp;dbname=xs836976_mornicafedb;charset=utf8','xs836976_user', 'shun0505');

                    $sql3 = "SELECT AVG(review_rank) FROM REVIEW where review_shopid = '$review_shop_id' ";
                    $stmt3 = array();
                    foreach ($pdo->query($sql3) as $row) {
                      array_push($stmt3,$row);
                    }

                    if(empty($stmt3)){
                      $emptyMessage = "該当するお店は登録されていません。";
                    }

                  } catch (PDOException $e) {
                    $errorMessage = 'データベースエラー' + $e->getMessage();
                    // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
                    echo $e->getMessage();
                  }

                  foreach ($stmt3 as $key3){
                    $rank = $key3['AVG(review_rank)'];
                  }

                ?>
              <div class="star-rating">
                <div class="star-rating-front" style="width: <?PHP echo $rank*20; ?>%">★★★★★</div>
                <div class="star-rating-back">★★★★★</div>
              </div>
              <p>評価：<?PHP echo round($rank,1); ?></p>
            </p>
            <p class="shop-prefecture"><?php echo $key['shop_prefecture'] ?></p>
            <p class="review-number"><i class="fas fa-comments"></i>
              レビュー：
                <?PHP 
                  $review_shop_id = $key['id'];
                  try {
                    $pdo = new PDO( 'mysql:host=mysql10048.xserver.jp;dbname=xs836976_mornicafedb;charset=utf8','xs836976_user', 'shun0505');

                    $sql2 = "SELECT COUNT(*) FROM REVIEW where review_shopid = '$review_shop_id' ";
                    $stmt2 = array();
                    foreach ($pdo->query($sql2) as $row) {
                      array_push($stmt2,$row);
                    }

                    if(empty($stmt2)){
                      $emptyMessage = "該当するお店は登録されていません。";
                    }

                  } catch (PDOException $e) {
                    $errorMessage = 'データベースエラー' + $e->getMessage();
                    // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
                    echo $e->getMessage();
                  }

                  foreach ($stmt2 as $key2){
                    echo $key2['COUNT(*)'];
                  }

                ?>
              件</p>
          </div>
          <form id="form<?php echo $key['id'] ?>" name="shoppageform" action="shoppage.php" method="POST">
            <input type="hidden" name="shopid" value="<?php echo $key['id'] ?>">
          </form>
          <div class="clear"></div>
        </div>
      <?php endforeach; ?>

      <p><?php echo $emptyMessage; ?></p>

    </div>
  </div>

  <div class="shoppost-wrapper">
    <div class="container">

      <p class="shoppost" onclick="transition(userloginUrl)"><?php echo $btntext; ?></p>
      

    </div>
  </div>

  

  <footer>
    <div class="container">
      <p>©︎ 2020 Shirotayama</p>
    </div>

  </footer>

  
  <script src="shoplistscript.js?12345">
  </script>
</body>
</html>
