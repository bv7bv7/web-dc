<?php
require_once'auth.php';
require_once'icd_h.php';
if(!($_SESSION["rl"]&16||($_SESSION["rl"]&12)&&isset($_GET['visor']))||!cBiDTChk())exit;
$sql_srv=bv7dc_select_db();
$viewer_id=$_SESSION["rl"]&12?$_GET['visor']+0:$_SESSION['id'];
if(mysql_result($res=mysql_query("SELECT GET_LOCK('bigquery',10)"),0)!=1
	||!($res=mysql_query("SELECT DISTINCT`id`FROM`usrs`".(($_SESSION["rl"]&12)&&$viewer_id==0?"":"WHERE`pusr_id`=".$viewer_id)))
	||!($r1=mysql_fetch_assoc($res))
){
	mysql_close($sql_srv);
	exit;
}
require"chart2.php";
echo"<title>Works of Client Viewer: ".$viewer_id."</title>";
$query="SELECT`subjobs`.`usr_id`,SUM(`subjobs`.`cnt`)/".(sl3_mh*1000000)." AS`th`,SUM(`subjobs`.`cnt`*`jobs`.`prc_gh`)/".(sl3_mh*1000*24*60*60)." AS`sd`"
	."FROM`subjobs`USE INDEX(`usr_time`)LEFT JOIN`jobs`ON`subjobs`.`job_id`=`jobs`.`id`"
	."WHERE`subjobs`.`st`=3 AND`subjobs`.`time`BETWEEN'".($fdt=cBiDTMySqlFrom())."'AND'".($tdt=cBiDTMySqlTo())."'AND(`subjobs`.`usr_id`=".$r1['id'];
while($row=mysql_fetch_assoc($res))$query.=" OR`subjobs`.`usr_id`=".$row['id'];
if(!($res=mysql_query($query.")GROUP BY 1"))){
	mysql_close($sql_srv);
	exit("</head><body></body></html>");
}
$chGroup=new ChGroup('$row["usr_id"]','ID');
$chGroup->charts=array(new Chart('$row["th"]','TH',0.1),new Chart('$row["sd"]','D',0.001));
$chGroup->parse(getInputICD());
mysql_query("SELECT RELEASE_LOCK('bigquery')");
mysql_close($sql_srv);
echo"</body></html>";