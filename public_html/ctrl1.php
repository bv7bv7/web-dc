<?php
if(isset($_GET['ctrl1id']))$ctrl1id=$_GET['ctrl1id'];
else{
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"ctrl1.css\"/><script type=\"text/javascript\" src=\"ctrl1.js\"></script>";
	$ctrl1id=0;
}
function ctrl1handleResponse($viewText){
	exit("<script type=\"text/javascript\">window.parent.ctrl1handleResponse({$_GET['ctrl1id']},'{$viewText}')</script></body></html>");
}
function ctrl1($viewText, $inputText, $submitText,$editable=true){
	global $ctrl1id;
	if($editable){
		$ctrl1id++;
		echo "<a id=\"ctrl1v{$ctrl1id}\" onclick=\"return c1se({$ctrl1id});\" href=\"#\">{$viewText}</a>"
			."<div class=\"c1e\" id=\"c1e{$ctrl1id}\" style=\"display:none;\">"
			."<form name=\"c1f{$ctrl1id}\" id=\"c1f{$ctrl1id}\" action=\"{$_SERVER['PHP_SELF']}\" onsubmit=\"return c1cs({$ctrl1id});\" onreset=\"return c1sv({$ctrl1id});\">"
			.$inputText
			."<br /><br />"
			."<input type=\"submit\" value=\"{$submitText}\"/> "
			."<input type=\"reset\"/>"
			."</form>"
			."</div>";
	}else
		echo $viewText;
}
function ctrl2handleResponse($viewText){
	exit("<script type=\"text/javascript\">window.parent.ctrl2handleResponse({$_GET['ctrl1id']},'{$viewText}')</script></body></html>");
}
function ctrl2($viewText2,$viewText,$inputText,$submitText){
	global $ctrl1id;
	$ctrl1id++;
	echo "<div class=\"c2v1\" id=\"c2v".$ctrl1id."\">".$viewText2."</div>"
		."<div id=\"ctrl1v".$ctrl1id."\"><a onclick=\"return c1se(".$ctrl1id.");\" href=\"#\">".$viewText."</a></div>"
		."<div class=\"c1e\" id=\"c1e".$ctrl1id."\" style=\"display:none;\">"
		."<form name=\"c1f".$ctrl1id."\" id=\"c1f".$ctrl1id."\" action=\"".$_SERVER['PHP_SELF']."\" onsubmit=\"return c1cs(".$ctrl1id.");\" onreset=\"return c1sv(".$ctrl1id.");\">"
		.$inputText
		."<br /><br />"
		."<input type=\"submit\" value=\"".$submitText."\"/> "
		."<input type=\"reset\"/>"
		."</form>"
		."</div>";
}
function ctrl3handleResponse($viewText,$inputText=false){
	exit("<script type=\"text/javascript\">window.parent.ctrl3handleResponse(".$_GET['ctrl1id'].",'".$viewText."'".($inputText===false?'':",'".$inputText."'").")</script></body></html>");
}
function ctrl3($viewText,$inputText,$submitText,$editable=true){
	global $ctrl1id;
	if($editable){
		$ctrl1id++;
		echo "<a id=\"c3v".$ctrl1id."\" onclick=\"return c3se(".$ctrl1id.");\" href=\"#\">".$viewText."</a>"
			."<div class=\"c1e\" id=\"c3e".$ctrl1id."\" style=\"display:none;\">"
			."<form name=\"c1f".$ctrl1id."\" id=\"c1f".$ctrl1id."\" action=\"".$_SERVER['PHP_SELF']."\" onsubmit=\"return c3cs(".$ctrl1id.");\" onreset=\"return c3sv(".$ctrl1id.");\">"
			."<div id=\"c3i".$ctrl1id."\">".$inputText."</div><br />"
			."<input type=\"submit\" value=\"".$submitText."\"/> "
			."<input type=\"reset\"/>"
			."</form>"
			."</div>";
	}else
		echo $viewText;
}
function ctrl4(){
	$limit=isset($_GET['l'])?$_GET['l']:100;
	$offset=isset($_GET['o'])?$_GET['o']:0;
	echo "<div class=\"c4m\"><div>"
		."<a href=\"".$_SERVER['PHP_SELF'].(isset($_GET['l'])?"?l=".$_GET['l']."&":'?')."o=0\">&#124;&#9668;</a>"
		."&nbsp;<a href=\"".$_SERVER['PHP_SELF']."?l=".$limit.'&o='.($offset>$limit?$offset-$limit:'0')."\">&#9668;</a>"
		."&nbsp;<a href=\"".$_SERVER['PHP_SELF']."?l=".$limit.'&o='.($offset+$limit)."\">&#9658;</a>"
		."</div></div>";
}
?>