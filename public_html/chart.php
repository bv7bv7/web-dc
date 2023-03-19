<?php
class ChGroup{
	var $charts;
	const vCnt=30;
	function ChGroup($expr,$head){
		$this->expr="\$this->v=".$expr.";";
		$this->head=$head;
	}
	function value(){
		global $row;
		eval($this->expr);
		return($this->v);
	}
	function Parse($head){
		global $res,$row;
		$chCnt=count($this->charts);
		$vCnt=round(self::vCnt/$chCnt);
		$gr1pr=floor(100/$chCnt);
		$cc=0;
		while($row=mysql_fetch_assoc($res)){
			foreach($this->charts as $chart)if($chart->max<$chart->value())$chart->max=$chart->v;
			$cc++;
		}
		foreach($this->charts as $chart){
			if($chart->int)$chart->max=max(ceil($chart->max/$vCnt)*$vCnt,$vCnt);
			$chart->step=$chart->max/$vCnt;
		}
		$prt=round(100/max($cc,1),3);
		$pr1=100/$vCnt;
		echo"<style type=\"text/css\">"
				.".gsl>div{height:".$pr1."%;}"
				.".gsl>div:first-child{height:".($pr1*1.5)."%;}"
				.".gsl>div:last-child{height:".($pr1*0.5)."%;}"
				.".gtb>tbody>tr>td{width:".$prt."%;}"
				.".gtb>tbody>tr:first-child>td,.gtb>tbody>tr:last-child>td{text-align:center;}"
				.".gtb>tbody>tr>td{min-width:25px;}"
				.".gsl{height:".$gr1pr."%;}"
				.".glh>div{height:".$pr1."%;}"
				.".glv>span{width:".$prt."%;}"
			."</style>"
			."</head><body>"
			."<table width=\"100%\" style=\"height:100%;\"><tbody>"
			."<tr><td colspan=\"2\">".$head."</td></tr>";
		foreach($this->charts as $chart){
			echo"<tr><td class=\"gsl\"><div>".$chart->head."</div>";
			for($i=$chart->max-$chart->step*2;$i>$chart->step/2;$i-=$chart->step)echo"<div>".($chart->int?$i:number_format($i,3,".",""))."</div>";
			echo"<div></div></td><td width=\"100%\" style=\"height:".$gr1pr."%;\"><div class=\"os\"><div class=\"glh\">";
			for($i=0;$i<$vCnt;$i++)echo"<div></div>";
			echo"</div><div class=\"glv\">";
			for($i=0;$i<$cc;$i++)echo"<span></span>";
			echo"</div><div class=\"gb\">";
			mysql_data_seek($res,0);
			for($i=0;$row=mysql_fetch_assoc($res);$i++)echo"<div style=\"top:".round(100*(1-($chart->value())/$chart->max),3)."%;height:".round($chart->v/$chart->max*100,3)."%;left:".round(100/$cc*$i,3)."%;width:".$prt."%\" title=\"".$this->head." ".$this->value().": ".round($chart->v,3)." ".$chart->head."\"></div>";
			echo"</div></div></td></tr>";
		}
		echo"<tr><td>".$this->head."</td><td valign=\"top\"><table class=\"gtb\" width=\"100%\"><tbody><tr valign=\"top\">";
		mysql_data_seek($res,0);
		while($row=mysql_fetch_assoc($res))echo"<td>".$this->value()."</td>";
		echo"</tr></tbody></table></td></tr></tbody></table>";
	}
}
class Chart{
	var $expr;
	var $head;
	var $max;
	var $step;
	var $int;
	var $v;
	function Chart($expr,$head,$minMax=false){
		$this->expr="\$this->v=".$expr.";";
		$this->head=$head;
		$this->int=$minMax===false;
		if(!$this->int)$this->max=$minMax;
	}
	function value(){
		global $row;
		eval($this->expr);
		return($this->v);
	}
}
echo"<!DOCTYPE html><html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/><link rel=\"stylesheet\" type=\"text/css\" href=\"s1.css\"/>";