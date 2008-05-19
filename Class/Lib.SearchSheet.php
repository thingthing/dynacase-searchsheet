<?php
/**
 * Function to view search sheet
 *
 * @author Anakeen 2008
 * @version $Id: Lib.SearchSheet.php,v 1.2 2008/05/19 16:32:50 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package SPREADSHEET
 */
 /**
 */
include_once("FDL/Lib.Dir.php");



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
    $head[]=array("head"=>$vc["head"],
		  "colnumber"=>$kc,
		  "attribute"=>$vc["attribute"]);      
  }
  $htmlrows=array();
  foreach ($rows as $k=>$v) {
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