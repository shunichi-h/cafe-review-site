
var shoppageUrl = "shoppage.php";
var userloginUrl = "";
var toppage = "index.php";
var logoutUrl = "logout.php";

if(loginstatus == "logout"){
  userloginUrl = "userlogin.php";
  document.getElementById("loginlogout-btn").classList.remove('loginlogout-btn');
  document.getElementById("san").style.display = "none";
}else {
  userloginUrl = "shoppost.php";
  document.getElementById("loginlogout-btn").onclick = logoutcheck;
  document.getElementById("loginlogout-btn").classList.add('loginlogout-btn');
}

function id(id){
  document.getElementById(id);
}

function transition(url){
  location.href = url;
}

function submitshopform(selectedid){
  var formid = "form" + selectedid;
  document.getElementById(formid).submit();
}

function selectedshopsend(selectedid){
  id(selected).value = selectedid;
}
  
function logoutcheck() {
  var select = confirm("ログアウトしますか？");

  if( select ) {
    transition(logoutUrl);
  }
}



