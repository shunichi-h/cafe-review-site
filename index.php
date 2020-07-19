<?php
session_start();
header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

// ログイン状態チェック
if (isset($_SESSION["NAME"])) {
  $loginstatus = "login";
  $login_display = "ようこそ、".$_SESSION["NAME"];
  $btntext = "レビューを投稿する";
  
}else {
  $loginstatus = "logout";
  $login_display = "ログイン";
  $btntext = "ログインまたはユーザー登録してレビューを投稿する";
}


// twitteroauth の読み込み
require "twitteroauth/vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

//Twitterのコンシュマーキー(APIキー)等読み込み
define('TWITTER_API_KEY', 'O05JHoIwhbxWfax7avsdymjdv'); //Twitterデベロッパープラットフォームで作成したアプリケーションのConsumer Key (API Key)
define('TWITTER_API_SECRET', 'V0zFNBrYxUOf971CoSHKJcX7TRRTqK0pBQaGk9C9GCtEqwwYBe');//Twitterデベロッパープラットフォームで作成したアプリケーションのConsumer Secret (API Secret)

//コールバックページのURL
define('CALLBACK_URL', 'https://www.morni-cafe.com/callback.php');//ここで指定するURLを、Twitterデベロッパープラットフォームで作成したアプリケーションのコールバックURLとして登録する必要あり

$access_token = '1234816620784046085-6mMlM0UbtZ29sZc2j9F27EBLprgy7L';
$access_token_secret = 'GjRulfysOKPelpuYs4j5xuawXe2kJaBZPNy5TyEg7QdLR';

//「abraham/twitteroauth」ライブラリのインスタンスを生成し、Twitterからリクエストトークンを取得する
$connection = new TwitterOAuth(TWITTER_API_KEY, TWITTER_API_SECRET, $access_token, $access_token_secret);
$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => CALLBACK_URL));

//リクエストトークンはコールバックページでも利用するためセッションに格納しておく
$_SESSION['oauth_token'] = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

//Twitterの認証画面のURL
$oauthUrl = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

?>

<script type="text/javascript">
  var loginstatus = '<?php echo $loginstatus; ?>';
</script>


<?php
date_default_timezone_set('Asia/Tokyo');

require_once 'php-graph-sdk-5.x/src/Facebook/autoload.php';


$fb = new Facebook\Facebook([
  'app_id' => '1447813592077996',
  'app_secret' => '477b0f13b0f0dae49f1cdb19339961fe',
  'default_graph_version' => 'v2.10',
  ]);

$helper = $fb->getRedirectLoginHelper();

$permissions = ['email']; // Optional permissions
$loginUrl = $helper->getLoginUrl('https://www.morni-cafe.com/callback2.php', $permissions);

$prefecture_array = array(
  '北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県',
  '埼玉県','千葉県','東京都','神奈川県','新潟県','富山県','石川県','福井県','山梨県','長野県',
  '岐阜県','静岡県','愛知県','三重県','滋賀県','京都府','大阪府','兵庫県','奈良県','和歌山県',
  '鳥取県','島根県','岡山県','広島県','山口県','徳島県','香川県','愛媛県','高知県','福岡県',
  '佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県','沖縄県','ハワイ'
);

?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>モーニンカフェ</title>
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="stylesheet.css?12345678901234567890123456">
  <link rel="stylesheet" href="responsive.css?20200302">
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <script　type="text/javascript" src="indexscript.js?1"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
</head>
<body>
  <div id="fb-root"></div>
  <script async defer crossorigin="anonymous" src="https://connect.facebook.net/ja_JP/sdk.js#xfbml=1&version=v7.0" nonce="QoGvMIMY"></script>
  <header>
    <div class="container">
      <div class="header-left">
        <p>モーニンカフェ</p>
      </div>
      <div class="header-right">
        <p id="loginlogout-btn" class="loginusername" onclick="transition(userloginUrl)"><?php echo $login_display; ?></p>
        <p class="loginlogoutbtn-tips">ログアウトする</p>
        <p id="san" class="san">さん</p>
      </div>
      
      <div class="clear"></div>
    </div>
  </header>

  <div class="top-wrapper">
    <div class="container">
      <h1>素敵な朝を探そう。</h1>
    </div>
  </div>

  <div class="searchform-wrapper">
    <div class="container">
     <div class="searchform">
      <form id="shopsearchForm" name="shopsearchForm" action="shoplist.php" method="POST">
        <select class="search prefecture" name="prefecturename">
          <option value="" selected>都道府県</option>
          <?php foreach ($prefecture_array as $key_prefecture): ?>
          　<option value="<?php echo $key_prefecture ?>"><?php echo $key_prefecture ?></option>
          <?php endforeach; ?>
        </select>
        <input class="search keyword" type="text" id="keyword" name="keyword" value="" placeholder="キーワードを入力">
        <input class="search search-btn" type="submit" value="検索">
      </form>
      
      <div class="clear"></div>
     </div>
    </div>
  </div>

  <div class="info-wrapper">
    <div class="container">
      <h2 class="top_text">”モーニンカフェ”はモーニングが楽しめるカフェ専門のレビューサイトです。<br>
「朝早くから営業している。」「モーニングだけの素敵なサービスが楽しめる。」「朝日が気持ちいい席がある。」あなたが素敵な朝を過ごせるお店を、見つけてください。教えてください。</h2>
    </div>
  </div>

  <div class="login-wrapper">
    <div class="container">
      <div class="login-btn-wrapper">
        <a id="loginorreviewbtn" class="btn login login-btn" href="#" onclick="transition(userloginUrl)"><?php echo $btntext; ?></a>
        <div id="snslogin" class="snslogin">
          <p>またはSNSでログインする</p>
          <a class="btn twitter" href="<?php echo $oauthUrl; ?>"><i class="fab fa-twitter"></i>Twitterアカウントでログイン</a>
          <a class="btn facebook"  href="<?PHP echo $loginUrl?>"><i class="fab fa-facebook-f"></i>Facebookアカウントでログイン</a>
        </div>
        <div class="clear"></div>
      </div>
    </div>
  </div>

  <footer>
    <div class="container">
      <p>©︎ 2020 Shirotayama</p>
    </div>

  </footer>

  
  <script src="indexscript.js?1234567">
  </script>
  <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>
</body>
</html>
