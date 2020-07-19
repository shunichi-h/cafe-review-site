
var shoppageUrl = "shoppage.php";
var userloginUrl = "";
var toppage = "index.php";
var logoutUrl = "logout.php";

if(loginstatus == "logout"){
  userloginUrl = "userlogin.php";
  document.reviewbtnform.action = "userlogin.php";
  document.shopupdateform.action = "userlogin.php";
  document.getElementById("loginlogout-btn").classList.remove('loginlogout-btn');
  document.getElementById("san").style.display = "none";
}else {
  userloginUrl = "shoppost.php";
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




