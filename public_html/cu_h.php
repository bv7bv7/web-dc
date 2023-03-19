<?php
require_once'mj_h.php';
function initCusts(){
	global $custs;
	$custs[0]=bv7lg("Empty","Не указан");
	if($res=mysql_query("SELECT * FROM`usrs`WHERE`rl`&14 AND`st`=1".($_SESSION["rl"]&12?"":($_SESSION["rl"]&14?" AND`id`=".$_SESSION['id']:" AND 0"))." ORDER BY`id`DESC"))while($row=mysql_fetch_assoc($res))$custs[$row['id']]=str_replace(' ','&nbsp;',str_pad($row['id'],7,' ',STR_PAD_LEFT).' '.str_pad($row['em'],30).' (limit-'.viewMJ($row['mj'])).')';
}
function getInputCust($cust,$nm=''){
	global $custs;
	if(!isset($custs))initCusts();
	$s='<select name="'.$nm.'cust" size="1" style="font-family:monospace;">';
	foreach($custs as $key=>$value){
		$s.='<option value="'.$key.'"';
		if($key==$cust)$s.=' selected="selected"';
		$s.='>'.$custs[$key].'</option>';
	}
	return $s.'</select>';
}
?>