<?php
if($_SERVER['HTTP_USER_AGENT']==""){
    header("HTTP/1.1 404 Not Found");
    exit;
}
require'global.php';
if($_SERVER['HTTP_USER_AGENT']=='DC_PHP_API_1.0'){
	if(!isset($_POST['id'])||!isset($_POST['pw']))exit;
	session_id("wgRWNoz5PUvt31m".($_POST['id']+0));
	ini_set('session.use_cookies','0');
}
ini_set('session.gc_maxlifetime','3600');
session_start();
if(!isset($_SESSION['lg']))$_SESSION['lg']=strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2));
if(!isset($_SESSION['id'])||!isset($_SESSION['rl'])||!isset($_SESSION["mu"])||!isset($_SESSION['pr'])||!isset($_SESSION['prf'])||!isset($_SESSION["muf"])||!isset($_SESSION["em"])){
	if(isset($_POST['id'])&&isset($_POST['pw'])){
		$sql_srv=bv7dc_select_db();
		if($us=mysql_fetch_assoc($res=mysql_query("SELECT * FROM`usrs`WHERE`st`=1 AND`id`=".($_POST['id']+0)." AND`pw`='".md5($_POST['pw'])."'LIMIT 1"))){
			$_SESSION["rl"]=$us["rl"];
			$_SESSION["id"]=$us["id"];
			$_SESSION["mu"]=$us["minusrs"];
			$_SESSION["pr"]=$us["priority"];
			$_SESSION["prf"]=$us["prior_f"];
			$_SESSION["muf"]=$us["mu_f"];
			$_SESSION["em"]=$us["em"];
		}
		mysql_close($sql_srv);
	}
	if(!isset($_SESSION["id"]))exit("<!DOCTYPE html><html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/><title>Login</title></head><body>"
		."<form action=\"".$_SERVER['REQUEST_URI']."\" method=\"post\"><table><caption>".bv7lg("Authentication","Аутентификация")."</caption><tbody>"
		."<tr><td align=\"right\">".bv7lg("Login","Имя")."</td><td><input type=\"text\" name=\"id\" value=\"".$_POST['id']."\"/></td></tr>"
		."<tr><td align=\"right\">".bv7lg("Password","Пароль")."</td><td><input type=\"password\" name=\"pw\" value=\"".$_POST['pw']."\"/></td></tr>"
		."</tbody></table><input type=\"submit\"/><br/><a href=\"reg.php\">".bv7lg("Registration","Регистрация")."</a></form></body></html>"
	);
}
require "op_h.php";
?>