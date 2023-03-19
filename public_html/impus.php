<?
require 'auth.php';
if (! ($_SESSION["rl"] & 8))
	exit;
if (! isset($_POST['c']))
	exit(__FILE__.__LINE__);
$sql_srv = bv7dc_select_db();
$ful = isset($_POST['f']) && $_POST['f'] == '1';
for($i = 0; $i < $_POST['c']; $i++) {
	if(!isset($_POST["u{$i}"]))
		exit(__FILE__.__LINE__);
	$u = explode(",", $_POST["u{$i}"]);
	$query = "SELECT `id` FROM `usrs` WHERE `id` = {$u[0]} LIMIT 1";
	$res = mysql_query($query);
	if (! $res) {
		echo $_POST["u{$i}"]. "<br />";
		echo $query. "<br />";
		exit(__FILE__.__LINE__);
	}
	$row = mysql_fetch_assoc($res);
	if(! $row) {
		$res = mysql_query("INSERT INTO `usrs` SET `id` = {$u[0]}");
		if (! $res)
			exit(__FILE__.__LINE__);
	}
	$res = mysql_query("UPDATE `usrs` "
		."SET  `st`        =".($u[1 ]==""?"`st`        ":    $u[1 ]    )
			.",`rl`        =".($u[2 ]==""?"`rl`        ":    $u[2 ]    )
			.",`pw`        =".($u[3 ]==""?"`pw`        ":"'".$u[3 ]."'")
			.",`em`        =".($u[4 ]==""?"`em`        ":"'".$u[4 ]."'")
			.",`wm`        =".($u[5 ]==""?"`wm`        ":"'".$u[5 ]."'")
			.",`lg`        =".($u[6 ]==""?"`lg`        ":"'".$u[6 ]."'")
			.",`pusr_id`   =".($u[7 ]==""?"`pusr_id`   ":    $u[7 ]    )
			.",`pr_add_pay`=".($u[8 ]==""?"`pr_add_pay`":    $u[8 ]    )
			.",`prc_gh`    =".($u[9 ]==""?"`prc_gh`    ":    $u[9 ]    )
			.",`pr2`       =".($u[10]==""?"`pr2`       ":    $u[10]    )
			.",`wm2`       =".($u[11]==""?"`wm2`       ":"'".$u[11]."'")
		. " WHERE `id` = {$u[0]} LIMIT 1");
	if (! $res)
		exit(__FILE__.__LINE__);
	if($ful) {
		$res = mysql_query("UPDATE `usrs`
			SET  `cnt_acc`    ={$u[12]}
				,`mh_acc`     =".$u[12]."/".sl3_mh."
				,`pay_acc`    ={$u[13]}
				,`cnt_prev`   ={$u[14]}
				,`mh_prev`    =".$u[14]."/".sl3_mh."
				,`pay_prev`   ={$u[15]}
				,`cnt2_reserv`={$u[16]}
				,`pay2_reserv`={$u[17]}
			WHERE `id` = {$u[0]}
			LIMIT 1
		");
		if (! $res)
			exit(__FILE__.__LINE__);
	}
}
header("Request-URI: usrs2.php");
header("Content-Location: usrs2.php");
header("Location: usrs.php2");
mysql_close($sql_srv);