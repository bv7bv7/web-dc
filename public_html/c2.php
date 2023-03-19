<?php
if(isset($_POST['c2id']))$c2id=$_POST['c2id'];
else{
	echo '<link rel="stylesheet" type="text/css" href="ctrl1.css"/><script type="text/javascript" src="c2.js"></script>';
	$c2id=0;
}
function ctrl2handleResponse($viewText){
	exit('<script type="text/javascript">window.parent.ctrl2handleResponse('.$_POST['c2id'].',\''.$viewText.'\')</script></body></html>');
}
function ctrl2($viewText2,$viewText,$inputText,$submitText){
	global $c2id;
	$c2id++;
	echo '<div class="c2v1" id="c2v'.$c2id.'">'.$viewText2.'</div>'
		.'<div id="c2x'.$c2id.'"><a onclick="return c2se('.$c2id.');" href="#">'.$viewText.'</a></div>'
		.'<div class="c1e" id="c2e'.$c2id.'" style="display:none;">'
		.'<form name="c2f'.$c2id.'" id="c2f'.$c2id.'" action="'.$_SERVER['PHP_SELF'].'" method="POST" target="c2IFrame" onsubmit="return c2cs('.$c2id.');" onreset="return c2sv('.$c2id.');">'
		.$inputText
		.'<input type="hidden" name="c2id" value="'.$c2id.'"/>'
		.'<br /><br />'
		.'<input type="submit" value="'.$submitText.'"/> '
		.'<input type="reset"/>'
		.'</form>'
		.'</div>';
}
?>