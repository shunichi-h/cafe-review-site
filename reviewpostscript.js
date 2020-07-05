
var userloginUrl;
var shoplistUrl;
var toppage = "index.php";

if(loginstatus == "logout"){
  userloginUrl = "userlogin.php";
}else {
  userloginUrl = "shoplist.php";
}

function transition(url){
  location.href = url;
}


var today = new Date();
today.setDate(today.getDate());
var yyyy = today.getFullYear();
var mm = ("0"+(today.getMonth()+1)).slice(-2);
var dd = ("0"+today.getDate()).slice(-2);
document.getElementById("reviewdate").value=yyyy+'-'+mm+'-'+dd;


