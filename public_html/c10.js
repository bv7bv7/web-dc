var c10s=new Array();
function C10(n,t,j){
	var name=n;
	var nj=j;
	var date=new Date(t*1000);
	var forDate=new Date();
	var tz=forDate.getTimezoneOffset();
	date.setMinutes(date.getMinutes()+forDate.getTimezoneOffset());
	var cells;
	var periods=[
		{
			'rows':4,
			'cols':5,
			'childPrefix':'',
			'next':function(sgn){date.setFullYear(date.getFullYear()+cells*sgn);},
			'getFullStr':function(){return forDate.getFullYear()+'-'+(forDate.getFullYear()+cells-1);},
			'getChildStr':function(d){return d.getFullYear();},
			'setFirstChild':function(){forDate.setFullYear(parseInt(forDate.getFullYear()/cells)*cells);},
			'setNextChild':function(d,sgn){d.setFullYear(d.getFullYear()+sgn);}
		},
		{
			'rows':3,
			'cols':4,
			'childPrefix':'-',
			'getChildStr':function(d){return ('0'+(d.getMonth()+1)).slice(-2);},
			'setFirstChild':function(){forDate.setMonth(0);},
			'setNextChild':function(d,sgn){
				var m=d.getMonth();
				d.setMonth(m+sgn);
				if((m+sgn)%12!=d.getMonth()){
					if(sgn>0)d.setDate(0);
					else d.setDate(d.getDate()+1);
				}
			}
		},
		{
			'rows':6,
			'cols':7,
			'childPrefix':'-',
			'head':(c10lg=='ru'?['Пн','Вт','Ср','Чт','Пт','Сб','Вс']:['Mon','Tues','Weds','Thurs','Fri','Sat','Sun']),
			'getChildStr':function(d){
				var s=('0'+d.getDate()).slice(-2);
				if(d.getMonth()!=date.getMonth())s='<span class="c10h">'+s+'</span>';
				return s;
			},
			'setFirstChild':function(){
				forDate.setDate(1);
				var dow=forDate.getDay();
				forDate.setDate(dow==0?-5:2-dow);
			},
			'setNextChild':function(d,sgn){d.setDate(d.getDate()+sgn);}
		},
		{
			'rows':4,
			'cols':6,
			'childPrefix':'&nbsp',
			'getChildStr':function(d){return ('0'+d.getHours()).slice(-2);},
			'setFirstChild':function(){forDate.setHours(0);},
			'setNextChild':function(d,sgn){d.setHours(d.getHours()+sgn);}
		},
		{
			'rows':6,
			'cols':10,
			'childPrefix':':',
			'getChildStr':function(d){return('0'+d.getMinutes()).slice(-2);},
			'setFirstChild':function(){forDate.setMinutes(0);},
			'setNextChild':function(d,sgn){d.setMinutes(d.getMinutes()+sgn);}
		}
	];
	var idx=periods.length - 1;
	function getTime(d){return parseInt(d.getTime()/1000);}
	function getHtmlStr(id){
		var e;
		var s='';
		for(var i=0;i<id;i++){
			s+=periods[i].childPrefix+'<span';
			if(i==idx&&document.getElementById('c10e'+nj)!=null&&inputVisible())s+=' class="c10e"';
			s+='>'+periods[i].getChildStr(date)+'</span>';
		}
		return s;
	}
	function getHtmlAnchor(){return '<input type="hidden" name="'+name+'" value="'+(getTime(date)/*+tz*60*/)+'"/><input type="hidden" name="tz'+name+'" value="'+tz+'"/>'+getHtmlStr(periods.length);}
	function inputRefresh(){
		var s;
		var p=periods[idx];
		cells=p.rows*p.cols;
		forDate.setTime(date.getTime());
		p.setFirstChild();
		if(idx==0)s=p.getFullStr();
		else s=getHtmlStr(idx);
		if(idx>0)s='<a onclick="return c10s[\''+nj+'\'].clickUp();">'+s+'</a>';
		s='<table><tr><td><table><tr><td><a onclick="return c10s[\''+nj+'\'].clickNext(-1);">&#9668;</a></td><td>'+s+'</td><td><a onclick="return c10s[\''+nj+'\'].clickNext(1);">&#9658;</a></td></tr></table></td></tr><tr><td><table>';
		if('head'in p){
			s+='<tr>';
			for(var c=0;c<p.cols;c++)s+='<th>'+p.head[c]+'</th>';
			s+='</tr>';
		}
		for(var r=0;r<p.rows;r++){
			s+='<tr>';
			for(var c=0;c<p.cols;c++,p.setNextChild(forDate,1))s+='<td><a onclick="return c10s[\''+nj+'\'].clickTable('+getTime(forDate)+');">'+p.getChildStr(forDate)+'</a></td>';
			s+='</tr>';
		}
		document.getElementById('c10e'+nj).innerHTML=s+'<table></td></tr></table>';
		anchorRefresh();
	}
	function inputStyle(){return document.getElementById('c10e'+nj).style;}
	function inputHide(){
		inputStyle().display="none";
		anchorRefresh();
	}
	function inputVisible(){return inputStyle().display=="";}
	function anchorRefresh(){document.getElementById('c10v'+nj).innerHTML=getHtmlAnchor();}
	this.anchorClick=function(){
		if(inputVisible())inputHide();
		else{
			var s=inputStyle();
			idx=periods.length - 1;
			s.display="";
			inputRefresh();
		}
	}
	this.clickNext=function(sgn){
		if(idx==0)periods[idx].next(sgn);
		else periods[idx-1].setNextChild(date,sgn)
		inputRefresh();
	}
	this.clickUp=function(){
		idx--;
		inputRefresh();
	}
	this.clickTable=function(t){
		date.setTime(t*1000);
		idx++;
		if(idx<periods.length)inputRefresh();
		else{
			idx=0;
			inputHide();
		}
	}
	document.write('<a id="c10v'+nj+'" onclick="return c10s[\''+nj+'\'].anchorClick();">'+getHtmlAnchor()+'</a><div id="c10e'+nj+'" style="display:none;"></div>');
}
function c10init(n,t){
	var j=c10s.length;
	c10s[j]=new C10(n,t,j);
}
function c10tz(){
	var tz=-(new Date()).getTimezoneOffset()/60;
	document.write('<input type="hidden" name="tz" value="'+tz+'"/>&nbsp;UTC'+(tz>=0?'+':'')+tz);
}
