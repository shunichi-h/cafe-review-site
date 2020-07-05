<?php
// セッション開始
session_start();


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



$db['host'] = "mysql10048.xserver.jp";  // DBサーバのURL
$db['user'] = "xs836976_user";  // ユーザー名
$db['pass'] = "shun0505";  // ユーザー名のパスワード
$db['dbname'] = "xs836976_mornicafedb";  // データベース名

// エラーメッセージの初期化
$errorMessage = "";

// ログインボタンが押された場合
if (isset($_POST["login"])) {
    // 1. メールアドレスの入力チェック
    if (empty($_POST["usermailadress"])) {  // emptyは値が空のとき
        $errorMessage = 'メールアドレスが未入力です。';
    } else if(!preg_match('/@/',$_POST["usermailadress"])){
      $errorMessage = 'メールアドレスを正しく入力してください。';
    } else if (empty($_POST["userpassword"])) {
        $errorMessage = 'パスワードが未入力です。';
    }

    if (!empty($_POST["usermailadress"]) && !empty($_POST["userpassword"])) {
        // 入力したメールアドレスを格納
        $usermailadress = $_POST["usermailadress"];

        // 2. ユーザIDとパスワードが入力されていたら認証する
        $dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);

        // 3. エラー処理
        try {
          $pdo = new PDO( 'mysql:host=mysql10048.xserver.jp;dbname=xs836976_mornicafedb;charset=utf8','xs836976_user', 'shun0505');

            $stmt = $pdo->prepare('SELECT * FROM USER WHERE user_mailadress = ?');
            $stmt->execute(array($usermailadress));

            $userpassword = $_POST["userpassword"];

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (password_verify($userpassword, $row['user_password'])) {
                    session_regenerate_id(true);

                    // 入力したIDのユーザー名を取得
                    $id = $row['id'];
                    $sql = "SELECT * FROM USER WHERE id = $id";  //入力したIDからユーザー名を取得
                    $stmt = $pdo->query($sql);
                    foreach ($stmt as $row) {
                        $row['user_name'];  // ユーザー名
                    }
                    $_SESSION["NAME"] = $row['user_name'];
                    header("Location: index.php");  // メイン画面へ遷移
                    exit();  // 処理終了
                } else {
                    // 認証失敗
                    $errorMessage = 'メールアドレスまたはパスワードに誤りがあります。';
                }
            } else {
                // 4. 認証成功なら、セッションIDを新規に発行する
                // 該当データなし
                $errorMessage = 'メールアドレスまたはパスワードに誤りがあります。';
            }
        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー';
            //$errorMessage = $sql;
            // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
            // echo $e->getMessage();
        }
    }
}
?>

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
  <link rel="stylesheet" type="text/css" href="userlogin-stylesheet.css?1234567">
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <script　type="text/javascript" src="script.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
</head>
<body>
  <div id="fb-root"></div>
  <script async defer crossorigin="anonymous" src="https://connect.facebook.net/ja_JP/sdk.js#xfbml=1&version=v7.0" nonce="QoGvMIMY"></script>

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

  <div class="loginform-wrapper">
    <div class="container">
      <h2 class="heading">ログイン</h2>
      <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
      <form id="loginForm" name="loginForm" action="" method="POST">
        <fieldset style="border: none;">
          <label for="usermailadress">メールアドレス</label>
          <input class="loginform mailadressform" type="text" id="usermailadress" name="usermailadress" placeholder="メールアドレスを入力">
          <label for="userpassword">パスワード</label>
          <input class="loginform passwordform" type="password" id="userpassword" name="userpassword" placeholder="パスワードを入力">
          <input class="btn login" type="submit" id="login" name="login" value="ログイン">
        </fieldset>
      </form>
      
      <a class="signup" href="signup.php">新規ユーザー登録はこちら</a>
      <p>またはSNSでログイン</p>
      <a class="btn twitter" href="<?php echo $oauthUrl; ?>"><i class="fab fa-twitter"></i>Twitterアカウントでログイン</a>
      <div class="fb-login-button" data-size="large" data-button-type="login_with" data-layout="default" data-auto-logout-link="false" data-use-continue-as="false" data-width="">Facebookアカウントでログイン</div>
        <form id="sessionform" method="post" action="callback2.php">
          <input name="facebookname" id="form" type="hidden">
        </form>

    </div>
  </div>

  

  <footer>
    <div class="container">
      <p>Copyright©︎SHUNICHI HATAEKYAMA. All Rights Reserved.</p>
    </div>

  </footer>

  
  <script src="userloginscript.js?123">
  </script>
</body>
</html>
