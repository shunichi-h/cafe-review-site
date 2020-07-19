<?php

session_start();

require_once 'php-graph-sdk-5.x/src/Facebook/autoload.php';


$fb = new Facebook\Facebook([
  'app_id' => '1447813592077996',
  'app_secret' => '477b0f13b0f0dae49f1cdb19339961fe',
  'default_graph_version' => 'v2.10',
  ]);

$helper = $fb->getRedirectLoginHelper();

try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exception\ResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exception\SDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (! isset($accessToken)) {
  if ($helper->getError()) {
    header('HTTP/1.0 401 Unauthorized');
    echo "Error: " . $helper->getError() . "\n";
    echo "Error Code: " . $helper->getErrorCode() . "\n";
    echo "Error Reason: " . $helper->getErrorReason() . "\n";
    echo "Error Description: " . $helper->getErrorDescription() . "\n";
  } else {
    header('HTTP/1.0 400 Bad Request');
    echo 'Bad request';
  }
  exit;
}

// Logged in
//echo '<h3>Access Token</h3>';
//var_dump($accessToken->getValue());

// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();

// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
//echo '<h3>Metadata</h3>';
//var_dump($tokenMetadata);

// Validation (these will throw FacebookSDKException's when they fail)
$tokenMetadata->validateAppId('1447813592077996');
// If you know the user ID this access token belongs to, you can validate it here
//$tokenMetadata->validateUserId('123');
$tokenMetadata->validateExpiration();

if (! $accessToken->isLongLived()) {
  // Exchanges a short-lived access token for a long-lived one
  try {
    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
  } catch (Facebook\Exception\SDKException $e) {
    echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
    exit;
  }

  //echo '<h3>Long-lived</h3>';
  //var_dump($accessToken->getValue());
}

$_SESSION['fb_access_token'] = (string) $accessToken;

// User is logged in with a long-lived access token.
// You can redirect them to a members-only page.
//header('Location: https://example.com/members.php');



try {
  // Returns a `Facebook\Response` object
  $response = $fb->get('/me?fields=id,name', $accessToken);
} catch(Facebook\Exception\ResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exception\SDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

$user = $response->getGraphUser();

//echo 'Name: ' . $user['name'];

$_SESSION["NAME"] = $user['name'];

// OR
// echo 'Name: ' . $user->getName();

?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>モーニンカフェ</title>
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="stylesheet.css?1">
  <link rel="stylesheet" href="responsive.css?20200302">
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <script　type="text/javascript" src="indexscript.js?1"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
</head>
<body>
  <header>
    <div class="container">
      <div class="header-left">
        <p class="header-btn" onclick="transition(toppage)">モーニンカフェ</p>
      </div>
      <div class="header-right">
       
      </div>
      <div class="clear"></div>
    </div>
  </header>

  
  <div class="logout-wrapper">
    <div class="container">
      <div class="login-btn-wrapper">
        <div class="logoutMessage"></div>
        <p>ログイン成功　<?php echo $_SESSION["NAME"]; ?>さん</p>
        <p><?PHP echo 'Name: ' . $user['name'];?></p>
        <p>ログイン成功　<?php echo $_SESSION["NAME"]; ?>さん</p>
        <a id="loginorreviewbtn" class="logoutbtn" href="index.php">トップページに戻る</a>
      </div>
    </div>
  </div>

  <footer>
    <div class="container">
      <p>©︎ 2020 Shirotayama</p>
    </div>

  </footer>

  
  <script src="indexscript.js?1234">
  </script>
</body>
</html>