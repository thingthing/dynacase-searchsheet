<?php

function viewsearchsheet() {
  include_once("SEARCHSHEET/Lib.SearchSheet.php");
  include_once("FDL/Class.SearchDoc.php");

  $famid=$this->getValue("ssh_idfamily");
  $s=new SearchDoc($this->dbaccess);
  $s->dirid=$this->getValue("ssh_idsearch");
  $s->setObjectReturn();
  $tdoc=$s->search();

  $yellow="#e4ff4d";
  $green="#a6e296";
  $blue="#96bae3";

  $taids=$this->getTValue("ssh_idacol");
  $tas=$this->getTValue("ssh_acol");
  $talabel=$this->getTValue("ssh_lcol");
  $tstyle=$this->getTValue("ssh_stylecol");
  $tbgcolor=$this->getTValue("ssh_bgcolor");
  $cols=array();
  foreach ($taids as $k=>$v) {
    $cols[]=array("color"=>$tbgcolor[$k],
		  "attribute"=>$v,
		  "head"=>($talabel[$k]=="")?$tas[$k]:$talabel[$k],
		  "style"=>$tstyle[$k],
		  "dyncolor"=>"",
		  "function"=>"");

      
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
      }    
  }

  $rows=array();
  $odd=false;

  // set values
  while ($v=$s->nextDoc()) {
    //    print_r2($v->getValues());
    $cells=array();
    $kc=0; 
    foreach ($cols as $kc=>$vc) { 
      if ($vc["attribute"]) {
	if (strstr($vc["attribute"],":")) {
	    $cells[$kc]=array("content"=>$v->getRValue($vc["attribute"]));
	  } else {
	    $cells[$kc]=array("content"=>$v->getHtmlAttrValue($vc["attribute"],'_blank'));
	  }
      } else if ($vc["function"]) {
	$ft=$vc["function"];
	$cells[$kc]=array("content"=>$ft($v));
      } else {
	$cells[$kc]=array("content"=>"nc");	
      }
      if ($vc["dyncolor"]) {
	$ft=$vc["dyncolor"];
	$cells[$kc]["color"]=$ft($v,$cells[$kc]["content"]);
	if ($cells[$kc]["color"]=="") $cells[$kc]["color"]=$vc["color"];

      } else $cells[$kc]["color"]=$vc["color"];
      $cells[$kc]["style"]=$vc["style"];
      $cells[$kc]["odd"] = $odd;
      $cells[$kc]["docid"]=$v->id;
      $kc++;
   }
    $rows[]=$cells;
    $odd = ($odd ? false : true);
  }

  $this->lay->set("num", "Section 2");
  $this->lay->set("thereport",
		    makeHtmlTable($cols,$rows));
}

?>