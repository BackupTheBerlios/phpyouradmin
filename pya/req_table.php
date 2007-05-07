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
$_SESSION["where_sup"]=$where_sup; //session_register("where_sup");

$reqcust=$lc_reqcust;
$_SESSION["reqcust"]=$reqcust; //session_register("reqcust");

// reset des variables de session de tri, d'ordre, d'enregistrement de d�ut et d'affichage des colonnes
//unregvar ("where_sup");
$_SESSION["tbchptri"]=array(); //unregvar ("tbchptri");
$_SESSION["tbordtri"]=array(); //unregvar ("tbordtri");
$_SESSION["FirstEnr"]=0;
$_SESSION["tbAfC"]=array(); //unregvar ("tbAfC");
$NoConfSuppr=$lc_NoConfSuppr;
$_SESSION["NoConfSuppr"]=$NoConfSuppr; //session_register("NoConfSuppr");

// regarde s'il existe des filtres ou selection d'affichage de colonnes, que si pas de req custom
if ($lc_NM_TABLE!="__reqcust") {
   $qr=msq("SELECT NM_CHAMP from $TBDname where NM_CHAMP!='$NmChDT' AND NM_TABLE='$lc_NM_TABLE' AND (VAL_DEFAUT!='' OR TYP_CHP!='') AND TYPAFF_L!='' order by ORDAFF_L, LIBELLE") ; // recupere libelle, ordre affichage et COMMENT, si type affichage ="HID", on affiche pas la table
   $nbrqr=db_num_rows($qr);
   }
// sinon, va directement sur la liste
if ($nbrqr==0 || $lc_NM_TABLE=="__reqcust") {
  header("location: list_table.php?lc_NM_TABLE=$lc_NM_TABLE&lc_where_sup=".urlencode($lc_where_sup)."&lc_nbligpp=$lc_nbligpp&lc_PgReq=0");
  }
else {

$title=trad(REQ_query_on_table).$lc_NM_TABLE." , ".trad(COM_database).$DBName;
include ("header.php");?>
<div align="center">
<form action="list_table.php" method="post" name="theform" ENCTYPE="multipart/form-data">
<input type="hidden" name="lc_nbligpp" value="<?=$lc_nbligpp;?>">
<input type="hidden" name="lc_NM_TABLE" value="<?=$lc_NM_TABLE;?>">
<input type="hidden" name="lc_where_sup">
<input type="hidden" name="lc_PgReq" value="1">
<? if (!$ss_parenv[noinfos]) { ?>
<H3><?=strtoupper(trad(com_database).$DBName)?></H3> <? } ?>
<H1><?=trad(REQ_crit_select).$lc_NM_TABLE;?></H1>
<h3><? echo trad(REQ_select_text);
if ($admadm!="1" && $ss_parenv[ro]!=true) { 
	echo "&nbsp;&nbsp;<a class=\"fxsmallbutton\" href=\"edit_table.php?lc_NM_TABLE=$lc_NM_TABLE&lc_adrr[edit_table.php]=".$_SERVER["PHP_SELF"]."\" title=\"".trad(LT_addrecord)."\"> <img src=\"new_r.gif\"> ".trad(LT_addrecord)." </a>";
     }
?> </h3>
<TABLE>
<THEAD>
<TH width="30%">Champ</TH><TH width="50%">Crit�e</TH><TH width="20%">A Afficher</TH></THEAD>
<?
$FCobj=new PYAobj();
$FCobj->NmTable=$lc_NM_TABLE;
$FCobj->NmBase=$DBName;
$nolig=0;
while ($res=db_fetch_array($qr))
  {
  $nolig++;
  $FCobj->NmChamp=$res[$ult[NM_CHAMP]];
  $FCobj->InitPO();
  echo "<TR class=\"".($nolig % 2==1 ? "backwhiten" : "backredc")."\"><TD><B>$FCobj->Libelle</B><BR><small>$FCobj->Comment</small></TD><TD>";
  $FCobj->EchoFilt();
  echo "</TD><TD>";
  $FCobj->EchoCSA();
  echo "</TD></TR>";
  }
?>
<tr><td colspan="3" align="center"><br>
<?=ret_adrr($_SERVER["PHP_SELF"],true,trad('REQ_retLT'))?>
<img src="./shim.gif" height="1" width="10">
<a href="#" onclick="document.theform.submit()" class="fxbutton"> <?=trad('BT_valider')?> </a>
</td></tr>
</TABLE>
</form>
</div>
<? include ("footer.php");
} // fin si il y a des champs crit�es
?>

