
var userloginUrl;
var shoplistUrl;
var toppage = "index.php";
var logoutUrl = "logout.php";

if(loginstatus == "logout"){
  userloginUrl = "userlogin.php";
  document.getElementById("loginlogout-btn").classList.remove('loginlogout-btn');
  document.getElementById("san").style.display = "none";
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



var today = new Date();
today.setDate(today.getDate());
var yyyy = today.getFullYear();
var mm = ("0"+(today.getMonth()+1)).slice(-2);
var dd = ("0"+today.getDate()).slice(-2);
document.getElementById("reviewdate").value=yyyy+'-'+mm+'-'+dd;


