<?php
echo '<script type="text/javascript">c10lg="'.$_SESSION['lg'].'"</script><link rel="stylesheet" type="text/css" href="c10.css"/><script type="text/javascript" src="c10.js"></script>';
function getCBiDT1($name,$t){
	if(isset($_GET[$name])){
		$t=$_GET[$name];
		if(isset($_GET['tz']))$t+=$_GET['tz']*3600;
	}else if(isset($_POST[$name])){
		$t=$_POST[$name];
		if(isset($_POST['tz']))$t+=$_POST['tz']*3600;
	}else $t+=date('Z');
	return'<span class="c10"><script type="text/javascript">c10init("'.$name.'",'.$t.');</script></span>';
}
function getCBiDT($nm=''){return getCBiDT1($nm.'fs',mktime(0,0,0)).'&nbsp;-&nbsp;'.getCBiDT1($nm.'ts',mktime(23,59,59)).'<span><script type="text/javascript">c10tz();</script></span>';}
function cBiDTChk1($nm){return isset($_GET[$nm])&&$_GET[$nm]>=mktime(0,0,0,1,1,($y=date('Y'))-1)&&$_GET[$nm]<mktime(0,0,0,1,0,$y+2)||isset($_POST[$nm])&&$_POST[$nm]>=mktime(0,0,0,1,1,($y=date('Y'))-1)&&$_POST[$nm]<mktime(0,0,0,1,0,$y+2);}
function cBiDTChk($nm=''){return cBiDTChk1($nm.'fs')&&cBiDTChk1($nm.'ts')&&(isset($_GET[$nm.'fs'])?$_GET[$nm.'fs']<=$_GET[$nm.'ts']:$_POST[$nm.'fs']<=$_POST[$nm.'ts']);}
function cBiDTMySql($nm,$s){return date('Y-m-d H:i:'.str_pad($s,2,'0',STR_PAD_LEFT),(isset($_GET[$nm])?$_GET[$nm]:$_POST[$nm])/*+date('Z')*/);}
function cBiDTMySqlFrom($nm=''){return cBiDTMySql($nm.'fs',0);}
function cBiDTMySqlTo($nm=''){return cBiDTMySql($nm.'ts',59);}
function cBiDTUsr1($nm,$t){return date('Y-m-d H:i',(isset($_GET[$nm])?$_GET[$nm]:$_POST[$nm])+$t);}
function cBiDTUsr($nm=''){return cBiDTUsr1($nm.'fs',$t=($uz=isset($_GET['tz'])?$_GET['tz']:$_POST['tz'])*3600-date('Z')).' - '.cBiDTUsr1($nm.'ts',$t).' UTC'.($uz>=0?'+':'').$uz;}
?>