<?php
require "auth.php";
if(!($_SESSION["rl"]&14))exit(__FILE__.__LINE__);
$sql_srv=bv7dc_select_db();
if(!($res=mysql_query("SELECT ROUND(SUM(`mhs`)/1000)AS`ghs`,COUNT(*)AS`ct`FROM`usrs`WHERE TIMESTAMPDIFF(SECOND,`usrs`.`time`,NOW())<=".cl_timeout))
	||!($row=mysql_fetch_assoc($res))
	||!($res3=mysql_query("SELECT ROUND(SUM(`mhs`)/1000)AS`ghs`FROM`usrs`WHERE TIMESTAMPDIFF(SECOND,`usrs`.`time_ia`,NOW())<=".cl_timeout))
	||!($row3=mysql_fetch_assoc($res3))
	||!($res2=mysql_query("SELECT ROUND(SUM((`cnt`-`progress`)/".sl3_mh."/2000))AS`ghq`,SUM(`minusrs`)AS`ctu`,MIN(`minusrs`)AS`minctu`FROM`jobs`WHERE`st`=1 AND`res`='' AND`cnt`>`progress`"))
	||!($row2=mysql_fetch_assoc($res2))
)exit(__FILE__.__LINE__);
$ghs=($row2['minctu']>0&&$row['ct']>$row2['ctu'])?$row['ghs']/$row['ct']*$row2['ctu']:$row['ghs'];
echo "<!DOCTYPE html><html><head><title>Info</title><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/><link rel=\"stylesheet\" type=\"text/css\" href=\"ctrl1.css\"/>"
	."</head><body><table border=\"1\"><tbody>"
	."<tr><td>Maximum available speed (GH/s)</td><td align=\"right\">".number_format($row3['ghs'],0,"."," ")."</td></tr>"
	."<tr><td>Current speed (GH/s)</td><td align=\"right\">".number_format($ghs,0,"."," ")."</td></tr>"
	."<tr><td>Current queue (GH)</td><td align=\"right\">".number_format($row2['ghq'],0,"."," ")."</td></tr>";
if($row['ghs']){
	$m=floor($row2['ghq']/$ghs/60);
	echo "<tr><td>Current queue (hh:mm)</td><td align=\"right\">".substr("0".floor($m/60),-2).":".substr("0".($m%60),-2)."</td></tr>";
}
echo "<tr></tr></tbody></table></body></html>";
mysql_close($sql_srv);
?>