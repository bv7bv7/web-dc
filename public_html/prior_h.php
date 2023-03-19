<?php
function inputPrior($prior,$id,$fast=false){
	$input='';
	$nm=$fast?'prf':'pr';
	for($i=5;$i>=-5;$i--){
		$input.='<input type="radio" name="'.$nm.'" value="'.$i.'"';
		if($i==$prior)$input.=' checked="checked"';
		$input.='/>&nbsp;'.$i.'<br/>';
	}
	return $input.'<input type="hidden" name="a" value="'.$nm.'"/><input type="hidden" name="k" value="'.$id.'"/>';
}
function viewMU($mu){return $mu?$mu:'&infin;';}
function inputMU($mu,$id){return '<input type="text" name="mu" value="'.$mu.'" size="5"/><input type="hidden" name="a" value="mu"/><input type="hidden" name="k" value="'.$id.'"/>';}
?>