<?php
require "auth.php";
if(!($_SESSION["rl"]&12))exit;
require "grp_h.php";
echo "<!DOCTYPE html><html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">";
require "ctrl1.php";
function viewPrc($prc){return $prc;}
function inputPrc($prc){return "<input type=\"hidden\" name=\"a\" value=\"chprc\"/><input type=\"text\" name=\"prc\" value=\"".$prc."\" size=\"11\"/>";}
function viewFNT($fnt){return $fnt?"Denied":"Allowed";}
function inputFNT($fnt){return "<input type=\"hidden\" name=\"a\" value=\"chfnt\"/><select name=\"fnt\" size=\"1\"><option value=\"".($fnt?"0":"1")."\" selected=\"selected\">".($fnt?"Allowed":"Denied")."</option></select>";}
$sql_srv=bv7dc_select_db();
if(isset($_GET['a'])){
	switch($_GET['a']){
	case 'chprc':
		if(mysql_query("UPDATE `def` SET `prc_gh`=".$_GET['prc']." WHERE 1 LIMIT 1")&&mysql_affected_rows())
			ctrl3handleResponse(viewPrc($_GET['prc']),inputPrc($_GET['prc']));
		break;
	case 'chfnt':
		if(mysql_query("UPDATE `def` SET `froz_nt`=".$_GET['fnt']." WHERE 1 LIMIT 1")&&mysql_affected_rows())
			ctrl3handleResponse(viewFNT($_GET['fnt']),inputFNT($_GET['fnt']));
		break;
	case 'chgrp':
		if(mysql_query("UPDATE `def` SET `grp_id`=".$_GET['grp']." WHERE 1 LIMIT 1")&&mysql_affected_rows())
			initGrp();
			ctrl3handleResponse(viewGrp($_GET['grp']),inputGrp($_GET['grp']));
		break;
	}
	echo "</head><body>";
}else{
	echo "<link rel=\"shortcut icon\" href=\"/favicon.ico\" type=\"image/x-icon\"/><link rel=\"shortcut\" href=\"/favicon.ico\" type=\"image/x-icon\"/><title>Settings</title></head><body>";
	initGrp();
	if (!($res=mysql_query("SELECT * FROM `def` LIMIT 1"))||!($row=mysql_fetch_assoc($res)))
		echo bv7lg("DB access error","Ошибка доступа к БД");
	else{
		echo "<table border=\"1\"><caption>Settings</caption><thead><tr><th>Parameter</th><th>Value</th></tr></thead><tbody><tr><td>Group For New Clients</td><td>";
		ctrl3(viewGrp($row['grp_id']),inputGrp($row['grp_id']),'Apply');
		echo "</td></tr><tr><td>Price For Clients For New Tasks (D for 1 GH/s/24)</td><td>";
		ctrl3(viewPrc($row['prc_gh']),inputPrc($row['prc_gh']),'Apply');
		echo "</td></tr><tr><td>Add New Tasks</td><td>";
		ctrl3(viewFNT($row['froz_nt']),inputFNT($row['froz_nt']),'Apply');
		echo "</td></tr></tbody></table>";
	}
}
mysql_close($sql_srv);
echo "</body></html>";
?>