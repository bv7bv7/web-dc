<?php
require "auth.php";
if(!($_SESSION["rl"]&28))exit(__FILE__.__LINE__);
require "def_ver.php";
$sql_srv=bv7dc_select_db();
if(!($res=mysql_query("SELECT `id`,`job_id`,`em`,`comment`,`ua`,`mhs`,`st`
		,TIMESTAMPDIFF(SECOND,`time`,NOW()) as `timeout`
		,TIMESTAMPDIFF(SECOND,`time`,NOW())>".cl_timeout." AS `discon`
		,IF(TIMESTAMPDIFF(SECOND,`time`,NOW())>".cl_timeout.",TIMESTAMPDIFF(SECOND,`time`,NOW()),0) AS `ord1`
		,ROUND(`pay_acc`,6) AS `pay_acc`
		,ROUND(IF(TIMESTAMPDIFF(HOUR,`timepay`,NOW())<=24,`pay_prev`,0),6) AS `pay_prev`"
		.",mhs0,mhs1,clreq_time
		,CONCAT(IF(max_temp0,CONCAT('<span class=\"',CASE WHEN max_temp0<80 THEN 'temp0'WHEN max_temp0<87 THEN 'temp1' WHEN max_temp0<90 THEN 'temp2' ELSE 'temp3' END,'\">',max_temp0,'</span>'),'')
			,IF(max_temp1,CONCAT('<span class=\"',CASE WHEN max_temp1<80 THEN 'temp0'WHEN max_temp1<87 THEN 'temp1' WHEN max_temp1<90 THEN 'temp2' ELSE 'temp3' END,'\">',max_temp1,'</span>'),'')
			,IF(max_temp2,CONCAT('<span class=\"',CASE WHEN max_temp2<80 THEN 'temp0'WHEN max_temp2<87 THEN 'temp1' WHEN max_temp2<90 THEN 'temp2' ELSE 'temp3' END,'\">',max_temp2,'</span>'),'')
			,IF(max_temp3,CONCAT('<span class=\"',CASE WHEN max_temp3<80 THEN 'temp0'WHEN max_temp3<87 THEN 'temp1' WHEN max_temp3<90 THEN 'temp2' ELSE 'temp3' END,'\">',max_temp3,'</span>'),'')
			,IF(max_temp4,CONCAT('<span class=\"',CASE WHEN max_temp4<80 THEN 'temp0'WHEN max_temp4<87 THEN 'temp1' WHEN max_temp4<90 THEN 'temp2' ELSE 'temp3' END,'\">',max_temp4,'</span>'),'')
			,IF(max_temp5,CONCAT('<span class=\"',CASE WHEN max_temp5<80 THEN 'temp0'WHEN max_temp5<87 THEN 'temp1' WHEN max_temp5<90 THEN 'temp2' ELSE 'temp3' END,'\">',max_temp5,'</span>'),'')
			,IF(max_temp6,CONCAT('<span class=\"',CASE WHEN max_temp6<80 THEN 'temp0'WHEN max_temp6<87 THEN 'temp1' WHEN max_temp6<90 THEN 'temp2' ELSE 'temp3' END,'\">',max_temp6,'</span>'),'')
			,IF(max_temp7,CONCAT('<span class=\"',CASE WHEN max_temp7<80 THEN 'temp0'WHEN max_temp7<87 THEN 'temp1' WHEN max_temp7<90 THEN 'temp2' ELSE 'temp3' END,'\">',max_temp7,'</span>'),'')
		) AS `html_max_temp`
		,CONCAT(IF(av_temp0,av_temp0,'')
			,IF(av_temp1,CONCAT(' ',av_temp1),'')
			,IF(av_temp2,CONCAT(' ',av_temp2),'')
			,IF(av_temp3,CONCAT(' ',av_temp3),'')
			,IF(av_temp4,CONCAT(' ',av_temp4),'')
			,IF(av_temp5,CONCAT(' ',av_temp5),'')
			,IF(av_temp6,CONCAT(' ',av_temp6),'')
			,IF(av_temp7,CONCAT(' ',av_temp7),'')
		) AS `html_av_temp`
		,CONCAT('<td',IF(`v2`<".maxv2.",' class=\"blue\">','>'),`v0`,'.0.',`v2`,IF(`v3`,CONCAT('x',`v3`),''),'</td>') AS `html_ver`
		,CONCAT(IF(av_fan0,av_fan0,'')
			,IF(av_fan1,CONCAT(' ',av_fan1),'')
			,IF(av_fan2,CONCAT(' ',av_fan2),'')
			,IF(av_fan3,CONCAT(' ',av_fan3),'')
			,IF(av_fan4,CONCAT(' ',av_fan4),'')
			,IF(av_fan5,CONCAT(' ',av_fan5),'')
			,IF(av_fan6,CONCAT(' ',av_fan6),'')
			,IF(av_fan7,CONCAT(' ',av_fan7),'')
		) AS `html_av_fan`
		,CONCAT(IF(av_util0,av_util0,'')
			,IF(av_util1,CONCAT(' ',av_util1),'')
			,IF(av_util2,CONCAT(' ',av_util2),'')
			,IF(av_util3,CONCAT(' ',av_util3),'')
			,IF(av_util4,CONCAT(' ',av_util4),'')
			,IF(av_util5,CONCAT(' ',av_util5),'')
			,IF(av_util6,CONCAT(' ',av_util6),'')
			,IF(av_util7,CONCAT(' ',av_util7),'')
		) AS `html_av_util`
		,CONCAT(IF(max_fan0,max_fan0,'')
			,IF(max_fan1,CONCAT(' ',max_fan1),'')
			,IF(max_fan2,CONCAT(' ',max_fan2),'')
			,IF(max_fan3,CONCAT(' ',max_fan3),'')
			,IF(max_fan4,CONCAT(' ',max_fan4),'')
			,IF(max_fan5,CONCAT(' ',max_fan5),'')
			,IF(max_fan6,CONCAT(' ',max_fan6),'')
			,IF(max_fan7,CONCAT(' ',max_fan7),'')
		) AS `html_max_fan`
		,CONCAT(IF(max_util0,max_util0,'')
			,IF(max_util1,CONCAT(' ',max_util1),'')
			,IF(max_util2,CONCAT(' ',max_util2),'')
			,IF(max_util3,CONCAT(' ',max_util3),'')
			,IF(max_util4,CONCAT(' ',max_util4),'')
			,IF(max_util5,CONCAT(' ',max_util5),'')
			,IF(max_util6,CONCAT(' ',max_util6),'')
			,IF(max_util7,CONCAT(' ',max_util7),'')
		) AS `html_max_util`
	FROM `usrs` USE INDEX(`PRIMARY`)
	WHERE `rl`&1".($_SESSION["rl"]&12?(isset($_GET['gr'])?" AND `grp_id`=".$_GET['gr']:""):" AND `pusr_id`=".$_SESSION["id"])
	.($_SESSION["rl"]&12?" AND TIMESTAMPDIFF(SECOND,`time`,NOW())<".(60*60*24*10):"")." ORDER BY `discon`,`ord1`,`id`"))
)exit(__FILE__.__LINE__);
echo "<!DOCTYPE html><html><head><title>Activity</title><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/><link rel=\"stylesheet\" type=\"text/css\" href=\"act.css\"/><link rel=\"stylesheet\" type=\"text/css\" href=\"ctrl1.css\"/></head><body>"
	."<table border=\"1\"><thead><tr><th>№</th><th>"
	.bv7lg("Client<br />ID</th><th>Comment</th><th>Email</th><th>mh/s</th><th>Timeout<br />(d h:m:s)</th><th>Client<br />soft.</th><th>Not<br />paid</th><th>Previous<br />payment</th><th>tºC<br />max</th><th>tºC<br />average</th><th>% Util.<br />max</th><th>% Util.<br />average</th><th>% Fan.<br />max</th><th>% Fan.<br />average</th>".($_SESSION["rl"]&12?"<th>Task<br />ID</th><th>Request<br />time":"")
		,"Код<br />клиента</th><th>Комментарий</th><th>Email</th><th>mh/s</th><th>Тайм-аут<br />(д ч:м:с)</th><th>ПО</th><th>Не<br />оплачено</th><th>Предыдущая<br />оплата</th><th>tºC<br />макс.</th><th>tºC<br />средн.</th><th>% Нагрузки<br />макс.</th><th>% Нагрузки<br />средн.</th><th>% Охл.<br />акс.</th><th>% Охл.<br />средн.</th".($_SESSION["rl"]&12?"<th>Код<br />задачи</th><th>Время<br />запроса":""))
	.($_SESSION["rl"]&12?"</th><th>&sum;mh/s":"")."</th></tr></thead><tbody>";
$row=mysql_fetch_assoc($res);
$smhs=$sn=$spays_acc=$spays_prev=$stime=0;
for($i=0;$i<2;$i++){
	echo "<tr class=\"header\"><td colspan=\"".($_SESSION["rl"]&12?'18':'15')."\">".($i?bv7lg("No active clients","Не активные клиенты"):bv7lg("Аctive clients","Активные клиенты"))."</td></tr>";
	$mhs=$n=$pays_acc=$pays_prev=$time=0;
	$vers=$row['html_ver'];
	$ve=true;
	while($row&&$row['discon']==$i){
	    $mhs+=$row["mhs"];
	    $smhs+=$row["mhs"];
	    $n++;
	    if($ve&&!($ve=$vers==$row['html_ver']))$vers="<td calss=\"blue\">".bv7lg("Different","Разные")."</td>";
		$pays_acc+=$row['pay_acc'];
		$pays_prev+=$row['pay_prev'];
		$time+=$row['clreq_time'];
		echo "<tr><td>".$n."</td><td";
		if($row['st']!=1)echo ' class="red"';
		echo ">".$row["id"]."</td><td>".$row['comment']."</td><td><a href=\"/e1.php".($_SESSION["rl"]&12?"?k=".$row["id"]:"")."\">".$row["em"]."</a></td><td><a href=\"/s2.php?k=".$row["id"]."\" target=\"_blank\" class=\"".($row["mhs"]<$row["mhs0"]?"mhs0":($row["mhs"]<$row["mhs1"]?"mhs1":"mhs2"))."\">".$row["mhs"]."</a></td><td>".($i?($row["timeout"]>=86400?floor($row["timeout"]/86400)." ":"").substr("0".(floor($row["timeout"]/3600)%24),-2). ":": ""). substr("0". (floor($row["timeout"]/60) % 60), -2). ":". substr("0". $row["timeout"]%60, -2)."</td>".$row['html_ver']."<td>".$row["pay_acc"]."</td><td>".($row["pay_prev"]!=0?$row["pay_prev"]:"")."</td><td>".$row['html_max_temp']."</td><td>".$row['html_av_temp']."</td><td>".$row['html_max_util']."</td><td>".$row['html_av_util']."</td><td>".$row['html_max_fan']."</td><td>".$row['html_av_fan']."</td>".($_SESSION["rl"]&12?"<td>".$row["job_id"]."</td><td>".$row['clreq_time']."</td><td>".$smhs."</td>":"")."</tr>";
		$row=mysql_fetch_assoc($res);
	}
	echo "<tr class=\"total\"><td>".$n."</td><td colspan=\"3\">".bv7lg("Total","Итого")."</td><td>".$mhs."</td><td></td>".$vers."<td>".sprintf("%.6f",$pays_acc)."</td><td>".sprintf("%.6f",$pays_prev)."</td><td></td><td></td><td></td><td></td><td></td><td></td>".($_SESSION["rl"]&12?"<td></td><td>".($n?round($time/$n):"")."</td><td>".$smhs."</td>":"")."</tr>";
	$sn+=$n;
	$spays_acc+=$pays_acc;
	$spays_prev+=$pays_prev;
	$stime+=$time;
}
echo"<tr class=\"total\"><td>".$sn."</td><td colspan=\"3\">".bv7lg("Grand Total","Общий итого")."</td><td>".$smhs."</td><td></td><td></td><td>".sprintf("%.6f",$spays_acc)."</td><td>".sprintf("%.6f",$spays_prev)."</td><td></td><td></td><td></td><td></td><td></td><td></td>".($_SESSION["rl"]&12?"<td></td><td>".($sn?round($stime/$sn):"")."</td><td>".$smhs."</td>":"")."</tr>"
	."</tbody></table></body></html>";
mysql_close($sql_srv);
?>