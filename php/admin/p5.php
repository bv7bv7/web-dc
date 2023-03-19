<?php
require "auth.php";
if(($_SESSION["rl"]&12)==0)exit;
echo "<!DOCTYPE html><html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><link rel=\"stylesheet\" type=\"text/css\" href=\"p3.css\"/>";
require "c2.php";
//define("usrs","tst_u");
define("usrs","tst_u");
echo "<title>Payments</title></head><body>";
$sql_srv=bv7dc_select_db();
if(isset($_POST["a"]))switch($_POST["a"]){
case "pay2":
	$spay=urldecode($_POST["p"]);
	$mcnt=urldecode($_POST["mc"]);
	$mpay=urldecode($_POST["mp"]);
//	echo'<br />'.$spay.'<br />'.$mcnt.'<br />'.$mpay.'<br />';
	if($mpay>=$spay && $spay>0){
		$scnt=min($mcnt,$spay/$mpay*$mcnt);
		$aid=explode("z",$_POST["ak"]);
		$acnt=explode("z",urldecode($_POST["ac"]));
		$apay=explode("z",urldecode($_POST["ap"]));
		$akr2=explode("z",urldecode($_POST["a2"]));
		for($i=0;$i<count($aid);$i++){
			$pay=min($spay,$apay[$i]);
			$cnt=min($scnt,$acnt[$i]);
			$query="UPDATE`".usrs."`SET`cnt_acc`=GREATEST(0,`cnt_acc`-".$cnt.")"
					.",`mh_acc`=`cnt_acc`/".sl3_mh
					.",`pay_acc`=GREATEST(`pay_acc`-".$pay.",0)"
					.",`cnt2_reserv`=".($akr2[$i]==0?"0":"`cnt2_reserv`+".$akr2[$i]."*".$cnt)
					.",`pay2_reserv`=".($akr2[$i]==0?"0":"`pay2_reserv`+".$akr2[$i]."*".$pay)
					.",`cnt_prev`=IF(TIMESTAMPDIFF(HOUR,`timepay`,NOW())<12,`cnt_prev`,0)+".$cnt
					.",`mh_prev`=`cnt_prev`/".sl3_mh
					.",`pay_prev`=IF(TIMESTAMPDIFF(HOUR,`timepay`,NOW())<12,`pay_prev`,0)+".$pay
					.",`time`=`time`,`timepay`=NOW()"
				."WHERE `id`=".$aid[$i]." AND`cnt_acc`-".$cnt.">=-10000 AND ROUND(`pay_acc`-".$pay.",12)>=0 LIMIT 1";
			echo'<br />'.$aid[$i].' : '.$cnt.' : '.$pay.'<br />';
			$res=mysql_query($query);
			if($res && mysql_affected_rows()==1){
				$spay-=$pay;
				$scnt-=$cnt;
			}
		}
		if($_POST["p"]==$spay)exit('</body></html>');
		ctrl2handleResponse(bv7lg("Paid: ","Оплачено: ").($_POST["p"]-$spay));
	}
	break;
}
define('min_pay',isset($_GET["min_pay"])?urldecode($_GET["min_pay"])+0:1);
define('dec_pay',isset($_GET['dec_pay'])?($_GET['dec_pay']>0?1/urldecode($_GET['dec_pay']):0):10); //	1 / x - accuracy
define('dl_pay',dec_pay>0?log10(dec_pay):0);//digits after decimal point
$qk2="IF(`wm2`='',0,`pr2`/100)";
$qcnt2_reserv="LEAST(`cnt_acc`,`cnt2_reserv`)";
$qpay2_reserv="LEAST(`pay_acc`,`pay2_reserv`)";
$qpay_acc2="((`pay_acc`-{$qpay2_reserv})*{$qk2}+{$qpay2_reserv})";
$qpay_acc1="(`pay_acc`-{$qpay_acc2})";
$qkadd="(1+`pr_add_pay`/100)";
$qcnt_acc2="ROUND((`cnt_acc`-{$qcnt2_reserv})*{$qk2}+{$qcnt2_reserv})";
$qcnt_acc1="(`cnt_acc`-{$qcnt_acc2})";
$query="SELECT *"
		.",IF(`wm2`=''OR`pr2`=100,0,`pr2`/(100-`pr2`))AS`k2pk1`"
		.",".$qcnt_acc1." AS`cnt_acc1`"
		.",".$qcnt_acc2." AS`cnt_acc2`"
		.",".$qpay_acc1." AS`pay_acc1`"
		.",".$qpay_acc2." AS`pay_acc2`"
		.",".$qpay_acc1."*".$qkadd." AS`pay_acc_pr1`"
		.",".$qpay_acc2."*".$qkadd." AS`pay_acc_pr2`"
		.",ROUND(".$qcnt_acc1."/".sl3_op_per_gh24.",3)AS`gh_acc1`"
		.",ROUND(".$qcnt_acc2."/".sl3_op_per_gh24.",3)AS`gh_acc2`"
		.",ROUND(`cnt_prev`/".sl3_op_per_gh24.",3)AS`gh_prev`"
	."FROM`".usrs."`WHERE`rl`&1 AND`pay_acc`>0 ORDER BY`em`,`wm`";
$res=mysql_query($query);
if(!$res)echo bv7lg("DB access error","Ошибка доступа к БД");
else{
	echo "<table border=\"1\"><caption>"
		.bv7lg("List of payments from ".min_pay."</caption><thead><tr><th rowspan=\"2\">Email</th><th rowspan=\"2\">ID</th><th rowspan=\"2\">Purse</th><th colspan=\"2\">Unpaid</th><th colspan=\"2\">+/-</th><th colspan=\"2\">Previous payment"
			,"Список оплат от ".min_pay."</caption><thead><tr><th rowspan=\"2\">Email</th><th rowspan=\"2\">Код</th><th rowspan=\"2\">Кошелек</th><th colspan=\"2\">Неоплачено</th><th colspan=\"2\">+/-</th><th colspan=\"2\">Предыдущая оплата"
		)."</th></tr><tr><th>gh/s/24h</th><th>D</th><th>%</th><th>D</th><th>gh/s/24h</th><th>D</th></tr></thead><tbody>";
	function pay2add1($wmid,$pay_pr,$wm,$gh,$pay,$cnt,$kreserv2){
		global $wms,$row;
		if($pay_pr>0){
			if(!isset($wms[$wmid]))
				$wms[$wmid]=array("spay_pr"=>$pay_pr,"aid"=>array($row["id"]),"awm"=>array($wm=>$wm),"sgh"=>$gh,"spay"=>$pay,"apay"=>array($pay),"spr_add"=>"","acnt"=>array($cnt),"scnt"=>$cnt,"akr2"=>array($kreserv2),"apr_add"=>array());
			else{
				$wms[$wmid]["spay_pr"]+=$pay_pr;
				$wms[$wmid]["aid"][]=$row["id"];
				$wms[$wmid]["awm"][$wm]=$wm;
				$wms[$wmid]["sgh"]+=$gh;
				$wms[$wmid]["spay"]+=$pay;
				$wms[$wmid]["apay"][]=$pay;
				$wms[$wmid]["acnt"][]=$cnt;
				$wms[$wmid]["scnt"]+=$cnt;
				$wms[$wmid]["akr2"][]=$kreserv2;
			}
			if($row["pr_add_pay"])$wms[$wmid]["apr_add"]["".$row["pr_add_pay"]]=$row["pr_add_pay"];
		}
	}
	$sgh=$spay=$spay_pr=$sgh_prev=$spay_prev=0.0;
	$row=mysql_fetch_assoc($res);
	while($row){
		$em=$row["em"];
		unset($wms);
		$wms=array();
		$wmcount=0;
		$gh_prev=$pay_prev=0.0;
		while($row && $em==$row["em"]){
			pay2add1($row["wm"],$row["pay_acc_pr1"],$row["wm"],$row["gh_acc1"],$row["pay_acc1"],$row["cnt_acc1"],$row["k2pk1"]);
			pay2add1($row["wm2"],$row["pay_acc_pr2"],$row["wm2"],$row["gh_acc2"],$row["pay_acc2"],$row["cnt_acc2"],$row["k2pk1"]==0?0:-1);
			$gh_prev+=$row["gh_prev"];
			$pay_prev+=$row["pay_prev"];
			$row=mysql_fetch_assoc($res);
		}
		$gh_prev=number_format($gh_prev,3,".","");
		$pay_prev=number_format($pay_prev,dl_pay,".","");
		foreach($wms as $key=>&$value){
			$value["spay_pr"]=(dec_pay?floor($value["spay_pr"]*dec_pay)/dec_pay:$value["spay_pr"]);
			if($value["spay_pr"]>=min_pay){
				$wmcount++;
				$value["sgh"]=round($value["sgh"],3);
			}
		}
		$i=0;
		foreach($wms as $key=>&$value){
			if($value["spay_pr"]>=min_pay){
				$value["spay"]=(dec_pay?floor($value["spay"]*dec_pay)/dec_pay:$value["spay"]);
				echo "<tr";
				if($i)echo" class=\"n\"";
				echo">";
				if(!$i){
					echo "<td";
					if($wmcount>1)echo " rowspan=\"".$wmcount."\"";
					echo ">".$em."</td>";
				}
				echo "<td>".implode(", ",$value["aid"])."</td><td>".implode(", ",$value["awm"])."</td><td>".$value["sgh"]."</td><td>";
				ctrl2($value["spay"]."<br />"
					,bv7lg("Pay...","Оплатить...")
					,bv7lg("Pay","Оплатить")
						." <input type=\"text\" name=\"p\" value=\"".$value["spay"]."\"/>D<br /><br />"
						."<input type=\"hidden\" name=\"mp\" value=\"".urlencode($value["spay"])."\"/>"
						."<input type=\"hidden\" name=\"mc\" value=\"".urlencode($value["scnt"])."\"/>"
						."<input type=\"hidden\" name=\"a\" value=\"pay2\"/>"
						."<input type=\"hidden\" name=\"ak\" value=\"".implode("z",$value["aid"])."\"/>"
						."<input type=\"hidden\" name=\"ap\" value=\"".urlencode(implode("z",$value["apay"]))."\"/>"
						."<input type=\"hidden\" name=\"ac\" value=\"".urlencode(implode("z",$value["acnt"]))."\"/>"
						."<input type=\"hidden\" name=\"a2\" value=\"".urlencode(implode("z",$value["akr2"]))."\"/>"
					,bv7lg("Paid","Оплачено")
				);
				echo "</td><td>".implode(", ",$value["apr_add"])."</td><td>".$value["spay_pr"]."</td>";
				if(!$i){
					echo "<td";
					if($wmcount>1)echo " rowspan=\"".$wmcount."\"";
					echo ">".$gh_prev."</td><td";
					if($wmcount>1)echo " rowspan=\"".$wmcount."\"";
					echo ">".$pay_prev."</td>";
					$sgh_prev+=$gh_prev;
					$spay_prev+=$pay_prev;
				}
				echo "</tr>";
				$i++;
				$sgh+=$value["sgh"];
				$spay+=$value["spay"];
				$spay_pr+=$value["spay_pr"];
			}
		}
	}
	mysql_close($sql_srv);
	echo "<tr class=\"total\"><td colspan=\"3\">".bv7lg("Total","Итого")."</td><td align=\"right\">".$sgh."</td><td align=\"right\">".$spay."</td><td align=\"center\">X</td><td align=\"right\">".$spay_pr."</td><td align=\"right\">".number_format($sgh_prev,3,".","")."</td><td align=\"right\">".number_format($spay_prev,dl_pay,".","")."</td></tr>"
		."</tbody></table>";
}
echo "</body></html>";		