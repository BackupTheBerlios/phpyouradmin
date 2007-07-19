<?
require("infos.php");
sess_start();
include_once("reg_glob.inc");
DBconnect();

if ($modif=="C") {
   	$modif=1;
   	$ss_parenv['ro']=true;
   }

if ($NM_TABLE!="__reqcust") {
   // recup libell�et commentaire de la table
   $LB_TABLE=RecLibTable($NM_TABLE,0);
   $COM_TABLE=RecLibTable($NM_TABLE,1);

   if ($modif==1)
      {$lbtitre=($ss_parenv['ro']==true ? trad(com_consultation) : trad(com_edition)).trad(ER_record_of_table). $LB_TABLE;}
   else if ($modif==2)
      {$lbtitre=trad(com_copy).trad(ER_record_of_table).$LB_TABLE;}
   else
       {$lbtitre=trad(com_add).trad(ER_record_of_table).$LB_TABLE;}
   }
else {
   $LB_TABLE=$ss_parenv[lbreqcust];
   $COM_TABLE="";
   $lbtitre=trad(ER_record_details);
   }

$title= trad(com_edition).trad(com_record)." $NM_TABLE (DB $DBName)";

$key=stripslashes($key);

include ("header.php");

if ($debug) DispDebug();?>

<div align="center">
<H1><?=$lbtitre; ?></H1>
<? if (!$ss_parenv[noinfos]) { ?>
   <small><?=$COM_TABLE;?></small>
   <H6>
   <? echo ($modif==1 ? trad(ER_record_car).$key : ""); ?>
   </H6>
   <? echo ($modif==2 ? "<H5><u>COPIE  !</u> pensez  a changer la cle ($key) si elle n'est pas en auto-increment !<br>- Attention les images ou fichiers lies ne sont pas copies !</H5>" : ""); ?>

<? } ?>
<BR>

<script language="Javascript">
function ConfReset() {
         if (confirm('<?=trad(ER_raz_confirm)?>')) document.theform.reset();
}
</script>
<form action="amact_table.php" method="post" name="theform" ENCTYPE="multipart/form-data">
<INPUT TYPE="hidden" NAME="modif" value="<? echo $modif ?>">
<? $keyfich=explode("=",$key);
$keyfich = $keyfich [1];?>
<INPUT TYPE="hidden" NAME="key" value="<? echo ($modif!=2 ? $key :"") ?>">
<INPUT TYPE="hidden" NAME="keyfich" value="<? echo ($modif!=2 ? $keyfich :"") ?>">
<?

$reqLChp="SELECT NM_CHAMP from $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP!='$NmChDT' AND (TYPEAFF!='HID' OR( TT_PDTMAJ!='' AND TT_PDTMAJ!= NULL)) ORDER BY ORDAFF, LIBELLE";

if ($modif==1 || $modif==2) { // recup des valeurs de l'enregistrement
    $where=" where ".$key.($where_sup=="" ? "" : " and $where_sup");
    if ($NM_TABLE!="__reqcust") {
	//$reqcust="SELECT * FROM $CSpIC$NM_TABLE$CSpIC";
	// on ne fait plus �, car � charge les champs blobs qui sont cach� pour rien...
	
	$rq1=db_query($reqLChp) or die ("req 2 invalide");
	while ($rw=db_fetch_row($rq1)) {
		$tbc[]=$rw[0];
		}
	$lctd=implode(",",$tbc);
	$reqcust="SELECT $lctd FROM $CSpIC$NM_TABLE$CSpIC";
	}
    $req=msq($reqcust." ".$where);
    $tbValChp=db_fetch_assoc($req);
  }


if ($NM_TABLE!="__reqcust") {
   // Cr�tion et Initialisation des propri�� des objets PYAobj
   $rq1=db_query($reqLChp) or die ("req 2 invalide");
   while ($CcChp=db_fetch_row($rq1)) { // boucles sur les champs
   
     $NM_CHAMP=$CcChp[0];
     $ECT[$NM_CHAMP]=new PYAobj();
     $ECT[$NM_CHAMP]->NmBase=$DBName;
     $ECT[$NM_CHAMP]->NmTable=$NM_TABLE;
     $ECT[$NM_CHAMP]->NmChamp=$NM_CHAMP;
     $ECT[$NM_CHAMP]->TypEdit=$modif;
     $ECT[$NM_CHAMP]->InitPO();
	 if ($ECT[$NM_CHAMP]->TypeAff=="POPL") $poplex=true; // s'il existe au moins une edition en popup li�
     }
   } // fin si pas req custom
else { // requete custom
     $ECT=InitPOReq($reqcust." ".$where,$DBName);
}
if ($poplex) JSpopup(); // s'il existe au moins une edition en popup li� colle le code d'ouverture d'une popup
?>
<TABLE BORDER="1" BORDERCOLOR="#FFF3F3" CELLSPACING="0" CELLPADDING="2">
<?

foreach ($ECT as $PYAObj) {
  if ($ss_parenv['ro']==true || $NM_TABLE=="__reqcust") $PYAObj->TypEdit="C"; // en consultation seule en readonly ou eq sp�iale
  $NM_CHAMP=$PYAObj->NmChamp;
  if ($modif!="") $PYAObj->ValChp=$tbValChp[$NM_CHAMP]; // si pas cr�tion (edit ou copy recup la val)

  // ICI les traitements avant Mise �Jour
  if ($modif==2) { // en cas de COPIE on annule la valeur auto incr�ent�
    if (stristr($PYAObj->FieldExtra,"auto_increment")) $PYAObj->ValChp="";
    }

  // traitement valeurs avant MAJ
  $PYAObj->InitAvMaj($$VarNomUserMAJ);

  if ($PYAObj->TypeAff!="HID") {
      echo "<TR><TD>".$PYAObj->Libelle;

    if ($PYAObj->Comment!="") echo "<BR><span class=\"legendes9px\">".$PYAObj->Comment."</span>";
     echo "</TD>\n<TD>";

    $PYAObj->EchoEditAll(false); // n'affiche pas les champs cach�!!!!!!!!!!!!!!!! //

     echo "</TD>\n</TR>"; //finit la ligne du tableau
   } else
        $PYAObj->EchoEditAll(true); // !!!!!!!!!!!!!!!! /

  } // fin while
?>
</table>

<div ALIGN="center">
<br>
<a href="<?=ret_adrr($_SERVER["PHP_SELF"])."?cfp=edit"?>" class="fxbutton"><?=($ss_parenv['ro']!=true ? "Annuler tous les changement et ":"")?>fermer</A>
<? // boutons valider et annuler que quand read only false
    if ($ss_parenv['ro']!=true) { ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
		<a href="<?=($poplex ? "closepop();" : "")?>javascript:ConfReset()" class="fxbutton"> <?=trad('BT_reset')?> </a>
		<!--<A HREF="javascript:ConfReset()" title="RAZ du formulaire"><IMG SRC="./annuler.gif" border="0"></a>-->
        &nbsp;&nbsp;&nbsp;&nbsp;
		<a href="#" onclick="<?=($poplex ? "closepop();" : "")?>document.theform.submit();" class="fxbutton"> <?=trad('BT_valider')?> </a>
<!--<INPUT TYPE="image" SRC="./valider.gif" border="0" onmouseover="self.status='Valider';return true">-->
    <?} ?>

</div>
<? include ("footer.php"); ?>
