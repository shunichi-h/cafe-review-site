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
  $btntext = "レビューを投稿する";
  $edittext = "店情報を編集する";
  
}else {
  $loginstatus = "logout";
  $login_display = "ログイン";
  $btntext = "ログインしてレビューを投稿する";
  $edittext = "ログインして店情報を編集する";
}

if (isset($_POST["shopid"])) {
  $shopid = $_POST["shopid"];
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

  $sql1 = "SELECT * FROM SHOP where id = $shopid";
  $stmt1 = array();
  foreach ($pdo->query($sql1) as $row) {
    array_push($stmt1,$row);
  }

  $sql2 = "SELECT * FROM REVIEW where review_shopid = $shopid";
  $stmt2 = array();
  foreach ($pdo->query($sql2) as $row) {
    array_push($stmt2,$row);
  }

  $sql3 = "SELECT AVG(review_rank) FROM REVIEW where review_shopid = $shopid ";
  $stmt3 = array();
  foreach ($pdo->query($sql3) as $row) {
    array_push($stmt3,$row);
  }

  foreach ($stmt3 as $key3){
    $rank = $key3['AVG(review_rank)'];
  }

  if(empty($stmt2)){
    $emptyMessage = "このお店のレビューはありません。";
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
  <link rel="stylesheet" type="text/css" href="shoppage-stylesheet.css?123456789012345678901234567890123456">
  <link rel="stylesheet" href="responsive.css?20200302">
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/5.4.5/css/swiper.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/5.4.5/css/swiper.min.css">
  <script　type="text/javascript" src="script.js"></script>
  <script src="https://unpkg.com/swiper/swiper-bundle.js"></script>
  <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
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

  <div class="shopphoto-wrapper">
    <div class="container">
      <div class="shop-photo">
        <?php foreach ($stmt1 as $key1): ?>
          <div class="swiper-container mb60">
              <!-- Additional required wrapper -->
              <div class="swiper-wrapper">
                  <!-- Slides -->
                    <?php
                    if(file_exists("./shopimages/{$key1['shop_photo1']}")){
                    print "<div class='swiper-slide'><img class='slide-image' src='./shopimages/{$key1['shop_photo1']}'></div>";
                    }else{
                      print "<div class='swiper-slide'><img class='slide-image' src='./shopimages/noimage.jpg'></div>";
                    }?>

                    <?php
                    if(file_exists("./shopimages/{$key1['shop_photo2']}")){
                    print "<div class='swiper-slide'><img class='slide-image' src='./shopimages/{$key1['shop_photo2']}'></div>";
                    }?>

                    <?php
                    if(file_exists("./shopimages/{$key1['shop_photo3']}")){
                    print "<div class='swiper-slide'><img class='slide-image' src='./shopimages/{$key1['shop_photo3']}'></div>";
                    }?>
              </div>
              <!-- If we need pagination -->
              <div class="swiper-pagination"></div>
              
              <!-- If we need navigation buttons -->
              <div class="swiper-button-prev"></div>
              <div class="swiper-button-next"></div>
          </div>
        <?php endforeach; ?>
      </div>
      
    </div>
  </div>

  <div class="shopinfomation-wrapper">
    <div class="container">
      <div class="shop-infomation">
        <?php foreach ($stmt1 as $key1): ?>
          <h2 class="shop-name"><?php echo $key1['shop_name'] ?></h2>
          <div class="star-rating">
              <div class="star-rating-front" style="width: <?PHP echo $rank*20; ?>%">★★★★★</div>
              <div class="star-rating-back">★★★★★</div>
          </div>
          <div class="review-rank"><?php echo round($rank,1); ?></div><br>
          <p class="first-line"><?php echo $key1['shop_prefecture'] ?></p>
          <p class="first-line">最寄駅　<?php echo $key1['shop_neareststation'] ?></p>
          <p class="first-line shop-openinghours">営業時間　<?php echo $key1['shop_openinghours'] ?> 〜 <?php echo $key1['shop_closingtime'] ?></p>
          <p class="second-line">モーニングサービス終了：　<?php echo $key1['shop_openinghours'] ?></p>
          <p class="second-line">住所　<?php echo $key1['shop_adress'] ?></p>
          <p class="third-line">電源（コンセント）　<?php echo $key1['shop_powersupply'] ?></p>
          <p class="third-line">Free Wi-Fi　<?php echo $key1['shop_freewi-fi'] ?></p>
        <?php endforeach; ?>
        <form name="shopupdateform" action="shopupdate.php" method="POST">
        <input name="shopid" type="hidden" value="<?php echo $shopid; ?>">
        <input type="submit" value="<?php echo $edittext; ?>">
      </form>
        <div class="clear"></div>
        
      </div>

    </div>
  </div>

  <div class="shopreviewlist-wrapper">
    <div class="container">
      <div class="heading">
        <h3>レビュー</h3>
      </div>

      <?php foreach ($stmt2 as $key2): ?>
      <div class="review">
      <h3 class="review-title"><?php echo $key2['review_titlle'] ?></h3>
        <div class="review-photo">
          
            <div class="swiper-container">
                <!-- Additional required wrapper -->
                <div class="swiper-wrapper">
                    <!-- Slides -->
                      <?php
                      if(file_exists("./reviewimages/{$key2['review_photo1']}")){
                      print "<div class='swiper-slide'><img class='slide-image' src='./reviewimages/{$key2['review_photo1']}'></div>";
                      }else{
                        print "<div class='swiper-slide'><img class='slide-image' src='./shopimages/noimage.jpg'></div>";
                      }?>

                      <?php
                      if(file_exists("./reviewimages/{$key2['review_photo2']}")){
                      print "<div class='swiper-slide'><img class='slide-image' src='./reviewimages/{$key2['review_photo2']}'></div>";
                      }?>

                      <?php
                      if(file_exists("./reviewimages/{$key2['review_photo3']}")){
                      print "<div class='swiper-slide'><img class='slide-image' src='./reviewimages/{$key2['review_photo3']}'></div>";
                      }?>
                </div>
                <!-- If we need pagination -->
                <div class="swiper-pagination"></div>
                
                <!-- If we need navigation buttons -->
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
          





          
        </div>
        <div class="review-text">
          
          <p class="review-user"><?php echo $key2['review_date'] ?></p>
          <p class="review-user"><?php echo $key2['review_user'] ?>さん</p>
          <div class="star-rating">
                <div class="star-rating-front" style="width: <?PHP echo $key2['review_rank']*20; ?>%">★★★★★</div>
                <div class="star-rating-back">★★★★★</div>
          </div>
          <p class="review-body"><?php echo $key2['review_text'] ?></p>
        </div>
        <div class="clear"></div>
      </div>
      <?php endforeach; ?>
      
      <div class="heading">
        <p><?php echo $emptyMessage; ?></p>
      </div>
      

    </div>
  </div>

  <div class="reviewbtn-wrapper">
    <div class="container">
      <form name="reviewbtnform" action="reviewpost.php" method="POST">
        <input name="shopid" type="hidden" value="<?php echo $shopid; ?>">
        <input class="review-btn" type="submit" value="<?php echo $btntext; ?>">
      </form>
      
      
    </div>
  </div>

  

  <footer>
    <div class="container">
      <p>©︎ 2020 Shirotayama</p>
    </div>

  </footer>

  <script src="shoppagescript.js?1234567"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.1/js/swiper.min.js"></script>
  <script>
		var mySwiper = new Swiper ('.swiper-container', {
			effect: "slide",
	    	loop: true,
		    pagination: '.swiper-pagination',
		    nextButton: '.swiper-button-next',
		    prevButton: '.swiper-button-prev',
	    })
	</script>
</body>
</html>
