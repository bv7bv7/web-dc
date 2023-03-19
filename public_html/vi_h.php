<?php
function initVisors()
{
	global $visors;
	$visors[0]=bv7lg("Empty","Не указан");
	if($res=mysql_query("SELECT `id`,`em` FROM `usrs` WHERE `rl`&16 AND `st`=1".($_SESSION["rl"]&12?"":($_SESSION["rl"]&16?" AND `id`=".$_SESSION['id']:" AND 0"))." ORDER BY `id` DESC"))while($row=mysql_fetch_assoc($res))$visors[$row['id']]=$row['em'];
}
function getTextVisor($visor){
	global $visors;
	if(!isset($visors))initVisors();
	return ($visor?$visor.' ':'').$visors[$visor];
}
function getInputVisor($visor)
{
	global $visors;
	if(!isset($visors))initVisors();
	$s='<select name="visor" size="1">';
	foreach($visors as $key=>$value){
		$s.='<option value="'.$key.'"';
		if($key==$visor)$s.=' selected="selected"';
		$s.='>'.getTextVisor($key).'</option>';
	}
	return $s.'</select>';
}
?>