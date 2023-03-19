<?php
define('cl_time',600); // time seconds/subjob
define('cl_timeout',cl_time*1.53); // timeout seconds/subjob
define('cl_timespd',cl_time*0.05); // min time for calc speed
define('cl_lock_timeout',1); // lock timeout seconds
define('mhs_min',1);
define('mhs_max',20000);
define('sl3_mh',5270000);
define('sl3_spd_per_mh',sl3_mh/1000);
define('sl3_op_task_per_mhs',cl_time*sl3_mh);
define('sl3_op_per_gh24',sl3_mh*86400000);
define('sl3_op_per_pay',1000000000000000);
define('sl3_pay_per_gh24',sl3_op_per_pay/sl3_op_per_gh24);

function mspc($ms){return'0'.substr(chunk_split($ms,1,'0'),0,-1);}
function nck2($h,$pc,$c){
	$r='';
	for($i=0;$i<$c;$i+=2)$r.='0'.((hexdec($h[$i].$h[$i+1])+hexdec($pc[$i]+$pc[$i+1]))%10);
	return $r;
}
function nck($th,$ms,$ln){
	$r='';
	$pc8='000000'.substr($ms,0,10);
	$pc7=substr($ms,11,13);
	for($i=1;$i<8;$i++){
		$h=substr(sha1(pack('H*','0'.$i.$th)),0,32);
		$prc8=$pc8;
		$prc7=$pc7;
		for($m=0;$m<2;$m++){
			$prc7=nck2(sha1(pack('H*','0'.$m.$h.$prc8)),$prc7,14);
			$prc8=nck2(sha1(pack('H*','0'.$m.$h.$prc7)),$prc8,16);
		}
		$f=$prc8.$prc7;
		$r.='#pw+';
		for($x=1;$x<32;$x+=2)$r.=$f[$x];
		$r.='+'.$i.'#'.$ln;
	}
	return $r;
}
?>