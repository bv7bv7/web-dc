<?php
function viewGrp($grp_id){
	global $agrp;
	return isset($agrp[$grp_id])?$grp_id.' '.$agrp[$grp_id]:'-- Not Set --';
}
function inputGrp($grp_id){
	global $agrp;
	$s="<input type=\"hidden\" name=\"a\" value=\"chgrp\"/><select name=\"grp\" size=\"1\"><option value=\"0\"".($grp_id?"":" selected=\"selected\"").">-- Not Set --</option>";
	foreach($agrp as $key=>$value)
		$s.="<option value=\"".$key."\"".($key==$grp_id?" selected=\"selected\"":"").">".$key.' '.$value."</option>";
	return $s."</select>";
}
function initGrp(){
	global $agrp;
	if($res=mysql_query("SELECT * FROM `grp` WHERE 1 ORDER BY `id`"))
		while($row=mysql_fetch_assoc($res))$agrp[$row['id']]=($row['name']?$row['name']:'Group '.$row['id']);
}
?>