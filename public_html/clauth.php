<?php
require 'cl_h.php';
if(!isset($_POST['i'])||!isset($_POST['w']))exit;
session_id("wgRWNoz5PUvt31m".$_POST['i']);
ini_set('session.gc_maxlifetime','3600');
ini_set('session.use_cookies','0');
session_start();
if(isset($_SESSION["wait"])){
	if ($_SESSION["wait"]>time()){
		setcookie('r',3);//Have not work
		exit;
	}else unset($_SESSION["wait"]);
}
$sql_srv=bv7dc_select_db();
if(!($res=mysql_query("SELECT`st`,`upd`,`id`,`grp_id`,`prc_gh`,`mhs`,`cnt_prev`,`cnt_acc`,`pay_prev`,`pay_acc`FROM`usrs`WHERE`id`=".($_POST['i']+0)." AND`pw`='".md5($_POST['w'])."'AND`rl`=1 LIMIT 1"))){
	setcookie('r',4);//Server busy, try again later.
	exit;
}
if ($cl=mysql_fetch_assoc($res)){
	if($cl['st']!=1){
		setcookie('r',5);//Autorization not success.
		exit;
	}
}else{
	setcookie('r',6);//Need registration.
	exit;
}
require 'op_h.php';
?>