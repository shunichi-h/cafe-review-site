<?php
session_start();

// ログイン状態チェック
if (isset($_SESSION["NAME"])) {
  $loginstatus = "login";
  $login_display = "ようこそ、".$_SESSION["NAME"]."さん";
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
define('CALLBACK_URL', 'http://localhost/callback.php');//ここで指定するURLを、Twitterデベロッパープラットフォームで作成したアプリケーションのコールバックURLとして登録する必要あり

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


<script>

  function statusChangeCallback(response) {  // Called with the results from FB.getLoginStatus().
    console.log('statusChangeCallback');
    console.log(response);                   // The current login status of the person.
    if (response.status === 'connected') {   // Logged into your webpage and Facebook.
      testAPI();  
    }
  }


  function checkLoginState() {               // Called when a person is finished with the Login Button.
    FB.getLoginStatus(function(response) {   // See the onlogin handler
      statusChangeCallback(response);
    });
  }


  window.fbAsyncInit = function() {
    FB.init({
      appId      : '1447813592077996',
      cookie     : true,                     // Enable cookies to allow the server to access the session.
      xfbml      : true,                     // Parse social plugins on this webpage.
      version    : 'v7.0'           // Use this Graph API version for this call.
    });


    FB.getLoginStatus(function(response) {   // Called after the JS SDK has been initialized.
      statusChangeCallback(response);        // Returns the login status.
    });
  };
 
  function testAPI() {                      // Testing Graph API after login.  See statusChangeCallback() for when this call is made.
    console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function(response) {
      console.log('Successful login for: ' + response.name);
      
      var uername = response.name;
      document.getElementById('form').value = uername;

      if(document.getElementById('form').value != ""){
        document.getElementById('sessionform').submit();
      }
      
    });
  }

</script>


<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>モニカフェ</title>
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="stylesheet.css?123">
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
        <p>モニカフェ</p>
      </div>
      <div class="header-right">
        <p id="loginlogout-btn" class="login-btn" onclick="transition(userloginUrl)"><?php echo $login_display; ?></p>
        <p class="loginlogoutbtn-tips">ログアウトする</p>
      </div>
      <div class="clear"></div>
    </div>
  </header>

  <div class="top-wrapper">
    <div class="container">
      <h1>モーニングカフェを共有するサイト</h1>
    </div>
  </div>

  <div class="searchform-wrapper">
    <div class="container">
     <div class="searchform">
      <form id="shopsearchForm" name="shopsearchForm" action="shoplist.php" method="POST">
        <select class="search prefecture" name="prefecturename">
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
        <input class="search keyword" type="text" id="keyword" name="keyword" value="" placeholder="キーワードを入力">
        <input class="search search-btn" type="submit" value="検索">
      </form>
      
      <div class="clear"></div>
     </div>
    </div>
  </div>

  <div class="info-wrapper">
    <div class="container">
      <h2>このサイトは、お気に入りのカフェを共有するサイトです。<br>あなたのお気に入りのカフェを共有しましょう。</h2>
    </div>
  </div>

  <div class="login-wrapper">
    <div class="container">
      <div class="login-btn-wrapper">
        <a id="loginorreviewbtn" class="btn login login-btn" href="#" onclick="transition(userloginUrl)"><?php echo $btntext; ?></a>
        <p>またはSNSでログインする</p>
        <div class="snslogin">
          <a class="btn twitter" href="<?php echo $oauthUrl; ?>"><i class="fab fa-twitter"></i>Twitterアカウントでログイン</a>
          <div class="fb-login-button" data-size="large" data-button-type="login_with" data-layout="default" data-auto-logout-link="false" data-use-continue-as="false" data-width="">Facebookアカウントでログイン</div>
          <form id="sessionform" method="post" action="callback2.php">
            <input name="facebookname" id="form" type="hidden">
          </form>
        </div>
        <div class="clear"></div>
      </div>
    </div>
  </div>

  <footer>
    <div class="container">
      <p>Copyright©︎SHUNICHI HATAEKYAMA. All Rights Reserved.</p>
    </div>

  </footer>

  
  <script src="indexscript.js?12345">
  </script>
  <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>
</body>
</html>
