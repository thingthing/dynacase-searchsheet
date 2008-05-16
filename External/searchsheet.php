<?php
function getdetailledsearch($dbaccess,$name) {//:SSH_IDSEARCH,SSH_SEARCH,SSH_IDFAMILY,SSH_FAMILY
  include_once("FDL/Class.SearchDoc.php");
 
  $tr = array();
  $s=new SearchDoc($dbaccess,"SEARCH");
  if ($name != "")     $s->addFilter("title ~* '".pg_escape_string($name)."'");
  $s->addFilter('se_famid is not null');
  $s->slice=20;
  $t=$s->search();
  foreach ($t as $k=>$v) {
    $tr[] = array($v["title"], $v["initid"], $v["title"],$v["se_famid"],$v["se_fam"]);
  }
  return $tr;
  }

function getSearchAttribute($dbacces,$famid,$name) {//SSH_IDACOL,SSH_ACOL,SSH_LCOL
  if ( ! $famid) return (_("family must be selected before"));
  $doc = createDoc($dbaccess, $famid,false);
  // internal attributes
  $ti = array("title" => _("doctitle"),
	      "revdate" => _("revdate"),
	      "revision" => _("revision"),
	      "owner" => _("owner"),
	      "state" => _("state"));
  
  $tr = array();
  while(list($k,$v) = each($ti)) {
    if (($name == "") ||    (eregi("$name", $v , $reg)))
      $tr[] = array($v , $k,$v);
    
  }

  if ($sort)  $tinter = $doc->GetSortAttributes();
  else $tinter = $doc->GetNormalAttributes();
  

  while(list($k,$v) = each($tinter)) {
    if (($name == "") ||    (eregi("$name", $v->labelText , $reg)))
      $tr[] = array($v->labelText ,
		    $v->id,$v->labelText);
    
  }
  return $tr;  
}


?>