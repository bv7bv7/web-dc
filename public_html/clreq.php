<?php
require 'clauth.php';
$time=microtime(1);
setcookie('r',4);//Server busy, try again later.
if($cl['upd']&&$v2<minv2){
	require("def_upd.php");//Server of updates
	setcookie('r',2); // the client is outdated
	$res=mysql_query("UPDATE `usrs` SET `upd`=0 WHERE `id`=".$cl["id"]." LIMIT 1");
	exit;
}
define('cl_ww_time',600);// Pause before check job afte apsent job
//define('cl_ww_time',000);// Pause before check job afte apsent job
function UpdateUsr($Set){
	global $v0,$v2,$v3,$cl;
	mysql_query("SELECT RELEASE_LOCK('clreq')");
	mysql_query("UPDATE`usrs`SET ".$Set.",`time_ia`=NOW(),`unnotified_ia`=`notify_ia`,`v0`=".$v0.",`v2`=".$v2.",`v3`=".$v3.",`ua`='".$_SERVER['HTTP_USER_AGENT']."'WHERE`id`=".$cl["id"]." LIMIT 1");
}
function Notify(){
	if(($res=mysql_query(($s3="SELECT`id`,`em`,`comment`,".($s1="`unnotified`=1 AND`notify`=1 AND TIMESTAMPDIFF(SECOND,`time`,NOW())>".cl_timeout)." AS`nwj`,".($s2="`unnotified_ia`=1 AND`notify_ia`=1 AND TIMESTAMPDIFF(SECOND,`time_ia`,NOW())>".cl_timeout)." AS`nia`FROM`usrs`WHERE`em`<>''AND`rl`=1 AND ").$s1." LIMIT 1"))&&($row=mysql_fetch_assoc($res))
		||($res=mysql_query($s3.$s2." LIMIT 1"))&&($row=mysql_fetch_assoc($res))){
//	if(($res=mysql_query("SELECT *,".($s1="`unnotified`AND`notify`AND TIMESTAMPDIFF(SECOND,`time`,NOW())>".cl_timeout)." AS`nwj`,".($s2="`unnotified_ia`AND`notify_ia`AND TIMESTAMPDIFF(SECOND,`time_ia`,NOW())>".cl_timeout)." AS`nia`FROM`usrs`WHERE((".$s1.")OR(".$s2."))AND`em`<>''AND`rl`&1 LIMIT 1"))&&($row=mysql_fetch_assoc($res))){
		mysql_query("UPDATE`usrs`SET`unnotified`=0,`unnotified_ia`=0,`time`=`time`WHERE`id`=".$row["id"]." LIMIT 1");
		bv7mail(filter_var($row["em"],FILTER_VALIDATE_EMAIL),$s="ID ".$row["id"]." -".$row['comment'].($row["nwj"]?" - without job":"").($row["nia"]?" - inactive":""),"DCClient ".$s);
	}
}
function Nothing(){
	UpdateUsr("`time`=`time`");
	$_SESSION["wait"]=time()+cl_ww_time;
	setcookie('r',3);//Doing nothing.
	Notify();
//	exit;
}
$s1=$h1=$o1="";
if(($res=mysql_query("SELECT`st`,`cust_id`,`only_cust`FROM`grp`WHERE`id`=".$cl['grp_id']." LIMIT 1"))&&($row=mysql_fetch_assoc($res))){
	if($row['st']!=1){
		Nothing();
		exit;
	}else if($row['cust_id']){
		$s1=",`jobs`.`usr_id`=".$row['cust_id']." AS `ord2`";
		$o1="`ord2`DESC,";
		if($row['only_cust'])$h1=" AND `ord2`=1";
	}
}
if(mysql_result($res=mysql_query("SELECT GET_LOCK('clreq',".cl_lock_timeout.")"),0)!=1){
	Nothing();
	exit;
//	exit(__FILE__.__LINE__);
}
//	Select Job
$query="SELECT `jobs`.`op_id`,`jobs`.`id` AS `job_id`,`jobs`.`minusrs`,`jobs`.`priority`,`jobs`.`res`
		,".($cl['prc_gh']==0?"`jobs`.`pay`/".sl3_pay_per_gh24:$cl['prc_gh'])." AS `prc_gh`
		,`minusrs` BETWEEN 1 AND SUM(000001*IF(`subjobs`.`st`=1 AND `subjobs`.`time`>DATE_SUB(NOW(),INTERVAL ".cl_timeout." SECOND),1,0))AS `ord1`
		,MAX(IF(`subjobs`.`st`=0 OR `subjobs`.`st`=1 AND `subjobs`.`time`<DATE_SUB(NOW(),INTERVAL ".cl_timeout." SECOND),1,0)) AS `is`".$s1."
	FROM `subjobs` USE INDEX (`job_st_time`) LEFT JOIN `jobs` USE INDEX (`st`) ON `subjobs`.`job_id`=`jobs`.`id`
	WHERE `jobs`.`st`=1 AND `subjobs`.`st`<>3 AND `jobs`.`res`=''
	GROUP BY `jobs`.`id` HAVING `is`>0 AND `ord1`=0".$h1." ORDER BY".$o1."`priority` DESC,`job_id` LIMIT 1";
if(!($res=mysql_query($query)))exit(__FILE__.__LINE__);
if(!($row=mysql_fetch_assoc($res))){
	Nothing();
	exit;
}
switch($row['op_id']){
case 0:
	//	Select Subjob
	$cnt=min(max($cl['mhs'],mhs_min),mhs_max)*sl3_op_task_per_mhs;
	$dm=str_pad("1",floor(log10($cnt/10))+1,"0");
	$query="SELECT *
			,IF(`d`,CONCAT_WS('-',`hs`,LPAD(`pf0`,15,'0'),LPAD(`pl0`,15,'0')),`par`) AS `p0`
			,`pl0`-`pf0`+1 AS `c0`
		FROM(SELECT *
				,IF(`d`,`pf1`-1,`pl1`) AS `pl0`
			FROM(SELECT *
					,`pf1` BETWEEN `pf0` AND `pl1` AS `d`
					,CONCAT_WS('-',`hs`,LPAD(`pf1`,15,'0'),LPAD(`pl1`,15,'0')) AS `p1`
				FROM(SELECT *
						,ROUND((`pf0`+".$cnt.")/".$dm.")*".$dm." AS `pf1`
					FROM(SELECT `id`
							,".$row['op_id']." AS `op_id`
							,`par`
							,`job_id`
							,".$row['prc_gh']." AS `prc_gh`
							,SUBSTRING_INDEX(`par`,'-',1) AS `hs`
							,SUBSTRING_INDEX(SUBSTRING_INDEX(`par`,'-',2),'-',-1) AS `pf0`
							,SUBSTRING_INDEX(`par`,'-',-1) AS `pl1`
						FROM `subjobs`
						WHERE `job_id`=".$row['job_id']." AND (`st`=0 OR `st`=1 AND `time`<DATE_SUB(NOW(),INTERVAL ".cl_timeout." SECOND))
						ORDER BY `st`,`time` LIMIT 1
					)AS `t1`
				)AS `t2`
			)AS `t3`
		)AS `t4`";
	if(!($res=mysql_query($query)))exit(__FILE__.__LINE__);
	if(!($row=mysql_fetch_assoc($res))){
		Nothing();
		exit;
	}
	if($row['d']){
		if(!mysql_query("UPDATE `subjobs` SET `par`='".$row['p1']."',`time`=`time` WHERE `id`=".$row['id']." LIMIT 1"))exit(__FILE__.__LINE__);
		$res=mysql_query("INSERT `subjobs` SET `job_id`=".$row['job_id'].",`st`=1,`par`='".$row['p0']."',`usr_id`=".$cl["id"].",`cnt`=".$row['c0'].",`ym`=EXTRACT(YEAR_MONTH FROM NOW())");
		$subjob_id=mysql_insert_id();
	}else{
		$subjob_id=$row['id'];
		$res=mysql_query("UPDATE `subjobs` SET `st`=1,`time`=NOW(),`ym`=EXTRACT(YEAR_MONTH FROM NOW()),`usr_id`=".$cl["id"].",`cnt`=".$row['c0']." WHERE `id`=".$subjob_id." LIMIT 1");
	}
	if($v2>=25)setcookie('e',$row['prc_gh']);
	else setcookie('pr',round($row['prc_gh']*sl3_pay_per_gh24));
	break;
}
if(!$res)exit(__FILE__.__LINE__);
UpdateUsr("`time`=NOW(),`job_id`=".$row['job_id'].",`unnotified`=`notify`,`clreq_time`=".round((microtime(1)-$time)*1000));
setcookie('si',$subjob_id);
setcookie('f',($row['d']?2:1));//Select not last part of job
if($v2>=25){
	setcookie('g',round($cl['cnt_prev']/sl3_mh));
	setcookie('h',round($cl['cnt_acc']/sl3_mh));
}else{
	setcookie('pc',$cl['cnt_prev']);
	setcookie('ac',$cl['cnt_acc']);
}
setcookie('op',$row['op_id']);
setcookie('par',$row['p0']);
setcookie('am',$cl['pay_acc']);
setcookie('pm',$cl['pay_prev']);
setcookie('r',0);// No error.
Notify();
?>