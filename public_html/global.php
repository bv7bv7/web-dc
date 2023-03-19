<?php
function bv7lg($en, $ru, $lg = 0)
{
	return ($lg? $lg: $_SESSION ['lg']) == "ru"? $ru: $en;
}
function bv7dc_select_db($srv = 0)
{
	if(! $srv)
		$srv = $_SERVER ['HTTP_HOST'];
	switch ($srv) {
	case 'dcclient.com':
	case '94.76.219.222':
	case 'www.dcclient.com':
		$mysql_user     = 'dcclient_dc';
		$mysql_password = "PS7thMLuDPmqBMg";
		$mysql_host     = "localhost";
		$mysql_database = "dcclient_dc";
		break;

	case '87.117.255.35':
 	case '5.77.41.189':
		$mysql_user     = 'dcclient';
		$mysql_password = "PS7thMLuDPmqBMg";
		$mysql_host     = "localhost";
		$mysql_database = "dcclient_dc";
		break;
	case 'dc.host56.com':
	case 'dc.netne.net':
	case 'dc.webatu.com':
	case 'calc.hostoi.com':
	case 'dc.netau.net':
	case 'calc.comli.com':
		switch ($srv)
		{
		case 'dc.host56.com':
			$mysql_user = "2582285";
			$mysql_host = "9";
			break;
		case 'dc.netne.net':
			$mysql_user = "6935564";
			$mysql_host = "8";
			break;
		case 'dc.webatu.com':
			$mysql_user = "4985402";
			$mysql_host = "12";
			break;
		case 'calc.hostoi.com':
			$mysql_user = "6188591";
			$mysql_host = "10";
			break;
		case 'dc.netau.net':
			$mysql_user = "8957224";
			$mysql_host = "7";
			break;
		case 'calc.comli.com':
			$mysql_user = "3544882";
			$mysql_host = "6";
			break;
		}
		$mysql_host     = "mysql". $mysql_host. ".000webhost.com";
		$mysql_user     = "a". $mysql_user. "_calc";
		$mysql_password = "dcDJde3asNS234dn4hmAJDsSCsdD";
		$mysql_database = $mysql_user;
		break;
	}
	$sql_srv = mysql_connect($mysql_host, $mysql_user, $mysql_password);
	if (! mysql_select_db($mysql_database, $sql_srv))
		echo "{$sql_srv}<br/>Error: mysql_select_db<br/>";
	return $sql_srv;
}
function bv7mail($to,$subject,$message)
{
	$dn=$_SERVER['HTTP_HOST']!="87.117.255.35"&&$_SERVER['HTTP_HOST']!="5.77.41.189"&&$_SERVER['HTTP_HOST']!="dcclient.com"&&$_SERVER['HTTP_HOST']!="www.dcclient.com";
	$fr=$dn?"support@".$_SERVER['HTTP_HOST']:"saidhasha@gmail.com";
	$from="bv7DC Server <".$fr.">";
	$subject=iconv("utf-8","ascii",$subject);
	$message=iconv("utf-8","ascii","<html><body>".$message."</body></html>");
	$date=date("r");
	$headers="From: ".$from.PHP_EOL
		."Reply-To: ".$from.PHP_EOL
		."Return-Path: ".$from.PHP_EOL
		."X-Mailer: PHP5".PHP_EOL
		."Content-Transfer-encoding: 8bit".PHP_EOL
		."MIME-Version: 1.0".PHP_EOL
		."X-MSMail-Priority: Normal".PHP_EOL
		."Importance: 1".PHP_EOL
		."Date: ".$date.PHP_EOL
		."Delivered-to: ".$to.PHP_EOL
		."Content-Type: text/html; charset=ascii";
//	$headers.=PHP_EOL.PHP_EOL
	return $dn?mail($to,$subject,$message,$headers):mail($to,$subject,$message,$headers, "-f".$fr);
}
function bv7mails_adm ($rl = 8, $subject, $message_en)
{
	if(!($res=mysql_query("SELECT `em` FROM `usrs` WHERE `st`=1 AND`rl` & ".$rl.">0 AND `notify`"))||!($row=mysql_fetch_assoc($res)))return false;
	$ref_ml=$row["em"];
	while(($row=mysql_fetch_assoc($res)))$ref_ml.= ",".$row["em"];
	return bv7mail($ref_ml,$subject,$message_en."<br /><a href='http://".$_SERVER['HTTP_HOST']."/ctrl.php'>http://".$_SERVER['HTTP_HOST']."</a>",$f_content,$f_name);
}
function bv7mailfs($to,$subject,$message,$fs)
{
	$dn=$_SERVER["HTTP_HOST"]!="87.117.255.35"&&$_SERVER["HTTP_HOST"]!="5.77.41.189"&&$_SERVER["HTTP_HOST"]!="dcclient.com"&&$_SERVER["HTTP_HOST"]!="www.dcclient.com";
	$fr=$dn?"support@".$_SERVER["HTTP_HOST"]:"saidhasha@gmail.com";
	$from="bv7DC Server <".$fr.">";
	$subject=iconv("utf-8","ascii",$subject);
	$message="--DCServerBoundary".PHP_EOL
		."Content-Type: text/html; charset=ascii".PHP_EOL
		."Content-Transfer-encoding: 8bit".PHP_EOL.PHP_EOL
		.iconv("utf-8","ascii","<html><body>".$message."</body></html>").PHP_EOL;
	foreach($fs as $f)$message.="--DCServerBoundary".PHP_EOL
		."Content-Type: application/octet-stream; name=\"".$f[0]."\"".PHP_EOL
		."Content-Transfer-encoding: 8bit".PHP_EOL
		."Content-Disposition: attachment; filename=\"".$f[0]."\"".PHP_EOL.PHP_EOL
		.iconv("utf-8","ascii",$f[1]).PHP_EOL;
	$message.="--DCServerBoundary--".PHP_EOL;
	$date=date("r");
	$headers="From: ".$from.PHP_EOL
		."Reply-To: ".$from.PHP_EOL
		."Return-Path: ".$from.PHP_EOL
		."X-Mailer: PHP5".PHP_EOL
		."MIME-Version: 1.0".PHP_EOL
		."X-MSMail-Priority: Normal".PHP_EOL
		."Importance: 1".PHP_EOL
		."Date: ".$date.PHP_EOL
		."Delivered-to: ".$to.PHP_EOL
		."Content-Type: multipart/mixed; boundary=\"DCServerBoundary\"";
//	$headers.=PHP_EOL.PHP_EOL;
	return $dn?mail($to,$subject,$message,$headers):mail($to,$subject,$message,$headers,"-f".$fr);
}
function bv7mailsfs_adm($rl=8,$subject,$message_en,$fs,$cust_id=0){
	if(!($res=mysql_query("SELECT`em`FROM`usrs`WHERE`st`=1 AND(`rl`=4 OR`rl`=8 OR`id`=".$cust_id.")AND`notify`=1")))return false;
	while(($row=mysql_fetch_assoc($res)))bv7mailfs($row["em"],$subject,$message_en,$fs);
	return true; 
}
function bv7pw_gen($pw_len=15){
	$res_str='';
	$sz=strlen($letters='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890')-1;
	while($pw_len--)$res_str.=$letters{mt_rand(0,$sz)};
	return $res_str;
}
?>