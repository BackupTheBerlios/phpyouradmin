<?
$dbgloc=false;
function echdb($str) {
global $dbgloc;
if ($dbgloc) echo $str;
}

//
// fonction d'affichage de valeur(s) d'une variable, eventuellement tableau, eventuellement associatif
// la d�ection du format est automatique
// MARHE PAS (ou PLus)
function echovar($nom_var,$ass="no",$echov=true) {
global $$nom_var;
$strres="<PRE><em> Variable $".$nom_var."</em>\n";
$strres.=var_export($$nom_var,true)."</PRE>";
if ($echov) 
	{echo $strres;}
else return($strres);
} 

function var_predump($nom_var,$echov=true) {
$strres="<PRE><em> Variable $".var_name($nom_var).":</em>\n";
$strres.=var_export($nom_var,true)."</PRE>";
if ($echov) 
	{echo $strres;}
else return($strres);
} 

function var_name($thevar) {
foreach($GLOBALS as $key => $val) if($val === $thevar) $vname = $key;
return ($vname);
}

function retvar($var2ret,$ass="no",$echov=true) {
if (is_array($var2ret)) {
  $strres="Tableau".($ass!="no" ? " associatif":"")." \$var2ret: ";
  if ($ass!="no") { //tableau associatif 
    foreach ($var2ret as $key=>$val) {
      $strres.= $key."=>".retvar($val,$ass,$echov)."<br/>";
      }
    } // fin si associatif
  else {
    $i=0;
    foreach ($var2ret as $val) {
      $strres.=$i."=>".$val.";";
      $i++; }
     }
  }
else { // pas tableau
  $strres="Variable \$var2ret:".$var2ret." (".gettype($var2ret).")";
}
if ($echov) 
	{echo $strres."<br/>\n";}
	else return($strres);
} 


?>