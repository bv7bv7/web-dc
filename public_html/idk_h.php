<?
require_once'c10.php';
require_once'cu_h.php';
function getInputIDK(){
	$s='';
	if($_SESSION["rl"]&14){
		$s.='<form method="get" target="idk" action="idk.php">'.bv7lg('Results','Результаты').'&nbsp;'.getCBiDT();
		if($_SESSION["rl"]&12){
			$sql_srv=bv7dc_select_db();
			$s.=getInputCust($_GET['cust']);
		}
		$s.='<input type="submit"/></form>';
	}
	return $s;
}
?>