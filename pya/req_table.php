<? 
require("infos.php");
sess_start();
DBconnect();

$ult=rtb_ultchp(); // tableau des noms de champs sensibles �la casse (�cause de pgsql...)
include_once("reg_glob.inc");

if (isset($lc_where_sup)) {
  $where_sup=$lc_where_sup;
  }
else $where_sup="";

//print_r($_REQUEST);

if ($where_sup !="") $_SESSION["where_sup"] = $where_sup;
if ($_REQUEST['reqcust_name']!= "") $_SESSION["reqcust_name"] = $_REQUEST['reqcust_name'];
if ($_REQUEST['lc_reqcust']!= "")  $_SESSION["lc_reqcust"] = $_REQUEST['lc_reqcust'];

$reqcust = $_SESSION["lc_reqcust"];

// reset des variables de session de tri, d'ordre, d'enregistrement de d�ut et d'affichage des colonnes
unregvar ("where_sup");
//$_SESSION["tbchptri"]=array(); 
unregvar ("tbchptri");
//$_SESSION["tbordtri"]=array(); 
unregvar ("tbordtri");
$_SESSION["FirstEnr"]=0;
//$_SESSION["tbAfC"]=array(); 
unregvar ("tbAfC");
$NoConfSuppr=$lc_NoConfSuppr;
$_SESSION["NoConfSuppr"]=$NoConfSuppr; //session_register("NoConfSuppr");

// regarde s'il existe des filtres ou selection d'affichage de colonnes, que si pas de req custom

if ($lc_NM_TABLE!="__reqcust") {
   $qr = db_query("SELECT NM_CHAMP from $TBDname where NM_CHAMP!='$NmChDT' AND NM_TABLE='$lc_NM_TABLE' AND (VAL_DEFAUT".$GLOBALS['sqllenstr0']."  OR TYP_CHP".$GLOBALS['sqllenstr0'].") AND TYPAFF_L".$GLOBALS['sqllenstr0']." order by ORDAFF_L, LIBELLE") ; // recupere libelle, ordre affichage et COMMENT, si type affichage ="HID", on affiche pas la table
   $nbrqr=db_num_rows($qr);
} else {
	$tbargscust = parseArgsReq($reqcust);
	$nbrqr = count($tbargscust);
}
// sinon, va directement sur la liste de réponses
if ($nbrqr==0) {
	$url = "list_table.php?lc_NM_TABLE=$lc_NM_TABLE&lc_where_sup=".urlencode($lc_where_sup)."&lc_nbligpp=$lc_nbligpp&lc_PgReq=0&lc_reqcust=".urlencode($_REQUEST['lc_reqcust']);
	outJS("window.location.replace('$url')",true) ;
  //header("location: $url");
  die();
}

$title=trad(REQ_query_on_table).$lc_NM_TABLE." , ".trad(COM_database).$DBName;
include ("header.php");?>
<div align="center">
<form action="list_table.php" method="post" name="theform" ENCTYPE="multipart/form-data">
<input type="hidden" name="lc_nbligpp" value="<?=$lc_nbligpp;?>">
<input type="hidden" name="lc_where_sup">
<input type="hidden" name="lc_PgReq" value="1">
<? if (!$ss_parenv[noinfos]) { 
	//echo var_export($_SESSION['memFilt']);
	echo "<H3>".strtoupper(trad(com_database).$DBName)."</H3>";
}
if (!$tbargscust) { // pas requête Custom
	?>
	<H1><?=trad(REQ_crit_select).$lc_NM_TABLE;?></H1>
	<P><? //echo trad(REQ_select_text);
	echo "&nbsp;&nbsp;<a class=\"fxsmallbutton\" href=\"req_table.php?clearCrit=true&lc_NM_TABLE=$lc_NM_TABLE\">". trad("REQ_clean_memFilt")."</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	if ($_REQUEST['clearCrit']) unset($_SESSION['memFilt']);
	if ($admadm!="1" && $ss_parenv[ro]!=true) { 
		echo "&nbsp;&nbsp;<a class=\"fxsmallbutton\" href=\"edit_table.php?lc_NM_TABLE=$lc_NM_TABLE&lc_adrr[edit_table.php]=".$_SERVER["PHP_SELF"]."\" title=\"".trad(LT_addrecord)."\"> <img src=\"new_r.gif\"> ".trad(LT_addrecord)." </a>";
		}
	?></P>
	<input type="hidden" name="lc_NM_TABLE" value="<?=$lc_NM_TABLE;?>">
	<TABLE>
	<TR class="THEAD">
	<TH>Champ</TH><TH>Critere</TH><TH>A Afficher</TH></TR>
	<?
	$FCobj=new PYAobj();
	$FCobj->NmTable=$lc_NM_TABLE;
	$FCobj->NmBase=$DBName;
	$nolig=0;
	while ($res=db_fetch_array($qr)) {
		$nolig++;
		$FCobj->NmChamp=$res[$ult[NM_CHAMP]];
		$FCobj->InitPO();
		echo "<TR class=\"".($nolig % 2==1 ? "backwhiten" : "backredc")."\"><TD><B>$FCobj->Libelle</B><BR><small>$FCobj->Comment</small></TD><TD>";
		$FCobj->EchoFilt();
		echo "</TD><TD>";
		$FCobj->EchoCSA();
		echo "</TD></TR>\n";
	}
} else { // req custom
	echo "<H1>".trad("REQ_crit_req_cust").$_SESSION["reqcust_name"]."</H1>";
	echo "<p>&nbsp;&nbsp;<a class=\"fxsmallbutton\" href=\"req_table.php?clearCrit=true&lc_NM_TABLE=$lc_NM_TABLE\">". trad("REQ_clean_memFilt")."</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	if ($_REQUEST['clearCrit']) unset($_SESSION['memFilt']);
	?></P>
	<input type="hidden" name="lc_NM_TABLE" value="__reqcust">
	<input type="hidden" name="lc_reqcust" value="<?=$reqcust?>">
	<TABLE>
	<TR class="THEAD">
	<TH>Paramètre</TH><TH>Valeur</TH><TH>Commentaire</TH></TR>
	<?
	$FCobj=new PYAobj();
	$FCobj->NmBase=$DBName;
	$nolig=0;
	foreach ($tbargscust as $arg) {
		$tbpropPya = hash_explode($arg);
		$nolig++;
		// pour l'instant on traite que le cas des filtres PYAObj
		$FCobj->NmTable = $tbpropPya['NmTable'];
		$FCobj->NmChamp = $tbpropPya['NmChamp'];
		$tblvarrqc[] = $tbpropPya['NmChamp'];
		$FCobj->InitPO();
		echo "<TR class=\"".($nolig % 2==1 ? "backwhiten" : "backredc")."\"><TD><B>$FCobj->Libelle</B><BR><small>$FCobj->Comment</small></TD><TD>";
		$FCobj->EchoFilt();
		echo '<input type="hidden" name="nvc_'.$tbpropPya['NmChamp'].'" value="'.$nolig.'">';
		echo "</TD><TD>";
		$FCobj->EchoCSA();
		echo "</TD></TR>\n";
	}
	echo '<input type="hidden" name="tblvarrqc" value="'.implode(",",$tblvarrqc).'">';

	
}
?>
<tr><td colspan="3" align="center"><br>
<?=trad(LT_nblig_aff_ppage)?> <input type="text" name="lc_nbligpp" size="3" maxlength="3" value="<? echo ($nbligpp>0 ? $nbligpp : $nbligpp_def) ?>"><br><?=ret_adrr($_SERVER["PHP_SELF"],true,'REQ_retLT')?>
<img src="./shim.gif" height="1" width="10">
<input type="submit" value="<?=trad('BT_valider')?>" class="fxbutton"/> 
</td></tr>
</TABLE>
</form>
</div>
<? include ("footer.php");
?>

