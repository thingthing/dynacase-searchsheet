<?php
/**
 * View report for S2
 *
 * @author Anakeen 2008
 * @version $Id: Lib.SearchSheet.php,v 1.1 2008/05/16 13:25:15 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package SPREADSHEET
 */
 /**
 */
include_once("FDL/Lib.Dir.php");
/**
 * Project maker
 * @param Action &$action current action
 */
function aeres_s2_report(&$action) {

  $famid="E_UR";
  $filtre=array();
  $dbaccess=$action->getParam("FREEDOM_DB");
  $tdoc= getChildDoc($dbaccess,0,
		     "0","ALL",
		     $filtre,  			
		     $action->user->id,
		     "ITEM",
		     $famid  );
  $yellow="#e4ff4d";
  $green="#a6e296";
  $blue="#96bae3";
  $cols=array("title"=>array("color"=>$green,
			     "function"=>"htmltitle",
			     "head"=>_("Declaration")),
	      "etab"=>array("color"=>$blue,
			    "attribute"=>"ae_etab",
			    "head"=>"Etablissement"),
	      "sector"=>array("color"=>$blue,
			      "head"=>"Secteur",
			      "attribute"=>"aeur_tds_principal"),
	      "dt"=>array("color"=>$yellow,
			  "head"=>"type de demande",
			  "attribute"=>"aeur_td_type"),
	      "valeur"=>array("color"=>$yellow,
			      "head"=>_("label precedent"),
			      "attribute"=>"s2eva_identite:adecu_filabe"),
	      "intitulé"=>array("color"=>$yellow,
				"head"=>_("intitule du dossier"),
				"attribute"=>"s2eva_identite:adecu_intitule"),
	      "reponsable"=>array("color"=>$yellow,
				"head"=>_("reponsable"),
				"attribute"=>"ae_rnom"),
	      "organisme"=>array("color"=>$yellow,
				"head"=>_("organisme"),
				"attribute"=>"s2eva_identite:adecu_onom"),
	      "vague"=>array("color"=>$yellow,
			     "attribute"=>"eva_vague"),
	      "rapportrecu"=>array("dyncolor"=>"colordaterepfuture",
				   "head"=>"rapport recu",
				   "color"=>$green,
				   "attribute"=>"s2eva_rep_date"),
	      "tutelle1"=>array("color"=>$green,				
				"attribute"=>"s2eva_cir_orga"),
	      "tutelle2"=>array("color"=>$green,				
				"attribute"=>"s2eva_cir_send"),
	      "tutelle3"=>array("color"=>$green,				
				"attribute"=>"s2eva_cir_daterep"),
	      "comment"=>array("color"=>$yellow,
			       "attribute"=>"s2eva_com_com"));
    
  $fdoc=new_doc($dbaccess,$famid);


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
  while ($v=getNextDoc($dbaccess,$tdoc)) {
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
      $cells[$kc]["odd"] = $odd;
      $cells[$kc]["docid"]=$v->id;
      $kc++;
   }
    $rows[]=$cells;
    $odd = ($odd ? false : true);
  }
  $action->lay->set("num", "Section 2");
  $action->lay->set("thereport",
		    makeHtmlTable($action,$cols,$rows));
}

function htmltitle(&$doc) {
  $ulink=getParam("CORE_STANDURL")."&app=FDL&action=FDL_CARD&id=".$doc->id;
  return "<a target=\"_blank\" href=\"$ulink\" oncontextmenu=\"popdoc(event,'$ulink');return false;\">".$doc->title."</a>";
}
function colordaterepfuture(&$doc,$content="") {
  $orange='#f1d79f';
  $red='#f1d79f';
  $d=$doc->getValue("s2eva_rep_date");
  $d=$content;
  if (! $d) return $orange;
  $r=$doc->isFutureDate($d);

  if ($r["err"]!="") return $red;

}
function makeHtmlTable($cols,$rows) {
  global $action;
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/FREEDOM/Layout/sorttable.js");
  $action->parent->AddJsRef("WHAT/Layout/prototype.js");
  $action->parent->AddJsRef("SEARCHSHEET/Layout/dynamictable.js");
  $action->parent->AddCssRef("SEARCHSHEET:dynamictable.css",true);
  
  $action->parent->AddJsRef($action->GetParam("CORE_STANDURL")."app=FDL&action=VIEWDOCJS");
  $lay=new Layout(getLayoutFile("SEARCHSHEET","dynamictable.xml"),$action);
  $head=array();
  foreach ($cols as $kc=>$vc) {
      $head[]=array("head"=>$vc["head"]);      
  }
  $htmlrows=array();
  foreach ($rows as $k=>$v) {
    for ($i=0;$i<1;$i++) 
    $htmlrows[]=makeRow($v);
  }

  $lay->setBlockData("HEAD",$head);
  $lay->setBlockData("HEAD2",$head);
  $lay->set("ROWS",implode($htmlrows,"\n"));
  return $lay->gen();
}

function makeRow($tcells) {
  static $lay;
  if (! $lay) $lay=new Layout(getLayoutFile("SEARCHSHEET","dynamictablerow.xml"));
  $lay->setBlockData("CELLS",$tcells);

  $first=current($tcells);
  $lay->set("docid",$first["docid"]);

  return $lay->gen();
  

}
?>