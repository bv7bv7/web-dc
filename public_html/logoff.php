<?
session_start ();
unset ($_SESSION ["lg"], $_SESSION ["id"], $_SESSION ["rl"]);
session_destroy();
header("Request-URI: ctrl.php");
header("Content-Location: ctrl.php");
header("Location: ctrl.php");
?>