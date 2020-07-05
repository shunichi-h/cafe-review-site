<?php

session_start();

if(isset($_POST["facebookname"])){
  $_SESSION["NAME"] = $_POST["facebookname"];
}

?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>モニカフェ</title>
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
        <div class="logoutMessage"></div>
        <p>ログイン成功　<?php echo $_SESSION["NAME"]; ?>さん</p>
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