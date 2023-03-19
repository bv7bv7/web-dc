var ctrl1IFrameObj;
function c1se(id){
document.getElementById("ctrl1v"+id).style.display="none";
document.getElementById("c1e"+id).style.display="";
return false;
};
function c1sv(id){
document.getElementById("c1e"+id).style.display="none";
document.getElementById("ctrl1v"+id).style.display="";
return true;
};
function c3se(id){
document.getElementById("c3e"+id).style.display=document.getElementById("c3e"+id).style.display==""?"none":"";
return false;
};
function c3sv(id){
document.getElementById("c3e"+id).style.display="none";
return true;
};
function ctrl1buildQueryString(theFormName){
var f=document.forms[theFormName];var s='';var e;
for(i=0;i<f.elements.length;i++){e=f.elements[i];if(e.name!=''&&((e.type!="radio"&&e.type!="checkbox")||e.checked))s+=((s=='')?'?':'&')+e.name+'='+encodeURIComponent(e.value);}
return s;
}
function ctrl1callToServerA(id){
if(!document.createElement) return true;
var IFrameDoc;var s=ctrl1buildQueryString("c1f"+id);
var URL=window.location.pathname+((s=='')?'?':s+'&')+'ctrl1id='+id;
if(!ctrl1IFrameObj && document.createElement){
	var tempIFrame=document.createElement('iframe');
	tempIFrame.setAttribute('id','ctrl1IFrame');
	tempIFrame.style.border='0px';
	tempIFrame.style.width='0px';
	tempIFrame.style.height='0px';
	ctrl1IFrameObj = document.body.appendChild(tempIFrame);
	if (document.frames) ctrl1IFrameObj = document.frames['ctrl1IFrame'];
}
if(navigator.userAgent.indexOf('Gecko') !=-1 && !ctrl1IFrameObj.contentDocument){
	setTimeout('ctrl1callToServerA('+id+')',10);
	return false;
}
if (ctrl1IFrameObj.contentDocument) IFrameDoc = ctrl1IFrameObj.contentDocument;
else if(ctrl1IFrameObj.contentWindow) IFrameDoc = ctrl1IFrameObj.contentWindow.document;
else if(ctrl1IFrameObj.document) IFrameDoc = ctrl1IFrameObj.document;
else return true;
IFrameDoc.location.replace(URL);
return false;
}
function c1cs(id){
var r=ctrl1callToServerA(id);
if(!r)c1sv(id);
return r;
}
function c3cs(id){
var r=ctrl1callToServerA(id);
if(!r)c3sv(id);
return r;
}
function ctrl1handleResponse(id,html){
var e=document.getElementById("ctrl1v"+id);
e.innerHTML=html;
c1sv(id);
}
function ctrl2handleResponse(id,html){
var e=document.getElementById("ctrl1v"+id);
e.innerHTML=html;
var e=document.getElementById("c2v"+id);
e.className="c2v2";
c1sv(id);
}
function ctrl3handleResponse(id,view,input){
var e=document.getElementById("c3v"+id);
e.innerHTML=view;
if(input){
	var e=document.getElementById("c3i"+id);
	e.innerHTML=input;
}
c3sv(id);
}
