<?
$NM_TABLE=$_REQUEST['lc_NM_TABLE'];
include("globvar.inc");
require("infos.php");
sess_start();
//include_once("reg_glob.inc");

DBconnect(); 

if (strstr($NM_TABLE,$id_vtb)) {
	$booTableVirt = true;
}
// si champ à supprimer 
if ($_REQUEST['chp2sup']) db_query("delete from $TBDname where NM_CHAMP='".$_REQUEST['chp2sup']."' AND  NM_TABLE='$NM_TABLE'");
// On compte le nombre d'enregistrement total correspondant �la table
// on realise la requ�e
// on compte le nombre de ligne renvoy� par la requ�e
$tbc = db_qr_res("SELECT count(*) FROM $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP!='$NmChDT' order by ORDAFF");
$nbrows = $tbc[0];
$req1="SELECT * FROM $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP!='$NmChDT' order by ORDAFF";
$result=msq($req1);

function es($val)
{
  global $vares;
  echo "\"$val\""; 
  echo (($vares==$val ? " selected" :""));
}
// affiche image de lien vers l'aide
function DHelp($rub) {
// avec image qui gave
// echo "<a href=\"javascript:popup('aide.php#".$rub."');\"><img src=\"help.gif\" border=\"0\" align=\"right\"></a>";
echo "<a href=\"#\" onclick=\"javascript:popup('aide.php#".$rub."');\">&nbsp;<B style=\"background: #FFFFFF\" >?</B>&nbsp;</a></font>";
}

$title="ADMADM table $NM_TABLE, base $DBName";
$admadm=1;
include ("header.php");
JSpopup(); ?>

<style type="text/css">
INPUT,SELECT,TEXTAREA {font-size:11px}
</style>
<SCRIPT>
/* fonction qui renvoie l'index d'un ��ent de formulaire
on est oblig�d'utiliser l'index car les noms de champas avec [] ne sont pas support� par javascript
*/
function getIndex(what) {
    for (var i=0;i<document.theform.elements.length;i++)
        if (what == document.theform.elements[i]) return i;
    return -1;
}

/* fonction appell� lorsqu'on change le type d'affichage :
- affiche un message d'alerte lorsqu'on choisit le type fichier-photo
- change la valeur de type d'affichage liste fonction du type d'affichage 
- met une mention optionnelle dans la valeur*/

function alertfic(valc,namec,ind) {
	indtafl=ind-2; // indice de l'element typ affichage ds liste
	indval=ind+1; // indice de l'element valeurs

	if (valc=="FICFOT") {
		alert('Pensez a crer un repertoire nomme' + namec +', avec les droits en ecriture pour le user du serveur web ....');
		document.theform.elements[indtafl].selectedIndex=5; // lien
		document.theform.elements[indval].value='#Dossier de stockage par defaut: '+namec;
	} else if (valc=='LDL' || valc=='LDTXT' || valc=='LDLM' || valc=='STAL' ||valc=='LD' ||valc=='LDM' ||valc=='POPL' ||valc=='POPLM' ) {
	if ( valc=='LDM'|| valc=='LD') { // remet type aff ds liste en auto si pas lie
		document.theform.elements[indtafl].selectedIndex=3;  // auto
	} else {
		document.theform.elements[indtafl].selectedIndex=4; // met type aff ds liste li� si li� en ppal
	}
	if (document.theform.elements[indval].value=='') {
		document.theform.elements[indval].value='Pensez a rentrer les valeurs ou le lien ici !';
	}
	document.theform.elements[indval].focus();
	} else if (valc=='HID' || valc=='') {
	document.theform.elements[indtafl].selectedIndex=0; // cache
	} else if (valc=='DT_TSTAMP_ED' || valc=='DT_TSTAMP_VS') {
	document.theform.elements[indtafl].selectedIndex=6; // timestamp
	} else {
	document.theform.elements[indtafl].selectedIndex=3; // auto
	}
	return;
}
// fonction appelée quand on change le filtre
function alertFilt(valc,namec,ind) {
indval=ind-1; // indice de l'element valeurs
<? if ($booTableVirt) { ?>
if (valc=='LDC') {
  if (document.theform.elements[indval].value=='') {
    document.theform.elements[indval].value='Pensez a rentrer le nom de la table physique ici !';
    }
  document.theform.elements[indval].focus();
} else if (valc=='LDLLV') {
	alert ("Ce choix est pour l'instant impossible avec une table virtuelle");
	document.theform.elements[ind].selectedIndex = 6; // liste der liée
}
<?} // fin si $booTableVirt ?>
return;
}
</SCRIPT>
<div align="center">
<BR>
<span class="titrered20px"> Base <?= $DBName?><br>
Propriétés d'édition de la table <?= $LB_TABLE." (".$NM_TABLE?>) </span>
<br>
<?
// on met deux fois la ligne de boutons..
$btline = '
<div>
  <a href="./LIST_TABLES.php?admadm=1" class="fxbutton">'.trad(BT_retour).'</a> 
<input type="hidden" name="formmaj" id="formmaj"/>
        &nbsp;&nbsp;&nbsp;&nbsp;
  <a href="#" onclick="document.theform.submit()" class="fxbutton"> '.trad(BT_valider).' </a>
        &nbsp;&nbsp;&nbsp;&nbsp;
  <a href="#" onclick="document.theform.formmaj.value=1;document.theform.submit()" class="fxbutton"> '.trad(BT_maj).' </a>
</div>  
';

echo "<H2>Caractéristiques globales de la table $NM_TABLE</H2>";
if ($booTableVirt) {
	echo "<H3>Attention, cette table est VIRTUELLE et n'existe pas en base</H3>";
}
?>
<form name="theform" action="./admadmresp.php" method="post">
<? // proprietes generales de la table
$reqg="SELECT * FROM $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP='$NmChDT'";
$resg=msq($reqg);
$i=0;
$row=db_fetch_assoc($resg);
$row=case_kup($row); // verrue a cause de PgSql dont les noms de champs sont insensibles �la case
echo "<input type=\"hidden\" name=\"NM_CHAMP[$i]\" value=\"".$row['NM_CHAMP']."\">";
echo "<TABLE><TR class=\"backredc\" valign=\"top\">";
// Nom du champ (inchangeable)
// Libelle du champ
echo "<TD><b>Libellé de la table :</b> <BR><input type=\"text\" name=\"LIBELLE[$i]\" value=\"".stripslashes($row['LIBELLE'])."\"></td>";
// Ordre d'affichage ds liste (sur 2 car)
$val=$row['ORDAFF_L'];
if (strlen($val)==1) $val="0".$val;
echo "<TD><b>Ordre d'affichage dans la liste</b><BR>\n";
echo "<input type=\"text\" name=\"ORDAFF_L[$i]\" value=\"".stripslashes($val)."\" size=\"5\"></td>";

// Type affichage ou non ds liste des tables
echo "<TD><b>Table affichée dans liste</b><BR>\n";
echo "<input type=\"radio\" name=\"TYPAFF_L[$i]\" value=\"AUT\"".($row['TYPAFF_L']!="" ? "checked" : "").">oui\n";
echo "<input type=\"radio\" name=\"TYPAFF_L[$i]\" value=\"\"".($row['TYPAFF_L']=="" ? "checked" : "").">non\n";
echo "</TD>";

echo "<TD><b>Commentaire sur la table :</b> <BR><TEXTAREA cols=\"30\" rows=\"2\" name=\"COMMENT[$i]\">".stripslashes($row[$GLOBALS["NmChpComment"]])."</TEXTAREA></td>";
echo "</TR></TABLE>\n";

// construction de tableaux associatif de hachage contenant 
// diverses infos sur les champs (type, null, auto_inc, val defaut)
// seulement si pas table virtuelle
if (!$booTableVirt) $table_def = db_table_defs($NM_TABLE);
?>
<br><?= $btline ?><H3><?= $nbrows; ?> champs dans cette table: </H3>

<input type="hidden" name="nbrows" value="<?= $nbrows; ?>">
<input type="hidden" name="NM_TABLE" value="<?= $NM_TABLE?>">
<!--On affiche les colonnes qui correspondent aux champs selectionnés -->
<TABLE BORDER="1" BORDERCOLOR="#FFF3F3" CELLSPACING="0" CELLPADDING="2">
<TR class="THEAD" valign="top">
<TD class="th"><u>Nom du champ</u><br>
Libellé à afficher<br><? DHelp("admlib") ?>
<span style="font: 9px">Propriété:<br>Type&nbsp;; Val. déf.&nbsp;; Null OK&nbsp;; Clé index&nbsp;; Extra</span>
</TD>
<TD class="th"><u>Affichage liste :</u><br>
- Ordre<? DHelp("admafl") ?>
<BR>- Type<? DHelp("admafl") ?></TD>
<TD class="th"><u>Edition :</u><br>
- Ordre dans formulaire<br>
- Type saisie<? DHelp("admtyped") ?></TD>
<TD class="th">Valeurs ou lien<? DHelp("admval") ?><br>
<span style="font: 9px">* séparés par des ","</span></TD>
<TD class="th"><u>Ecran de requête :</u><br>
- Type filtre<? DHelp("admtypfilt") ?>
<BR>- Affichage sélectionnable<? DHelp("admafsel") ?></TD>
<TD class="th"><u>Traitements automatiques :</u><br>
- avant MAJ <? DHelp("admttavmaj") ?><br>
- pendant MAJ  <? DHelp("admttpdtmaj") ?><br>
- après MAJ</TD>
<TD class="th">Commentaires sur ce champ....</TD>
</TR>
<? $i=1;
$chpvide4vtb = $booTableVirt; // rajoute ligne vide si table virtuelle
$onemorerow = ($row = db_fetch_assoc($result));
$bcle = $onemorerow || $chpvide4vtb;

while ($bcle) {
    $row=case_kup($row); // verrue �cause de PgSql dont les noms de champs sont insensibles �la case

    echo "<TR>";
    if (!$onemorerow && $chpvide4vtb) { // c'est la fin
		$chpvide4vtb = false; // 1 seule ligne vide en plus, donc apres on arrete
		echo "<TD><input type=\"text\" name=\"NM_CHAMP[$i]\" value=\"\"><BR>";
		$row['ORDAFF'] = $lastval + 1;
    } else {
		// Nom du champ (inchangeable sf en derniere ligne de vtb)
		$NM_CHAMP=$row['NM_CHAMP'];
		$btSuppr = ($booTableVirt ? '<a href="admdesct.php?lc_NM_TABLE='.$NM_TABLE.'&chp2sup='.$NM_CHAMP.'" class="fxbutton">!Suppr!</a> ': "");
		echo "<TD>$btSuppr<B>".$NM_CHAMP."</b><BR>";
		echo "<input type=\"hidden\" name=\"NM_CHAMP[$i]\" value=\"".$NM_CHAMP."\">";
    }

    // Libelle du champ
    echo "<input type=\"text\" name=\"LIBELLE[$i]\" value=\"".stripslashes($row['LIBELLE'])."\">";
    // Caract�istiques du champ (inchangeables), et affich�s que si tble non virtuelle
    if (!$booTableVirt) {
    	echo "<BR><span style=\"font: 9px\">".$table_def[$NM_CHAMP][FieldType]."&nbsp;; ".$table_def[$NM_CHAMP][FieldValDef]."&nbsp;; ".$table_def[$NM_CHAMP][FieldNullOk]."&nbsp;;".$table_def[$NM_CHAMP][FieldKey]."&nbsp;; ".$table_def[$NM_CHAMP][FieldExtra]."\n";
	} // fin si table pas virtuelle

    // Ordre d'affichage ds liste (sur 2 car)
    $val=$row['ORDAFF_L'];
    if (strlen($val)==1) $val="0".$val;
    echo "<TD><input type=\"text\" name=\"ORDAFF_L[$i]\" value=\"".stripslashes($val)."\" size=\"5\">";

    // Type d'affichage ds liste
    // les index servent pour le javascript qui change le type d'affichage de champ dans la liste
    $vares=$row['TYPAFF_L'];
    ?><BR>
    <select name="TYPAFF_L[<?= $i ?>]">
      <option value=<? es(""); //0?>> Caché</option>
      <option value=<? es("TRQ"); //1?>> Tronqué</option>
      <option value=<? es("NOR"); //2?>> Normal</option>
      <option value=<? es("AUT"); //3?>> Auto</option>
      <option value=<? es("LNK"); //4?>> Lié</option>
      <option value=<? es("AHREF"); //5?>> Lien HTML</option>
      <option value=<? es("DT_TS"); ?>> Date timestamp </option>
    </select>
    </TD>
    <?
    // Ordre d'affichage ds �ition (sur 2 car)
    echo "<TD><input type=\"text\" name=\"ORDAFF[$i]\" value=\"";
    $val=$row['ORDAFF'];
    if (strlen($val)==1) $val="0".$val;
    echo stripslashes($val);
	$lastval = $val;
    echo "\" size=\"5\">";

    // Type d'affichage ds �ition
    $vares=$row['TYPEAFF'];
    ?><BR>
    <select name="TYPEAFF[<?=$i?>]" onchange="alertfic(this.value,'<?=$DBName."_".$NM_TABLE."_".$row['NM_CHAMP']; ?>',getIndex(this));" id="typeAff<?=$i?>">
      <option value=<? es("HID"); ?>> Caché</option>
      <option value=<? es("TXT"); ?>> Boite Texte</option>
      <option value=<? es("TXA"); ?>> Text Area</option>
      <option value=<? es("TXARTE"); ?>> Text Area avec RTE</option>
      <option value=<? es("AUT"); ?>> Auto</option>
      <option value=<? es("CKBX"); ?>> Case à cocher</option>
      <option value=<? es("LD"); ?>> Liste Deroulante</option>
      <option value=<? es("LDTXT"); ?>>Liste Deroul. + Boite Texte</option>
      <option value=<? es("LDL"); ?>> Liste Deroul. Liée</option>
      <option value=<? es("LDM"); ?>> Liste Deroul. choix mult.</option>
      <option value=<? es("LDLM"); ?>> Liste Der. Liée choix mult.</option>
      <option value=<? es("POPL"); ?>> Popup de sélection</option>
      <option value=<? es("POPLM"); ?>> Popup sélect. mult</option>
      <option value=<? es("STA"); ?>> Statique</option>
      <option value=<? es("STAL"); ?>> Statique Liée </option>
      <option value=<? es("FICFOT"); ?>> Fichier-Photo </option>
      <option value=<? es("DT_TSTAMP_ED"); ?>> Date timestamp editable</option>
      <option value=<? es("DT_TSTAMP_VS"); ?>> Date timestamp statique</option>
      <option value=<? es("HR_MN"); ?>> Heure : Minute</option>
      <option value=<? es(""); //0?>> Aucun (null)</option>
    </select>
    </TD>
    <?
    // Valeurs possibles
    $butt = '<a href="#" onclick="popup(\'pop_edit_valeurs.php?i='.$i.'&NM_TABLE='.$NM_TABLE.'&NM_CHAMP='.$row['NM_CHAMP'].'&btv='.$booTableVirt.'\',600,700)">Assistant valeurs et liens...</a>';
    echo '<TD>'.$butt.'<TEXTAREA cols="30" rows="5" name="VALEURS['.$i.']" id="valeurs'.$i.'">'.stripslashes($row['VALEURS'])."</TEXTAREA></TD>\n";
//    essai avec textarea au lieu de boite
//    echo "<TD><input size=\"70\" type=\"text\" name=\"VALEURS[$i]\" value=\"".stripslashes($row['VALEURS'])."\"></td>";

    // Type Tri
    // (autrefois valeurs par d�aut, d'o le nom du champ)
    $vares=$row['VAL_DEFAUT'];
    ?><TD>
    <select name="VAL_DEFAUT[<?=$i?>]" onchange="alertFilt(this.value,'<?=$DBName."_".$NM_TABLE."_".$row['NM_CHAMP']; ?>',getIndex(this));">
      <option value=<? es(""); ?>> Aucun</option>
      <option value=<? es("INPLIKE"); ?>>Entrée (Like)</option>
      <option value=<? es("EGAL"); ?>>Entrée (=)</option>
      <option value=<? es("NOTNUL"); ?>>Non nul (>0, !='')</option>
      <option value=<? es("LDC"); ?>>Liste dér. Valeurs champ</option>
      <option value=<? es("LDF"); ?>>L.D. (clé>)Val. fixées, Set ou Enum</option>
      <option value=<? es("LDL"); ?>>L.D. Valeurs liées dynam.</option>
	<option value=<? es("LDLLV"); ?>>L.D. Valeurs liées dyn. limitée vals chp</option>
      <option value=<? es("DANT"); ?>>Date antérieure</option>
      <option value=<? es("DPOST"); ?>>Date postérieure</option>
      <option value=<? es("DATAP"); ?>>Date antérieure et postérieure</option>
      <option value=<? es("VINF"); ?>>Valeur inferieure a</option>
      <option value=<? es("VSUP"); ?>>Valeur superieure a</option>
      <option value=<? es("VIS"); ?>>Valeur comprise entre bornes</option>
      
    </select>
    <?// affichage s�ectionnable dans liste (autrefois Type du champ, maintenant r�up�� d'o le nom du champ...)
    $vares=$row['TYP_CHP'];
    ?>
    <select name="TYP_CHP[<?=$i ?>]">
      <option value=<? es(""); ?>> Non</option>
      <option value=<? es("OCD"); ?>>Oui, coché par défaut</option>
      <option value=<? es("ONCD"); ?>>Oui, Non coché par défaut</option>
    </select>
    </TD>
    
    <? // TRAITEMENTS AUTO
    // Traitement avant MAJ
    $vares=$row['TT_AVMAJ'];
    ?><TD>
    <? // Traitement avant MAJ
    echo "Avant: <br>"; //<input type=\"text\" name=\"TT_AVMAJ2[$i]\" value=\"".$row['TT_AVMAJ']."\"
    ?>
    <select name="TT_AVMAJ[<?= $i ?>]">
      <option value=<? es(""); ?>>Aucun</option>
      <option value=<? es("DJ"); ?>>Date du jour</option>
      <option value=<? es("DJSN"); ?>> Date Jour si null avt</option>
      <option value=<? es("DJP2MSN"); ?>> Date Jour +2 mois si null avt</option>
      <option value=<? es("US"); ?>> Code User MAJ</option>
      <option value=<? es("USSN"); ?>> Code User MAJ si null avt</option>
      <option value=<? es("EDOOFT"); ?>> Edition uniqt si new et copie</option>
    </select>
    <?
    // Traitement pendant MAJ
    echo "<br>Pendant (JS):";// <input type=\"text\" name=\"TT_PDTMAJ[$i]\" value=\"".$row['TT_PDTMAJ']."\">
    // $tbEvenmtVFAutoJS est défini dans fonctions.php
    DispLDandTxt ($tbEvenmtVFAutoJS,"TT_PDTMAJ[$i]",$row['TT_PDTMAJ']);
    // Traitement apr� MAJ
    echo "<BR>Après: <input type=\"text\" name=\"TT_APRMAJ[$i]\" value=\"".$row['TT_APRMAJ']."\"></td>";
    
    // Commentaire
    echo "<TD><TEXTAREA cols=\"30\" rows=\"4\" name=\"COMMENT[$i]\">".stripslashes($row[$GLOBALS["NmChpComment"]])."</TEXTAREA></td>";
    echo "</TR>\n";
    $i++;
    if ($onemorerow) $onemorerow = ($row=db_fetch_assoc($result));
    $bcle = $onemorerow || $chpvide4vtb;
}// fin boucle sur les champs ?>
</table>
<?=$btline?>
  </form>
</div>
<? include ("footer.php"); ?>
</BODY>
</HTML>
