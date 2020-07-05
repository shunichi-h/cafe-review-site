<?php
// twitteroauth の読み込み
require "twitteroauth/vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

//Twitterのコンシュマーキー(APIキー)等読み込み
define('TWITTER_API_KEY', 'O05JHoIwhbxWfax7avsdymjdv'); //Consumer Key (API Key)
define('TWITTER_API_SECRET', 'V0zFNBrYxUOf971CoSHKJcX7TRRTqK0pBQaGk9C9GCtEqwwYBe');//Consumer Secret (API Secret)


session_start();

//リクエストトークンを使い、アクセストークンを取得する
$twitter_connect = new TwitterOAuth(TWITTER_API_KEY, TWITTER_API_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
$access_token = $twitter_connect->oauth('oauth/access_token', array('oauth_verifier' => $_GET['oauth_verifier'], 'oauth_token'=> $_GET['oauth_token']));


//アクセストークンからユーザの情報を取得する
$user_connect = new TwitterOAuth(TWITTER_API_KEY, TWITTER_API_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
$user_info = $user_connect->get('account/verify_credentials');//アカウントの有効性を確認するためのエンドポイント
?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>モニカフェ</title>
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="logout-stylesheet.css?12">
  <link rel="stylesheet" href="responsive.css?20200302">
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <script　type="text/javascript" src="indexscript.js?1"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
</head>
<body>
  <header>
    <div class="container">
      <div class="header-left">
        <p class="header-btn" onclick="transition(toppage)">モニカフェ</p>
      </div>
      <div class="header-right">
       
      </div>
      <div class="clear"></div>
    </div>
  </header>

  
  <div class="logout-wrapper">
    <div class="container">
      <div class="login-btn-wrapper">
        <div class="logoutMessage">
        <?php
          //ユーザ情報が取得できればcomplete.html、それ以外はerror.htmlに移動する
          if(isset($user_info->id_str)){
		      ?>
			    <?PHP $_SESSION["NAME"] = $user_info->name;?>
              <p>ログイン成功　<?php echo $user_info->name; ?>さん</p>
              
            <?php }else{ ?>
              <p>ログイン失敗</p>	
          <?php } ?>
        </div>
        <a id="loginorreviewbtn" class="logoutbtn" href="index.php">トップページに戻る</a>
      </div>
      
    </div>
  </div>

  <footer>
    <div class="container">
      <p>Copyright©︎SHUNICHI HATAEKYAMA. All Rights Reserved.</p>
    </div>

  </footer>

  
  <script src="indexscript.js?1234">
  </script>
</body>
</html>