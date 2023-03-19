<?php
echo"<link rel=\"stylesheet\" type=\"text/css\" href=\"ctrl1.css\"/>";
$ym_last=date("Y")*12+date("m")-1;
$ym_cur=isset($_GET['o'])?$_GET['o']:$ym_last;
function ctrl7show($ym){return(floor($ym/12)."-".str_pad($ym%12+1,2,"0",STR_PAD_LEFT));}
function ctrl7(){
	global $ym_last,$ym_cur;
	$a="<a href=\"".$_SERVER['PHP_SELF']."?";
	foreach($_GET as $key=>$value)if($key!="o")$a.=$key."=".$value."&";
	echo "<div class=\"c4m\"><div>".($a.="o=").($ym_prev=$ym_cur-1)."\">".ctrl7show($ym_prev)."&#9668;</a>";
	if($ym_cur!=$ym_last){
		if(($ym_next=$ym_cur+1)!=$ym_last)echo"&nbsp;".$a.$ym_next."\">&#9658;".ctrl7show($ym_next)."</a>";
		echo"&nbsp;".$a.$ym_last."\">&#9658;&#124;".ctrl7show($ym_last)."</a>&nbsp;";
	}
	echo"</div></div>";
}
?>