<? require("infos.php");
include_once("reg_glob.inc");
sess_start();
DBconnect();
// reset des variables de session de tri
unset($_SESSION["where_sup"]); //unregvar ("where_sup");
unset($_SESSION["tbchptri"]); //unregvar ("tbchptri");
unset($_SESSION["tbordtri"]); //unregvar ("tbordtri");
unset($_SESSION["tbAfC"]); //unregvar ("tbAfC");
unset($_SESSION["FirstEnr"]); //unregvar ("FirstEnr");
unset($_SESSION["ss_parenv"]['NoConfSuppr']); //unregvar ("ss_parenv['NoConfSuppr']");
if ($cfLB="vrai") unset($_SESSION["reqcust"]); //unregvar("reqcust"); // si on vient de la liste des bases, on anule la req
// suppression de la var de session au cas ou on ai appelé un ajout directement
if (isset($ss_adrr['edit_table.php']))
   {
   unset ($ss_adrr['edit_table.php']);
   unset($_SESSION["ss_adrr"]); //session_register("ss_adrr");
   }

$title=($admadm==1? trad(LT_titleadm) : trad(LT_titleedit))." ".$DBName;
include ("header.php");

?>

<? if ($admadm=="1") { // affiche les liens en orange pour bien différencier
?>
<STYLE>
A {color: <?=$admadm_color?>}
A:visited {color: <?=$admadm_color?>}

</STYLE>
<? } // fin styles pour adm=1

JSprotectlnk(); // colle le code JS d'une fonction qui protège un lien par un mot de passe
?>
<SCRIPT language="JavaScript">
function verif(theform)
{
  if (document.theform.lc_NM_TABLE.value=="")
    {
      alert ("<?=trad(LT_notable)?>");
      return false;
    }
  return true;
}
function subm(table)
{ // attention, document. est nécéssaire pour Mozilla
  document.theform.lc_NM_TABLE.value=table;
  if (table=='__reqcust' && document.theform.lc_reqcust.value=='') {
     alert('<?=trad(LT_reqv)?>'); }
  else document.theform.submit();
}

</SCRIPT>

<? if ($debug && isset ($cktbAfC)) { // le cookie est mémorisé fonctionne
  $tbAfC=explode(";",$cktbAfC);
  echovar ("tbAfC","yes");
  }?>

<form action="<?=($admadm!="1" ? "req_table.php" : "admdesct.php") ?>" method="post" name="theform" onsubmit="return verif(this)" ENCTYPE="multipart/form-data">
<input type="hidden" name="lc_NM_TABLE">
<H1><?=($admadm==1 ? trad(LT_titlehadm) : trad(LT_titlehedit))." ".$DBName?></H1>
<h2><?=trad(LT_clicktable)?></h2>
<?=($admadm=="1" ? "<h2>".trad(LT_bcadm)."</h2>" :"")?>
<ul>
<?
// affiche liste des tables fonction de ce qu'il y a dans TABLE0COMM
$TYPAFFLHID=($admadm=="1" ? "" :  " AND TYPAFF_L!='' ");
$qr=msq("SELECT NM_TABLE, LIBELLE, COMMENT from $TBDname where NM_CHAMP='$NmChDT' AND NM_TABLE!='$TBDname' $TYPAFFLHID order by ORDAFF_L, LIBELLE") ; // recupere libelle, ordre affichage et COMMENT, si type affichage ="HID", on affiche pas la table
while ($res=db_fetch_row($qr))
  {
  $tb_name=$res[0];
  $tb_lbl=stripslashes($res[1]);
  $tb_comment=stripslashes($res[2]);
  // type=\"radio\" => maintenant liste à puces
  echo "<LI><a href=\"javascript:subm('$tb_name');\" title=\"".($tb_comment!="" ? $tb_comment : "Acces table")."\">".$tb_lbl."</a>&nbsp;&nbsp;<small>($tb_name)</small>";
  if ($admadm!="1" && $ss_parenv[ro]!=true) {
     echo "&nbsp;&nbsp;<a class=\"fxsmallbutton\" href=\"edit_table.php?lc_NM_TABLE=$tb_name&lc_adrr[edit_table.php]=".$_SERVER["PHP_SELF"]."\" title=\"".trad(LT_addrecord)."\">  <img src=\"new_r.gif\"> </a>";
     }
  echo "<br>\n";
// commentaire affiché maintenant en bulle
  } // fin boucle sur les tables
echo "</UL>";
if ($admadm!="1" ) {
JSprotectlnk();
  ?><input type="hidden" name="lc_FirstEnr"value="0"><?
  if ($ss_parenv[blair]!="1" && $ss_parenv[ro]!=true) {
    ?>
    <h3>&#149; &nbsp;<a href="javascript:subm('__reqcust');" title="Requete speciale ( ! no clause LIMIT !)"><?=trad(LT_reqcust)?></a></h3>
    <textarea name="lc_reqcust" cols="100" rows="5"><?=$reqcust?></textarea>
    <input type="hidden" name="lc_parenv[lbreqcust]" value="Requête spécifique utilisateur"><br>
     <br>
    <?=trad(LT_nblig_aff_ppage)?>
    <input type="text" name="lc_nbligpp" size="3" maxlength="3" value="<? echo ($nbligpp>0 ? $nbligpp : $nbligpp_def) ?>"><br>
    <input type="checkbox" name="lc_NoConfSuppr" value="No"><?=trad(LT_noconfirmdelete)?><br><br>
    <small><?=trad(BT_click)?> <a class="fxsmallbutton" href="#" onclick="protectlnk('admadm.php','<?=$jsppwd?>','<?=trad(com_enter_password)?>');"><?=trad(BT_here)?></a> <?=trad(LT_change_table_edit_prop)?></small>
    <br>

    <? } // fin si pas blaireau ni lecture seule
  if ($ss_parenv[blair]!="1") { // si pas blaireau seulement
     ?>
    <br>
      <?=ret_adrr($_SERVER["PHP_SELF"],true,"LT_retLB")?><br><br>
     <?
     } // fin si pas blaireau
    } // fin si admadm<>1
   else
   { // admadm=1
   ?>
  <small><br><br>&#149; <?=trad(BT_click)?> <A class="fxsmallbutton" HREF="LIST_TABLES.php?admadm=0"><?=trad(BT_here)?></A> <?=trad(LT_goback_content_table_edit)?>
  <br><br>&#149; <?=trad("BT_click")?> <a class="fxsmallbutton" href="CREATE_DESC_TABLES.php?DBName=<?=$DBName?>"><?=trad("BT_here")?></a> <?=trad("LB_createDT")?></small>
<? } // fin si admadm<>1
?>
</form>
<? include ("footer.php"); ?>
