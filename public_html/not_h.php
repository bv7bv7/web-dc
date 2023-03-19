<?php
function viewNotify($notify){return $notify?bv7lg("Yes","Да"):bv7lg("No","Нет");}
function inputNotify($notify,$key,$action="n"){
	return "<p><input type=\"radio\" name=\"n\" value=\"0\"".($notify?" checked=\"checked\"":"")."/>&nbsp;".bv7lg("Do&nbsp;not&nbsp;notify", "Не&nbsp;оповещать")
		."<br/><input type=\"radio\" name=\"n\" value=\"1\"".($notify?"":" checked=\"checked\"")."/>&nbsp;".bv7lg("Notify","Оповещать")
		."</p><input type=\"hidden\" name=\"a\" value=\"".$action."\"/><input type=\"hidden\" name=\"k\" value=\"".$key."\"/>";
}
?>