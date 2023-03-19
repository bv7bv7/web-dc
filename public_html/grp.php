<?php
require "auth.php";
if(!($_SESSION["rl"]&12))exit;
echo "<!DOCTYPE html><html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>";
require "ctrl1.php";
if($_SESSION['lg']=='ru')$vst=array(0=>"<font color=\"gray\">Нет работы</font>",1=>"<font color=\"green\">Есть работа</font>",4=>"<font color=\"black\">Удален</font>");
else $vst=array(0=>"<font color=\"gray\">No Work</font>",1=>"<font color=\"green\">Have Work</font>",4=>"<font color=\"black\">Deleted</font>");
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
function viewText3nm($nm,$id){return $nm==''?'Group '.$id:' '.$nm.' ';}
function inputText3nm($nm,$id){return "<input type=\"hidden\" name=\"a\" value=\"chnm\"/><input type=\"hidden\" name=\"k\" value=\"".$id."\"/><input type=\"text\" name=\"nm\" value=\"".$nm."\" size=\"50\"/>";}
function viewText3cu($cu_id){
	global $cu;
	return isset($cu[$cu_id])?$cu_id.' '.$cu[$cu_id]:'-- Not Set --';
}
function inputText3cu($cu_id,$id){
	global $cu;
	$s="<input type=\"hidden\" name=\"a\" value=\"chcu\"/><input type=\"hidden\" name=\"k\" value=\"".$id."\"/><select name=\"cu\" size=\"1\"><option value=\"0\"".($cu_id?"":" selected=\"selected\"").">-- Not Set --</option>";
	foreach($cu as $key=>$value)
		$s.="<option value=\"".$key."\"".($key==$cu_id?" selected=\"selected\"":"").">".$key.' '.$value."</option>";
	return $s."</select>";
}
function viewText3oc($only_cu){return $only_cu?'Yes':'No';}
function inputText3oc($only_cu,$id){
	$s='<input type="hidden" name="a" value="choc"/><input type="hidden" name="k" value="'.$id.'"/><input type="checkbox" name="oc" value="1"';
	if($only_cu)$s.=' checked="checked"';
	return $s.'/>Yes';
}
function initCu(){
	global $cu;
	if($res=mysql_query("SELECT * FROM `usrs` WHERE `rl`=2 AND `st`=1 ORDER BY `id` DESC"))
		while($row=mysql_fetch_assoc($res))$cu[$row['id']]=$row['em'];
}
function inputText3add(){return "<input type=\"hidden\" name=\"a\" value=\"add\"/>";}
function echoRow($id,$st,$cu_id,$nm,$only_cu){
	global $vst;
	echo "<tr><td>".$id."</td><td>";
	ctrl3($vst[$st],inputText3st($st,$id),'Apply');
	echo "</td><td>";
	ctrl3(viewText3cu($cu_id),inputText3cu($cu_id,$id),'Apply');
	echo "</td><td>";
	ctrl3(viewText3oc($only_cu),inputText3oc($only_cu,$id),'Apply');
	echo "</td><td>";
	ctrl3(viewText3nm($nm,$id),inputText3nm($nm,$id),'Apply');
	echo "</td><td><a href=\"act.php?gr=".$id."\" target=\"act\">Open</a></td></tr>";
}
$sql_srv=bv7dc_select_db();
if(isset($_GET['a'])){
	$when=" WHERE `id`=".$_GET["k"]." LIMIT 1";
	switch($_GET['a']){
	case 'chst':
		if(($_GET["st"]==4
			?($res=mysql_query("SELECT * FROM `usrs` WHERE `rl`&1 AND grp_id=".$_GET["k"]." LIMIT 1"))&&!mysql_num_rows($res)&&mysql_query("DELETE FROM `grp`".$when)
			:mysql_query("UPDATE `grp` SET `st`=".$_GET["st"].$when)
		)&&mysql_affected_rows())
			ctrl3handleResponse($vst[$_GET["st"]],inputText3st($_GET["st"],$_GET["k"]));
		break;
	case 'chnm':
		if(mysql_query("UPDATE `grp` SET `name`='".($nm=urldecode($_GET['nm']))."'".$when)&&mysql_affected_rows())
			ctrl3handleResponse(viewText3nm($nm,$_GET["k"]),inputText3nm($nm,$_GET["k"]));
		break;
	case 'chcu':
		if(mysql_query("UPDATE `grp` SET `cust_id`=".$_GET['cu'].$when)&&mysql_affected_rows())
			initCu();
			ctrl3handleResponse(viewText3cu($_GET['cu']),inputText3cu($_GET['cu'],$_GET["k"]));
		break;
	case 'choc':
		$oc=isset($_GET['oc'])&&$_GET['oc']==1?1:0;
		if(mysql_query("UPDATE `grp` SET `only_cust`=".$oc.$when)&&mysql_affected_rows())
			ctrl3handleResponse(viewText3oc($oc),inputText3oc($oc,$_GET["k"]));
		break;
	case 'add':
		if(mysql_query("INSERT INTO `grp` SET `name`=''")&&mysql_affected_rows())
			$new_id=mysql_insert_id();
			initCu();
			echo "<script type=\"text/javascript\">"
				."var el=window.parent.document.createElement('tr');"
				."el.innerHTML='";
			echoRow($new_id,0,0,'',false);
			echo "';"
				."window.parent.document.getElementById('input').insertBefore(el, null);"
				."</script>";
			ctrl3handleResponse('Add New',inputText3add());
		break;
	}
	echo "</head><body>";
}else{
	echo "<link rel=\"shortcut icon\" href=\"/favicon.ico\" type=\"image/x-icon\"/><link rel=\"shortcut\" href=\"/favicon.ico\" type=\"image/x-icon\"/><title>Groups</title></head><body>";
	initCu();
	if (!($res=mysql_query("SELECT * FROM `grp` ORDER BY `id`")))
		echo bv7lg("DB access error","Ошибка доступа к БД");
	else{
		echo($_SESSION['lg']=='ru'
			?"<table border=\"1\"><caption>Группы клиентов</caption><thead><tr><th>Код</th><th>Состояние</th><th>Заказчик</th><th>Только укзанный заказчик</th><th>Название</th><th>Активность</th></tr></thead><tbody id=\"input\">"
			:"<table border=\"1\"><caption>Groups Of Clients</caption><thead><tr><th>ID</th><th>State</th><th>Customer</th><th>Only this Customer</th><th>Name</th><th>Activity</th></tr></thead><tbody id=\"input\">"
		);
		while($row=mysql_fetch_assoc($res))echoRow($row["id"],$row["st"],$row["cust_id"]+0,$row['name'],$row['only_cust']);
		echo "<tr class=\"total\"></tr></tbody></table>";
		ctrl3('Add New',inputText3add(),"Add New");
	}
}
mysql_close($sql_srv);
echo "</body></html>";
?>