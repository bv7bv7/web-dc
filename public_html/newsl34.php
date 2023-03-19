<?php
require "auth.php";
if (!($_SESSION["rl"]&14))exit;
echo "<!DOCTYPE html><html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><link rel=\"stylesheet\" type=\"text/css\" href=\"ctrl1.css\" /></head><body>";
$sql_srv=bv7dc_select_db();
$ru=$_SESSION["lg"]=="ru";
$err_pws=$err_pwe=$err_db=$err_rp=$err_rpo=$err_prior=$err_mu=0;
if(isset($_POST['a'])&&$_POST["a"]=="chk"){
	if($_SESSION["rl"]&12){
		$cust=$_POST['cust'];
		$err_pws=!preg_match('/^\d{15,15}$/',$pws=$_POST['pws']);
		$err_pwe=!preg_match('/^\d{15,15}$/',$pwe=$_POST['pwe']);
		$err_mu=!preg_match('/^\d{0,2}$/',$mu=$_POST['mu']);
		$err_prior=!preg_match('/^-?[0-5]$/',$prior=$_POST['pr']);
	}else{
		$cust=$_SESSION['id'];
		$pws='000000000000000';
		$pwe='999999999999999';
		$mu=isset($_POST['sm'])&&$_POST['sm']=='1'&&$_SESSION['muf']?$_SESSION['muf']:($_SESSION['mu']?$_SESSION['mu']:10);
		$prior=isset($_POST['sm'])&&$_POST['sm']=='1'&&isset($_SESSION['prf'])?$_SESSION['prf']:$_SESSION['pr'];
	}
	if(!($cu=mysql_fetch_assoc($res=mysql_query("SELECT * FROM`usrs`WHERE`st`=1 AND`id`=".($cust+0)." LIMIT 1")))||$cu["mj"]==0)exit($ru?"Исчерпан лимит задач":"Limit tasks is exhausted</body></html>");
	$err_db=$err_db||!($res=mysql_query("SELECT * FROM `def` WHERE 1 LIMIT 1"))||!($row=mysql_fetch_assoc($res));
	if(!$err_db&&!$err_pws&&!$err_pwe&&!$err_mu&&!$err_prior){
		if($row['froz_nt'])exit("Error: Service Mode</body></html>");
		$pg=$row['prc_gh'];
		$cj=0;
		for($i=0;$i<count($_POST['hs']);$i++){
			$hs=strtoupper($_POST['hs'][$i]).':00'.substr($_POST['iemi'][$i],0,14).'00';
			$par=$hs."-".str_pad($pws,15,"0",STR_PAD_LEFT)."-".str_pad($pwe,15,"0",STR_PAD_LEFT);
			if(!($err_mj=$cu["mj"]>=0&&$cu["mj"]<=$cj)&&!($err_db=!($res=mysql_query("SELECT * FROM `jobs` WHERE `op_id`=0 AND `par`='".$par."' LIMIT 1"))))
			{
				if(!($err_rp=mysql_num_rows($res)>=1))
				{
					if(!($err_db=!mysql_query("INSERT INTO `jobs` SET `usr_id`=".$cust.",`op_id`=0,`par`='".$par."',`par2`='".$_POST['iemi'][$i]."',`cnt`=".$pwe."-".$pws."+1,`st`=1,`pay`=".$pg."*".sl3_pay_per_gh24.",`prc_gh`=".$pg.",`minusrs`=".($mu+0).",`priority`=".$prior.",`timecreate`=NOW()")
						||!mysql_query("INSERT `subjobs` SET `job_id`=".($job_id=mysql_insert_id()).",`par`='".$par."',`ym`=EXTRACT(YEAR_MONTH FROM NOW())")
					)){
						echo ($i+1).") ".$_POST['iemi'][$i].": ".bv7lg('Your request was successfully saved.','Ваш запрос успешно сохранен.')."<br />";
						$cj+=1;
					}
				}else $err_rpo=($row=mysql_fetch_assoc($res))&&$row['usr_id']!=$cust;
			}
			if($err_db)echo"<font color=\"red\">".($i+1).") ".$_POST['iemi'][$i].": ".bv7lg("Error: Access to jobs DB. Repeat the request later.","Ошибка доступа к БД задач. Повторите запрос позже")."</font><br />";
			if($err_rp)echo"<font color=\"red\">".($i+1).") ".$_POST['iemi'][$i].": ".bv7lg("Warning: Your request is already stored.","Ваш запрос уже сохранен.").($err_rpo?bv7lg(" For another customer."," Для другого заказчика."):"")."</font><br />";
			if($err_mj)echo"<font color=\"red\">".($i+1).") ".$_POST['iemi'][$i].": ".($ru?"Исчерпан лимит задач.":"Error: Limit tasks is exhausted.")."</font><br />";
		}
		if($cu["mj"]>0&&$cj>0)mysql_query("UPDATE`usrs`SET`time`=`time`,`mj`=".$cu["mj"]."-".$cj." WHERE`id`=".$cu["id"]." LIMIT 1");
	}
}
if(!isset($_POST["a"])||$err_pws||$err_pwe||$err_db||$err_rp||$err_rpo){
	if((!isset($us)&&!($us=mysql_fetch_assoc($res=mysql_query("SELECT * FROM`usrs`WHERE`st`=1 AND`id`=".($_SESSION['id']+0)." LIMIT 1"))))||$us["mj"]==0)exit($ru?"Исчерпан лимит задач":"Limit tasks is exhausted</body></html>");
//	require "mj_h.php";
	require_once'cu_h.php';
	echo "<div id=\"drop_zone\" class=\"ctrl2e\"><input type=\"file\" id=\"files\" name=\"files[]\" multiple=\"multiple\"/><br />"
		.bv7lg("Drop files here","Перетащите файлы сюда")
		."<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\"><table border=\"1\"><caption>".($ru?"Укажите параметры для декодирования SL3":"Specify parameters for SL3 decryption")."</caption>"
		."<tr><td align=\"right\">".($ru?"Лимит задач":"Limit of Tasks")."</td><td>".viewMJ($us["mj"])."</td></tr><tr><td align=\"right\">";
	if($_SESSION["rl"]&12){
		echo bv7lg('First password','Начальный пароль').'</td>'
			.'<td><input type="text" name="pws" value="'.(isset($_POST['pws'])?$_POST['pws']:'000000000000000').'" size="15" /><font color="red">'.($err_pws?bv7lg('Invalid first password. Must be 15 digits.','Ошибочный начальный пароль. Должно быть 15 цифр.'):'').'</font></td>'
			.'</tr><tr>'
			.'<td align="right">'.bv7lg('Last password','Конечный пароль').'</td>'
			.'<td><input type="text" name="pwe" value="'.(isset($_POST['pwe'])?$_POST['pwe']:'999999999999999').'" size="15" /><font color="red">'.($err_pwe?bv7lg('Invalid last password. Must be 15 digits.','Ошибочный конечный пароль. Должно быть 15 цифр.'):'').'</font></td>'
			.'</tr><tr>'
			.'<td align="right">'.bv7lg('Clients per task','Клиентов на задачу').'</td>'
			.'<td><input type="text" name="mu" value="'.(isset($_POST['mu'])?$_POST['mu']:($_SESSION['mu']?$_SESSION['mu']:10)).'" size="5" />&nbsp;(0 - Unlimit)<font color="red">'.($err_mu?bv7lg('Invalid count. Must be number 0-99 or empty.','Ошибочное количество. Должно быть число 0-99 или пусто.'):'').'</font></td>'
			.'</tr><tr>'
			.'<td align="right">'.bv7lg('Priority','Приоритет').'</td>'
			.'<td><input type="text" name="pr" value="'.(isset($_POST['pr'])?$_POST['pr']:$_SESSION['pr']).'" size="3" />&nbsp;(-5...5)<font color="red">'.($err_prior?bv7lg('Invalid priority. Must be number from -5 to 5.','Ошибочный приоритет. Должно быть число от -5 до 5.'):'').'</font></td>'
			.'</tr><tr>'
			.'<td align="right">'.bv7lg('Customer','Заказчик').'</td>'
			.'<td>'.getInputCust($_POST['cust']?$_POST['cust']:$_SESSION['id']);
	}else if($_SESSION["rl"]&2&&($_SESSION["muf"]||$_SESSION['pr']!=$_SESSION['prf'])){
		echo bv7lg("Speed Mode","Режим скорости")."</td><td><select name=\"sm\" size=\"1\">"
			."<option value=\"0\"".(!isset($_POST["sm"])||$_POST["sm"]=="0"?" selected=\"selected\"":"").">".bv7lg("Slow.","Медленно.")."</option>"
			."<option value=\"1\"".(isset($_POST["sm"])&&$_POST["sm"]=="1"?" selected=\"selected\"":"").">".bv7lg("Fast.","Быстро.")."</option>"
			."</select>";
	}
	echo "</td></tr><tr></tr></tbody></table>"
		."<input type=\"hidden\" name=\"a\" value=\"chk\">"
		."<input type=\"submit\" id=\"submit\" style=\"display:none\"/>"
		."<font color=\"red\">".($err_db?bv7lg("Error: Access to jobs DB. Repeat the request later.","Ошибка доступа к БД задач. Повторите запрос позже"):"").($err_rp?bv7lg("Warning: Your request is already stored.","Ваш запрос уже сохранен.").($err_rpo?bv7lg(" For another customer."," Для другого заказчика."):""):"")."</font>"
		."<table border=\"1\"><thead><tr><th>№</th><th>IMEI</th><th>HASH</th></tr></thead><tbody id=\"input\"></tbody></table></form></div><script type=\"text/javascript\" src=\"newsl34.js\"></script>";
}
echo "</body></html>";
?>