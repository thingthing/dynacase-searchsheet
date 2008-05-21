<?php
public $defaultview="SEARCHSHEET:VIEWSEARCHSHEET";

function viewsearchsheet() { 
  $this->lay->set("ID", $this->id);
  $limit=$this->getValue("ssh_limit");
  $this->lay->set("limit", $limit);
  $this->lay->set("thereport",$this->getHTMLReport());



  // set actions now
  $tapp=$this->getTValue("ssh_application");
  $tact=$this->getTValue("ssh_action");
  $targ=$this->getTValue("ssh_attribute");
  $taid=$this->getTValue("ssh_adocids");
  $tlbl=$this->getTValue("ssh_alabel");
  $ttgt=$this->getTValue("ssh_atarget");
  $tactions=array();
  foreach ($tapp as $k=>$v) {
    $tactions[$k]=array("act"=>$tact[$k],
			"app"=>$tapp[$k],
			"arg"=>$targ[$k],
			"aid"=>$taid[$k],
			"tgt"=>$ttgt[$k],
			"lbl"=>$tlbl[$k]);
  }
  $this->lay->setBlockData("ACTIONS",$tactions);
  $limits=array();
  foreach (array(10,20,50,100) as $v) {
    $limits[$v]=$v;
  }
  $limits[$limit]=$limit;
  asort($limits);
  $limits["ALL"]=_("no limit");

  foreach ($limits as $k=>$v) {
    $topt[]=array("optval"=>$k,
		  "optselected"=>($k==$limit)?"selected":"",
		  "optlabel"=>$v);
  }

  $this->lay->setBlockData("optlimit",$topt);

}


function refreshreport() {
  $filter=getHttpVars("filter");
  $f="";
  if ($filter) {
    $filter=substr($filter,1,-1);
    $filters=explode('][',$filter);
    foreach ($filters as $k=>$v) {
      list($col,$val)=explode('|',$v);
      $f[$col]=strtolower($val);
    }
  }

  $this->lay->template=$this->getHTMLReport($f,"",getHttpVars("limit"),getHttpVars("page"));

}

function getHTMLReport($filters="",$sort="",$limit="",$page="",$type="html") {
  include_once("SEARCHSHEET/Lib.SearchSheet.php");
  include_once("FDL/Class.SearchDoc.php");

  $famid=$this->getValue("ssh_idfamily");
  $s=new SearchDoc($this->dbaccess);  
  $s->dirid=$this->getValue("ssh_idsearch");
  $s->setObjectReturn();
  if ($limit=="") $limit=intval($this->getValue("ssh_limit"));
  else $limit=intval($limit);

  if ($page > 0) $start=($limit*$page);
  else $start=0;

  //  if (($limit > 0) && ($filters=="")) $s->slice=$limit;
  $tdoc=$s->search();

  $this->lay->set("NEEDLIMIT",($limit > 0) && ($limit <= $s->count()));
  if ($limit > 0) $this->lay->set("pagesnumber",floor(($s->count()/$limit)-0.001)+1);
  else $this->lay->set("pagesnumber","");
  $yellow="#e4ff4d";
  $green="#a6e296";
  $blue="#96bae3";

  $taids=$this->getTValue("ssh_idacol");
  $tas=$this->getTValue("ssh_acol");
  $talabel=$this->getTValue("ssh_lcol");
  $tstyle=$this->getTValue("ssh_stylecol");
  $tdyncolor=$this->getTValue("ssh_bgdyncolor");
  $tbgcolor=$this->getTValue("ssh_bgcolor");
  $tdynval=$this->getTValue("ssh_dynvalue");
  $cols=array();
  foreach ($taids as $k=>$v) {
    $cols[]=array("color"=>$tbgcolor[$k],
		  "attribute"=>$v,
		  "head"=>($talabel[$k]=="")?$tas[$k]:$talabel[$k],
		  "style"=>$tstyle[$k],
		  "dyncolor"=>$tdyncolor[$k],
		  "function"=>$tdynval[$k]);
      
  }
  
    
  $fdoc=new_doc($this->dbaccess,$famid);


  // complete head label
  foreach ($cols as $k=>$v) {    
      if ($v["attribute"]) {
	if ($cols[$k]["head"]=="") {
	  $oa=$fdoc->getAttribute($v["attribute"]);
	  if ($oa) $cols[$k]["head"]=ucfirst($oa->labelText);
	} else {
	  $cols[$k]["head"]=ucfirst($cols[$k]["head"]);
	}
	if (($v["attribute"]=="title") && ($type=='html')){
	  $cols[$k]["function"]="htmltitle";
	}
      }
       $cols[$k]["filtervalue"]="";
  }

  if ($filters) {
    foreach ($filters as $kf=>$vf) {
      $cols[$kf]["filtervalue"]=$vf;
    }
  }
  $rows=array();
  $odd=false;

  // set values  
  $nbdoc=0;
  while ($v=$s->nextDoc()) {
    //    print_r2($v->getValues());
    $cells=array();
    $kc=0; 
    foreach ($cols as $kc=>$vc) { 
      if ($vc["function"]) {
	$ft=$vc["function"];
	if (substr($ft,0,2)=='::') {
	  $cells[$kc]=array("content"=>$v->applyMethod($ft,"",-1,array($v->getRValue($vc["attribute"]))));
	} else {
	  if (function_exists($ft)) {	    
	    $cells[$kc]=array("content"=>$ft($v));
	  } else {
	    $cells[$kc]=array("content"=>sprintf(_("method or function %s not exists"),$vc["function"]));
	  }
	}
      } else {if ($vc["attribute"]) {
	  if (strstr($vc["attribute"],":")) {
	    $cells[$kc]=array("content"=>$v->getRValue($vc["attribute"],"",true,($type=='html')));
	  } else {
	      if ($type=='html')  $cells[$kc]=array("content"=>$v->getHtmlAttrValue($vc["attribute"],'_blank'));
	      else $cells[$kc]=array("content"=>$v->getValue($vc["attribute"]));
	  }
	} else   {
	  $cells[$kc]=array("content"=>"nc");	
	}
      }
      if ($vc["dyncolor"]) {
	$ft=$vc["dyncolor"];
	if ($ft) {
	  $cells[$kc]["color"]=$v->applyMethod($ft,"",-1,array($cells[$kc]["content"]));
	} 
	
	//	$cells[$kc]["color"]=$ft($v,$cells[$kc]["content"]);
	if ($cells[$kc]["color"]=="") $cells[$kc]["color"]=$vc["color"];

      } else $cells[$kc]["color"]=$vc["color"];
      $cells[$kc]["style"]=$vc["style"];
      $cells[$kc]["odd"] = $odd;
      $cells[$kc]["docid"]=$v->id;
      $kc++;
    }
    
    $good=true;
    if ($filters) {
      foreach ($filters as $kf=>$vf) {
	if (! strstr(strtolower($cells[$kf]["content"]),$vf)) {
	  $good=false;
	  break;
	}
      }
    }

    if ($good) {
      if ($start > 0) {
	$start--;
      } else {
	$rows[]=$cells;
	$nbdoc++;
	if (($limit > 0) && ($nbdoc >= $limit)) break;
      }
    }
  }

  if ($type=="html")  return (makeHtmlTable($cols,$rows,$this->lay->get("NEEDLIMIT"),($limit==0) || ($nbdoc<$limit)));
  else return (makeCsvTable($cols,$rows,$this->lay->get("NEEDLIMIT"),($limit==0) || ($nbdoc<$limit)));
}

/**
 * return csv shhet
 */
function csvsearchsheet() {
  

  $this->lay->template=$this->getHTMLReport($f,"",getHttpVars("limit"),getHttpVars("page"),"csv");

}

?>