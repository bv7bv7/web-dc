<?php
require "auth.php";
if(!($_SESSION["rl"]&28))exit;
require "grp_h.php";
require "not_h.php";
require "ctrl5.php";
function grpSet($set){
	if(($res=mysql_query("SELECT * FROM`usrs`WHERE`rl`&1 AND`id`=".($_GET["k"]+0)." LIMIT 1"))&&($row=mysql_fetch_assoc($res))&&(mysql_query("UPDATE`usrs`SET ".$set.",`time`=`time`WHERE`rl`&1 AND`em`='".$row["em"]."'")))ctrl6handleResponse();
	else echo bv7lg("DB access error","Ошибка доступа к БД");
}
$sql_srv=bv7dc_select_db();
if(isset($_GET['m'])&&$_GET['m']==1&&$_SESSION["rl"]&12)
	switch($_GET['a']){
	case "chaprcgh":
		grpSet("`prc_gh`=".($_GET['prcgh']+0.0));
		break;
	case "chapayaccprcgh":
		grpSet("`pay_acc`=`cnt_acc`/".sl3_op_per_gh24."*`prc_gh`");
		break;
	case 'chvisor':
		grpSet("`pusr_id`=".($_GET['visor']+0));
		break;
	case 'chgrp':
		grpSet("`grp_id`=".($_GET['grp']+0));
		break;
	case 'chpa':
		grpSet("`pr_add_pay`=".($_GET['pa']+0));
		break;
	}
echo "<!DOCTYPE html><html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>";
require "ctrl1.php";
require_once'vi_h.php';
function viewText1wm2($wm2,$pr2){return $wm2==""?($_SESSION["rl"]&12?bv7lg("Set...","Указать..."):bv7lg("Empty","Не указан")):$wm2." : ".$pr2."%";}
function viewText3st($st){
	switch ($st){
	case 0:
		return "<font color=\"gray\">".bv7lg("Not&nbsp;checked","Не&nbsp;проверен")."</font>";
	case 1:
		return "<font color=\"green\">".bv7lg("Сhecked","Проверен")."</font>";
	case 4:
		return "<font color=\"black\">".bv7lg("Deleted","Удален")."</font>";
	}
}
function inputSt($st,$checked=false){
	$input="<input type=\"radio\" name=\"st\" value=\"".$st."\"";
	if($checked)
		$input.=" checked=\"checked\"";
	return $input."/>&nbsp;".viewText3st($st)."<br />";
}
function inputText3st($st,$id){
	$input="<input type=\"hidden\" name=\"a\" value=\"chst\"/><input type=\"hidden\" name=\"k\" value=\"".$id."\"/>";
	switch($st){
	case 0:
		$input.=inputSt(1,true)
			.inputSt(4);
		break;
	case 1:
		$input.=inputSt(0,true);
		break;
	case 4:
		$input.=viewText3st(4);
		break;
	}
	return $input;
}
function inputText3visor($visor){return"<input type=\"hidden\" name=\"a\" value=\"chvisor\"/>".getInputVisor($visor);}
function viewText3comment($comment){return"&nbsp;".($comment?$comment:bv7lg("Empty","Пусто"))."&nbsp;";}
function inputText3comment($comment,$idf){
	return "<input type=\"hidden\" name=\"a\" value=\"chcomment\"/>"
		."<input type=\"hidden\" name=\"k\" value=\"".$idf."\"/>"
		."<input type=\"text\" name=\"comment\" value=\"".$comment."\"/>";
}
function viewText3mhs($mhs0,$mhs1){
	return "<span class=\"mhs0\">".$mhs0."</span>&nbsp;<span class=\"mhs1\">".$mhs1."</span>&nbsp;<span class=\"mhs2\">∞</span>";
}
function inputText3mhs($mhs0,$mhs1,$idf){
	return "<input type=\"hidden\" name=\"a\" value=\"chmhs\"/>"
		."<input type=\"hidden\" name=\"k\" value=\"".$idf."\"/>"
		."<span class=\"mhs0\">Syle &lt;</span>&nbsp;<input type=\"text\" name=\"mhs0\" value=\"".$mhs0."\" size=\"5\"/> mh/s<br/>"
		."<span class=\"mhs1\">Syle &lt;</span>&nbsp;<input type=\"text\" name=\"mhs1\" value=\"".$mhs1."\" size=\"5\"/> mh/s<br/>"
		."<span class=\"mhs2\">Syle &lt;</span>&nbsp;∞ mh/s";
}
function inputId($id){return "<input type=\"hidden\" name=\"k\" value=\"".$id."\"/>";}
function inputPrAdd($pr){return "<input type=\"hidden\" name=\"a\" value=\"chpa\"/><input type=\"text\" name=\"pa\" value=\"".$pr."\" size=\"6\"/>%";}
if(isset($_GET['a'])){
	if(!isset($_GET["m"])&&isset($_GET["k"])){
		if($_SESSION["rl"]&12)
			switch($_GET['a']){
			case 'chwm2':
			    if(($_GET['wm2']==""?$_GET['pr2']==0:$_GET['pr2']>=0&&$_GET['pr2']<=100)
					&&mysql_query("UPDATE`usrs`SET`wm2`='".$_GET['wm2']."',pr2=".$_GET['pr2'].",`time`=`time`WHERE`id`=".($_GET['k']+0)." LIMIT 1")
					&&mysql_affected_rows()
				)
					ctrl1handleResponse(viewText1wm2($_GET['wm2'],$_GET['pr2']));
				break;
			case 'chst':
				if(mysql_query(
						($_GET["st"]==4
							?"DELETE FROM `usrs`"
							:"UPDATE `usrs` SET `st`=".$_GET["st"].",`time`=`time`"
						)." WHERE `id`=".($_GET["k"]+0)." LIMIT 1"
					)&&mysql_affected_rows()
				)ctrl3handleResponse(viewText3st($_GET["st"]),inputText3st($_GET["st"],($_GET["k"]+0)));
				break;
			case 'chvisor':
				if(mysql_query("UPDATE `usrs`SET`pusr_id`=".$_GET["visor"].",`time`=`time`WHERE`id`=".($_GET["k"]+0)." LIMIT 1")
					&&mysql_affected_rows()
				){
					ctrl3handleResponse(getTextVisor($_GET["visor"]));
				}
				break;
			case 'chgrp':
				if(mysql_query("UPDATE`usrs`SET`grp_id`=".($_GET['grp']+0).",`time`=`time`WHERE`id`=".($_GET["k"]+0)." LIMIT 1")&&mysql_affected_rows()){
					initGrp();
					ctrl3handleResponse(viewGrp($_GET['grp']),inputGrp($_GET['grp']).inputId($_GET["k"]+0));
				}
				break;
			case 'chpa':
				$pa=$_GET['pa']+0;
				if(mysql_query("UPDATE`usrs`SET`pr_add_pay`=".$pa.",`time`=`time`WHERE`id`=".($_GET["k"]+0)." LIMIT 1")&&mysql_affected_rows())
					ctrl3handleResponse($pa,inputPrAdd($pa).inputId($_GET["k"]+0));
				break;
			}
		switch($_GET['a']){
		case 'n':
			if(mysql_query("UPDATE`usrs`SET`notify`=".($_GET["n"]+0).",`time`=`time`WHERE`id`=".($_GET["k"]+0).($_SESSION["rl"]&12?"":" AND `pusr_id`=".($_SESSION["id"]+0))." LIMIT 1")&&mysql_affected_rows())
				ctrl3handleResponse(viewNotify($_GET["n"]+0),inputNotify($_GET["n"]+0,$_GET["k"]+0));
			break;
		case 'nia':
			if(mysql_query("UPDATE`usrs`SET`notify_ia`=".($_GET["n"]+0).",`time`=`time`WHERE`id`=".($_GET["k"]+0).($_SESSION["rl"]&12?"":" AND `pusr_id`=".($_SESSION["id"]+0))." LIMIT 1")&&mysql_affected_rows())
				ctrl3handleResponse(viewNotify($_GET["n"]+0),inputNotify($_GET["n"]+0,$_GET["k"]+0,"nia"));
			break;
		case 'chcomment':
			$comment=str_replace("'","`",urldecode($_GET["comment"]));
			if(mysql_query("UPDATE`usrs`SET`comment`='".$comment."',`time`=`time`WHERE`id`=".($_GET["k"]+0).($_SESSION["rl"]&12?"":" AND`pusr_id`=".($_SESSION["id"]+0))." LIMIT 1")&&mysql_affected_rows())
				ctrl3handleResponse(viewText3comment($comment));
			break;
		case 'chmhs':
			$comment=str_replace("'","`",urldecode($_GET["comment"]));
			if(mysql_query("UPDATE`usrs`SET`mhs0`=".$_GET['mhs0'].",`mhs1`=".$_GET['mhs1'].",`time`=`time`WHERE`id`=".($_GET["k"]+0).($_SESSION["rl"]&12?"":" AND`pusr_id`=".($_SESSION["id"]+0))." LIMIT 1")&&mysql_affected_rows())
				ctrl3handleResponse(viewText3mhs($_GET['mhs0'],$_GET['mhs1']));
			break;
		}
	}
	exit("</head></html>");
}
echo"<title>";
$from="FROM`usrs`WHERE`rl`&1 ";
if($_SESSION["rl"]&12){
	if(($res=mysql_query("SELECT * ".$from."AND`id`=".($_GET["k"]+0)." LIMIT 1"))&&($row=mysql_fetch_assoc($res))){
		$from.="AND`em`='".($em=$row["em"])."'";
		echo $em;
	}
	else exit;
	$inpAll="<input type=\"hidden\" name=\"m\" value=\"1\"/>".inputId($_GET["k"]+0);
}else{
	$from.="AND`pusr_id`=".$_SESSION["id"]." ";
	echo"Clients";
}
echo "</title></head><body>";
initGrp();
$query="SELECT *
		,`cnt_acc`/".sl3_op_per_gh24." AS`gh_acc`
		,`cnt_prev`/".sl3_op_per_gh24." AS`gh_prev`
		,`pay_acc`+`pay_acc`*`pr_add_pay`/100 AS`pay_acc_pr`
		,TIMESTAMPDIFF(SECOND,`usrs`.`timepay`,NOW()) as`paytimeout`"
	.$from."ORDER BY`id`";
$res=mysql_query($query);
if (!$res)
	exit(bv7lg("DB access error","Ошибка доступа к БД")."</body></html>");
echo"<div id=\"debug\"></div><table border=\"1\"><caption><a href=\"/usrs2.php\">".bv7lg("List of clients","Список клиентов")." </a>";
if($_SESSION["rl"]&12)echo $em;
echo"</caption><thead><tr>"
	."<th rowspan=\"2\">".bv7lg("ID","Код")."</th>"
	."<th colspan=\"2\">".bv7lg("Purse","Кошелек")."</th>"
	."<th rowspan=\"2\">".bv7lg("State","Состояние")."</th>"
	."<th colspan=\"2\">".bv7lg("Notify when","Оповещать при")."</th>"
	."<th colspan=\"2\">".bv7lg("Unpaid","Не оплачено")."</th>";
if($_SESSION["rl"]&12)
	echo "<th colspan=\"2\">+/-</th>";
echo "<th colspan=\"3\">".bv7lg("Previous payment","Предыдущая оплата")."</th>";
if($_SESSION["rl"]&12)
	echo "<th rowspan=\"2\">".bv7lg("D for 1 gh/sec<br/>per 24h","D за 1 ГХ/сек<br/>в сутки")."</th>";
echo "<th rowspan=\"2\">".bv7lg("Client Viewer<br/>(ID, Email)","Наблюдатель за клиентом<br/>(код, Email)")."</th>"
	."<th rowspan=\"2\">".bv7lg("Comment","Комментарий")."</th>"
	."<th rowspan=\"2\">".bv7lg("Speed<br/>style","Стиль<br/>скорости")."</th>";
if($_SESSION["rl"]&12)
	echo "<th rowspan=\"2\">Group</th>";
echo "</tr><tr>"
	."<th>".bv7lg("Primary","Основной")."</th>"
	."<th>".bv7lg("Additional","Дополнительный")."</th>"
	."<th>".bv7lg("Task missing","отсутсвии заданий")."</th>"
	."<th>".bv7lg("Inactivity","неактивности")."</th>"
	."<th>gh/s/24h</th>"
	."<th>D</th>";
if($_SESSION["rl"]&12)
	echo "<th>%</th><th>D</th>";
echo "<th>gh/s/24h</th>"
	."<th>D</th>"
	."<th>".bv7lg("Еlapsed","Прошло")."</th>"
	."</tr></thead><tbody>";
$sgh_acc=$spay_acc=$spay_acc_pr=$sgh_prev=$spay_prev=$group=$prAdd=0;
$visor=null;
$sApp=bv7lg("Apply","Применить");
while($row=mysql_fetch_assoc($res))
{
	$sgh_acc+=$row['gh_acc'];
	$spay_acc+=$row['pay_acc'];
	$spay_acc_pr+=$row['pay_acc_pr'];
	$sgh_prev+=$row['gh_prev'];
	$spay_prev+=$row['pay_prev'];
	if($visor===null)
		$visor=$row['pusr_id'];
	else
		if($visor!=$row['pusr_id'])
			$visor=-1;
	if(!isset($sprc_gh))
		$sprc_gh = $row['prc_gh'];
	else
		if($sprc_gh != $row['prc_gh'])
		    $sprc_gh = 0;
	$group=$row['grp_id'];
	$prAdd=$row["pr_add_pay"];
	$inpId=inputId($row['id']);
	echo'<tr><td><a href="/s2.php?k='.$row["id"].'">'.$row["id"].'</a></td><td>'.$row["wm"].'</td><td>';
	ctrl1(viewText1wm2($row['wm2'],$row['pr2'])
		, bv7lg("Purse", "Кошелек")
		." <input type=\"text\" name=\"wm2\" value=\"".$row['wm2']."\"/><br /><br />"
		.bv7lg("Piece", "Доля")
		." <input type=\"text\" name=\"pr2\" value=\"".$row['pr2']."\" size=\"4\"/>%"
		."<input type=\"hidden\" name=\"a\" value=\"chwm2\"/>"
		.$inpId
		,bv7lg('Save', 'Сохранить')
		,$_SESSION["rl"]&12
	);
	echo "</td><td>";
	ctrl3(viewText3st($row["st"]),inputText3st($row["st"],$row['id']),$sApp,$_SESSION["rl"]&12);
	echo "</td><td>";
	ctrl3(viewNotify($row["notify"]),inputNotify($row["notify"],$row['id']),$sApp);
	echo "</td><td>";
	ctrl3(viewNotify($row["notify_ia"]),inputNotify($row["notify"],$row['id'],"nia"),$sApp);
	echo "</td><td align=\"right\">".$row["gh_acc"]."</td><td align=\"right\">".round($row["pay_acc"],2)."&nbsp;D</td>";
	if($_SESSION["rl"]&12){
		echo "<td align=\"right\">";
		ctrl3($prAdd,inputPrAdd($prAdd).$inpId,$sApp);
		echo "</td><td align=\"right\">".round($row["pay_acc_pr"],2)."</td>";
	}
	echo "<td align=\"right\">".$row["gh_prev"]."</td>"
		."<td align=\"right\">".round($row["pay_prev"],2)."D</td>"
		."<td align=\"right\">".($row["paytimeout"]>=86400?floor($row["paytimeout"]/86400)." ":"").substr("0".(floor($row["paytimeout"]/3600)%24),-2).":".substr("0".(floor($row["paytimeout"]/60)%60),-2).":".substr("0".$row["paytimeout"]%60,-2)."</td>";
	if($_SESSION["rl"]&12)
		echo "<td align=\"right\">".($row["prc_gh"]==0?"&nbsp;":$row["prc_gh"])."</td>";
	echo "<td>";
	ctrl3(getTextVisor($row['pusr_id']),inputText3visor($row['pusr_id']).$inpId,$sApp,$_SESSION["rl"]&12);
	echo "</td><td>";
	ctrl3(viewText3comment($row['comment']),inputText3comment($row['comment'],$row['id']),$sApp);
	echo "</td><td>";
	ctrl3(viewText3mhs($row['mhs0'],$row['mhs1']),inputText3mhs($row['mhs0'],$row['mhs1'],$row['id']),$sApp);
	if($_SESSION["rl"]&12){
		echo "</td><td>";
		ctrl3(viewGrp($row['grp_id']),inputGrp($row['grp_id']).$inpId,$sApp);
	}
	echo "</td></tr>";
}
mysql_close($sql_srv);
$sAppAll=bv7lg('Apply to all','Применить для всех');
echo "<tr class=\"total\">"
	."<td colspan=\"6\">".bv7lg("Total","Итого")."</td>"
	."<td align=\"right\">".$sgh_acc."</td>"
	."<td align=\"right\">".round($spay_acc,2);
if($_SESSION["rl"]&12){
	echo"<br />";
	$s=bv7lg('Recalc from D for 1 gh/sec per 24h','Пересчитать от D за 1 ГХ/сек в сутки');
	ctrl3($s,"<input type=\"hidden\" name=\"a\" value=\"chapayaccprcgh\"/>".$inpAll,$s);
}
echo "</td>";
if($_SESSION["rl"]&12){
	echo "<td align=\"right\">";
	ctrl3("Change For All",inputPrAdd($prAdd).$inpAll,$sAppAll);
	echo "</td>"
		."<td align=\"right\">".round($spay_acc_pr,2)."</td>";
}
echo "<td align=\"right\">".$sgh_prev."</td>"
	."<td align=\"right\">".round($spay_prev,2)."</td>"
	."<td align=\"center\">X</td>";
if($_SESSION["rl"]&12){
	echo"<td>";
	ctrl3($sprc_gh,
		"<input type=\"hidden\" name=\"a\" value=\"chaprcgh\"/>"
		."<input type=\"text\" name=\"prcgh\" value=\"".$sprc_gh."\" SIZE=\"5\"/>".$inpAll,
		$sAppAll
	);
	echo"</td>";
}
if($visor===null)
	$visor=0;
echo "<td>";
if($_SESSION["rl"]&12)
	ctrl3(getTextVisor($visor),inputText3visor($visor).$inpAll,$sAppAll);
else
	echo $visor==-1?"<font color=\"red\">".bv7lg("Various","Различные")."</font>":getTextVisor($visor);
echo "</td><td></td><td>";
if($_SESSION["rl"]&12){
	echo "</td><td>";
	ctrl3(viewGrp($group),inputGrp($group).$inpAll,$sAppAll);
}
echo "</td></tr></tbody></table></body></html>";
?>