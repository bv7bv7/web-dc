<?php
require 'clauth.php';
if(!isset($_COOKIE['si'])||!isset($_COOKIE['op'])){
	setcookie('r',1);//Unknown error
	exit(__FILE__.__LINE__);
}
function killreq(){
	global $cl;
	mysql_query("UPDATE `subjobs` SET `st`=0,`time`=`time`,`usr_id`=0 WHERE `id`=".$_COOKIE['si']." AND `usr_id`=".$cl["id"]." AND `st`=1 LIMIT 1");
	exit(__FILE__.__LINE__);
}
setcookie('r',4);//Server busy, try again later.
if(mysql_result($res=mysql_query("SELECT GET_LOCK('clreq',".cl_lock_timeout.")"), 0)!=1) exit(__FILE__.__LINE__);
setcookie('r',0);// No error.
if(!isset($_POST['par'])) killreq(); // Client do not calculated subjob
$par=urldecode($_POST['par']);
// Client calculated (past of) subjob
switch($_COOKIE['op']){
case 0:
	$pars=explode("-",$par,3);
	$res=mysql_query("SELECT *
			,`pc0`/".sl3_mh."/(`timeout`+1)<=".mhs_max." AS `okmhs`
			,`pc0`+".$cl['cnt_acc']." AS `cnt_acc`
			,(`pc0`+".$cl['cnt_acc'].")/".sl3_mh." AS `mh_acc`
			,".$cl['pay_acc']."+`pc0`*`prc_gh`/".sl3_op_per_gh24." AS `pay_acc`"
			.(isset($_POST['res'])
			?",CONCAT('imei ',`pi`)AS `mlsub`
				,CONCAT('Task completed: SL3 decr.<br />ID: ',`job_id`,'<br />[IMEI]<br />',`pi`,'<br />[TARGET_HASH]<br />',`ph`,'<br />[MASTER_SP_CODE]<br />',`msc`)AS `mlmsg`
				,CONCAT('[IMEI]\r\n',`pi`,'\r\n[TARGET_HASH]\r\n',`ph`,'\r\n[MASTER_SP_CODE]\r\n',`msc`)AS `mlflc`
				,CONCAT(`pi`,'.cod')AS `mlfln`"
			:"")
		."FROM(SELECT *
				,`pl0`-`pf0`+1 AS `pc0`"
				.(isset($_POST['res'])
				?",SUBSTRING_INDEX(`bphi`,':',1)AS `ph`
					,IF(`job_par2`='',CONCAT(SUBSTRING_INDEX(`bphi`,':',-1),'X'),`job_par2`)AS `pi`
					,'".mspc($_POST['res'])."' AS `msc`"
				:",IF(`c`>0,CONCAT_WS('-',`bphi`,LPAD(`pf0`,15,'0'),LPAD(`pl0`,15,'0')),'')AS `p0`
					,IF(`c`>1,CONCAT_WS('-',`bphi`,LPAD(`pf1`,15,'0'),LPAD(`pl1`,15,'0')),'')AS `p1`
					,IF(`c`>2,CONCAT_WS('-',`bphi`,LPAD(`pf2`,15,'0'),LPAD(`pl2`,15,'0')),'')AS `p2`"
				)
			."FROM(SELECT *
					,IF(`bphi`<>'".$pars[0]."' OR `bpf`>".$pars[2]." OR `bpl`<".$pars[1].",0,1+IF(`bpf`<".$pars[1].",1,0)+IF(`bpl`>".$pars[2].",1,0))AS `c`
					,IF(`bpf`>".$pars[1].",`bpf`,".$pars[1].")AS `pf0`
					,IF(`bpl`<".$pars[2].",`bpl`,".$pars[2].")AS `pl0`"
					.(isset($_POST['res'])?""
					:",IF(`bpf`<".$pars[1].",`bpf`,".$pars[2]."+1)AS `pf1`
						,IF(`bpf`<".$pars[1].",".$pars[1]."-1,`bpl`)AS `pl1`
						,".$pars[2]."+1 AS `pf2`
						,`bpl` AS `pl2`"
					)
				."FROM(SELECT `subjobs`.`job_id`,`subjobs`.`time`,`subjobs`.`usr_id` AS `usr_id`,`jobs`.`usr_id` AS `cust_id`,`jobs`.`par` AS `job_par`,`jobs`.`par2` AS `job_par2`
						,".($cl['prc_gh']==0?"`jobs`.`pay`/".sl3_pay_per_gh24:$cl['prc_gh'])." AS `prc_gh`
						,TIMESTAMPDIFF(SECOND,`subjobs`.`time`,NOW())AS `timeout`
						,SUBSTRING_INDEX(`subjobs`.`par`,'-',1)AS `bphi`
						,SUBSTRING_INDEX(SUBSTRING_INDEX(`subjobs`.`par`,'-',2),'-',-1)AS `bpf`
						,SUBSTRING_INDEX(`subjobs`.`par`,'-',-1)AS `bpl`
					FROM `subjobs` LEFT JOIN `jobs` ON `subjobs`.`job_id`=`jobs`.`id`
					WHERE `subjobs`.`id`=".$_COOKIE['si']." AND `subjobs`.`st`<>3 LIMIT 1
				)AS `t1`
			)AS `t2`
		)AS `t3`");
	break;
default:
	exit(__FILE__.__LINE__);
}
if(!$res)exit(__FILE__.__LINE__);
if(!($sj=mysql_fetch_assoc($res))) exit(__FILE__.__LINE__);
$isres=false;
if(isset($_POST['res'])){ //Result present
	//!!! Add `cnt` to jobs.php
	// Close current subjob
	if($sj['pc0']<=0
		||!mysql_query("UPDATE `subjobs` SET `par`='".$par."',`cnt`=".$sj['pc0'].",`st`=3,`usr_id`=".$cl["id"].",`fnd`=1,`ym`=EXTRACT(YEAR_MONTH FROM NOW())WHERE `id`=".$_COOKIE['si']." AND UNHEX(SHA1(x'".$sj['msc']."00".substr($sj['pi'],0,14)."00'))=x'".$sj['ph']."' LIMIT 1")
		||mysql_affected_rows()!=1
	)exit(__FILE__.__LINE__);;
	// Close job
	if(!mysql_query("UPDATE `jobs` SET `st`=2,`res`='".$_POST['res']."',`progress`=`progress`+".$sj['pc0'].",`timefinish`=NOW() WHERE `id`=".$sj['job_id']." AND `res`='' LIMIT 1"))
		exit(__FILE__.__LINE__);
	$isres=mysql_affected_rows()==1;
}else{ // Result missing
	if($sj['c']==0)killreq(); // Params not actual
	if($sj['okmhs']==0)
		exit(__FILE__.__LINE__);
	switch($sj['c']){
	case 1://We consider all of the subproblem
		//Close subjob
		if(!mysql_query("UPDATE `subjobs` SET `usr_id`=".$cl["id"].",`st`=3,`cnt`=".$sj['pc0'].",`ym`=EXTRACT(YEAR_MONTH FROM NOW()) WHERE `id`=".$_COOKIE['si']." LIMIT 1"))
			exit(__FILE__.__LINE__);
		break;
	case 3: // Consider the third sub
		// Add the second third of the remaining subproblem
		if(!mysql_query("INSERT `subjobs` SET `job_id`=".$sj['job_id'].",`usr_id`=0,`st`=0,`time`='".$sj['time']."',`ym`=EXTRACT(YEAR_MONTH FROM`time`),`par`='".$sj['p2']."'"))
			exit(__FILE__.__LINE__);
	case 2: // We consider a half subproblem
		// Change rest half part of subjob
		// OR
		// Change the first third of the remaining subproblem
		// Add the considered part of the subtasks
		if(!mysql_query("UPDATE `subjobs` SET ".($sj['usr_id']==$cl["id"]?"`usr_id`=0,`st`=0,":"")."`time`=`time`,`par`='".$sj['p1']."' WHERE `id`=".$_COOKIE['si']." LIMIT 1")
			||!mysql_query("INSERT `subjobs` SET `job_id`=".$sj['job_id'].",`usr_id`=".$cl["id"].",`st`=3,`par`='".$sj['p0']."',`cnt`=".$sj['pc0'].",`ym`=EXTRACT(YEAR_MONTH FROM NOW())")
		)exit(__FILE__.__LINE__);
		break;
	default:
		exit(__FILE__.__LINE__);
	}
	// Add progress to job
	if(!mysql_query("UPDATE `jobs` SET `progress`=`progress`+".$sj['pc0']." WHERE `id`=".$sj['job_id']." LIMIT 1"))
		exit(__FILE__.__LINE__);
}
mysql_query("SELECT RELEASE_LOCK('clreq')");
if($isres) //Result present
	bv7mailsfs_adm(14,$sj['mlsub'],$sj['mlmsg'],array(array($sj['mlfln'],$sj['mlflc']),array($sj['pi'].'_nck.txt',nck($sj['ph'],$sj['msc'],"\r\n"))),$sj['cust_id']);
setcookie('r',1);//Unknown error
// Update user email &...
// Update user speed of operation
$query="UPDATE `usrs` SET `cnt_acc`=".$sj['cnt_acc'].",`mh_acc`=".$sj['mh_acc'].",`pay_acc`=".$sj['pay_acc'].",`time`=NOW(),`time_ia`=NOW(),`v0`=".$v0.",`v2`=".$v2.",`v3`=".$v3.",`ua`='".$_SERVER['HTTP_USER_AGENT']."',`job_id`=".$sj['job_id'];
if(isset($_POST["e"]))
	$query.=",`em`='".trim($_POST["e"])."'";
if(isset($_POST["p"]))
	$query.=",`wm`='".$_POST["p"]."'";
//m - Temps Max (delim - "z")
if(isset($_POST['m'])){
	$vs=explode('z',$_POST['m'],8);
	$query.=",`max_temp0`=".$vs[0].",`max_temp1`=".$vs[1].",`max_temp2`=".$vs[2].",`max_temp3`=".$vs[3].",`max_temp4`=".$vs[4].",`max_temp5`=".$vs[5].",`max_temp6`=".$vs[6].",`max_temp7`=".$vs[7];
}
if($v2>=25){
	//h(a - x.0.24) - Temps Average (delim - "z")
	if(isset($_POST['h'])){
		$vs=explode('z',$_POST['h'],8);
		$query.=",`av_temp0`=".$vs[0].",`av_temp1`=".$vs[1].",`av_temp2`=".$vs[2].",`av_temp3`=".$vs[3].",`av_temp4`=".$vs[4].",`av_temp5`=".$vs[5].",`av_temp6`=".$vs[6].",`av_temp7`=".$vs[7];
	}
	//b - Fans Max (delim - "z")
	if(isset($_POST['b'])){
		$vs=explode('z',$_POST['b'],8);
		$query.=",`max_fan0`=".$vs[0].",`max_fan1`=".$vs[1].",`max_fan2`=".$vs[2].",`max_fan3`=".$vs[3].",`max_fan4`=".$vs[4].",`max_fan5`=".$vs[5].",`max_fan6`=".$vs[6].",`max_fan7`=".$vs[7];
	}
	//c - Fans Average (delim - "z")
	if(isset($_POST['c'])){
		$vs=explode('z',$_POST['c'],8);
		$query.=",`av_fan0`=".$vs[0].",`av_fan1`=".$vs[1].",`av_fan2`=".$vs[2].",`av_fan3`=".$vs[3].",`av_fan4`=".$vs[4].",`av_fan5`=".$vs[5].",`av_fan6`=".$vs[6].",`av_fan7`=".$vs[7];
	}
	//f - Utils Max (delim - "z")
	if(isset($_POST['f'])){
		$vs=explode('z',$_POST['f'],8);
		$query.=",`max_util0`=".$vs[0].",`max_util1`=".$vs[1].",`max_util2`=".$vs[2].",`max_util3`=".$vs[3].",`max_util4`=".$vs[4].",`max_util5`=".$vs[5].",`max_util6`=".$vs[6].",`max_util7`=".$vs[7];
	}
	//g - Utils Average (delim - "z")
	if(isset($_POST['g'])){
		$vs=explode('z',$_POST['g'],8);
		$query.=",`av_util0`=".$vs[0].",`av_util1`=".$vs[1].",`av_util2`=".$vs[2].",`av_util3`=".$vs[3].",`av_util4`=".$vs[4].",`av_util5`=".$vs[5].",`av_util6`=".$vs[6].",`av_util7`=".$vs[7];
	}
	if(isset($_POST['j'])){
		if($_POST['j']>0&&$_POST['j']<mhs_max&&$sj['timeout']>cl_timespd)
			$query.=",`mhs`=ROUND(".$_POST['j'].")";
		else if($sj['c']==1&&$sj['usr_id']==$cl["id"]&&isset($_COOKIE['f'])&&$_COOKIE['f']>1){
			$query.=",`mhs`=ROUND(IF(`mhs`,`mhs`,1)*".cl_time;
			if($sj['timeout']!=0)
				$query.="/".$sj['timeout'];
			$query.=")";
		}
	}
}else{
	//h(a - x.0.24) - Temps Average (delim - "z")
	if(isset($_POST['a'])){
		$vs=explode('z',$_POST['a'],8);
		$query.=",`av_temp0`=".$vs[0].",`av_temp1`=".$vs[1].",`av_temp2`=".$vs[2].",`av_temp3`=".$vs[3].",`av_temp4`=".$vs[4].",`av_temp5`=".$vs[5].",`av_temp6`=".$vs[6].",`av_temp7`=".$vs[7];
	}
	if(isset($_POST['s'])){
		if($_POST['s']>0&&$_POST['s']/sl3_spd_per_mh<mhs_max&&$sj['timeout']>cl_timespd)
			$query.=",`mhs`=ROUND(".$_POST['s']."/".sl3_spd_per_mh.")";
		else if($sj['c']==1&&$sj['usr_id']==$cl["id"]&&isset($_COOKIE['f'])&&$_COOKIE['f']>1){
			$query.=",`mhs`=ROUND(IF(`mhs`,`mhs`,1)*".cl_time;
			if($sj['timeout']!=0)
				$query.="/".$sj['timeout'];
			$query.=")";
		}
	}
}
if(!mysql_query($query." WHERE `id`=".$cl["id"]." LIMIT 1"))exit(__FILE__.__LINE__);
if($v2>=25){
	setcookie('h',$sj['mh_acc']);
	setcookie('g',round($cl['cnt_prev']/sl3_mh));
}else{
	setcookie('ac',$sj['cnt_acc']);
	setcookie('pc',$cl['cnt_prev']);
}
setcookie('pm',$cl['pay_prev']);
setcookie('am',$sj['pay_acc']);
setcookie('r',0);// No error.
mysql_close($sql_srv);
?>