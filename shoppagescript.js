
var shoppageUrl = "shoppage.php";
var userloginUrl = "";
var toppage = "index.php";

if(loginstatus == "logout"){
  userloginUrl = "userlogin.php";
  document.reviewbtnform.action = "userlogin.php";
}else {
  userloginUrl = "shoppost.php";
}


function transition(url){
  location.href = url;
}




