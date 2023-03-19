var c2IFrameObj;
function c2AddIFrame(){if(!c2IFrameObj && document.createElement){
var tempIFrame=document.createElement('iframe');
tempIFrame.setAttribute('id','c2IFrame');
tempIFrame.setAttribute('name','c2IFrame');
tempIFrame.style.border='0px';
tempIFrame.style.width='0px';
tempIFrame.style.height='0px';
c2IFrameObj=document.body.appendChild(tempIFrame);
if(document.frames)c2IFrameObj=document.frames['c2IFrame'];
}}
function c2se(id){
document.getElementById("c2x"+id).style.display="none";
document.getElementById("c2e"+id).style.display="";
return false;
};
function c2sv(id){
document.getElementById("c2e"+id).style.display="none";
document.getElementById("c2x"+id).style.display="";
return true;
};
function c2cs(id){
c2AddIFrame();
document.forms['c2f'+id].submit();
c2sv(id);
}
function ctrl2handleResponse(id,html){
var e=document.getElementById("c2x"+id);
e.innerHTML=html;
var e=document.getElementById("c2v"+id);
e.className="c2v2";
c2sv(id);
}