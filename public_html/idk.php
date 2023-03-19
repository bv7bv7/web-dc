<?php
require'auth.php';
if(!($_SESSION["rl"]&14))exit(__FILE__.__LINE__);
$sql_srv=bv7dc_select_db();
echo'<!DOCTYPE html><html><head><title>Results</title><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><link rel="stylesheet" type="text/css" href="ctrl1.css"/>';
require_once'idk_h.php';
echo'</head><body>'.getInputIDK();
if(cBiDTChk()){
	echo'<table border="1"><tbody>';
	if(($res2=mysql_query("SELECT COUNT(*)AS`ct`FROM`jobs`WHERE`st`<>1 AND`res`<>''AND`timefinish`BETWEEN'".($fdt=cBiDTMySqlFrom())."'AND'".($tdt=cBiDTMySqlTo())."'".($_SESSION["rl"]&12?($_GET['cust']?"AND`usr_id`=".($_GET['cust']+0):""):"AND`usr_id`=".$_SESSION["id"])))
		&&($row2=mysql_fetch_assoc($res2))
	){
		echo'<tr><td>Number of results</td><td align="right">'.$row2['ct'].'</td></tr>';
		if(($_SESSION["rl"]&12)
			&&($res=mysql_query("SELECT SUM(`pay_prev`)AS`pays_prev`FROM`usrs`WHERE`timepay`BETWEEN'".$fdt."'AND'".$tdt."'"))
			&&($row=mysql_fetch_assoc($res))
		)echo'<tr><td>Previous payment</td><td align="right">'.number_format($row['pays_prev'],2,".","").'</td></tr>';
	}
	echo'<tr></tr></tbody></table>';
}
echo'</body></html>';
?>