<?php
require "auth.php";
if(!($_SESSION["rl"]&14))exit(__FILE__.__LINE__);
echo "<!DOCTYPE html><html><head><title>Tasks Info</title><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/><link rel=\"stylesheet\" type=\"text/css\" href=\"ctrl1.css\"/>"
	."</head><body></body><form method=\"get\" action=\"".$_SERVER['PHP_SELF']."\"><table border=\"1\"><tbody>"
	."<tr><td>Task ID From</td><td><input type=\"text\" name=\"tf\" value=\"".$_GET['tf']."\" SIZE=\"8\"/></td></tr>"
	."<tr><td>Task ID To</td><td><input type=\"text\" name=\"tt\" value=\"".$_GET['tt']."\" SIZE=\"8\"/></td></tr>";
if($_SESSION["rl"]&12||isset($_GET['tf'])){
	$sql_srv=bv7dc_select_db();
	if($_SESSION["rl"]&12){
		$res=mysql_query("SELECT `id`,`em` FROM `usrs` WHERE `rl`&14 AND `st`=1 ORDER BY `id` DESC");
		echo"<tr><td>Customer</td><td><select name=\"c\" size=\"1\"><option value=\"0\"".((!isset($_GET['c'])||$_GET['c']==0)?" selected=\"selected\"":"").">-- All --</option>";
		while($row=mysql_fetch_assoc($res))
			echo "<option value=\"".$row['id']."\"".(isset($_GET['c'])&&$row['id']==$_GET['c']?" selected=\"selected\"":"").">".$row['id'].' '.$row['em']."</option>";
		echo "</select></td></tr>";
	}
	if(isset($_GET['tf'])){
		$res=mysql_query("SELECT COUNT(*) AS `ct` FROM `jobs` WHERE `id`>=".($_GET['tf']+0).($_GET['tt']!=''?" AND `id`<=".($_GET['tt']+0):"").($_SESSION["rl"]&12?($_GET['c']?" AND `usr_id`=".($_GET['c']+0):""):" AND `usr_id`=".$_SESSION["id"]));
		$row=mysql_fetch_assoc($res);
		echo "<tr><td>Count Of Tasks</td><td>".$row['ct']."</td></tr>";
	}
	mysql_close($sql_srv);
}
echo "<tr></tr></tbody></table><input type=\"submit\"/></form></html>";
?>