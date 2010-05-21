<? 
require("infos.php");
sess_start();
//include_once("reg_glob.inc");
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
$nbrows = $_REQUEST['nbrows'];

if ($_REQUEST["NM_CHAMP"][$nbrows + 1] != "") {
	$nbrows++;
	$onemorefield = true;
}


for ($i=0;$i<=$nbrows;$i++) {
  $NM_CHAMP = $_REQUEST['NM_CHAMP'][$i];
  $LIBELLEt=db_escape_string($_REQUEST['LIBELLE'][$i]);
  $ORDAFF_Lt=db_escape_string($_REQUEST['ORDAFF_L'][$i]);
  $TYPAFF_Lt=$_REQUEST['TYPAFF_L'][$i];
  $ORDAFFt=db_escape_string($_REQUEST['ORDAFF'][$i]);
  $TYPEAFFt=$_REQUEST['TYPEAFF'][$i];
  $VALEURSt=db_escape_string($_REQUEST['VALEURS'][$i]);
  $VAL_DEFAUTt=db_escape_string($_REQUEST['VAL_DEFAUT'][$i]);
  $TT_AVMAJt=db_escape_string(($_REQUEST['TT_AVMAJ'][$i]!="" ? $_REQUEST['TT_AVMAJ'][$i] : $_REQUEST['TT_AVMAJ2'][$i]));
  $TT_PDTMAJt=db_escape_string($_REQUEST['TT_PDTMAJ'][$i]);
  $TT_APRMAJt=db_escape_string($_REQUEST['TT_APRMAJ'][$i]);
  $TYP_CHPt=db_escape_string($_REQUEST['TYP_CHP'][$i]);
  $COMMENTt=db_escape_string($_REQUEST['COMMENT'][$i]);
  
/*  $LIBELLEt =db_escape_string($LIBELLEt);
  $ORDAFF_Lt =db_escape_string($ORDAFF_Lt);
//  $TYPAFF_Lt =db_escape_string($TYPAFF_Lt);
  $ORDAFFt =db_escape_string($ORDAFFt);
//  $TYPEAFFt =db_escape_string($TYPEAFFt);
  $VALEURSt =db_escape_string($VALEURSt);
  $VAL_DEFAUTt =db_escape_string($VAL_DEFAUTt);
  $TT_AVMAJt =db_escape_string($TT_AVMAJt);
  $TT_PDTMAJt =db_escape_string($TT_PDTMAJt);
  $TT_APRMAJt =db_escape_string($TT_APRMAJt);
  $TYP_CHPt =db_escape_string($TYP_CHPt);
  $COMMENTt =db_escape_string($COMMENTt);*/
  
// si dernier champ inséré
  if ($onemorefield && $i == $nbrows) {
	$query = "INSERT INTO $TBDname (NM_TABLE ,NM_CHAMP,LIBELLE,ORDAFF_L,TYPAFF_L,ORDAFF,TYPEAFF,VALEURS,VAL_DEFAUT,TT_AVMAJ,TT_PDTMAJ,TT_APRMAJ,TYP_CHP,".$GLOBALS["NmChpComment"].")
VALUES ('$NM_TABLE','".$NM_CHAMP."','$LIBELLEt','$ORDAFF_Lt','$TYPAFF_Lt','$ORDAFFt','$TYPEAFFt','$VALEURSt','$VAL_DEFAUTt','$TT_AVMAJt','$TT_PDTMAJt','$TT_APRMAJt','$TYP_CHPt','$COMMENTt' )";
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
	".$GLOBALS["NmChpComment"]."='$COMMENTt'
	where NM_TABLE='$NM_TABLE' AND NM_CHAMP='$NM_CHAMP'";
  }
  //echo $query;
  db_query($query) or die ("req invalide : <BR><I>$query</I>");
}

header ($_REQUEST['formmaj']!=1 ? "location: ./LIST_TABLES.php?admadm=1" : "location: ./admdesct.php?lc_NM_TABLE=$NM_TABLE"); 
?>
