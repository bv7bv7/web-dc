<?php
if(substr($_SERVER['HTTP_USER_AGENT'],0,8)!='DCClient')exit;
if(substr($_SERVER['HTTP_USER_AGENT'],-1)=='O'){
	$v0=$_SERVER['HTTP_USER_AGENT']{8}=='0'?2:3;
	$v2=24;
	$v3=0;
}else{
	$v0=$_SERVER['HTTP_USER_AGENT']{8};
	$v2=substr($_SERVER['HTTP_USER_AGENT'],-4,2);
	$v3=substr($_SERVER['HTTP_USER_AGENT'],-2);
}
require 'def_ver.php';
if($v0<minv0)exit;
if($v2<maxv2){
	if(($v3=='64'||$v3=='32')&&$v2<maxv2)require("def_upd.php");//Server of updates
	if($v2<minv2){
		setcookie('r',2);//the client is outdated
		exit;
	}
}
//d - Version of a List of Servers on the DCClient
if(isset($_POST['d'])&&$_POST['d']<srvv){
	require 'def_srvs.php';
	foreach($srvs as $key=>$value){
		setcookie("ba".$key,$value[0]); // ba[x] - Type of Server № x
		setcookie("bb".$key,$value[1]); // bb[x] - Name of Server № x
		setcookie("bc".$key,$value[2]); // bc[x] - URL of Server № x
	}
	setcookie("c",srvv);//c - Version of List of Servers
}
require 'global.php';
?>