var tasks = new Array();
function hFS(files){
for(var i=0,f;f=files[i];i++){
var reader=new FileReader();
reader.onload=(function(theFile){
	return function(e) {
		var task;
		var iemi;
		var hash;
		if(/^.*\.(bcl|fnx)$/i.test(theFile.name)){
			iemi=/^imei=(\d{15})$/im.exec(e.target.result);
			hash=/^hash=([0-9A-F]{40})[0-9A-F]*$/im.exec(e.target.result);
		}else if(/^.*\.(log|txt)$/i.test(theFile.name)){
			iemi=/^(\d{15})\s*$/m.exec(e.target.result);
			hash=/^h?([0-9A-F]{40})[0-9A-F]*\s*$/im.exec(e.target.result);
		}else if(/^.*\d{15}\.sha$/i.test(theFile.name)){
			if(/\s*\[IMEI\]\s+(\d{15})\s*/i.test(e.target.result))
				iemi=/\s*\[IMEI\]\s+(\d{15})\s*/i.exec(e.target.result);
			else
				iemi=/^.*(\d{15}).sha$/im.exec(theFile.name);
			hash=/^([0-9A-F]{40})[0-9A-F]*$/im.exec(e.target.result);
		}else if(/^.*\d{15}.*Hashcat.*\.bat$/i.test(theFile.name)||/^.*Hashcat.*\d{15}.*\.bat$/i.test(theFile.name)){
			iemi=/^.*(\d{15}).*$/im.exec(theFile.name);
			hash=/^\w+Hashcat-lite\d{2}\s([0-9A-F]{40}):00\d{14}00\s.+$/im.exec(e.target.result);
		}else if(/^.*\d{15}.*ighashgpu.*\.bat$/i.test(theFile.name)||/^.*ighashgpu.*\d{15}.*\.bat$/i.test(theFile.name)){
			iemi=/^.*(\d{15}).*$/im.exec(theFile.name);
			hash=/^.+\s-h:([0-9A-F]{40})\s.+$/im.exec(e.target.result);
		}
		if(iemi!==undefined||hash!==undefined){
			var ii=iemi[1];
			var hh=hash[1];
			var ok=true;
			for(var i=0;i<tasks.length&&ok;i++)ok=tasks[i].iemi!=ii||tasks[i].hash!=hh;
			if(ok){
				var i=tasks.length;
				tasks[i]=new Object();
				tasks[i].iemi=ii;
				tasks[i].hash=hh;
				var el=window.document.createElement('tr');
				el.innerHTML="<td>"+(i+1)+"</td><td><input type='text' name='iemi[]' value='"+ii+"' size='15' readonly='readonly' /></td><td><input type='text' name='hs[]' value='"+hh+"' size='50' readonly='readonly' /></td>";
				window.document.getElementById('input').insertBefore(el, null);
				window.document.getElementById('submit').style.display="";
			}
		}
    	};
})(f);
reader.readAsText(f);
}};
function handleFileSelect(evt){hFS(evt.target.files)};
function handleFileDrop(evt){
evt.stopPropagation();
evt.preventDefault();
hFS(evt.dataTransfer.files);
};
function handleDragOver(evt){
evt.stopPropagation();
evt.preventDefault();
evt.dataTransfer.dropEffect='copy';
};
window.document.getElementById('files').addEventListener('change',handleFileSelect,false);
var dropZone=window.document.getElementById('drop_zone');
dropZone.addEventListener('dragover',handleDragOver,false);
dropZone.addEventListener('drop',handleFileDrop,false);