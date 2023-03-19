<?php
// The constants should be replaced with actual values
define('dc_URL','http://5.77.41.189/');  // URL DC Server with the symbol "/" at the end
define('dc_id',1628);                    // Number ID same as on the DC Server
define('dc_password','cenO9UkGbwNiN4V'); // Password - 15 alphanumeric characters to access DC Server

// Create New Task and return Numeric Task ID or 0 if fail. Return error message in the third parameter
function dc_AddTaskSL3($imei,$target_hash,&$Error,$fast=false){
	$ch=curl_init(dc_URL.'newsl32.php');
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_TIMEOUT,10);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query(array('id'=>dc_id,'pw'=>dc_password,'a'=>'chk','sm'=>($fast?'1':'0'),'hs'=>$target_hash,'iemi'=>$imei)));
	curl_setopt($ch,CURLOPT_USERAGENT,'DC_PHP_API_1.0');
	$result=curl_exec($ch);
	curl_close($ch);
	if(preg_match('/Task\sID\s*(\d+)\D/i',$result,$matches)){
		$Error='';
		return($matches[1]);
	}else{
		if(preg_match('/Error[^<]*/i',$result,$matches))$Error=$matches[0];
		else $Error='Unknown Error';
		return(0);
	}
}

// Return MASTER_SP_CODE or 0 if not found or fail. Return % progress in the second parameter
function dc_GetMasterSPCode($task_id,&$progress){
	$ch=curl_init(dc_URL.'j3.php');
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_TIMEOUT,10);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query(array('id'=>dc_id,'pw'=>dc_password,'jb'=>$task_id)));
	curl_setopt($ch,CURLOPT_USERAGENT,'DC_PHP_API_1.0');
	$result=curl_exec($ch);
	curl_close($ch);
	if(preg_match('/Progress\s*(\d+)%/i',$result,$matches))$progress=$matches[1];
	else $progress=0;
	if(preg_match('/MASTER_SP_CODE\s*(\d+)\D/i',$result,$matches))return($matches[1]);
	else return(0);
}

// Return MASTER_SP_CODE or 0 if not found or fail. Return % progress in the second parameter. Return State in the third parameter
function dc_GetMasterSPCode2($task_id,&$progress,&$state){
	$ch=curl_init(dc_URL.'j3.php');
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_TIMEOUT,10);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query(array('id'=>dc_id,'pw'=>dc_password,'jb'=>$task_id)));
	curl_setopt($ch,CURLOPT_USERAGENT,'DC_PHP_API_1.0');
	$result=curl_exec($ch);
	curl_close($ch);
	if(preg_match('/Progress\s*(\d+)%/i',$result,$matches))$progress=$matches[1];
	else $progress=0;
	if(preg_match('/State\s*(\w+)\W/i',$result,$matches))$state=$matches[1];
	else $state='';
	if(preg_match('/MASTER_SP_CODE\s*(\d+)\D/i',$result,$matches))return($matches[1]);
	else return(0);
}

// Change State of the Task and return: 0 - fail, 1 - success. Second parameter: 0 - Pause, 1 - Run.
function dc_ChangeTaskState($task_id,$state){
	$ch=curl_init(dc_URL.'j3.php');
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_TIMEOUT,10);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query(array('id'=>dc_id,'pw'=>dc_password,'jb'=>$task_id,'st'=>$state?1:5)));
	curl_setopt($ch,CURLOPT_USERAGENT,'DC_PHP_API_1.0');
	$result=curl_exec($ch);
	curl_close($ch);
	return(preg_match('/\WOk\W/i',$result)?1:0);
}
?>