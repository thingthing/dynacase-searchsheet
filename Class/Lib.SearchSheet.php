<?php
/**
 * Function to view search sheet
 *
 * @author Anakeen 2008
 * @version $Id: Lib.SearchSheet.php,v 1.4 2008/05/21 09:41:29 eric Exp $
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

/**
 * construct html table
 * 
 * @param array $cols table head definition
 * @param array $row table body definition
 * @param bool $limit is true if no limit in curren searches
 * @param bool $reachlimit is true if limit and it is on last page
 */
function makeHtmlTable($cols,$rows,$limit=false,$reachlimit=false) {
  global $action;
  $action->parent->AddJsRef("WHAT/Layout/prototype.js");
  $action->parent->AddJsRef("SEARCHSHEET/Layout/dynamictable.js");
  $action->parent->AddCssRef("SEARCHSHEET:dynamictable.css",true);
  
  $action->parent->AddJsRef($action->GetParam("CORE_STANDURL")."app=FDL&action=VIEWDOCJS");
  $lay=new Layout(getLayoutFile("SEARCHSHEET","dynamictable.xml"),$action);
  $head=array();
  foreach ($cols as $kc=>$vc) {
    $head[]=array("head"=>$vc["head"],
		  "colnumber"=>$kc,
		  "filtervalue"=>$vc["filtervalue"],
		  "attribute"=>$vc["attribute"]); 
  }
  $htmlrows=array();
  foreach ($rows as $k=>$v) {
    $htmlrows[]=makeHTMLRow($v);
  }

  $lay->setBlockData("HEAD",$head);
  $lay->setBlockData("HEAD2",$head);
  $lay->set("ROWS",implode($htmlrows,"\n"));
  $lay->set("REACHLIMIT",$reachlimit?'1':'0');
  $lay->set("HASLIMIT",$limit);
  return $lay->gen();
}
/**
 * construct html tr
 * 
 * @param array $cells table row content
 */
function makeHTMLRow($tcells) {
  static $lay;
  if (! $lay) $lay=new Layout(getLayoutFile("SEARCHSHEET","dynamictablerow.xml"));
  $lay->setBlockData("CELLS",$tcells);

  $first=current($tcells);
  $lay->set("docid",$first["docid"]);

  return $lay->gen();  
}

/**
 * construct html table
 * 
 * @param array $cols table head definition
 * @param array $row table body definition
 * @param bool $limit is true if no limit in curren searches
 * @param bool $reachlimit is true if limit and it is on last page
 */
function makeCsvTable($cols,$rows,$limit=false,$reachlimit=false) {
  global $action;
  
  $lay=new Layout(getLayoutFile("SEARCHSHEET","dynamictable.csv"),$action);
  $head=array();
  foreach ($cols as $kc=>$vc) {
    $head[]=array("head"=>$vc["head"],
		  "colnumber"=>$kc,
		  "filtervalue"=>$vc["filtervalue"],
		  "attribute"=>$vc["attribute"]); 
  }
  $htmlrows=array();
  foreach ($rows as $k=>$v) {
    $htmlrows[]=makeCsvRow($v);
  }

  $lay->setBlockData("HEAD",$head);
  $lay->setBlockData("HEAD2",$head);
  $lay->set("ROWS",implode($htmlrows,"\n"));
  $lay->set("REACHLIMIT",$reachlimit?'1':'0');
  $lay->set("HASLIMIT",$limit);
  return $lay->gen();
}
/**
 * construct html tr
 * 
 * @param array $cells table row content
 */
function makeCsvRow($tcells) {
  foreach ($tcells as $k=>$v) {
    $row[]=str_replace(";"," - ",$v["content"]);
  }
  return implode($row,';');
  
}
?>