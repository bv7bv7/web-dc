<?php
require"auth.php";
if(!($_SESSION["rl"]&28)||!isset($_GET['k']))exit;
$sql_srv=bv7dc_select_db();
if(!($res=mysql_query("SELECT * FROM `usrs` WHERE `id`=".$_GET["k"].($_SESSION["rl"]&12?"":" AND `pusr_id`=".$_SESSION["id"])." LIMIT 1"))||!($cl=mysql_fetch_assoc($res))){
	mysql_close($sql_srv);
	exit;
}
define('t_z',4*3600);
require"chart2.php";
require"c8.php";
$c8=new C8(mktime(date("G")+1,0,0),12*60*60,'date("Y-m-d H:i",$v+'.(4*3600).')');
$query="SELECT`cnt`,`st`,`time`FROM`subjobs`USE INDEX(`usr_time`)WHERE`usr_id`=".($_GET['k']+0)." AND`time`BETWEEN'".date("Y-m-d H:i:s",$c8->prev-3600)."'AND'".date('Y-m-d H:i:s',$c8->cur)."'ORDER BY`time`LIMIT 100";
if(!($res=mysql_query($query)))exit;
$chGroup=new ChGroupX('strtotime($row["time"])','(UTC+4/MSK)<br />'.date('d/m/Y',$c8->prev+t_z),$c8->prev,$c8->cur,60*15,'date("H:i",$v+'.t_z.')');
$chGroup->charts=array(new Chart('$row["st"]==3&&$prevRow!==NULL?$row["cnt"]/'.(sl3_mh*1000).'/($prevRow["time"]==$row["time"]?0.5:strtotime($row["time"])-strtotime($prevRow["time"])):NULL',"gh/s",0.01));
//$chGroup->charts=new array(new Chart('1',"gh/s",0.01));
$chGroup->Parse('Email: <a href="/e1.php?k='.($_GET['k']+0).'">'.$cl['em'].'</a>&nbsp;&nbsp;ID: '.($_GET['k']+0));
mysql_close($sql_srv);
$c8->Parse();
echo"</body></html>";
?>