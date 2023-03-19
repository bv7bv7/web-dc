<?php
require"auth.php";
if(!($_SESSION["rl"]&12)||!isset($_GET['k']))exit;
$sql_srv=bv7dc_select_db();
if(mysql_result($res=mysql_query("SELECT GET_LOCK('bigquery',10)"),0)!=1
	||!($res=mysql_query("SELECT DISTINCT`usr_id`FROM`subjobs`USE INDEX(`job_st_usr`)WHERE`job_id`=".($_GET['k']+0)." AND`st`=3"))
	||!($r1=mysql_fetch_assoc($res))
){
	mysql_close($sql_srv);
	exit;
}
require"chart.php";
require"ctrl7.php";
echo"<title>TASK: ".($_GET['k']+0)."</title>";
$query="SELECT SUM(`cnt`)/".(sl3_mh*1000000)." AS`th`"
		.",SUM(`fnd`)AS`rc`"
		.",`usr_id`"
	."FROM`subjobs`USE INDEX(`st_ym_usr`)"
	."WHERE`st`=3 AND`ym`=".(floor($ym_cur/12)*100+$ym_cur%12+1)." AND(`usr_id`=".$r1["usr_id"];
while($row=mysql_fetch_assoc($res))$query.=" OR`usr_id`=".$row["usr_id"];
$ym=ctrl7show($ym_cur);
if(!($res=mysql_query($query.")GROUP BY 3"))){
	mysql_close($sql_srv);
	exit("</head><body></body></html>");
}
$chGroup=new ChGroup("\$row[\"usr_id\"]","ID");
$chGroup->charts=array(new Chart("\$row[\"th\"]","TH",0.1),new Chart("\$row[\"rc\"]/\$row[\"th\"]","Results/TH",0.001),new Chart("\$row[\"rc\"]","Results"));
$chGroup->parse("Clients Of Task ID: <a href=\"/j3.php?k=".($_GET['k']+0)."\">".($_GET['k']+0)."</a>&nbsp;&nbsp;Period: ".$ym);
mysql_query("SELECT RELEASE_LOCK('bigquery')");
mysql_close($sql_srv);
ctrl7();
echo"</body></html>";