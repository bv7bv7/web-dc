<?php
class C8{
	var $last;
	var $cur;
	var $step;
	var $prev;
	var $expr;
	function __construct($last,$step,$expr='$v'){
		$this->last=$last;
		$this->cur=isset($_GET['o'])?$_GET['o']:$last;
		$this->prev=$this->cur-($this->step=$step);
		$this->expr='return('.$expr.');';
	}
	function ToString($v){return(eval($this->expr));}
	function Parse(){
		$a='<a href="'.$_SERVER['PHP_SELF'].'?';
		foreach($_GET as $key=>$value)if($key!='o')$a.=$key.'='.$value.'&';
		echo'<div class="c4m"><div>'.($a.='o=').$this->prev."\">".$this->ToString($this->prev)."&#9668;</a>";
		if($this->cur<$this->last){
			if(($next=$this->cur+$this->step)<$this->last)echo"&nbsp;".$a.$next."\">&#9658;".$this->ToString($next)."</a>";
			echo"&nbsp;".$a.$this->last."\">&#9658;&#124;".$this->ToString($this->last)."</a>&nbsp;";
		}
		echo"</div></div>";
	}
}
echo"<link rel=\"stylesheet\" type=\"text/css\" href=\"ctrl1.css\"/>";
?>