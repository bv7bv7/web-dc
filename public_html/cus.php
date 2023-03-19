<?php
require "auth.php";
if(!($_SESSION["rl"]&30))
	exit;
echo "<!DOCTYPE html><html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">";
require "ctrl1.php";
require "prior_h.php";
require "not_h.php";
require_once'mj_h.php';
if($_SESSION['lg']=='ru')$vst=array(0=>"<font color=\"gray\">Не&nbsp;проверен</font>",1=>"<font color=\"green\">Проверен</font>",4=>"<font color=\"black\">Удален</font>");
else $vst=array(0=>"<font color=\"gray\">Not&nbsp;checked</font>",1=>"<font color=\"green\">Сhecked</font>",4=>"<font color=\"black\">Deleted</font>");
function inputSt($st,$checked=false){
	global $vst;
	$input="<input type=\"radio\" name=\"st\" value=\"".$st."\"";
	if($checked)
		$input.=" checked=\"checked\"";
	return $input."/>&nbsp;".$vst[$st]."<br />";
}
function inputText3st($st,$id){
	global $vst;
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
		$input.=$vst[4];
		break;
	}
	return $input;
}
function viewText3em($em){return ($em=filter_var($em,FILTER_VALIDATE_EMAIL))==''?bv7lg("Empty","Не указан"):$em;}
function inputText3em($em,$id){return "<input type=\"hidden\" name=\"a\" value=\"chem\"/><input type=\"hidden\" name=\"k\" value=\"".$id."\"/><input type=\"text\" name=\"em\" value=\"".$em."\" size=\"50\"/>";}
function inputText3pw($id,$pw=''){return "<input type=\"hidden\" name=\"a\" value=\"chpw\"/><input type=\"hidden\" name=\"k\" value=\"".$id."\"/>".$pw;}
function inputMU_f($mu,$id){return "<input type=\"text\" name=\"muf\" value=\"".$mu."\" size=\"5\"/><input type=\"hidden\" name=\"a\" value=\"muf\"/><input type=\"hidden\" name=\"k\" value=\"".$id."\"/>";}
function inputMJ($mj,$id){return"<input type=\"text\" name=\"mj\" value=\"".$mj."\" size=\"5\"/> (-1=&infin;)<input type=\"hidden\" name=\"a\" value=\"mj\"/><input type=\"hidden\" name=\"k\" value=\"".$id."\"/>";}
$wusr=" WHERE(".($_SESSION["rl"]&12?"`rl`&".(($_SESSION["rl"]&8?4:0)|18)." OR ":"")."`id`=".$_SESSION["id"].")";
$sql_srv=bv7dc_select_db();
if(isset($_GET['a'])){
	$wusr.="AND`id`=".($key=filter_var($_GET['k'],FILTER_SANITIZE_NUMBER_INT))." LIMIT 1";
	switch($_GET['a']){
	case 'chem':
		if(mysql_query("UPDATE `usrs` SET `em`='".$_GET["em"]."',`time`=`time`".$wusr)&&mysql_affected_rows())
			ctrl3handleResponse(viewText3em($_GET["em"]),inputText3em($_GET["em"],$_GET["k"]));
		break;
	case 'chpw':
		if(mysql_query("UPDATE `usrs` SET `pw`='".md5($pw=bv7pw_gen())."',`time`=`time`".$wusr)&&mysql_affected_rows())
			ctrl3handleResponse($pw,inputText3pw($_GET["k"],$pw));
		break;
	case 'n':
		if(mysql_query("UPDATE `usrs` SET `notify`=".$_GET["n"].",`time`=`time`".$wusr)&&mysql_affected_rows())
			ctrl3handleResponse(viewNotify($_GET["n"]),inputNotify($_GET["n"],$_GET["k"]));
		break;
	default:
		if($_SESSION["rl"]&12)switch($_GET['a']){
		case 'chst':
			if(mysql_query(($_GET["st"]==4?"DELETE FROM `usrs`":"UPDATE `usrs` SET `st`=".$_GET["st"].",`time`=`time`").$wusr)&&mysql_affected_rows())
				ctrl3handleResponse($vst[$_GET["st"]],inputText3st($_GET["st"],$_GET["k"]));
			break;
		case 'pr':
			if(mysql_query("UPDATE `usrs` SET `priority`=".($prior=filter_var($_GET['pr'],FILTER_SANITIZE_NUMBER_INT)).",`time`=`time`".$wusr)&&mysql_affected_rows())
				ctrl3handleResponse($prior,inputPrior($prior,$key));
			break;
		case 'prf':
			if(mysql_query("UPDATE `usrs` SET `prior_f`=".($prior=filter_var($_GET['prf'],FILTER_SANITIZE_NUMBER_INT)).",`time`=`time`".$wusr)&&mysql_affected_rows())
				ctrl3handleResponse($prior,inputPrior($prior,$key,true));
			break;
		case 'mu':
			if(mysql_query("UPDATE `usrs` SET `minusrs`=".($val=filter_var($_GET['mu'],FILTER_SANITIZE_NUMBER_INT)).",`time`=`time`".$wusr)&&mysql_affected_rows())
				ctrl3handleResponse(viewMU($val),inputMU($val,$key));
			break;
		case 'muf':
			if(mysql_query("UPDATE `usrs` SET `mu_f`=".($val=filter_var($_GET['muf'],FILTER_SANITIZE_NUMBER_INT)).",`time`=`time`".$wusr)&&mysql_affected_rows())
				ctrl3handleResponse(viewMU($val),inputMU_f($val,$key));
			break;
		case 'mj':
			if(mysql_query("UPDATE`usrs`SET`mj`=".($val=filter_var($_GET['mj'],FILTER_SANITIZE_NUMBER_INT)).",`time`=`time`".$wusr)&&mysql_affected_rows())
				ctrl3handleResponse(viewMJ($val),inputMJ($val,$key));
			break;
		}
	}
	echo "</head><body>";
}else{
	echo "<link rel=\"shortcut icon\" href=\"/favicon.ico\" type=\"image/x-icon\"/><link rel=\"shortcut\" href=\"/favicon.ico\" type=\"image/x-icon\"/><title>User Sets</title></head><body>";
	if (!($res=mysql_query("SELECT * FROM `usrs`".$wusr."ORDER BY `id` DESC")))
		echo bv7lg("DB access error","Ошибка доступа к БД");
	else{
		$tApp=bv7lg("Apply","Применить");
		$tGen=bv7lg("Generate","Сгенерировать");
		$tRS=$_SESSION["rl"]&12?" rowspan=\"2\"":"";
		echo"<table border=\"1\"><caption>".($_SESSION["lg"]=="ru"
			?"Пользователи</caption><thead><tr><th".$tRS.">Код</th><th".$tRS.">Роль</th><th".$tRS.">Состояние</th><th".$tRS.">Email</th><th".$tRS.">Новый<br />пароль"
			:"Users</caption><thead><tr><th".$tRS.">ID</th><th".$tRS.">Role</th><th".$tRS.">State</th><th".$tRS.">Email</th><th".$tRS.">New<br />Password"
		);
		if($_SESSION["rl"]&14){
			echo"</th><th".$tRS.">".($_SESSION["lg"]=="ru"
				?"Отсылать<br />E-mail</th><th".$tRS.">Лимит<br />задач"
				:"Send<br />E-Mails</th><th".$tRS.">Limit of<br />Tasks"
			);
			if($_SESSION['rl']&12)echo'<th colspan="2">'.($_SESSION['lg']=='ru'
				?'Приоритет задач</th><th colspan="2">Клиентов на задачу</th></tr><tr><th>Медленно</th><th>Быстро<th>Медленно</th><th>Быстро'
				:'Priority for Tasks</th><th colspan="2">Clients per Task</th></tr><tr><th>Slow</th><th>Fast<th>Slow</th><th>Fast'
			);
		}
		echo"</th></tr></thead><tbody>";
		while($row=mysql_fetch_assoc($res)){
			echo "<tr><td>".$row["id"]."</td><td>".($row['rl']&8?"Admin":($row['rl']&4?'Moderator':($row['rl']&2?'Customer':($row['rl']&16?'ClientViewer':'???'))))."</td><td>";
			ctrl3($vst[$row["st"]],inputText3st($row["st"],$row['id']),$tApp,$_SESSION["id"]!=$row['id']);
			echo "</td><td>";
			ctrl3(viewText3em($row["em"]),inputText3em($row["em"],$row['id']),$tApp,$_SESSION["id"]!=$row['id']||$_SESSION["rl"]&8);
			echo "</td><td>";
			ctrl3($tGen."...",inputText3pw($row['id']),$tGen);
			echo "</td>";
			if($_SESSION["rl"]&14){
				echo"<td>";
				ctrl3(viewNotify($row["notify"]),inputNotify($row["notify"],$row['id']),$tApp);
				echo"</td><td>";
				if($row['rl']&14)ctrl3(viewMJ($row["mj"]),inputMJ($row["mj"],$row['id']),$tApp,$_SESSION["rl"]&12);
				echo"</td>";
				if($_SESSION["rl"]&12){
					if($row['rl']&14){
						echo "<td>";
						ctrl3($row['priority'],inputPrior($row['priority'],$row['id']),bv7lg("Apply","Применить"));
						echo "</td><td>";
						ctrl3($row['prior_f'],inputPrior($row['prior_f'],$row['id'],true),bv7lg("Apply","Применить"));
						echo "</td><td>";
						ctrl3(viewMU($row['minusrs']),inputMU($row['minusrs'],$row['id']),bv7lg("Apply","Применить"));
						echo "</td><td>";
						ctrl3(viewMU($row['mu_f']),inputMU_f($row['mu_f'],$row['id']),bv7lg("Apply","Применить"));
						echo "</td>";
					}else echo "<td></td><td></td><td></td><td></td>";
				}
			}
			echo "</tr>";
		}
		echo "</tbody></table>";
	}
}
mysql_close($sql_srv);
echo "</body></html>";
?>