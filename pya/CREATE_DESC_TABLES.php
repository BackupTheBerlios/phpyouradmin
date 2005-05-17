<?
// utilitaire permettant de générer la table DESC_TABLE qui décrit les autres pour l'édition

include_once("reg_glob.inc");
require("infos.php");
include("globvar.inc");
sess_start();
DBconnect();

$title="CREATION DE $TBDname";
$admadm=1; // titre avec les !!
include ("header.php"); 
include ("reg_glob.inc");?>
<H1>Super Administration de phpYourAdmin</H1>
<H2>Administration de la table de description <?=$TBDname?></H2> 
<?
if ($STEP=="") {
?>
<h2>Bonjour !</h2>
Cette page va vous permettre de visualiser ou (re)générer la table utilitaire (nommée <b><? echo $TBDname; ?></b>) permettant la description pour édition des autres tables de la base <b><? echo $DBName; ?></b><br><br>
<h3><u>Attention:</u> Les opérations qui suivent sont excessivement risquées...</b></h3>
Sélectionner une base dans la liste et cliquez sur le bouton SUITE ci-dessous pour commencer ...<Br>
<form action="./CREATE_DESC_TABLES.php" method="post">
<h3>Liste des bases du serveur <I><? echo "$LBHost </I>($DBHost)"; ?><br>

<? $tblbas=db_show_bases(); 
foreach($tblbas as $bas) {
	$tbldbas[$bas]=$bas;
}
DispLD($tbldbas,"DBName");
?>
<br><br>
<input type="hidden" name="STEP" value="1">
<input type="submit"  class="fxbutton" value="SUITE >>">
</form><br>
<? 
} //FIN SI STEP="", ie page d'accueil

else if ($STEP=="1") {

?>
<?
//mysql_select_db($DBName) or die ("Impossible d'ouvrir la base de données $DBName.");
?>
<h2>Etape 2</h2>
La base sélectionnée est <B><U><?=$DBName?></B></U><br><br>
Cette page va vous permettre de visualiser ou (re)générer la table utilitaire (nommée <b><? echo $TBDname; ?></b>) permettant la description pour édition des autres tables de la base <b><? echo $DBName; ?></b><br><br>
Vous pouvez éditer un <a class="alertered14px" href="CONSULT_DESC_TABLES.php?DBName=<?=$DBName?>">état complet</a> ou un <a class="alertered14px" href="CONSULT_DESC_TABLES.php?DBName=<?=$DBName?>&simpl=1">état simplifié</a> de la base</A><br><br>
<h3><u>Attention:</u> Les opérations qui suivent sont excessivement risquées...</b></h3>
Sélectionner une ou plusieurs tables (Ctrl+clic)dans la liste, et cliquez sur le bouton SUITE ci-dessous pour continuer ...<Br>
<form action="./CREATE_DESC_TABLES.php" name="theform" method="post">
<table>
<tr><td><h3>Liste des tables <? echo "(serveur $LBHost $DBHost)"; ?></h3>
<? $tbltab=db_show_tables($DBName); 
//echovar("tblbtab");
?>

<select name="TableName[]" multiple size="10">
<? 
foreach($tbltab as $rst) {
  $LNmTb.=$rst.";"; // construit une chaine avec ts les noms de tables de la base
  if (strtolower($rst)!=strtolower($TBDname))
     echo "<OPTION VALUE=\"$rst\">$rst</OPTION>\n";
  }
?>
</select>
<br><a href="#" onclick="setSelectOptions('theform', 'TableName[]', true); return false;">Tout sélectionner</a>
<br><a href="#" onclick="setSelectOptions('theform', 'TableName[]', false); return false;">Tout désélectionner</a>
</td>
<td>
<?
// effacement des enregistrements des tables qui n'existent plus
// que si DESC_TABLES existe !
if (stristr($LNmTb,$TBDname)) {
   $rqdt=msq("select NM_TABLE from $TBDname group by NM_TABLE");
   while ($rpdt=db_fetch_assoc($rqdt)) {
         if (!stristr($LNmTb,$rpdt[NM_TABLE])) {
            msq("delete from $TBDname where NM_TABLE='$rpdt[NM_TABLE]'");
            echo "<B>Enregistrements de la table <u>$rpdt[NM_TABLE] effacés!</u></b><BR>\n";
            }
         }
   }
?>
<br>
<input type="radio" name="CREATION" value="false" checked>Consulter la table<br>
<input type="radio" name="CREATION" value="MAJ">Mettre à jour la table: les nouveaux champs créés, les anciens supprimés, mais les existants inchangés<br>
<input type="radio" name="CREATION" value="vrai" onclick="if (this.checked) {alert ('Soyez certain de vouloir re-générer tout ou partie de la table de description !\n Toutes les valeurs préalablement saisies seront écrasées si elles existent !');}" >(re)générer la table (!)
<BR></h3>
<input type="checkbox" name="AFFALL" value="vrai">Affichage des caractéristiques <u>complètes</u> de chaque champ dans l'écran suivant<BR><BR>
<input type="checkbox" name="VALAUTO" value="vrai"> Affectation de valeurs automatiques pour certains champs en fonction de leur nom:<BR>
&#149; champs contenant <?=$dtmaj?> (variable $dtmaj) : date du jour auto<BR>
&#149; champs contenant <?=$dtcrea?> (variable $dtcrea): date du jour auto si pas nulle avant<BR>
&#149; champs contenant <?=$usmaj?> : code user affecté (variable $VarNomUserMAJ=<?=$VarNomUserMAJ?>, et lié statique par la chaine <?=$chpperlie?> (variable $chpperlie)<BR>
Les variables ci-dessus sont définies dans infos.php<BR><BR>

<input type="hidden" name="DBName" value="<?=$DBName?>">
<input type="hidden" name="STEP" value="2">
</td></tr></table>
<input type="submit" value="SUITE >>"  class="fxbutton">
</form>
<?
}
else if ($STEP=="2") 
{
if ($CREATION=="vrai") {
  echo "<H2>(RE)GENERATION DE LA TABLE $TBDname</H2>"; }
else {
  echo "<H2>VISUALISATION DE LA BASE $DBName</H2>";
  }
if (count($TableName)>0 ) {
   if ($AFFALL=="vrai") {
      echo "<H3>Tables sélectionnées : </H3><UL>";
      foreach ($TableName as $Table) {
        echo"<LI> $Table";
        }
      echo "</UL>";
   }
   // test d'abord l'existence de la table DESC_TABLES
  $tbltab=db_show_tables($DBName); 
   
 $trouve=in_array($TBDname,$tbltab);
  
  if ($trouve && $CREATION=="vrai" && count($TableName)>0) {
    // effacement des enregistrements, mais uniquement ceux des tables sélectionnées
    foreach ($TableName as $Table) {
      msq("DELETE FROM $TBDname where NM_TABLE='$Table'") or die ("Req. de vidage de  invalide !");
      }
    }   
  
  elseif ( !$trouve && ($CREATION!="vrai")) {
    echo "<BR><span class=\"normalred11px\">LA TABLE <B>$TBDname</B> n'existe pas ! Impossible de la visualiser</span><BR><br>";
    echo "<A HREF=\"CREATE_DESC_TABLES.php\"><img src=\"retour.gif\" border=\"0\"></A>";
    exit();} 
    
  elseif ($CREATION=="vrai")
    {
    // pour la requête, faire un copier coller de ce qui vient de phpmyadmin
    if (in_array($TBDname,db_show_tables($DBName))) db_query("DROP TABLE $TBDname");
    $reqC="CREATE TABLE $TBDname (
    NM_TABLE varchar(50) NOT NULL,
     NM_CHAMP varchar(50) NOT NULL,
     LIBELLE varchar(50) NOT NULL,
     ORDAFF_L varchar(5) DEFAULT '0' ,
     TYPAFF_L varchar(5) DEFAULT 'AUT' ,
     ORDAFF varchar(5) DEFAULT '0' ,
     TYPEAFF varchar(20) DEFAULT 'AUT',
     VALEURS varchar(255),
     VAL_DEFAUT varchar(200),
     TT_AVMAJ varchar(255) ,
     TT_PDTMAJ varchar(255),
     TT_APRMAJ varchar(255),
     TYP_CHP varchar(255),
     COMMENT text)";
     /* rab , pas vraiment indispensable, et qui ne fonctionne pas avec PostGresql
     KEY NM_CHAMP (NM_CHAMP),
     KEY NM_TABLE (NM_TABLE),
     KEY ORDAFF_L (ORDAFF_L),
     KEY ORDAFF (ORDAFF))*/
    db_query($reqC) or die ("requete de creation invalide: <BR>$ReqC");
    } // creation et pas d'existence
    
  // début remplissage de la table en fonction des autres
  
 foreach($tbltab as $NM_TABLE) {
    $tbtoregen=false;
    foreach ($TableName as $Table) {
      if ($Table==$NM_TABLE) {
        $tbtoregen=true;
        break;      
        } 
      }
    if ($tbtoregen) { // table a regénérer
         $rqlibt=msq("SELECT LIBELLE, COMMENT from $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP='$NmChDT'");
         if (db_num_rows($rqlibt) >0) $rwlibt=db_fetch_assoc($rqlibt);
         echo "<H3>Table <I>".$NM_TABLE."</I> ($rwlibt[LIBELLE] <small>$rwlibt[COMMENT]</small>)</H3>";
	 
         $resf=msq("select * from $CSpIC$NM_TABLE$CSpIC LIMIT 0"); // uniquement pour avoir la liste des champs
      	// DU au fait que la fonction mysql_field_flags ne fonctionne correctement qu'avec un resultat "NORMAL" et pas avec une requete du type SHOW FIELDS
         if ($_SESSION[db_type]=="mysql") $table_def = mysql_query("SHOW FIELDS FROM $CSpIC$NM_TABLE$CSpIC");
        //$resf=mysql_list_fields ($DBName, $CSpIC$NM_TABLE$CSpIC);
         if ($AFFALL=="vrai") echo "<BLOCKQUOTE>La table $NM_TABLE comporte ".mysql_num_fields($resf)." champs :<BR><FONT SIZE=\"-1\">"; 
        // insère un champ commun de description de la table
        if ($CREATION=="vrai") msq("INSERT INTO $TBDname (NM_TABLE, NM_CHAMP,LIBELLE, ORDAFF, ORDAFF_L) values
	  ('$NM_TABLE','$NmChDT','$NM_TABLE', '$i', '$i')");
        for ($j = 0; $j < db_num_fields($resf); $j++) {
          if ($_SESSION[db_type]=="mysql") $row_table_def = mysql_fetch_array($table_def);
          $NM_CHAMP=db_field_name ($resf, $j);
          //$NM_CHAMP=$row_table_def['Field'];
          $tbNM_CHAMP[$j]=$NM_CHAMP;
          $TYP_CHAMP="";
          $CREATMAJ=false;
          if ($CREATION=="MAJ") {
              $rqCE=msq("SELECT * from $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP='$NM_CHAMP'");
            // si champ pas existant il est a créer
            if (db_num_rows($rqCE)==0) $CREATMAJ=true; 
              }
          // cree l'enregistrement en MAJ ou 
          if ($CREATION=="vrai" || $CREATMAJ)  {
            // init spéciales en fonction des noms ou des types de champs
            // des types, etc
            echo "<B><U>Création </U></B>";
            $TT_AVMAJ="";
            $TYPEAFF="AUT";
            $TYPAFF_L="AUT";
            $COMMENT="";
            $LIBELLE=$NM_CHAMP;
            $VALEURS="";
            if ($VALAUTO=="vrai") {
               if (stristr ($NM_CHAMP,$dtmaj))
                 {$TYPEAFF="STA"; // affichage statique (non modifiable)
                 $TYPAFF_L=""; // pas d'affichage ds la liste 
                 $TT_AVMAJ="DJ"; // mise à jour auto de la date de MAJ
                 $LIBELLE="MAJ le";
                 }
               if (stristr ($NM_CHAMP,$dtcrea)) 
                 {$TYPEAFF="STA"; // affichage statique (non modifiable)
                 $TYPAFF_L=""; // pas d'affichage ds la liste 
                 $TT_AVMAJ="DJSN";// mise à jour auto de la date de creation
                 $LIBELLE="Date Creation";
                 }
               if (stristr ($NM_CHAMP,$usmaj)) 
                 {$TYPEAFF="STAL"; // affichage statique lié (non modifiable)
                 $TYPAFF_L=""; // pas d'affichage ds la liste
                 $VALEURS=$chpperlie;
                 $TT_AVMAJ="US";
                 $LIBELLE="MAJ par";
                 }
            } // fin si VALAUTO
            $val=$j; // force ordre d'aff sur 2 car
            if (strlen($val)==1) $val="0".$val;
	    if ($_SESSION[db_type]=="mysql") {
		if (stristr(mysql_field_flags ($resf, $j),"auto_increment")) {
		$TYPEAFF="STA";
		$COMMENT=addslashes("Valeur auto incrémentée, impossible à changer par l'utilisateur");
		} // fin si champ auto incrémenté
	    } elseif ($_SESSION[db_type]=="pgsql") {
	    	if (strstr(db_field_type ($resf, $j),"geometry")) {
			$TYPEAFF="TXA";
			$TYPAFF_L="";
			$TT_AVMAJ="sql:astext(%1)";
			$TT_APRMAJ="sql:geometryfromtext(%1)";
		}
	    }
      
            msq("INSERT INTO $TBDname 
	     (NM_TABLE, NM_CHAMP, LIBELLE, TYPEAFF, VALEURS, ORDAFF, ORDAFF_L, TYPAFF_L, TYP_CHP, TT_AVMAJ, TT_PDTMAJ,TT_APRMAJ,COMMENT)
	     values
	      ('$NM_TABLE', '$NM_CHAMP', '$LIBELLE', '$TYPEAFF', '$VALEURS', '$val', '$val','$TYPAFF_L', '$TYP_CHAMP', '$TT_AVMAJ','$TT_PDTMAJ','$TT_APRMAJ', '$COMMENT')");
            }  // fin si champ créé dans la liste
      echo "<B>".$NM_CHAMP."</B>, ";
      if ($_SESSION[db_type]!="mysql") $row_table_def['Type']=db_field_type($resf,$j);
      echo" de type ".$row_table_def['Type'];
      $LIBELLE=RecupLib($TBDname,"NM_CHAMP","LIBELLE",$NM_CHAMP);
      if ($LIBELLE!=$NM_CHAMP) echo " - <I>$LIBELLE</I>";
      $TYPEAFF=RecupLib($TBDname,"NM_CHAMP","TYPEAFF",$NM_CHAMP);
      if ($TYPEAFF!="") echo "<small> Type aff.: $TYPEAFF</small>";
      $VALEURS=RecupLib($TBDname,"NM_CHAMP","VALEURS",$NM_CHAMP);
      if ($VALEURS!="") echo "<small> Valeurs: $VALEURS</small>";
      echo "<BR>";
      $row_table_def['True_Type'] = ereg_replace('\\(.*', '', $row_table_def['Type']);
         if ($AFFALL=="vrai") echo "Type epuré: ".$row_table_def['True_Type']."<BR>";
      if (strstr($row_table_def['True_Type'], 'enum')) {
            $enum        = str_replace('enum(', '', $row_table_def['Type']);
            $enum        = ereg_replace('\\)$', '', $enum);
            $enum        = explode('\',\'', substr($enum, 1, -1));
            $enum_cnt    = count($enum);
        if ($AFFALL=="vrai") {
           echo "Liste de valeurs enum: ";
           for ($l=0;$l<$enum_cnt;$l++) {
             echo $enum[$l]." - ";      
             }
           echo "<BR>";
           }
        } // fin si énum
         if ($AFFALL=="vrai" && $_SESSION[db_type]=="mysql") echo "Flags MySql:".mysql_field_flags ($resf, $j)."<BR>";
      } // fin boucle sur les champs de la table
    
    // en MAJ on enlève les champs plus existants
    if ($CREATION=="MAJ") {
        $rqLCE=msq("SELECT NM_CHAMP from $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP!='$NmChDT'");
        while ($rpLCE=db_fetch_row($rqLCE)) {
            // si champ n'existe plus l'enlève
            if (!in_array($rpLCE[0],$tbNM_CHAMP)) { 
                msq("DELETE FROM $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP='$rpLCE[0]'");
                echo "<br>Champ <b>$rpLCE[0] <u>supprimé</u></b> de la table de description! <BR>";
                } // fin si a supprimer
            }
        } // fin si MAJ
    echo "</FONT></BLOCKQUOTE>";
    } // fin si pas table de definition des autres
    $i++;
    } // fin boucle sur les tables de la base
  ?>
  <P>Cliquez <b><a href="LIST_TABLES.php?admadm=1&lc_DBName=<? echo $DBName; ?>">ICI</a></b> pour changer les propriétés d'EDITION des tables .....
  <P>Cliquez <b><a href="LIST_TABLES.php?lc_DBName=<? echo $DBName; ?>">ICI</a></b> pour éditer le CONTENU des tables.....<br>
  <?
  } // si nbre tables selectionnnées >0
else echo "<H3> Vous devez sélectionner au moins une table !</H3>";

} // fin tests sur step
?>
<br><a href="javascipt:history.back()" class="fxbutton"> << RETOUR</A>
<H3>Infos Serveur <?=pinfserv()?></H3>
</body>
</html>

