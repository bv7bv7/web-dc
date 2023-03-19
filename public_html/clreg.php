<?php
if(!isset($_POST['w'])||$_POST['w']!="YqqQCyERFveh8hJ"||(isset($_POST['j'])?$_POST['j']:$_POST['s'])=='')exit;
require 'cl_h.php';
setcookie('r',1);//Unknown error
$usr_mhs=isset($_POST['j'])?$_POST['j']:$_POST['s']/5270;
$query=" `usrs` SET `mhs`=".$usr_mhs.",`v0`=".$v0.",`v2`=".$v2.",`v3`=".$v3.",`ua`='".$_SERVER['HTTP_USER_AGENT']."'";
if(isset($_POST["e"]))$query.=",`em`='".trim($_POST["e"])."'";
if(isset($_POST["p"]))$query.=",`wm`='".$_POST["p"]."'";
//m - Temps Max (delim - "z")
if(isset($_POST['m'])){
	$vs=explode('z',$_POST['m'],8);
	$query.=",`max_temp0`=".$vs[0].",`max_temp1`=".$vs[1].",`max_temp2`=".$vs[2].",`max_temp3`=".$vs[3].",`max_temp4`=".$vs[4].",`max_temp5`=".$vs[5].",`max_temp6`=".$vs[6].",`max_temp7`=".$vs[7];
}
//h - Temps Average (delim - "z")
if(isset($_POST['h'])){
	$vs=explode('z',$_POST['h'],8);
	$query.=",`av_temp0`=".$vs[0].",`av_temp1`=".$vs[1].",`av_temp2`=".$vs[2].",`av_temp3`=".$vs[3].",`av_temp4`=".$vs[4].",`av_temp5`=".$vs[5].",`av_temp6`=".$vs[6].",`av_temp7`=".$vs[7];
}
//b - Fans Max (delim - "z")
if(isset($_POST['b'])){
	$vs=explode('z',$_POST['b'],8);
	$query.=",`max_fan0`=".$vs[0].",`max_fan1`=".$vs[1].",`max_fan2`=".$vs[2].",`max_fan3`=".$vs[3].",`max_fan4`=".$vs[4].",`max_fan5`=".$vs[5].",`max_fan6`=".$vs[6].",`max_fan7`=".$vs[7];
}
//c - Fans Average (delim - "z")
if(isset($_POST['c'])){
	$vs=explode('z',$_POST['c'],8);
	$query.=",`av_fan0`=".$vs[0].",`av_fan1`=".$vs[1].",`av_fan2`=".$vs[2].",`av_fan3`=".$vs[3].",`av_fan4`=".$vs[4].",`av_fan5`=".$vs[5].",`av_fan6`=".$vs[6].",`av_fan7`=".$vs[7];
}
//f - Utils Max (delim - "z")
if(isset($_POST['f'])){
	$vs=explode('z',$_POST['f'],8);
	$query.=",`max_util0`=".$vs[0].",`max_util1`=".$vs[1].",`max_util2`=".$vs[2].",`max_util3`=".$vs[3].",`max_util4`=".$vs[4].",`max_util5`=".$vs[5].",`max_util6`=".$vs[6].",`max_util7`=".$vs[7];
}
//g - Utils Average (delim - "z")
if(isset($_POST['g'])){
	$vs=explode('z',$_POST['g'],8);
	$query.=",`av_util0`=".$vs[0].",`av_util1`=".$vs[1].",`av_util2`=".$vs[2].",`av_util3`=".$vs[3].",`av_util4`=".$vs[4].",`av_util5`=".$vs[5].",`av_util6`=".$vs[6].",`av_util7`=".$vs[7];
}
$sql_srv=bv7dc_select_db();
if(isset($_POST['i'])
	&&isset($_POST['a'])
	&&$_POST['i']!=''
	&&$_POST['a']!=''
	&&mysql_num_rows($res=mysql_query("SELECT * FROM `usrs` WHERE `id`=".$_POST['i']." AND `rl`&1 AND `pw`='".($hp=md5($_POST['a']))."' LIMIT 1"))
){
	mysql_query("UPDATE".$query." WHERE `id`=".$_POST['i']." AND `rl`&1 AND `pw`='".$hp."' LIMIT 1");
	if($v2<25){
		setcookie('i',$_POST['i']);
		setcookie('w',$_POST['a']);
	}
}else if(mysql_query("INSERT INTO".$query.",`st`=0,`rl`=1,`pw`='".md5($pw=bv7pw_gen())."'".(($res=mysql_query("SELECT * FROM `def` LIMIT 1"))&&($row=mysql_fetch_assoc($res))?",grp_id=".$row['grp_id']:""))){
	setcookie('i',mysql_insert_id());
	setcookie('w',$pw);
}else exit;
setcookie('r',0);// No error.
?>