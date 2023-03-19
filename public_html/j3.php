<?php
require'auth.php';
if(!($_SESSION["rl"]&(14)))exit(__FILE__.__LINE__);
$is_cust=($_SESSION["rl"]&14)==2;
if(isset($_GET['jd'])){
	$sql_srv=bv7dc_select_db();
	if(!($res=mysql_query("SELECT`par`,`par2`,`res`FROM`jobs`WHERE`id`=".$_GET['jd']." AND`res`<>''".($is_cust?"AND`usr_id`=".$_SESSION["id"]." ":"")."LIMIT 1"))
		||!($row=mysql_fetch_assoc($res))
	)exit;
	$pars=explode("-",$row['par'],1);
	$pars1=explode(':',$pars[0],2);
	header("Content-type: text/plain; charset=windows-1251");
	header("Content-Disposition: attachment; filename=\"".($iemi=($row['par2']==''?substr($pars1[1],2,14).'X':$row['par2'])).".cod\"");
	exit("[IMEI]\r\n".$iemi."\r\n[TARGET_HASH]\r\n".$pars1[0]."\r\n[MASTER_SP_CODE]\r\n".'0'.substr(chunk_split($row['res'],1,'0'),0,-1));
}else if(isset($_GET['jc'])){
	$sql_srv=bv7dc_select_db();
	if(!($res=mysql_query("SELECT`par`,`par2`,`res`FROM`jobs`WHERE`id`=".$_GET['jc']." AND`res`<>''".($is_cust?"AND`usr_id`=".$_SESSION["id"]." ":"")."LIMIT 1"))
		||!($row=mysql_fetch_assoc($res))
	)exit;
	$pars=explode("-",$row['par'],1);
	$pars1=explode(':',$pars[0],2);
	header("Content-type: text/plain; charset=windows-1251");
	header("Content-Disposition: attachment; filename=\"".($iemi=($row['par2']==''?substr($pars1[1],2,14).'X':$row['par2']))."_nck.txt\"");
	exit(nck($pars1[0],mspc($row['res']),"\r\n"));
}else if(isset($_POST['jb'])){
	$sql_srv=bv7dc_select_db();
	if(isset($_POST['st'])){
		if(($_POST['st']==1||$_POST['st']==5)
			&&mysql_query("UPDATE`jobs`SET`st`=".$_POST['st'].",".($_POST['st']==5?"`pause_begin`=NOW()WHERE`st`=1":"`pause_sec`=`pause_sec`+TIMESTAMPDIFF(SECOND,`pause_begin`,NOW())WHERE`st`=5")." AND`id`=".$_POST['jb']." LIMIT 1")
			&&mysql_affected_rows()>0
		)exit("<!DOCTYPE html><html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/><body>Ok</body></html>");
	}else{
		if(!($res=mysql_query("SELECT`res`,IF(`cnt`=0,0,ROUND(`progress`/`cnt`*100))AS`pr_jb`,`st`FROM`jobs`WHERE`id`=".$_POST['jb']." ".($is_cust?"AND`usr_id`=".$_SESSION["id"]." ":"")."LIMIT 1"))
			||!($row=mysql_fetch_assoc($res))
		)exit;
		echo"<!DOCTYPE html><html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/><body>";
		if($row['res'])echo"MASTER_SP_CODE ".'0'.substr(chunk_split($row['res'],1,'0'),0,-1)."<br />";
		$stn=array(0=>"Not checked",1=>"Runs",2=>"Ready",3=>"Closed",4=>"Deleted",5=>"Paused");
		exit("Progress ".$row['pr_jb']."%<br />State ".$stn[$row['st']]."</body></html>");
	}
}
require'prior_h.php';
require'ctrl5.php';
function html_red($if,$inner){
    if($if)$inner="<font color=\"red\">".$inner."</font>";
	return $inner;
}
function input3st($st,$name){return "<input type=\"radio\" name=\"st\" value=\"".$st."\"/>&nbsp;".$name."<br />";}
function inputText3st($st,$id){
	global $vst;
	if($st==4)
		$input="<font color=\"red\">".bv7lg("The task is removed","Задача удалена")."</font>";
	else{
		$input="";
		if($_SESSION["rl"]&12)switch($st){
		case 3:
			$input.=input3st(4,bv7lg("Remove","Удалить"))
				.input3st(0,bv7lg ("Restore","Восстановить"));
			break;
		case 1:
			$input.=input3st(5,bv7lg("Pause","Приостановить"))
				.input3st(0,bv7lg("Stop","Остановить"));
			break;
		case 0:
			$input.=input3st(1,bv7lg("Run","Запустить"))
				.input3st(3,bv7lg("Close","Закрыть"));
			break;
		case 2:
			$input.=input3st(3,bv7lg("Show for Customer","Показать заказчику"));
			break;
		case 5:
			$input.=input3st(1,bv7lg("Run","Запустить"));
			break;
		}else switch($st){
		case 1:
			$input.=input3st(5,bv7lg("Pause","Приостановить"));
			break;
		case 5:
			$input.=input3st(1,bv7lg("Run","Запустить"));
			break;
		}
		$input.="<input type=\"hidden\" name=\"a\" value=\"st\"/><input type=\"hidden\" name=\"k\" value=\"".$id."\"/>";
	}
	return $input;
}
function inputText3pay($prc_gh,$id){return "<input type=\"hidden\" name=\"a\" value=\"pg\"/><input type=\"text\" name=\"pg\" value=\"".$prc_gh."\" size=\"5\"/><input type=\"hidden\" name=\"k\" value=\"".$id."\"/>";}
echo "<!DOCTYPE html><html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/><link rel=\"stylesheet\" type=\"text/css\" href=\"j3.css\"/>";
require "ctrl1.php";
if($_SESSION['lg']=='ru')$vst=array(0=>"<font color=\"gray\">Не проверено</font>",1=>"<font color=\"green\">Выполняется</font>",2=>"<font color=\"red\">Результат есть</font>",3=>"<font color=\"black\">Закрыто</font>",4=>"<font color=\"red\">Удалена</font>",5=>"<font color=\"gray\">Приостановлено</font>");
else $vst=array(0=>"<font color=\"gray\">Not checked</font>",1=>"<font color=\"green\">Runs</font>",2=>"<font color=\"red\">Ready</font>",3=>"<font color=\"black\">Closed</font>",4=>"<font color=\"red\">Deleted</font>",5=>"<font color=\"gray\">Paused</font>");
echo "<title>Tasks</title></head><body>";
$lst=!($_SESSION["rl"]&12)||!isset($_GET['k']);
if(!$lst)$job_id=$_GET['k'];
$sql_srv = bv7dc_select_db();
if(isset($_GET["a"])){
	switch($_GET["a"]){
	case 'st':
		$row=mysql_fetch_assoc(mysql_query("SELECT`jobs`.`par`,`jobs`.`op_id`,`usrs`.`rl`,`usrs`.`em`,`jobs`.`st`FROM`jobs`LEFT JOIN`usrs`ON`jobs`.`usr_id`=`usrs`.`id`WHERE`jobs`.`id`=".$_GET['k']." LIMIT 1"));
		if($_GET['st']==4)$ok=$_SESSION["rl"]&12
			&&mysql_query("DELETE FROM `jobs` WHERE `id`=".$_GET['k']." LIMIT 1")
			&&mysql_query("DELETE FROM `subjobs` WHERE `job_id`=".$_GET['k']);
		else if($ok=($_SESSION["rl"]&12||$_GET['st']==1&&$row['st']==5||$_GET['st']==5&&$row['st']==1)
			&&($_GET['st']!=1
				||mysql_fetch_assoc(mysql_query("SELECT `job_id` FROM `subjobs` WHERE `job_id`=".$_GET['k']." LIMIT 1"))
				||mysql_query("INSERT`subjobs`SET`job_id`=".$_GET['k'].",`par`='".$row['par']."',`ym`=EXTRACT(YEAR_MONTH FROM NOW())"))
		){
			$query="UPDATE`jobs`SET";
			if($_GET['st']==5)$query.="`pause_begin`=NOW(),";
			else if($row['st']==5)$query.="`pause_sec`=`pause_sec`+TIMESTAMPDIFF(SECOND,`pause_begin`,NOW()),";
			$ok=mysql_query($query."`st`=".$_GET['st']." WHERE`id`=".$_GET['k']." LIMIT 1");
		}
		if($ok&&mysql_affected_rows()>0){
			ctrl3handleResponse($vst[$_GET['st']],inputText3st($_GET['st'],$_GET['k']));
		}
		break;
	}
	if($_SESSION["rl"]&12){
		switch($_GET["a"]){
		case 'pg':
			if(mysql_query("UPDATE `jobs` SET `pay`=".$_GET['pg']."*".sl3_pay_per_gh24.",`prc_gh`=".$_GET['pg']." WHERE `id`=".$_GET['k']." LIMIT 1")&&mysql_affected_rows());
				ctrl3handleResponse($_GET['pg'],inputText3pay($_GET['pg'],$_GET['k']));
			break;
		case 'pr':
			if(mysql_query("UPDATE `jobs` SET `priority`=".($prior=filter_var($_GET['pr'],FILTER_SANITIZE_NUMBER_INT))." WHERE `id`=".($key=filter_var($_GET['k'],FILTER_SANITIZE_NUMBER_INT))." LIMIT 1")&&mysql_affected_rows())
				ctrl3handleResponse($prior,inputPrior($prior,$key));
			break;
		case 'mu':
			if(mysql_query("UPDATE `jobs` SET `minusrs`=".($val=filter_var($_GET['mu'],FILTER_SANITIZE_NUMBER_INT))." WHERE `id`=".($key=filter_var($_GET['k'],FILTER_SANITIZE_NUMBER_INT))." LIMIT 1")&&mysql_affected_rows())
				ctrl3handleResponse(viewMU($val),inputMU($val,$key));
			break;
		case 'rclc':
			$s=isset($_GET["fusr"])?" AND `usr_id`=".$_GET['fusr']:"";
			if(isset($_GET["c"])&&$_GET["c"]=="1"){
				mysql_query("INSERT INTO `arc_sj` SELECT * FROM `subjobs` WHERE `job_id`=".$job_id." AND `st`=3".$s);
				mysql_query("INSERT INTO `arc_j` SELECT * FROM `jobs` WHERE `id`=".$job_id." LIMIT 1");
			}
			if(mysql_query("UPDATE `subjobs` SET `st`=0,`time`=`time`WHERE `job_id`=".$job_id." AND `st`=3".$s)&&mysql_affected_rows()>0){
				if(isset($_GET["fusr"]))ctrl2handleResponse(bv7lg("Is recalculated","Пересчитывается"));
				else ctrl6handleResponse();
			}
			break;
		}
	}
	exit;
}
$iemi=filter_var($_GET['iemi'],FILTER_SANITIZE_NUMBER_INT);
$query="SELECT`jobs`.`id`,`jobs`.`st`,`jobs`.`usr_id`,`jobs`.`op_id`,`jobs`.`par`,`jobs`.`res`,`jobs`.`cnt`,`jobs`.`progress`,`jobs`.`minusrs`,`jobs`.`par2`,`jobs`.`priority`"
	.",`jobs`.`pay`/".sl3_pay_per_gh24." AS `prc_gh`"
	.",IF(`jobs`.`res`<>''OR(`jobs`.`progress`>=`jobs`.`cnt`AND`jobs`.`cnt`>0),1,0)AS`pr_c`"
	.",IF(`jobs`.`res`<>''OR(`jobs`.`progress`>=`jobs`.`cnt`AND`jobs`.`cnt`>0),ROUND(`jobs`.`progress`/`jobs`.`cnt`*100),0)AS`pr_s`"
	.",IF(`jobs`.`cnt`=0,0,ROUND(`jobs`.`progress`/`jobs`.`cnt`*100))AS`pr_jb`"
	.",TIMESTAMPDIFF(SECOND,`jobs`.`timecreate`,IF(`jobs`.`st`=1,NOW(),IF(`jobs`.`st`=5,`jobs`.`pause_begin`,`jobs`.`timefinish`)))-`jobs`.`pause_sec`AS`timecalc`"
	.",IF(`jobs`.`timefinish`,TIMESTAMPADD(SECOND,".($_GET['tz']*3600-date('Z')).",`jobs`.`timefinish`),'')AS`usrfinish`";
if(!$lst)$query.=",SUBSTRING_INDEX(`jobs`.`par`,':',1)AS`ph`"
	.",IF(`jobs`.`par2`='',CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(`jobs`.`par`,'-',1),':',-1),'X'),`jobs`.`par2`)AS`pi`"
	.",SUBSTRING_INDEX(SUBSTRING_INDEX(`jobs`.`par`,'-',2),'-',-1)AS`pf`"
	.",SUBSTRING_INDEX(`jobs`.`par`,'-',-1)AS`pl`";
if(!$is_cust)$query.=",`usrs`.`em`";
$query.="FROM`jobs`";
if($iemi)$query.="USE INDEX(`par2`)";
if(!$is_cust)$query.="LEFT JOIN`usrs`ON`jobs`.`usr_id`=`usrs`.`id`";
$query.="WHERE".($is_cust?"`jobs`.`usr_id`=".$_SESSION["id"]:(!$lst?"`jobs`.`id`=".$job_id:" 1"))." ";
if($iemi)$query.="AND`par2`='".$iemi."'";
$query.="ORDER BY`jobs`.`id`DESC LIMIT ";
if($lst){
	if(isset($_GET['o']))$query.=$_GET['o'].',';
	$query.=isset($_GET['l'])?$_GET['l']:"100";
}else $query.="1";
$res=mysql_query($query);
if(!$res)exit($query.'<br />'.__FILE__.__LINE__);
if($lst)ctrl4();
$ssm=$is_cust&&$_SESSION["muf"];
echo "<table border=\"1\" class=\"j\"><caption>".($lst?bv7lg("Tasks","Задачи").($iemi?" IMEI:".$iemi:""):bv7lg("Task","Задача"))."</caption><thead><tr>"
	."<th>№</th>"
	."<th>".bv7lg("ID","Код")."</th>"
	."<th>".bv7lg("State","Состояние")."</th>";
if($_SESSION["rl"]&12)
	echo "<th>D/GH/s/24</th><th>".bv7lg("Prio-<br />rity","Прио<br />ритет")."</th><th>".bv7lg("Cli-<br />ents","Кли-<br />ентов")."</th>";
else if($ssm) echo "<th>Speed<br />Mode</th>";
echo "<th>".bv7lg("Customer<br />ID, Email","Заказчик<br />Код, почта")."</th>";
if(!$lst)echo "<th>".bv7lg("Operation","Операция")."</th>";
echo "<th>".bv7lg("Options","Параметры")."</th>"
	."<th>".bv7lg("Result","Результат")."</th>";
if(!$lst)echo "<th>".bv7lg("operations","Операций")."</th>";
echo'<th>'.bv7lg('% in<br />the task','% в<br />задаче').'</th>'
	.'<th>'.bv7lg('Time<br />h:m','Время<br />ч:м').'</th>'
	.'<th>'.bv7lg('End<br />time','Время<br />завершения').' (UTC'.($_GET['tz']>=0?'+':'').$_GET['tz'].')</th>'
	.'</tr></thead><tbody>';
$pr_s=$pr_c=$tc=0;
while ($job=mysql_fetch_assoc($res)){
	$pr_c+=$job['pr_c'];
	$pr_s+=$job['pr_s'];
	if($job['st']==1)$tc=max($tc,$job['timecalc']);
	echo "<tr>"
		."<td>".$pr_c."</td>"
		."<td>".(($lst&&$_SESSION["rl"]&12)?"<a href=\"".$_SERVER['PHP_SELF']."?k=".$job['id']."\" target=\"_blank\">".$job['id']."</a>":$job['id'])."</td>"
		."<td>";
	ctrl3($vst[$job['st']],inputText3st($job['st'],$job['id']),bv7lg('Apply','Применить'),$_SESSION["rl"]&12||$job['st']==1||$job['st']==5);
	
	if($_SESSION["rl"]&12){
		echo "<td>";
		ctrl3($job['prc_gh'],inputText3pay($job['prc_gh'],$job['id']),bv7lg("Apply","Применить"));
		echo "</td><td>";
		ctrl3($job['priority'],inputPrior($job['priority'],$job['id']),bv7lg("Apply","Применить"));
		echo "</td><td>";
		ctrl3(viewMU($job['minusrs']),inputMU($job['minusrs'],$job['id']),bv7lg("Apply","Применить"));
		echo "</td>";
	}else if($ssm)echo "<td>".($job['minusrs']>=$_SESSION["muf"]?"Fast":"Slow")."</td>"; 
	echo "<td>".$job['usr_id']."<br />".($is_cust?$_SESSION['em']:$job['em'])."</td>";
	if($lst)echo "<td>".$job['par2']."</td>";
	else echo "<td>".bv7lg("SL3 decr.","Декод.SL3")."</td>"
		."<td><font size=\"1\">".bv7lg("HASH","ХЕШ").": ".$job['ph']."<br />IMEI: ".$job['pi']."<br />"
			.bv7lg("Password start","Начальный пароль").": ".$job['pf']."<br />"
			.bv7lg("Password end","Конечный пароль",$lg).": ".$job['pl']."</font></td>";
	echo "<td>".($job['res']?"<a href=\"".$_SERVER['PHP_SELF']."?jd=".$job['id']."\">".$job['res']."</a> <a href=\"".$_SERVER['PHP_SELF']."?jc=".$job['id']."\">NCK</a>":$job['res'])."</td>";
	if(!$lst)echo'<td align="right">'.$job['progress'].'</td>';
	echo'<td align="right">'.$job['pr_jb'].'%</td>'
		.'<td align="right">'.floor($job['timecalc']/3600).':'.substr('0'.(floor($job['timecalc']/60)%60),-2).'</td>'
		.'<td>'.$job['usrfinish'].'</td>'
		.'</tr>';
	if(!$lst)break;
}
if($lst)
	echo '<tr class="total">'
		.'<td align="right">'.$pr_c.'</td>'
		.'<td colspan="'.(5+($_SESSION['rl']&12?3:($ssm?1:0))+($lst?0:2)).'">'.bv7lg('Total','Итого').'</td>'
		.'<td align="right">'.($pr_c?round($pr_s/$pr_c):'0').'%</td>'
		.'<td align="right">'.floor($tc/3600).':'.substr('0'.(floor($tc/60)%60),-2).'</td>'
		.'<td></td>'
		.'</tr>';
echo "</tbody></table>";
if(!$lst&&$job){
	if($job["res"]!=""
		&&($res=mysql_query("SELECT`arc_sj`.`usr_id`,`arc_sj`.`par`FROM`arc_sj`USE INDEX(`job_st`)LEFT JOIN`arc_j`ON`arc_sj`.`job_id`=`arc_j`.`id`WHERE`arc_sj`.`job_id`=".$job["id"]." AND`arc_j`.`res`=''AND`arc_sj`.`st`=3 AND ".strrev($job["res"])." BETWEEN SUBSTRING_INDEX(SUBSTRING_INDEX(`arc_sj`.`par`,'-',2),'-',-1)AND SUBSTRING_INDEX(`arc_sj`.`par`,'-',-1)LIMIT 1"))
		&&($row=mysql_fetch_assoc($res))){
		echo"<p><font color=\"red\">Client ID ".$row["usr_id"]." return wrong result!</font> <a href=\"ic.php?k=".$row["usr_id"]."\" target=\"ic\">Analysis of the Client</a></p>";
		if($_SESSION["rl"]&8)echo"<p>".$row["par"]."</p>";
	}
	$query="SELECT `subjobs`.`usr_id`,`jobs`.`progress`,`jobs`.`cnt` as `job_cnt`,`usrs`.`em`,`usrs`.`lg`,`usrs`.`mhs`"
		.",SUM(`subjobs`.`cnt`*IF(`subjobs`.`st`=3,1,0)) as `usr_cnt`"
		.",SUM(`subjobs`.`cnt`*IF(`subjobs`.`st`=1,1,0)) as `wait_cnt`"
		.",TIMESTAMPDIFF(SECOND,MAX(`subjobs`.`time`),NOW()) as `timeout`"
		.",TIMESTAMPDIFF(SECOND,MIN(`subjobs`.`time`),NOW()) as `timeout1`"
		.",SUM(IF(`jobs`.`progress`=0 OR `subjobs`.`st`<>3,0,`subjobs`.`cnt`/`jobs`.`progress`*100)) AS `pr_op`"
		.",SUM(IF(`jobs`.`cnt`=0 OR `subjobs`.`st`<>3,0,`subjobs`.`cnt`/`jobs`.`cnt`*100)) AS `pr_jb`"
		." FROM `subjobs`USE INDEX(`job_usr`) LEFT JOIN `usrs` ON `subjobs`.`usr_id`=`usrs`.`id` LEFT JOIN `jobs` ON `subjobs`.`job_id`=`jobs`.`id`"
		." WHERE `subjobs`.`job_id`=".$job_id." AND`jobs`.`id`=".$job_id." AND (`subjobs`.`st`=3 OR `subjobs`.`st`=1) AND `subjobs`.`usr_id`<>0"
		." GROUP BY `subjobs`.`usr_id`";
	$res = mysql_query ($query);
	if(!$res) exit(__FILE__.__LINE__);
	echo "<table border=\"1\"><caption>".bv7lg("Executors","Исполнители")."</caption><thead><tr>"
		."<th>№</th>"
		."<th>".bv7lg("ID","ИД")."</th>"
		."<th>".bv7lg("Email","Электронная<br />почта")."</th>"
		."<th>".bv7lg("operations<br />expected","Ожидаемые<br />операции")."</th>"
		."<th>".bv7lg("operations","Операций")."</th>"
		."<th>".bv7lg("% оf<br />operations","% среди<br />операций")."</th>"
		."<th>".bv7lg("% in the<br />task","% в<br />задаче")."</th>"
		."<th>MH/s</th>"
		."<th>".bv7lg("Timeout<br />(min:sec)","Тайм-аут<br />(мин:сек)")."</th>"
		."<th>".bv7lg("Timeout from login<br />(hour:min:sec)","Тайм-аут с момента подкл.<br />(час:мин:сек)")."</th>"
		."<th>".bv7lg("Effectiveness","Результативность")."</th>"
		."</tr></thead><tbody>";
	$mhs=$cnt=$n=$wcnt=0;
	while($row=mysql_fetch_assoc($res)){
	    $mhs+=$row['mhs'];
	    $cnt+=$row['usr_cnt'];
	    $wcnt+=$row['wait_cnt'];
	    $n++;
	    echo "<tr>"
	    	."<td align=\"right\">".$n."</td>"
	    	."<td align=\"right\">".$row['usr_id']."</td>"
	    	."<td><a href=\"/e1.php?k=".$row['usr_id']."\">".$row['em']."</a></td>"
	    	."<td align=\"right\">".$row['wait_cnt']."</td>"
	    	."<td align=\"right\">";
		if($row['usr_cnt']>0)
			ctrl2($row['usr_cnt']."&nbsp;"
				,bv7lg("Repeat the calculation...","Пересчитать...")
				,"<input type=\"hidden\" name=\"a\" value=\"rclc\"/>"
					."<input type=\"hidden\" name=\"k\" value=\"".$job_id."\"/>"
					."<input type=\"hidden\" name=\"fusr\" value=\"".$row['usr_id']."\"/>"
					."<input type=\"checkbox\" name=\"c\" value=\"1\" checked=\"checked\"/>Copy to Archive"
				,bv7lg("Repeat the calculation","Пересчитать"));
		else
			echo "0";
		echo '</td>'
			.'<td align="right">'.round($row['pr_op']).'%</td>'
			.'<td align="right">'.round($row['pr_jb']).'%</td>'
			.'<td align="right"><a href="/s2.php?k='.$row['usr_id'].'" target="_blank"'.($row['mhs']<mhs_min||$row['mhs']>mhs_max?' class="red"':'').'>'.$row['mhs'].'</a></td>'
			.'<td align="right"'.($row['timeout']>=cl_timeout?' class="red"':'').'>'.floor($row['timeout']/60).':'.substr('0'.$row['timeout']%60,-2).'</td>'
			.'<td align="right">'.floor($row['timeout1']/60/60).':'.substr('0'.floor($row['timeout1']/60)%60,-2).':'.substr('0'.$row['timeout1']%60,-2).'</td>'
			.'<td><a href="/ic.php?k='.$row['usr_id'].'" target="_blank">Open</a></td>'
			.'</tr>';
	}
	echo "<tr class=\"total\">"
		."<td colspan=\"3\">".bv7lg("Total","Итого")."</td>"
		."<td align=\"right\">".sprintf("%.0f",$wcnt)."</td>"
		."<td align=\"right\">".sprintf("%.0f",$cnt)."<br />";
	ctrl3(bv7lg("Repeat the calculation for all","Пересчитать все")
		,"<input type=\"hidden\" name=\"a\" value=\"rclc\"/>"
			."<input type=\"hidden\" name=\"k\" value=\"".$job_id."\"/>"
			."<input type=\"checkbox\" name=\"c\" value=\"1\" checked=\"checked\"/>Copy to Archive"
		,bv7lg("Repeat the calculation for all","Пересчитать все")
		,$cnt>0
	);
	echo"</td>"
		."<td></td>"
		."<td></td>"
		."<td align=\"right\">".$mhs."</td>"
		."<td></td>"
		."<td></td>"
		."<td><a href=\"/ij.php?k=".$job["id"]."\" target=\"_blank\">Open</a></td>"
		."</tr></tbody></table>";
}
echo "</body></html>";
mysql_close($sql_srv);
?>