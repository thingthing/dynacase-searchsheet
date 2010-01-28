<?php

/**
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 */

function getdetailledsearch($dbaccess,$name) {//:SSH_IDSEARCH,SSH_SEARCH,SSH_IDFAMILY,SSH_FAMILY
  include_once("FDL/Class.SearchDoc.php");
 
  $tr = array();
  $s=new SearchDoc($dbaccess,"SEARCH");
  if ($name != "")     $s->addFilter("title ~* '".pg_escape_string($name)."'");
  $s->addFilter('se_famid is not null');
  $s->slice=20;
  $t=$s->search();

  $s2=new SearchDoc($dbaccess,"SSEARCH");
  if ($name != "")     $s2->addFilter("title ~* '".pg_escape_string($name)."'");
  $s2->slice=20;
  $t2=$s2->search();


  
  foreach ($t as $k=>$v) {
    $tr[] = array($v["title"], $v["initid"], $v["title"],$v["se_famid"],$v["se_fam"]);
  }

  foreach ($t2 as $k=>$v) {
    $tr[] = array($v["title"].' - '._("special search"), $v["initid"], $v["title"]," "," ");
  }
  return $tr;
  }

function getSearchAttribute($dbaccess,$famid,$name) {//SSH_IDACOL,SSH_ACOL,SSH_LCOL
  if ( ! $famid) return (_("family must be selected before"));
  $doc = createDoc($dbaccess, $famid,false);
  // internal attributes
  $ti = array("title" => _("doctitle"),
	      "revdate" => _("revdate"),
	      "revision" => _("revision"),
	      "owner" => _("owner"),
	      "state" => _("state"));
  
  $tr = array();

  $pattern_name = preg_quote($name);
  foreach($ti as $k=>$v) {
    if (($name == "") ||    (preg_match("/$pattern_name/i", $v , $reg)))
      $tr[] = array($v , $k,$v);
    
  }

  if ($sort)  $tinter = $doc->GetSortAttributes();
  else $tinter = $doc->GetNormalAttributes();
  

  while(list($k,$v) = each($tinter)) {
    if (($name == "") ||    (preg_match("/$pattern_name/i", $v->labelText , $reg)))
      $tr[] = array($v->labelText ,
		    $v->id,$v->labelText);
    
  }
  return $tr;  
}


?>