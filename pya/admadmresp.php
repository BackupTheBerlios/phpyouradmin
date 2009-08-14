<? 
require("infos.php");
sess_start();
include_once("reg_glob.inc");
include("globvar.inc");

DBconnect($DBName);
  
//  echo "<PRE>";
//  print_r($_REQUEST);
//  die();
/* réponse à modif des caract d'édition d'une table
if (!db_case_sens()) {
	$TBDname=strtolower($TBDname);
	foreach ($nmc as $k=>$v) {
		$nmc[$k]=strtolower($v);
	}
}*/
// construction du set
// dans le cas des tables virtuelles, il peut y avoir un champ en plus
if ($NM_CHAMP[$nbrows + 1] != "") {
	$nbrows++;
	$onemorefield = true;
}

for ($i=0;$i<=$nbrows;$i++) {
  $LIBELLEt=addslashes($LIBELLE[$i]);
  $ORDAFF_Lt=addslashes($ORDAFF_L[$i]);
  $TYPAFF_Lt=$TYPAFF_L[$i];
  $ORDAFFt=addslashes($ORDAFF[$i]);
  $TYPEAFFt=$TYPEAFF[$i];
  $VALEURSt=addslashes($VALEURS[$i]);
  $VAL_DEFAUTt=addslashes($VAL_DEFAUT[$i]);
  $TT_AVMAJt=($TT_AVMAJ[$i]!="" ? $TT_AVMAJ[$i] : $TT_AVMAJ2[$i]);
  $TT_PDTMAJt=$TT_PDTMAJ[$i];
  $TT_APRMAJt=$TT_APRMAJ[$i];
  $TYP_CHPt=addslashes($TYP_CHP[$i]);
  $COMMENTt=addslashes($COMMENT[$i]); 
  
  $LIBELLEt =addslashes($LIBELLEt);
  $ORDAFF_Lt =addslashes($ORDAFF_Lt);
//  $TYPAFF_Lt =addslashes($TYPAFF_Lt);
  $ORDAFFt =addslashes($ORDAFFt);
//  $TYPEAFFt =addslashes($TYPEAFFt);
  $VALEURSt =addslashes($VALEURSt);
  $VAL_DEFAUTt =addslashes($VAL_DEFAUTt);
  $TT_AVMAJt =addslashes($TT_AVMAJt);
  $TT_PDTMAJt =addslashes($TT_PDTMAJt);
  $TT_APRMAJt =addslashes($TT_APRMAJt);
  $TYP_CHPt =addslashes($TYP_CHPt);
  $COMMENTt =addslashes($COMMENTt); 
// si dernier champ inséré
  if ($onemorefield && $i == $nbrows) {
	$query = "INSERT INTO $TBDname (NM_TABLE ,NM_CHAMP,LIBELLE,ORDAFF_L,TYPAFF_L,ORDAFF,TYPEAFF,VALEURS,VAL_DEFAUT,TT_AVMAJ,TT_PDTMAJ,TT_APRMAJ,TYP_CHP,COMMENT)
VALUES ('$NM_TABLE','".$NM_CHAMP[$i]."','$LIBELLEt','$ORDAFF_Lt','$TYPAFF_Lt','$ORDAFFt','$TYPEAFFt','$VALEURSt','$VAL_DEFAUTt','$TT_AVMAJt','$TT_PDTMAJt','$TT_APRMAJt','$TYP_CHPt','$COMMENTt' )";
  } else {
	$query="UPDATE $TBDname SET 
	LIBELLE='$LIBELLEt',
	ORDAFF_L='$ORDAFF_Lt',
	TYPAFF_L='$TYPAFF_Lt',
	ORDAFF='$ORDAFFt',
	TYPEAFF='$TYPEAFFt',
	VALEURS='$VALEURSt',
	VAL_DEFAUT='$VAL_DEFAUTt',
	TT_AVMAJ='$TT_AVMAJt',
	TT_PDTMAJ='$TT_PDTMAJt',
	TT_APRMAJ='$TT_APRMAJt',
	TYP_CHP='$TYP_CHPt',
	COMMENT='$COMMENTt' 
	where NM_TABLE='$NM_TABLE' AND NM_CHAMP='$NM_CHAMP[$i]'";
  }
  db_query($query) or die ("req invalide : <BR><I>$query</I>");
}

header ($_REQUEST['formmaj']!=1 ? "location: ./LIST_TABLES.php?admadm=1" : "location: ./admdesct.php?lc_NM_TABLE=$NM_TABLE"); 
?>
