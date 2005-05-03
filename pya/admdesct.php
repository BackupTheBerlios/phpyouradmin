<?
require("infos.php");
sess_start();
include_once("reg_glob.inc");
$NM_TABLE=$lc_NM_TABLE;
DBconnect(); 
// On compte le nombre d'enregistrement total correspondant à la table
// on realise la requête
$req1="SELECT * FROM $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP!='$NmChDT' order by ORDAFF";
$result=msq($req1);
// on compte le nombre de ligne renvoyée par la requête
$nbrows=mysql_num_rows($result);
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

<SCRIPT>
/* fonction qui renvoie l'index d'un élément de formulaire
on est obligé d'utiliser l'index car les noms de champas avec [] ne sont pas supportés par javascript
*/
function getIndex(what) {
    for (var i=0;i<document.theform.elements.length;i++)
        if (what == document.theform.elements[i]) return i;
    return -1;
}

/* fonction appellée lorsqu'on change le type d'affichage :
- affiche un message d'alerte lorsqu'on choisit le type fichier-photo
- change la valeur de type d'affichage liste fonction du type d'affichage 
- met une mention optionnelle dans la valeur*/

function alertfic(valc,namec,ind)
{
indtafl=ind-2; // indice de l'element typ affichage ds liste
indval=ind+1; // indice de l'element valeurs

if (valc=="FICFOT"){
    alert('Pensez à créer un répertoire nommé ' + namec +', avec les droits en écriture pour le user du serveur web ....');
     document.theform.elements[indtafl].selectedIndex=5; // lien
     document.theform.elements[indval].value='#Dossier de stockage par défaut: '+namec;
  }    

else if (valc=='LDL' || valc=='LDLM' || valc=='STAL' ||valc=='LD' ||valc=='LDM') 
  {
  if ( valc=='LDM'|| valc=='LD') { // remet type aff ds liste en auto si pas lié
    document.theform.elements[indtafl].selectedIndex=3; } // auto
  else { 
    document.theform.elements[indtafl].selectedIndex=4; // met type aff ds liste liée si liée en ppal
    }
  if (document.theform.elements[indval].value=='') {
    document.theform.elements[indval].value='Pensez à rentrer les valeurs ou le lien ici !';
    }
  document.theform.elements[indval].focus();
  }

else if (valc=='HID') 
  {
  document.theform.elements[indtafl].selectedIndex=0; // caché
  }
else {
  document.theform.elements[indtafl].selectedIndex=3; // auto
  }
return;
}
</SCRIPT>
<div align="center">
<BR>
<span class="titrered20px"> Base <?= $DBName?><br>
Propriétés d'édition de la table <?= $LB_TABLE." (".$NM_TABLE?>) </span>
<br>
<?
echo "<H2>Caractéristiques globales de la table $NM_TABLE</H2>";
?>
<form name="theform" action="./admadmresp.php" method="post">
<? // propriétés générales de la table
$reqg="SELECT * FROM $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP='$NmChDT'";
$resg=msq($reqg);
$i=0;
$row=mysql_fetch_array($resg);
echo "<input type=\"hidden\" name=\"NM_CHAMP[$i]\" value=\"".$row['NM_CHAMP']."\">";
 
  echo "<TABLE><TR class=\"backredc\" valign=\"top\">";
  // Nom du champ (inchangeable)
  // Libellé du champ
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
  
  echo "<TD><b>Commentaire sur la table :</b> <BR><TEXTAREA cols=\"30\" rows=\"2\" name=\"COMMENT[$i]\">".stripslashes($row['COMMENT'])."</TEXTAREA></td>";
  echo "</TR>\n";

// construction de tableaux associatif de hachage contenant 
// diverses infos sur les champs (type, null, auto_inc, val defaut)
$table_def = msq("SHOW FIELDS FROM $CSpIC$NM_TABLE$CSpIC");
while ($row_table_def = mysql_fetch_array($table_def)) {
    $NM_CHAMP=$row_table_def['Field'];
  $FieldType[$NM_CHAMP]=$row_table_def['Type'];
  $FieldValDef[$NM_CHAMP]=($row_table_def['Default']!="" ? $row_table_def['Default'] : "ø" );
  // si nouvel enregistrement, affecte la valeur par défaut
  $FieldNullOk[$NM_CHAMP]=($row_table_def['Null']=="YES" ? "yes" : "no"); // YES ou rien
  $FieldKey[$NM_CHAMP]=($row_table_def['Key']!="" ? $row_table_def['Key'] : "ø"); // clé=PRI, index=MUL, unique=UNI
  $FieldExtra[$NM_CHAMP]=$row_table_def['Extra']; // auto_increment 
  }
?>
</TABLE>
<br><span class="chapitrered12px"><?= $nbrows; ?> champs dans cette table: </span><br><br>
<input type="hidden" name="nbrows" value="<?= $nbrows; ?>">
<input type="hidden" name="NM_TABLE" value="<?= $NM_TABLE?>">
    <!--On affiche les colonnes qui correspondent aux champs selectionnés-->
    <TABLE BORDER="1" BORDERCOLOR="#FFF3F3" CELLSPACING="0" CELLPADDING="2">
    <TR class="backredF_boldwhite" valign="top">
    <TD><u>Nom du champ</u><br>
    Libellé à afficher<br><? DHelp("admlib") ?>
    <span style="font: 9px">Propriétés:<br>Type&nbsp;; Val. déf.&nbsp;; Null OK&nbsp;; Clé/index&nbsp;; Extra</span>
    </td>
    <TD><u>Affichage liste :</u><br>
    - Ordre<? DHelp("admafl") ?>
    <BR>- Type<? DHelp("admafl") ?></td>
    <TD><u>Edition :</u><br>
    - Ordre dans formulaire<br>
    - Type contrôle saisie<? DHelp("admtyped") ?></td>
    <TD>Valeurs ou lien<? DHelp("admval") ?><br>
    <span style="font: 9px">* séparés par des ","</span></td>
    <TD><u>Ecran de requête :</u><br>
    - Type filtre<? DHelp("admtypfilt") ?>
    <BR>- Affichage sélectionnable<? DHelp("admafsel") ?></td>
    <TD><u>Traitements automatiques :</u><br>
    - avant MAJ <? DHelp("admttavmaj") ?><br>
    - pendant MAJ  <? DHelp("admttpdtmaj") ?><br>
    - après MAJ</td>
    <TD>Commentaires sur ce champ....</td>
  </TR>
  <? $i=1;
  while ($row=mysql_fetch_array($result)) {
    echo "<TR>";
    // Nom du champ (inchangeable)
    $NM_CHAMP=$row['NM_CHAMP'];
    echo "<TD><B>".$NM_CHAMP."</b><BR>";
    echo "<input type=\"hidden\" name=\"NM_CHAMP[$i]\" value=\"".$NM_CHAMP."\">";
    // Libellé du champ
    echo "<input type=\"text\" name=\"LIBELLE[$i]\" value=\"".stripslashes($row['LIBELLE'])."\">";
    // Caractéristiques du champ (inchangeables)
    echo "<BR><span style=\"font: 9px\">".$FieldType[$NM_CHAMP]."&nbsp;; ".$FieldValDef[$NM_CHAMP]."&nbsp;; ".$FieldNullOk[$NM_CHAMP]."&nbsp;;".$FieldKey[$NM_CHAMP]."&nbsp;; ".$FieldExtra[$NM_CHAMP]."</TD>\n"; // auto

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
      <option value=<? es("LNK"); //4?>> Liée</option>
      <option value=<? es("AHREF"); //5?>> Lien HTML</option>
    </select>
    </TD>
    <?
    // Ordre d'affichage ds édition (sur 2 car)
    echo "<TD><input type=\"text\" name=\"ORDAFF[$i]\" value=\"";
    $val=$row['ORDAFF'];
    if (strlen($val)==1) $val="0".$val;
    echo stripslashes($val);
    echo "\" size=\"5\">";
    
    // Type d'affichage ds édition
    $vares=$row['TYPEAFF'];
    ?><BR>
    <select name="TYPEAFF[<?=$i?>]" onchange="alertfic(this.value,'<?=$DBName."_".$NM_TABLE."_".$row['NM_CHAMP']; ?>',getIndex(this));">
      <option value=<? es("HID"); ?>> Caché</option>
      <option value=<? es("TXT"); ?>> Boite Texte</option>
      <option value=<? es("TXA"); ?>> Text Area</option>
      <option value=<? es("AUT"); ?>> Auto</option>
      <option value=<? es("LD"); ?>> Liste Deroulante</option>
      <option value=<? es("LDL"); ?>> Liste Deroul. Liée</option>
      <option value=<? es("LDM"); ?>> Liste Deroul. choix mult.</option>
      <option value=<? es("LDLM"); ?>> Liste Der. Liée choix mult.</option>
      <option value=<? es("STA"); ?>> Statique</option>
      <option value=<? es("STAL"); ?>> Statique Liée </option>
      <option value=<? es("FICFOT"); ?>> Fichier-Photo </option>
    </select>
    </TD>
    <?
    // Valeurs possibles
    echo "<TD><TEXTAREA cols=\"25\" rows=\"3\" name=\"VALEURS[$i]\">".stripslashes($row['VALEURS'])."</TEXTAREA></TD>\n";
//    essai avec textarea au lieu de boite
//    echo "<TD><input size=\"70\" type=\"text\" name=\"VALEURS[$i]\" value=\"".stripslashes($row['VALEURS'])."\"></td>";

    // Type Tri
    // (autrefois valeurs par défaut, d'où le nom du champ)
    $vares=$row['VAL_DEFAUT'];
    ?><TD>
    <select name="VAL_DEFAUT[<?=$i?>]">
      <option value=<? es(""); ?>> Aucun</option>
      <option value=<? es("INPLIKE"); ?>>Entrée (Like)</option>
      <option value=<? es("LDC"); ?>>Liste dér. Valeurs champ</option>
      <option value=<? es("LDF"); ?>>L.D. (clé->)Val. fixées, Set ou Enum</option>
      <option value=<? es("LDL"); ?>>L.D. Valeurs liées dynam.</option>
      <option value=<? es("DANT"); ?>>Date antérieure</option>
      <option value=<? es("DPOST"); ?>>Date postérieure</option>
      <option value=<? es("DATAP"); ?>>Date antérieure et postérieure</option>
    </select>
    <?// affichage sélectionnable dans liste (autrefois Type du champ, maintenant récupéré, d'où le nom du champ...)
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
    <select name="TT_AVMAJ[<?= $i ?>]">
      <option value=<? es(""); ?>> Aucun</option>
      <option value=<? es("DJ"); ?>> Date du jour</option>
      <option value=<? es("DJSN"); ?>> Date Jour si nulle avant</option>
      <option value=<? es("DJP2MSN"); ?>> Date Jour +2 mois si nulle avant</option>
      <option value=<? es("US"); ?>> Code User MAJ</option>
      <option value=<? es("USSN"); ?>> Code User MAJ si nul avant</option>
      <option value=<? es("EDOOFT"); ?>> Edition uniqt sur nouveau et copie</option>
    </select>
    <?
    // Traitement pendant MAJ
    echo "<input type=\"text\" name=\"TT_PDTMAJ[$i]\" value=\"".$row['TT_PDTMAJ']."\">";
    // Traitement après MAJ
    echo "<BR><input type=\"text\" name=\"TT_APRMAJ[$i]\" value=\"".$row['TT_APRMAJ']."\"></td>";
    
    // Commentaire
    echo "<TD><TEXTAREA cols=\"30\" rows=\"4\" name=\"COMMENT[$i]\">".stripslashes($row['COMMENT'])."</TEXTAREA></td>";
    echo "</TR>\n";
    $i++;
    }?>
</table>
  <br>
  <a href="./LIST_TABLES.php?admadm=1"><img src="./annuler.gif" border="0" onmouseover="self.status='Retour';return true"></A> 
  <input type="image" src="valider.gif" border="0">
  </form>
</div>
<? include ("footer.php"); ?>
</BODY>
</HTML>
