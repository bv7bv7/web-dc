<?
require_once'c10.php';
require_once'vi_h.php';
function getInputICD(){
	$s='';
	if($_SESSION["rl"]&28){
		$s.='<form method="get" target="icd" action="icd.php">'.bv7lg('Сlients work','Работа клиентов').'&nbsp;'.getCBiDT();
		if($_SESSION["rl"]&12){
			$sql_srv=bv7dc_select_db();
			$s.=getInputVisor($_GET['visor']);
		}
		$s.='<input type="submit"/></form>';
	}
	return $s;
}
?>