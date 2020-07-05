
var userloginUrl;
var shoplistUrl;

var logoutUrl = "logout.php";

if(loginstatus == "logout"){
  userloginUrl = "userlogin.php";
  document.getElementById("loginlogout-btn").classList.remove('loginlogout-btn');
}else {
  userloginUrl = "shoplist.php";
  document.getElementById("loginlogout-btn").onclick = logoutcheck;
  document.getElementById("loginlogout-btn").classList.add('loginlogout-btn');
}

function transition(url){
  location.href = url;
}

function logoutcheck() {
  var select = confirm("ログアウトしますか？");

  if( select ) {
    transition(logoutUrl);
  }
}




