<?php
require 'auth.php';
if(!($_SESSION["rl"]&8)||!isset($_GET["run"]))exit;
require'dc_api.php';
echo"<!DOCTYPE html><html><head><link rel=\"shortcut icon\" href=\"/favicon.ico\" type=\"image/x-icon\"/><link rel=\"hortcut\" href=\"/favicon.ico\" type=\"image/x-icon\"/><title>adm</TITLE><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/></head><body>";
switch($_GET['run']){
case "restore_time_sj":
	$sql_srv=bv7dc_select_db();
	if(!($ssj=mysql_query("SELECT * FROM `subjobs` WHERE `st` = 3 AND `time` = '2012-10-22 08:50:04' AND `fnd` = 1")))exit("Error select `subjobs`<br />".__FILE__.__LINE__);
	while($rsj=mysql_fetch_assoc($ssj)){
		if(!($ssj2=mysql_query("SELECT `time` FROM `subjobs` WHERE `id` > ".$rsj["id"]." AND `usr_id`=".$rsj["usr_id"]." AND `time` < '2012-10-22 09:00:04' ORDER BY `id` LIMIT 1")))exit("Error select `subjobs`<br />".__FILE__.__LINE__);
		if(!($rsj2=mysql_fetch_assoc($ssj2)))echo"Not found time for `usr_id`=".$rsj["usr_id"]." `subjobs`.`id`=".$rsj["id"]."<br />";
		else if(!($usj=mysql_query($query="UPDATE `subjobs` SET `time` = '".$rsj2["time"]."' - INTERVAL 10 MINUTE WHERE `id` = ".$rsj["id"])))exit("Error update `time` for `usr_id`=".$rsj["usr_id"]." `subjobs`.`id`=".$rsj["id"]."<br />".$query."<br />".__FILE__.__LINE__);
		else if(mysql_affected_rows()==0)exit("Error update `time` for `usr_id`=".$rsj["usr_id"]." `subjobs`.`id`=".$rsj["id"]."<br />".__FILE__.__LINE__);
	}
	mysql_close($sql_srv);
	break;
case 'dc_AddTaskSL3':
	$sql_srv=bv7dc_select_db();
	echo "Task ID ".dc_AddTaskSL3($_GET['imei'],$_GET['target_hash'],$Error,$_GET['fast']).'<br />'.$Error;
	mysql_close($sql_srv);
	break;
case 'dc_GetMasterSPCode':
	$sql_srv=bv7dc_select_db();
	echo "MASTER_SP_CODE ".dc_GetMasterSPCode($_GET['task_id'],$progress)."<br />Progress ".$progress;
	mysql_close($sql_srv);
	break;
case 'dc_GetMasterSPCode2':
	$sql_srv=bv7dc_select_db();
	echo "MASTER_SP_CODE ".dc_GetMasterSPCode2($_GET['task_id'],$progress,$state)."<br />Progress ".$progress.'<br />State '.$state;
	mysql_close($sql_srv);
	break;
case 'dc_ChangeTaskState':
	$sql_srv=bv7dc_select_db();
	echo "Result ".dc_ChangeTaskState($_GET['task_id'],$_GET['state']);
	mysql_close($sql_srv);
	break;
}
echo"</body><html>";
?>