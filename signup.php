<?php
// セッション開始
session_start();

$db['host'] = "mysql10048.xserver.jp";  // DBサーバのURL
$db['user'] = "xs836976_user";  // ユーザー名
$db['pass'] = "shun0505";  // ユーザー名のパスワード
$db['dbname'] = "xs836976_mornicafedb";  // データベース名

// エラーメッセージ、登録完了メッセージの初期化
$errorMessage = "";
$signUpMessage = "";

// ログインボタンが押された場合
if (isset($_POST["signUp"])) {
    // 1. ユーザIDの入力チェック
    if (empty($_POST["username"])) {  // 値が空のとき
        $errorMessage = 'ユーザー名が未入力です。';
    } else if (mb_strlen($_POST["username"], 'UTF-8') > 15) {
      $errorMessage = 'ユーザー名は15文字以内で入力してください。';
    } else if (empty($_POST["usermailadress"])) {
      $errorMessage = 'メールアドレスが未入力です。';
    } else if(!preg_match('/@/',$_POST["usermailadress"])){
      $errorMessage = 'メールアドレスを正しく入力してください。';
    } else if (empty($_POST["userpassword"])) {
        $errorMessage = 'パスワードが未入力です。';
    } else if (mb_strlen($_POST["userpassword"], 'UTF-8') < 6 || mb_strlen($_POST["userpassword"], 'UTF-8') > 20) {
      $errorMessage = 'パスワードは6〜20文字で入力してください。';
    } else if (empty($_POST["userpassword2"])) {
        $errorMessage = 'パスワードが未入力です。';
    }

    if (!empty($_POST["username"]) && !empty($_POST["usermailadress"]) && !empty($_POST["userpassword"]) && !empty($_POST["userpassword2"]) && $_POST["userpassword"] === $_POST["userpassword2"] && mb_strlen($_POST["userpassword"], 'UTF-8') > 6 && mb_strlen($_POST["userpassword"], 'UTF-8') < 20) {
        // 入力したユーザ名とメールアドレスとパスワードを格納
        $username = $_POST["username"];
        $usermailadress = $_POST["usermailadress"];
        $userpassword = $_POST["userpassword"];
    
        // 2. ユーザ名とメールアドレスとパスワードが入力されていたら認証する
        $dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);

        // 3. エラー処理
        try {
          $pdo = new PDO( 'mysql:host=mysql10048.xserver.jp;dbname=xs836976_mornicafedb;charset=utf8','xs836976_user', 'shun0505');

            $stmt = $pdo->prepare("INSERT INTO USER(`user_name`, `user_mailadress`, `user_password`) VALUES (?, ?, ?)");

            $stmt->execute(array($username, $usermailadress, password_hash($userpassword, PASSWORD_DEFAULT)));  // パスワードのハッシュ化を行う（今回は文字列のみなのでbindValue(変数の内容が変わらない)を使用せず、直接excuteに渡しても問題ない）
            $userid = $pdo->lastinsertid();  // 登録した(DB側でauto_incrementした)IDを$useridに入れる

            $signUpMessage = '登録が完了しました。あなたのユーザー名は '. $username. ' です。パスワードは '. $userpassword. ' です。';  // ログイン時に使用するIDとパスワード
        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー' + $e->getMessage();
            // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
            echo $e->getMessage();
        }
    } else if($_POST["userpassword"] != $_POST["userpassword2"]) {
        $errorMessage = 'パスワードに誤りがあります。';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>モニカフェ</title>
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="signup-stylesheet.css?12345">
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
        
      </div>
      <div class="clear"></div>
    </div>
  </header>

  <div class="loginform-wrapper">
    <div class="container">
      <h2 class="heading">新規登録</h2>
      <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
      <div><font color="#0000ff"><?php echo htmlspecialchars($signUpMessage, ENT_QUOTES); ?></font></div>

      <form id="signupForm" name="signupForm" action="" method="POST">
        <fieldset style="border: none;">
          <label for="username">ユーザー名（15文字以内）</label>
          <input class="loginform usernameform" type="text" id="username" name="username" placeholder="ユーザー名を入力" value="<?php if (!empty($_POST["username"])) {echo htmlspecialchars($_POST["username"], ENT_QUOTES);} ?>">
      
          <label for="usermailadress">メールアドレス</label>
          <input class="loginform mailadressform" type="text" id="usermailadress" name="usermailadress" placeholder="メールアドレスを入力">
      
          <label for="userpassword">パスワード（半角6~20文字）</label>
          <input class="loginform passwordform" type="password" id="userpassword" name="userpassword" placeholder="パスワードを入力">

          <label for="userpassword2">パスワード（確認用）</label>
          <input class="loginform password2form" type="password" id="userpassword2" name="userpassword2" placeholder="パスワードを入力(確認用)">
          <input class="btn login" type="submit" id="signUp" name="signUp" value="登録">
        </fieldset>
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
