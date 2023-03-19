<?php
require "auth.php";
if(!($_SESSION["rl"]&12))
	exit;
echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">";
require "ctrl1.php";
echo "<title>Clients</title></head><body>";
$sql_srv=bv7dc_select_db();
$qpt=24*(isset($_GET['pt'])?$_GET['pt']:1);
$query="SELECT GROUP_CONCAT(DISTINCT CONCAT('_',IF(`st`=1,'r','u'),`id`) ORDER BY `id` SEPARATOR ', ') AS `usr_ids`,
		`em`,
		GROUP_CONCAT(DISTINCT `wm` ORDER BY `id` SEPARATOR ', ') AS `usr_wms`,
		SUM(`cnt_acc`/".sl3_op_per_gh24.") AS `gh_acc`,
		SUM(`pay_acc`) AS `pays_acc`,
		SUM(IF(TIMESTAMPDIFF(HOUR,`timepay`,NOW())<=".$qpt.",`cnt_prev`/".sl3_op_per_gh24.",0)) AS `gh_prev`,
		SUM(IF(TIMESTAMPDIFF(HOUR,`timepay`,NOW())<=".$qpt.",`pay_prev`,0)) AS `pays_prev`,
		MIN(`id`) AS `usr_idf`,
		GROUP_CONCAT(DISTINCT `pr_add_pay` ORDER BY `id`) AS `pr_add`,
		SUM(`pay_acc` + `pay_acc` * `pr_add_pay` / 100) AS `pay_acc_pr`,
		GROUP_CONCAT(DISTINCT `prc_gh` ORDER BY `id` SEPARATOR '; ') AS `aprc_gh`
	FROM `usrs`	WHERE `rl`&1 ";
if(isset($_GET['st']))$query.="AND `st`=".($_GET['st']+0)." ";
$query.="GROUP BY `em` ORDER BY `usr_idf` DESC";
$res=mysql_query ($query);
if (!$res)
	echo bv7lg ("DB access error","Ошибка доступа к БД");
else
{
	echo'<table border="1"><caption>'.bv7lg('List of clients','Список клиентов');
	if(isset($_GET['st'])&&($_GET['st']+0==0))echo'('.bv7lg('unchecked only','только не проверенные').')';
	echo'</caption><thead><tr>'
		.'<th rowspan="2">'.bv7lg('ID','Код').'</th>'
		.'<th rowspan="2">Email</th>'
		.'<th rowspan="2">'.bv7lg('Purse','Кошелек').'</th>'
		.'<th colspan="2">'.bv7lg('Unpaid','Не оплачено').'</th>'
		.'<th colspan="2">+/-</th>'
		.'<th colspan="2">'.bv7lg('Previous payment','Предыдущая оплата').'</th>'
		.'<th rowspan="2">'.bv7lg('D for 1 gh','D за 1 ГХ').'</th>'
		.'</tr><tr>'
		.'<th>gh/s/24h</th>'
		.'<th>D</th>'
		.'<th>%</th>'
		.'<th>D</th>'
		.'<th>gh/s/24h</th>'
		.'<th>D</th>'
		.'</tr></thead><tbody>';
	$sgh_acc=$spays_acc=$spay_acc_pr=$sgh_prev=$spays_prev=0;
	while($row=mysql_fetch_assoc($res))
	{
		$sgh_acc+=$row['gh_acc'];
		$spays_acc+=$row['pays_acc'];
		$spay_acc_pr+=$row['pay_acc_pr'];
		$sgh_prev+=$row['gh_prev'];
		$spays_prev+=$row['pays_prev'];
		echo '<tr>'
			.'<td width="10%">'.preg_replace('/_u(\d+)/','<a href="/s2.php?k=\\1" class="red">\\1</a>',preg_replace('/_r(\d+)/','<a href="/s2.php?k=\\1">\\1</a>',$row['usr_ids'])).'</td>'
			.'<td><a href="/e1.php?k='.$row['usr_idf'].'" target="_blank">'.($row['em']==''?'&nbsp;':$row['em']).'</a></td>'
			.'<td width="10%">'.($row['usr_wms']==''?'&nbsp;':$row['usr_wms']).'</td>'
			.'<td align="right">'.$row['gh_acc'].'</td>'
			.'<td align="right">'.round($row['pays_acc'],2).'&nbsp;D</td>'
			.'<td align="right">'.($row['pr_add']==''?'&nbsp;':$row['pr_add']).'</td>'
			.'<td align="right">'.round($row['pay_acc_pr'],2).'</td>'
			.'<td align="right">'.$row['gh_prev'].'</td>'
			.'<td align="right">'.round($row['pays_prev'],2).'D</td>'
			.'<td align="right">'.($row['aprc_gh']==0?'&nbsp;':$row['aprc_gh']).'</td>'
			.'</tr>';
	}
	echo "<tr class=\"total\">"
		."<td colspan=\"3\">".bv7lg("Total","Итого")."</td>"
		."<td align=\"right\">".$sgh_acc."</td>"
		."<td align=\"right\">".round($spays_acc,2)."</td>"
		."<td align=\"right\">X</td>"
		."<td align=\"right\">".round($spay_acc_pr,2)."</td>"
		."<td align=\"right\">".$sgh_prev."</td>"
		."<td align=\"right\">".round($spays_prev,2)."</td>"
		."<td align=\"right\">X</td>"
		."</tr></tbody></table>";
}
mysql_close($sql_srv);
echo "</body></html>";
?>