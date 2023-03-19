<?php
function ctrl5handleResponse($gets=''){
	$url=$_SERVER['PHP_SELF'].$gets;
	header("Request-URI: ".$url);
	header("Content-Location: ".$url);
	header("Location: ".$url);
}
function ctrl6handleResponse(){echo "<script type=\"text/javascript\">window.parent.location.reload();</script>";}
?>