include_js('FREEDOM/Layout/sorttable.js');
var LIMIT=false;

function trackCR(event) {
  var intKeyCode;

  if (!event) event=window.event;
  intKeyCode=event.keyCode;
  if (intKeyCode == 13) return true;

  return false;
}

function filtertablerrow(event,inp) {  
  if (LIMIT) {
    resetReport();
  } else {
    var c=0;
    var rowth=inp.parentNode;
    var i=rowth.previousSiblings().length;
    var filter=inp.value.toLowerCase();
    //    alert(i+"~"+filter);
    var tbody=rowth.parentNode.parentNode.parentNode.getElementsByTagName('tbody')[0];


    var rows=tbody.getElementsByTagName('tr');
    var arows = $A(rows);
    arows.each(function(tr){
	filterrow(tr,i,filter);
      });
  }
}

function filterrow(row,col,filter) {
  var i;
  var filtertd=row.getElementsByTagName('td')[col];
  

  if (filtertd) {    
    var nbhide=row.readAttribute('nbhide');
    if (! nbhide) nbhide=0;
    nbhide=parseInt(nbhide);
    if (! filtertd.innerHTML.toLowerCase().include(filter)) {
      if (! row.readAttribute('hide'+col)) {
	row.writeAttribute('hide'+col,1);
	nbhide++;
	row.writeAttribute('nbhide',nbhide);
	row.hide();
      } 
    } else {      
      if (row.readAttribute('hide'+col)) {
	if (nbhide > 0)  nbhide--;
	row.writeAttribute('nbhide',nbhide);
	row.writeAttribute('hide'+col,false);
      }
      if (nbhide <= 0) 	{
	row.writeAttribute('nbhide',false);
	row.show();
      }
    }
    row.title=nbhide;
  } 
}

function sendReportAction(event,url,inputname) {
  var e; 
  if (! event.element)  e=window.event.srcElement;    
  else e=event.element();

  var newinputname=inputname+'[]';
  var target = e.getAttribute('target');
  var f=$('sendreport');
  f.action=url;
  f.target=target;
  var rows=f.getElementsByTagName('input');
  var arows = $A(rows);
  arows.each(function(inp){
      if (! inp.parentNode.parentNode.visible()) inp.checked=false;
      if ((inp.name) && (inp.getAttribute('isdocid')=='1'))   inp.name=newinputname;
    });

  f.submit();

}

function selectornotall(event) {
  var e; 
  
  e=$('checkall');
  if (!e) {
    alert('checkall');
  }
  var target = e.getAttribute('target');
  var f=$('sendreport');
 
  var rows=f.getElementsByTagName('input');
  var arows = $A(rows);
  arows.each(function(inp){
      if ((inp.name) && (inp.getAttribute('isdocid')=='1')) {
	if ($(inp.parentNode.parentNode).visible()) inp.checked=e.checked;
	else inp.checked=false;
      }
    });
}
function setUrlContent(aurl,cible){

  globalcursor('wait');
    var temp;
    new Ajax.Request(aurl, {
      method: 'get',
      asynchronous:false,
	  evalScripts:true,
      onComplete: function(transport) {        
        temp = transport.responseText;
      }
    });

    cible.innerHTML=temp.stripScripts();
    temp.evalScripts();
    unglobalcursor();

    return temp;
}

function resetReport() {
  var p=$('ipage');
  if (p) p.value=1; // reset pages
  refreshReport();
}

function refreshReport() {
  var corestandurl=window.location.pathname+'?sole=Y';
  var url=corestandurl+'&app=FDL&action=VIEWSCARD&zone=SEARCHSHEET:REFRESHREPORT';

  var f=$('sendreport');
  url+='&id='+$('sheetid').value;
  if ($('ilimit')) if ($('ilimit').value=='') $('ilimit').value='ALL';
  url+='&limit='+$('ilimit').getValue();
  if ($('ipage')) url+='&page='+(parseInt($('ipage').value)-1).toString();

  var rows=f.getElementsByTagName('input');
  var arows = $A(rows);
  var filter='';
  arows.each(function(inp){
      if (inp.getAttribute('colnumber') && (inp.getAttribute('colnumber')!='') && (inp.value!='')){
	if (inp.value)
	filter = filter+'['+inp.getAttribute('colnumber')+'|'+inp.value+']';
      }
    });
  url+='&filter='+filter;
  setUrlContent(url,$('report'));
}

function viewReportPage(offset) {
  if ($('ipage')) {
    var sl=parseInt($('ipage').value);
    sl+=offset;
    $('ipage').value=sl;
    viewPrevious();
  }
  refreshReport();
}

function reachLimit(reach) {
  var n=$('anext');
  if (n) {
    if (reach=='1')  n.style.visibility='hidden';
    else n.style.visibility='visible';
  }
}
function viewPrevious() {
  var n=$('aprev');
  var sl=parseInt($('ipage').value);
  if (sl > 1) n.style.visibility='visible';
  else n.style.visibility='hidden';
  var n=$('anext');
  if (n) n.style.visibility='visible';
}

function resetFilters() {
  var f=$('sendreport');  
  var rows=f.getElementsByTagName('input');
  var arows = $A(rows);
  
  arows.each(function(inp){
      if (inp.getAttribute('colnumber') && (inp.getAttribute('colnumber')!='') && (inp.value!='')){
	 inp.value='';	
      }
    });
  
  resetReport();
}
