<?
require'auth.php';
echo'<!DOCTYPE html><html><head><link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/><link rel="shortcut" href="/favicon.ico" type="image/x-icon"/><title>DC ';
require'def_srvs.php';
$iSrvNew=-1;
for($i=0;$i<count($srvs);$i++)
	if(parse_url($srvs[$i][2],PHP_URL_HOST)==$_SERVER['SERVER_NAME']){
		echo $srvs[$i][1];
		break;
	}else if(parse_url($srvs[$i][3],PHP_URL_HOST)==$_SERVER['SERVER_NAME']){
		$iSrvNew=$i;
		break;
	}
echo'</TITLE><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
require_once'icd_h.php';
require_once'idk_h.php';
echo'</head><body><table border="1"><caption>'.bv7lg('Control panel','Панель управления').' 2.1.8a</caption>'
	.'<thead><tr><th>'.bv7lg('Actions','Действия').'</th><th>'.bv7lg('Information','Информация').'</th></tr></thead><tbody><tr><td><table border="1">';
if($_SESSION["rl"]&12)
	echo'<tr><td><ul>'
		.'<li><a href="usrs2.php" target="usrs">'.bv7lg('Clients List (all)','Список клиентов (полный)').'</a></li>'
		.'<li><a href="usrs2.php?st=0" target="usrs">'.bv7lg('Clients List (unchecked only)','Список клиентов (только не проверенные)').'</a></li>'
		.'<li><a href="grp.php" target="grp">Groups Of Clients</a></li>'
		.'<li><a href="set.php" target="set">Settings</a></li>'
		.'<li><form method="get" target="pay" action="p4.php">'
		.bv7lg('Credit payment from','Зачет оплаты от').' <input type="text" name="min_pay" value="1.00" size="5"/> D '
		.bv7lg('with accuracy','с точностью').' <input type="text" name="dec_pay" value="0.1" size="6"/> D '
		.'<input type="submit"/></form></li>'
		.'</ul></td></tr>';
if($_SESSION["rl"]&14)
	echo'<tr><td><a href="info.php" target="info">'.bv7lg('Global Info','Общая информация').'</a>'
			.'<br><a href="it.php" target="it">Tasks Info</a></td></tr>'
		.'<tr><td><form action="j3.php" target="_blank" method="GET">'.bv7lg('Task List','Список задач').'-<input type="text" name="l" value="100" size="4"> IMEI:<input type="text" name="iemi" size="15"><span><script type="text/javascript">c10tz();</script></span> <input type="submit"/></form></td></tr>'
		.'<tr><td>'.getInputIDK().'</td></tr>'
		.'<tr><td>'.bv7lg('Create Task','Создать задачу').'<ul>'
			.'<li><a href="newsl32.php" target="newjob">'.bv7lg('SL3 decryption (method 2)','Декодирование SL3 (способ 2)').'</a></li>'
			.'<li><a href="newsl34.php" target="newjob">'.bv7lg('SL3 decryption (method 3)','Декодирование SL3 (способ 3)').'</a></li>'
		.'</ul></td></tr>';
if($_SESSION["rl"]&(30))
	echo "<tr><td><a href=\"cus.php\" target=\"cus\">".bv7lg("User Settings","Настройки пользователя")."</a></td></tr>";
if($_SESSION["rl"]&28)
	echo'<tr><td><a href="act.php" target="_blank">'.bv7lg('Clients activity','Активность клиентов').'</a></td></tr>'
		.'<tr><td>'.getInputICD().'</td></tr>';
function mspc2($ms){return strlen($ms)<30?mspc($ms):$ms;}
if($_SESSION["rl"]&12){
	switch($_POST['a']){
	case 'gen_hs':
		$mspc=mspc2($_POST['mspc']);
		$hs=strtoupper(sha1(pack('H*',$mspc."00".substr($_POST['iemi'],0,14)."00")));
		break;
	case 'gen_nck':
		$mspc=mspc2($_POST['mspc2']);
		$hs=$_POST['hs'];
		break;
	}
	echo'<tr><td><form action="'.$_SERVER['PHP_SELF'].'" method="post"><table border="1"><caption>'.bv7lg('Other tools','Дополнительные инструменты').'</caption>'
		.'<tfoot><tr><td colspan="2"><input type="submit"/></td></tr></tfoot><tbody>'
		.'<tr><td><input type="radio" name="a" value="gen_pw"/></td><td>'.bv7lg('Generate password of length','Сгенерировать пароль длиной').'<input type="text" name="pw_len" value="15"/>.</td></tr>'
		.'<tr><td><input type="radio" name="a" value="gen_hs"/></td><td>'.bv7lg('Generate hash of IMEI','Сгенерировать хеш для IMEI:').':<input type="text" name="iemi" value="'.$_POST['iemi'].'" size="15"/> M_SP_C:<input type="text" name="mspc" value="'.$mspc.'" size="15"/></td></tr>'
		.'<tr><td><input type="radio" name="a" value="gen_nck"/></td><td>'.bv7lg('Generate NCK of HASH','Сгенерировать NCK для хеш:').':<input type="text" name="hs" value="'.$hs.'" size="40"/> M_SP_C:<input type="text" name="mspc2" value="'.$mspc.'" size="15"/></td></tr>'
		.'<tr><td><input type="radio" name="a" value="add_cust"/></td><td>'.bv7lg('Add customer','Добавить заказчика').'<br/>Email:<input type="text" name="cust_em" value="'.$_POST['cust_em'].'"/></td></tr>'
		.'<tr><td><input type="radio" name="a" value="snd_res"/></td><td>'.bv7lg('Send results for ','Отправить результаты за ').getCBiDT('sr').bv7lg(' of Customer ',' заказчика ').getInputCust(isset($_POST['srcust'])?$_POST['srcust']:$_SESSION['id'],'sr').bv7lg(' to ',' на почту ').$_SESSION['em'].'</td></tr>';
	if($_SESSION["rl"]&8){
		echo "<tr><td><input type=\"radio\" name=\"a\" value=\"exp_us\"/></td><td>".bv7lg("Export users to","Экспорт пользователей на").":";
		for($i=0;$i<count($srvs);$i++)
			if(parse_url($srvs[$i][2],PHP_URL_HOST)!=$_SERVER['SERVER_NAME'])
				echo "<br/><input type=\"radio\" name=\"srv\" value=\"".$i."\"/>&nbsp;<a href=\"".$srvs[$i][2]."\">".$srvs[$i][1].": ".parse_url($srvs[$i][2],PHP_URL_HOST)."</a>";
		echo "<br/><input type=\"checkbox\" name=\"exp_ful\" value=\"1\"/>&nbsp;".bv7lg("with D & operations","Вместе с D и операциями")."</td></tr>"
			."<tr><td><input type=\"radio\" name=\"a\" value=\"conv_db\"/></td><td>".bv7lg("Convert DB to current version","Обновление БД до последней версии").".</td></tr>"
			."<tr><td><input type=\"radio\" name=\"a\" value=\"snd_em\"/></td><td>".bv7lg("Send message","Отправить сообщение").": <input type=\"text\" name=\"msg_em\" value=\"".$_POST['msg_em']."\"/><br/>".bv7lg("Subject","Тема").": <input type=\"text\" name=\"msg_sb\" value=\"".$_POST['msg_sb']."\"/><br/>E-mail: <input type=\"text\" name=\"em\" value=\"".$_POST['em']."\"/><input type=\"text\" name=\"cust_id\" value=\"".$_POST['cust_id']."\"/></td></tr>"
			."<tr><td><input type=\"radio\" name=\"a\" value=\"clc_pay_acc\"/></td><td>".bv7lg("Calculate the unpaid D.<br/>Cost","Пересчитать неоплаченные D<br/>Цена").": <input type=\"text\" name=\"prc1015\" value=\"".$_POST['prc1015']."\" size=\"5\"/>D ".bv7lg("for 1E+15 operations (unchanged individual payments)","за 1E+15 операц. (без изменения персональных ставок)")."</td></tr>";
	}
	echo'<tr><td><input type="radio" name="a" value="grp_upd"/></td><td>'.bv7lg('Batch editing of databases','Групповое изменение баз').':'
	//	."<br/><input type=\"radio\" name=\"fn\" value=\"den_usr_upd\"/>".bv7lg("Deny clients version ","Запретить клиентов версии ").ua2ver(LastAgent4)
	//	."<br/><input type=\"radio\" name=\"fn\" value=\"enb_usr_upd\"/>".bv7lg("Allow clients version ","Разрешить клиентов версии ").ua2ver(LastAgent4)
		.'<br/><input type="radio" name="fn" value="del_job_old"/>'.bv7lg('Delete tasks, except the last ','Удалить задачи, кроме последних ').' <input type="text" name="jc" value="500"/>'
		.'</td></tr></tbody></table></form></td></tr>';
}
echo "<tr><td><a href=\"logoff.php\">".bv7lg("Logoff","Выход")."</a></td></tr></table></td><td><table border=\"1\" width=\"100%\"><tr><td>";
$err='';
if($_SESSION["rl"]&8)
	switch($_POST['a']){
	case 'clc_pay_acc':
		$sql_srv=bv7dc_select_db();
		if(mysql_result($res=mysql_query("SELECT GET_LOCK('clreq', 10)"), 0) != 1)$err=__FILE__.__LINE__;
		else{
			if (!($res=mysql_query("UPDATE `usrs` SET `pay_acc`=`cnt_acc`*".$_POST['prc1015']."/1000000000000000,`time`=`time` WHERE `prc_gh`=0")))$err=__FILE__.__LINE__;
			mysql_query("SELECT RELEASE_LOCK('clreq')");
		}
		mysql_close($sql_srv);
		break;
	case 'exp_us':
		$sql_srv=bv7dc_select_db();
		if (!($res=mysql_query("SELECT CONCAT_WS(',', `usrs`.`id`, `usrs`.`st`, `usrs`.`rl`, `usrs`.`pw`, `usrs`.`em`, `usrs`.`wm`, `usrs`.`lg`, `usrs`.`pusr_id`, `usrs`.`pr_add_pay`, `usrs`.`prc_gh`, `usrs`.`pr2`, `usrs`.`wm2`) AS `u`"
			.",CONCAT_WS(',', `usrs`.`cnt_acc`, `usrs`.`pay_acc`, `usrs`.`cnt_prev`, `usrs`.`pay_prev`,`usrs`.`cnt2_reserv`,`usrs`.`pay2_reserv`) AS `p`"
			."FROM `usrs`")))exit(__FILE__.__LINE__);
		echo "<form action=\"".$srvs[$_POST['srv']][2]."impus.php\" method=\"post\"><input type=\"hidden\" name=\"id\" value=\"".$_SESSION["id"]."\"/>Password: <input type=\"password\" name=\"pw\" value=\"\"/>";
		if($ful=isset($_POST['exp_ful'])&&$_POST['exp_ful']=="1")
			echo "<input type=\"hidden\" name=\"f\" value=\"1\"/>";
		$i=0;
		while($row=mysql_fetch_assoc($res)){
			echo "<input type=\"hidden\" name=\"u".$i."\" value=\"".$row['u'].($ful?",".$row['p']:"")."\"/>";
			$i++;
		}
		echo "<input type=\"hidden\" name=\"c\" value=\"".$i."\"/><input type=\"submit\" value=\"Export users to ".$srvs[$_POST['srv']][2]."\"/></form>";
		mysql_close($sql_srv);
		break;
	case 'conv_db':
		$sql_srv=bv7dc_select_db();
		if(mysql_result($res=mysql_query("SELECT GET_LOCK('clreq',".cl_lock_timeout.")"),0)!=1)exit(__FILE__.__LINE__);
		mysql_query("CREATE TABLE `jobs` (
			`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, 
			`usr_id` INT UNSIGNED NOT NULL, 
			`op_id` INT UNSIGNED NOT NULL, 
			`st` TINYINT UNSIGNED NOT NULL DEFAULT 0, 
			`par` VARCHAR(254) NOT NULL, 
			`res` VARCHAR(254) NOT NULL, 
			`cnt` BIGINT UNSIGNED NOT NULL,
			`progress` BIGINT UNSIGNED NOT NULL,
			`pay` DOUBLE UNSIGNED NOT NULL DEFAULT 0,
			`minusrs` TINYINT UNSIGNED NOT NULL,
			`par2` VARCHAR(15) NOT NULL,
			`priority` TINYINT NOT NULL,
			`prc_gh` DOUBLE UNSIGNED NOT NULL,
			`timecreate`DATETIME NOT NULL,
			`timefinish`DATETIME NOT NULL,
			`pause_begin`DATETIME NOT NULL,
			`pause_sec`INT UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY(`id`), 
			INDEX(`op_id`, `st`), 
			INDEX (`par` (41)),
			INDEX ( `priority` )
			,INDEX (`st`)
		)");
		mysql_query("CREATE TABLE `subjobs` (
			`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`job_id` INT UNSIGNED NOT NULL,
			`usr_id` INT UNSIGNED NOT NULL,
			`st` TINYINT UNSIGNED NOT NULL DEFAULT 0,
			`time` TIMESTAMP,
			`par` VARCHAR(254) NOT NULL,
			`res` VARCHAR(254) NOT NULL,
			`cnt` BIGINT UNSIGNED NOT NULL,
			`fnd` BOOL NOT NULL,
			`fnd` BOOL NOT NULL,
			PRIMARY KEY(`id`),
			INDEX(`job_id`),
			INDEX(`st`),
			INDEX(`time`)
			,INDEX`job_st`(`job_id`,`st`)
			,INDEX `job_st_usr`(`job_id`,`st`,`usr_id`)
			,INDEX `job_st_time`(`job_id`,`st`,`time`)
			,INDEX `st_usr_ym`(`st`,`usr_id`,`ym`)
			,INDEX `st_ym_usr`(`st`,`ym`,`usr_id`)
			,INDEX `usr_time`(`usr_id`,`time`)
			,INDEX`job_usr`(`job_id`,`usr_id`)
		)");
		mysql_query("CREATE TABLE `grp`(`id` INT UNSIGNED NOT NULL AUTO_INCREMENT
			,`st` TINYINT UNSIGNED NOT NULL DEFAULT 0
			,`name` VARCHAR(254) NOT NULL
			,`cust_id` INT UNSIGNED NOT NULL DEFAULT 0
			,`only_cust` BOOL NOT NULL DEFAULT 0
			,PRIMARY KEY(`id`)
		)");
		mysql_query("CREATE TABLE `def`(`grp_id` INT UNSIGNED NOT NULL
			,`prc_gh` DOUBLE UNSIGNED NOT NULL
			,`froz_nt` BOOL NOT NULL DEFAULT 0
		)");
		mysql_query("CREATE TABLE IF NOT EXISTS `arc_sj` LIKE `subjobs`");
		mysql_query("CREATE TABLE IF NOT EXISTS `arc_j` LIKE `jobs`");
		$res=mysql_query("OPTIMIZE TABLE `usrs`,`subjobs`,`jobs`,`grp`,`arc_sj`,`arc_j`");
		mysql_query("SELECT RELEASE_LOCK('clreq')");
		mysql_close($sql_srv);
		echo 'Conversion completed';
		break;
	case 'snd_em':
		$err.="<br/>";
		$em=$_POST['em'];
		if($_POST['em']==''&&$_POST['cust_id']!=''){
			$sql_srv=bv7dc_select_db();
			$err.=(bv7mailsfs_adm(14,$_POST['msg_sb'],$_POST['msg_em'],array(array("test.txt","content test.txt"),array("test2.txt","content test2.txt")),$_POST['cust_id'])?bv7lg("Message sending", "Сообщение отправлено"):bv7lg("Message not sending","Почтовое сообщение не отправлено"))
				."<br/>E-Mail: ".$em."<br/>Subject: ".$_POST['msg_sb']."<br/>Message:<br/>".$_POST['msg_em']."<br/>";
			mysql_close($sql_srv);
		}else $err.=(bv7mail($em,$_POST['msg_sb'],$_POST['msg_em'])?bv7lg("Message sending", "Сообщение отправлено"):bv7lg("Message not sending","Почтовое сообщение не отправлено"))
			."<br/>E-Mail: ".$em."<br/>Subject: ".$_POST['msg_sb']."<br/>Message:<br/>".$_POST['msg_em']."<br/>";
		break;
	}
if($_SESSION["rl"]&12)
	switch($_POST['a']){
	case 'grp_upd':
	case 'add_cust':
		$sql=bv7dc_select_db();
		$query="";
		switch($_POST['a']){
		case 'grp_upd':
			switch($_POST['fn']){
/*			case 'den_usr_upd':
				$query="UPDATE `usrs` SET `st`=0,`time`=`time` WHERE `rl`=1 AND `ua`='".LastAgent4."' AND `st`=1";
				break;
			case 'enb_usr_upd':
				$query="UPDATE `usrs` SET `st`=1,`time`=`time` WHERE `rl`=1 AND `ua`='".LastAgent4."' AND `st`=0";
				break;
*/			case 'del_job_old':
				$query="SELECT `id` FROM `jobs` WHERE 1 ORDER BY `id` DESC LIMIT 1";
				if(!($res=mysql_query($query)))exit(__FILE__.__LINE__);
				if(!($row=mysql_fetch_assoc($res)))exit(__FILE__.__LINE__);
				$job_id=$row['id']-$_POST['jc'];
				$query="DELETE FROM `subjobs` WHERE `job_id`<".$job_id;
				if(!($res=mysql_query($query)))exit($query."<br/>".__FILE__.__LINE__);
				$query="DELETE FROM `jobs` WHERE `id`<".$job_id;
				break;
			}
			break;
		case 'add_cust':
			echo "Email: ".($em=filter_var($_POST['cust_em'],FILTER_VALIDATE_EMAIL));
			if($em=="")$err="Error: Email";
			else $query="INSERT `usrs` SET `st`=1,`rl`=2,`pw`='".md5($pw=bv7pw_gen())."',`em`='".$em."',`notify`=1";
			break;
		}
		$err.="<br/>";
		if($res=mysql_query($query)){
			switch($_POST['a']){
			case 'add_cust':
				echo "<br/>Login: ".mysql_insert_id()."<br/>Password: ".$pw;
				break;
			}
			$err.="The operation completed successfully";
		}else $err.="Error: ".__FILE__.__LINE__;
		mysql_close($sql);
		break;
	case 'gen_pw':
		echo 'Password: '.($pw=bv7pw_gen($_POST['pw_len'])).'<br/>Hash MD5: '.md5($pw);
		break;
	case 'gen_hs':
		echo "[IMEI]<br/>".$_POST['iemi']."<br/>[TARGET_HASH]<br/>".$hs."<br/>[MASTER_SP_CODE]<br/>".$mspc;
		break;
	case 'gen_nck':
		echo "[TARGET_HASH]<br/>".$hs."<br/>[MASTER_SP_CODE]<br/>".$mspc."<br/>[NCK]<br/>".nck($hs,$mspc,"<br/>");
		break;
	case 'snd_res':
		$sql=bv7dc_select_db();
		if(!($res=mysql_query("SELECT`par2`,SUBSTRING_INDEX(`par`,':',1)AS`ph`,`res`FROM`jobs`WHERE`st`<>1 AND`res`<>''AND`timefinish`BETWEEN'".cBiDTMySqlFrom('sr')."'AND'".cBiDTMySqlTo('sr')."'".($_POST['srcust']?"AND`usr_id`=".($_POST['srcust']+0):""))))exit(__FILE__.__LINE__);
		while($row=mysql_fetch_assoc($res)){
			$fls[]=array($row['par2'].'.cod','[IMEI]\r\n'.$row['par2'].'\r\n[TARGET_HASH]\r\n'.$row['ph'].'\r\n[MASTER_SP_CODE]\r\n'.($mspc=mspc($row['res'])));
			$fls[]=array($row['par2'].'_nck.txt',nck($row['ph'],$mspc,"\r\n"));
		}
		mysql_close($sql);
		if(isset($fls[0][0]))bv7mailfs($_SESSION['em'],'Results for '.cBiDTUsr('sr').' Customer '.$_POST['srcust'],'',$fls,$sj['cust_id']);
		else $err='Nothing Emailed';
		break;
	}
if($iSrvNew>=0)echo "<br/><font color=\"red\">".bv7lg("Server moved to ", "Сервер переехал на ")."<a href=\"".$srvs[$iSrvNew][2]."\">".$srvs[$iSrvNew][2]."</a></font>";
echo "<br/><font color=\"red\">".$err."</font></td></tr>";
if($_SESSION["rl"]&12){
	echo "<tr><td>".bv7lg("Servers", "Список северов").":";
	for($i=0;$i<count($srvs);$i++)
		echo "<br/>".(($host=parse_url($srvs[$i][2],PHP_URL_HOST))==$_SERVER['SERVER_NAME']?$srvs[$i][1].": ".$host:"<a href=\"".$srvs[$i][2]."\">".$srvs[$i][1].": ".$host."</a>");
	echo "</td></tr>";
}
if($_SESSION["rl"]&8)
	echo "<tr><td><table border=\"1\"><caption>".bv7lg("System variables","Системные переменные")."</caption><thead><tr><th>".bv7lg("Name","Имя")."</th><th>".bv7lg("Value","Значение")."</th></tr></thead><tbody>"
		."<tr><td>\$_SERVER['HTTP_HOST']</td><td>".$_SERVER['HTTP_HOST']."</td></tr>"
		."<tr><td>\$_SERVER['PHP_SELF']</td><td>".$_SERVER['PHP_SELF']."</td></tr>"
		."<tr><td>\$_SERVER['SERVER_NAME']</td><td>".$_SERVER['SERVER_NAME']."</td></tr>"
		."<tr><td>\$_SERVER['REQUEST_URI']</td><td>".$_SERVER['REQUEST_URI']."</td></tr>"
		."<tr><td>\$_SERVER ['PHP_AUTH_USER']</td><td>".$_SERVER['PHP_AUTH_USER']."</td></tr>"
		."<tr><td>999999999999999 + 1</td><td>".((999999999999999 - 1)*10)."</td></tr>"
		."<tr><td>Server Time</td><td>".date("Y-m-d h:m:s")."</td></tr>"
		."<tr><td>Server Timezone</td><td>".date("Z")."</td></tr>"
		."<tr><td>sprintf(\"%.0f\",999999999999999)</td><td>".sprintf("%.0f",999999999999999)."</td></tr>"
		."<tr><td>str_pad(\"1\",floor(log10(1))+1,\"0\")</td><td>".str_pad("1",floor(log10(1))+1,"0")."</td></tr>"
		."<tr><td>microtime(\"1\")</td><td>".microtime(1)."</td></tr>"
		."</tbody></table></td></tr>";
echo "</table></td></tr></tbody></table></body></html>";
?>