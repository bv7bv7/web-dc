<?php
require"auth.php";
if(!($_SESSION["rl"]&12)||!isset($_GET['k']))exit;
$sql_srv=bv7dc_select_db();
if(mysql_result($res=mysql_query("SELECT GET_LOCK('bigquery',10)"),0)!=1
	||!($res=mysql_query("SELECT * FROM`usrs`WHERE`id`=".$_GET["k"]." LIMIT 1"))
	||!($cl=mysql_fetch_assoc($res))
	||!($res=mysql_query("SELECT SUM(`cnt`)/".(sl3_mh*1000000)." AS`th`,SUM(`fnd`)AS`rc`,`ym`FROM`subjobs`USE INDEX(`st_usr_ym`)WHERE`usr_id`=".$cl["id"]." AND`st`=3 GROUP BY 3"))
){
	mysql_close($sql_srv);
	exit;
}
require"chart2.php";
echo"<title>ID: ".$cl["id"]."</title>";
$chGroup=new ChGroup('$row["ym"]','Month');
$chGroup->charts=array(new Chart('$row["th"]','TH',0.1),new Chart('$row["rc"]/$row["th"]','Results/TH',0.001),new Chart('$row["rc"]','Results'));
$chGroup->parse('Email: <a href="/e1.php?k='.$cl['id'].'">'.$cl['em'].'</a>&nbsp;&nbsp;ID: '.$cl['id']);
mysql_query("SELECT RELEASE_LOCK('bigquery')");
mysql_close($sql_srv);
echo"</body></html>";