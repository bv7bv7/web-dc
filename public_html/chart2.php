<?php
class ChGroup{
	var $charts;
	var $v;
	var $top;
	var $height;
	var $left;
	var $width;
	var $title;
	var $count;
	var $i;
	var $gridWidth;
	var $present;
	const vCnt=30;
	const vReserv=2;
	function __construct($expr,$head){
		$this->expr="\$this->v=".$expr.";";
		$this->head=$head;
	}
	function value(){
		global $row;
		eval($this->expr);
		return($this->v);
	}
	function SetGridWidth(){$this->width=$this->gridWidth=round(100/max($this->count,1),3);}
	function CalcLW(){
		$this->left=round(100/$this->count*$this->i,3);
		$this->title=$this->head." ".$this->value();
	}
	function CalcRect(&$chart){
		$chart->value();
		if($chart->v!==NULL){
			$this->top=round(100*(1-($chart->v)/$chart->max),3);
			$this->height=round($chart->v/$chart->max*100,3);
		}
		$this->CalcLW();
	}
	function Bottom(){
		global $res,$row;
		if($this->present){
			mysql_data_seek($res,0);
			while($row=mysql_fetch_assoc($res))echo"<td>".$this->value()."</td>";
		}
	}
	function Parse($head){
		global $res,$row,$prevRow;
		$chCnt=count($this->charts);
		$vCnt=round(self::vCnt/$chCnt);
		$gr1pr=floor(100/$chCnt);
		$this->count=0;
		$prevRow=NULL;
		$this->present=false;
		while($row=mysql_fetch_assoc($res)){
			foreach($this->charts as $chart)if($chart->value()!==NULL&&$chart->max<$chart->v)$chart->max=$chart->v;
			$this->count++;
			$prevRow=$row;
			$this->present=true;
		}
		$this->SetGridWidth();
		foreach($this->charts as $chart){
			if($chart->int)$chart->max=max(ceil($chart->max/($vCnt-self::vReserv))*$vCnt,$vCnt);
			else $chart->max=$chart->max/($vCnt-self::vReserv)*$vCnt;
			$chart->step=$chart->max/$vCnt;
		}
		$pr1=100/$vCnt;
		echo"<style type=\"text/css\">"
				.".gsl>div{height:".$pr1."%;}"
				.".gsl>div:first-child{height:".($pr1*1.5)."%;}"
				.".gsl>div:last-child{height:".($pr1*0.5)."%;}"
				.".gtb>tbody>tr>td{width:".$this->gridWidth."%;}"
				.".gtb>tbody>tr:first-child>td,.gtb>tbody>tr:last-child>td{text-align:center;}"
				.".gtb>tbody>tr>td{min-width:25px;}"
				.".gsl{height:".$gr1pr."%;}"
				.".glh>div{height:".$pr1."%;}"
				.".glv>span{width:".$this->gridWidth."%;}"
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
			for($this->i=0;$this->i<$this->count;$this->i++)echo"<span></span>";
			echo"</div><div class=\"gb\">";
			$prevRow=NULL;
			if($this->present){
				mysql_data_seek($res,0);
				for($this->i=0;$row=mysql_fetch_assoc($res);$this->i++){
					$this->CalcRect($chart);
					if($this->left!==NULL&&$chart->v!==NULL)echo"<div style=\"top:".$this->top."%;height:".$this->height."%;left:".$this->left."%;width:".$this->width."%\" title=\"".$this->title.": ".round($chart->v,3)." ".$chart->head."\"></div>";
					$prevRow=$row;
				}
			}
			echo"</div></div></td></tr>";
		}
		echo'<tr><td class="glb">'.$this->head.'</td><td valign="top"><table class="gtb" width="100%"><tbody><tr valign="top">';
		$this->Bottom();
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
	function __construct($expr,$head,$minMax=false){
		$this->expr="\$this->v=".$expr.";";
		$this->head=$head;
		$this->int=$minMax===false;
		if(!$this->int)$this->max=$minMax;
	}
	function value(){
		global $row,$prevRow;
		eval($this->expr);
		return($this->v);
	}
}
class ChGroupX extends ChGroup{
	var $start;
	var $end;
	var $step;
	var $right;
	var $headX;
	var $prevV;
	var $headXExpr;
	function __construct($expr,$head,$start,$end,$step,$headXExpr='$v'){
		parent::__construct($expr,$head);
		$this->expr='$this->prevV=$this->v;'.$this->expr;
		$this->start=$start;
		$this->end=$end;
		$this->step=$step;
		$this->right=NULL;
		$this->v=NULL;
		$this->headXExpr=$headXExpr;
	}
	function SetGridWidth(){
		$this->count=($this->end-$this->start)/$this->step;
		parent::SetGridWidth();
	}
	function CalcLW(){
		$this->left=$this->right;
		$this->right=100*($this->value()-$this->start)/($this->end-$this->start);
		$h=$this->GetHeadX($this->v);
		if($this->right<=0)$this->left=NULL;
		else if($this->left!==NULL){
			if($this->left<0)$this->left=0;
			$this->width=$this->right-$this->left;
			$this->title=$this->headX."-".$h;
		}
		$this->headX=$h;
	}
	function GetHeadX($v){return(eval('return('.$this->headXExpr.');'));}
	function Bottom(){
		echo'<td style="width:'.($w=$this->gridWidth/2).'%;min-width:13px%;">&nbsp;</td>';
		for($i=$this->start+$this->step;$i<$this->end;$i+=$this->step)echo"<td>".$this->GetHeadX($i)."</td>";
		echo'<td style="width:'.$w.'%;min-width:12px;">&nbsp;</td>';
	}
}
echo"<!DOCTYPE html><html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/><link rel=\"stylesheet\" type=\"text/css\" href=\"s1.css\"/>";