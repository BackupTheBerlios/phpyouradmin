<?
// utilitaire permettant de g�n�rer la table DESC_TABLE qui d�crit les autres pour l'�dition

include_once("reg_glob.inc");
require("infos.php");
include("globvar.inc");

mysql_connect($DBHost,$DBUser, $DBPass) or die ("Impossible de se connecter au serveur $DBHost (user: $DBUser, passwd: $DBPass)");

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
Cette page va vous permettre de visualiser ou (re)g�n�rer la table utilitaire (nomm�e <b><? echo $TBDname; ?></b>) permettant la description pour �dition des autres tables de la base <b><? echo $DBName; ?></b><br><br>
<h3><u>Attention:</u> Les op�rations qui suivent sont excessivement risqu�es...</b></h3>
S�lectionner une base dans la liste et cliquez sur le bouton SUITE ci-dessous pour commencer ...<Br>
<form action="./CREATE_DESC_TABLES.php" method="post">
<h3>Liste des bases du serveur <I><? echo "$LBHost </I>($DBHost)"; ?><br>
<select name="DBName">
<? $resb=msq("SHOW DATABASES");
while ($tresb=mysql_fetch_row($resb)) {
  echo "<OPTION VALUE=\"$tresb[0]\">$tresb[0]</OPTION>\n";
  }
?>
</select>
<input type="hidden" name="STEP" value="1">
<input type="submit" value="SUITE >>">
</form><br>
<? 
} //FIN SI STEP="", ie page d'accueil

else if ($STEP=="1") {

?>
<?
mysql_select_db($DBName) or die ("Impossible d'ouvrir la base de donn�es $DBName.");
?>
<h2>Etape 2</h2>
La base s�lectionn�e est <B><U><?=$DBName?></B></U><br><br>
Cette page va vous permettre de visualiser ou (re)g�n�rer la table utilitaire (nomm�e <b><? echo $TBDname; ?></b>) permettant la description pour �dition des autres tables de la base <b><? echo $DBName; ?></b><br><br>
Vous pouvez �diter un <a class="alertered14px" href="CONSULT_DESC_TABLES.php?DBName=<?=$DBName?>">�tat complet</a> ou un <a class="alertered14px" href="CONSULT_DESC_TABLES.php?DBName=<?=$DBName?>&simpl=1">�tat simplifi�</a> de la base</A><br><br>
<h3><u>Attention:</u> Les op�rations qui suivent sont excessivement risqu�es...</b></h3>
S�lectionner une ou plusieurs tables (Ctrl+clic)dans la liste, et cliquez sur le bouton SUITE ci-dessous pour continuer ...<Br>
<form action="./CREATE_DESC_TABLES.php" name="theform" method="post">
<table>
<tr><td><h3>Liste des tables <? echo "(serveur $LBHost $DBHost)"; ?></h3>
<select name="TableName[]" multiple size="10">
<? $trest=msq("SHOW TABLES FROM $DBName");// $trest = mysql_list_tables($DBName);
while ($rst=mysql_fetch_row($trest)) {
  $LNmTb.=$rst[0].";"; // construit une chaine avec ts les noms de tables de la base
  if (strtolower($rst[0])!=strtolower($TBDname))
     echo "<OPTION VALUE=\"$rst[0]\">$rst[0]</OPTION>\n";
  }
?>
</select>
<br><a href="#" onclick="setSelectOptions('theform', 'TableName[]', true); return false;">Tout s�lectionner</a>
<br><a href="#" onclick="setSelectOptions('theform', 'TableName[]', false); return false;">Tout d�s�lectionner</a>
</td>
<td>
<?
// effacement des enregistrements des tables qui n'existent plus
// que si DESC_TABLES existe !
if (stristr($LNmTb,$TBDname)) {
   $rqdt=msq("select NM_TABLE from $TBDname group by NM_TABLE");
   while ($rpdt=mysql_fetch_array($rqdt)) {
         if (!stristr($LNmTb,$rpdt[NM_TABLE])) {
            msq("delete from $TBDname where NM_TABLE='$rpdt[NM_TABLE]'");
            echo "<B>Enregistrements de la table <u>$rpdt[NM_TABLE] effac�s!</u></b><BR>\n";
            }
         }
   }
?>
<br>
<input type="radio" name="CREATION" value="false" checked>Consulter la table<br>
<input type="radio" name="CREATION" value="MAJ">Mettre � jour la table: les nouveaux champs cr��s, les anciens supprim�s, mais les existants inchang�s<br>
<input type="radio" name="CREATION" value="vrai" onclick="if (this.checked) {alert ('Soyez certain de vouloir re-g�n�rer tout ou partie de la table de description !\n Toutes les valeurs pr�alablement saisies seront �cras�es si elles existent !');}" >(re)g�n�rer la table (!)
<BR></h3>
<input type="checkbox" name="AFFALL" value="vrai">Affichage des caract�ristiques <u>compl�tes</u> de chaque champ dans l'�cran suivant<BR><BR>
<input type="checkbox" name="VALAUTO" value="vrai"> Affectation de valeurs automatiques pour certains champs en fonction de leur nom:<BR>
&#149; champs contenant <?=$dtmaj?> (variable $dtmaj) : date du jour auto<BR>
&#149; champs contenant <?=$dtcrea?> (variable $dtcrea): date du jour auto si pas nulle avant<BR>
&#149; champs contenant <?=$usmaj?> : code user affect� (variable $VarNomUserMAJ=<?=$VarNomUserMAJ?>, et li� statique par la chaine <?=$chpperlie?> (variable $chpperlie)<BR>
Les variables ci-dessus sont d�finies dans infos.php<BR><BR>

<input type="hidden" name="DBName" value="<?=$DBName?>">
<input type="hidden" name="STEP" value="2">
</td></tr></table>
<input type="submit" value="SUITE >>">
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
      echo "<H3>Tables s�lectionn�es : </H3><UL>";
      foreach ($TableName as $Table) {
        echo"<LI> $Table";
        }
      echo "</UL>";
   }
  mysql_select_db($DBName) or die ("Impossible d'ouvrir la base de donn�es $DBName.");
  
  // test d'abord l'existence de la table DESC_TABLES
  $result = mysql_list_tables($DBName);
  $i = 0;
  $trouve=false;
  while ($i < mysql_num_rows($result)) 
    {
      $tb_names[$i] = mysql_tablename($result, $i);
    if (strtolower($tb_names[$i])==strtolower($TBDname))
      {$trouve=true;
       break; }
      $i++;
    } // fin boucle sur les tables
  
  if ($trouve && $CREATION=="vrai" && count($TableName)>0) {
    // effacement des enregistrements, mais uniquement ceux des tables s�lectionn�es
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
    // pour la requ�te, faire un copier coller de ce qui vient de phpmyadmin
    $reqC="CREATE TABLE $TBDname (
    NM_TABLE varchar(50) NOT NULL,
     NM_CHAMP varchar(50) NOT NULL,
     LIBELLE varchar(50) NOT NULL,
     ORDAFF_L varchar(5) DEFAULT '0' NOT NULL,
     TYPAFF_L varchar(5) DEFAULT 'AUT' NOT NULL,
     ORDAFF varchar(5) DEFAULT '0' NOT NULL,
     TYPEAFF varchar(20) DEFAULT 'AUT' NOT NULL,
     VALEURS varchar(255) NOT NULL,
     VAL_DEFAUT varchar(200) NOT NULL,
     TT_AVMAJ varchar(20) NOT NULL,
     TT_PDTMAJ varchar(20) NOT NULL,
     TT_APRMAJ varchar(20) NOT NULL,
     TYP_CHP varchar(255) NOT NULL,
     COMMENT tinytext NOT NULL,
     KEY NM_CHAMP (NM_CHAMP),
     KEY NM_TABLE (NM_TABLE),
     KEY ORDAFF_L (ORDAFF_L),
     KEY ORDAFF (ORDAFF))";
    mysql_query($reqC) or die ("requete de creation invalide: <BR>$ReqC");
    } // creation et pas d'existence
  
  // d�but remplissage de la table en fonction des autres
  
  $result = mysql_list_tables($DBName);
  $i = 0;
  while ($i < mysql_num_rows($result)) 
    {
    $tb_names[$i] = mysql_tablename($result, $i);
    $NM_TABLE=$tb_names[$i];
    $tbtoregen=false;
    foreach ($TableName as $Table) {
      if ($Table==$NM_TABLE) {
        $tbtoregen=true;
        break;      
        } 
      }
    if ($tbtoregen) { // table a reg�n�rer
         $rqlibt=msq("SELECT LIBELLE, COMMENT from $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP='$NmChDT'");
         if (mysql_num_rows($rqlibt) >0) $rwlibt=mysql_fetch_array($rqlibt);
         echo "<H3>Table <I>".$NM_TABLE."</I> ($rwlibt[LIBELLE] <small>$rwlibt[COMMENT]</small>)</H3>";
         $resf=msq("select * from $CSpIC$NM_TABLE$CSpIC LIMIT 0"); // uniquement pour avoir la liste des champs
      	// DU au fait que la fonction mysql_field_flags ne fonctionne correctement qu'avec un resultat "NORMAL" et pas avec une requete du type SHOW FIELDS
         $table_def = mysql_query("SHOW FIELDS FROM $CSpIC$NM_TABLE$CSpIC");
        //$resf=mysql_list_fields ($DBName, $CSpIC$NM_TABLE$CSpIC);
           if ($AFFALL=="vrai")
echo "<BLOCKQUOTE>La table $NM_TABLE comporte ".mysql_num_fields($resf)." champs :<BR><FONT SIZE=\"-1\">"; 
        $fields_cnt     = mysql_num_rows($table_def);
        // ins�re un champ commun de description de la table
        if ($CREATION=="vrai") msq("INSERT INTO $TBDname set NM_TABLE='$NM_TABLE', NM_CHAMP='$NmChDT',LIBELLE='$NM_TABLE', ORDAFF='$i', ORDAFF_L='$i'");
        for ($j = 0; $j < $fields_cnt; $j++) {
          $row_table_def   = mysql_fetch_array($table_def);
          //$NM_CHAMP=mysql_field_name ($resf, $j);
          $NM_CHAMP=$row_table_def['Field'];
          $tbNM_CHAMP[$j]=$NM_CHAMP;
          $TYP_CHAMP="";
          $CREATMAJ=false;
          if ($CREATION=="MAJ") {
              $rqCE=msq("SELECT * from $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP='$NM_CHAMP'");
            // si champ pas existant il est a cr�er
            if (mysql_num_rows($rqCE)==0) $CREATMAJ=true; 
              }
          // cree l'enregistrement en MAJ ou 
          if ($CREATION=="vrai" || $CREATMAJ)  {
            // init sp�ciales en fonction des noms de champs
            // des types, etc
            echo "<B><U>Cr�ation </U></B>";
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
                 $TT_AVMAJ="DJ"; // mise � jour auto de la date de MAJ
                 $LIBELLE="MAJ le";
                 }
               if (stristr ($NM_CHAMP,$dtcrea)) 
                 {$TYPEAFF="STA"; // affichage statique (non modifiable)
                 $TYPAFF_L=""; // pas d'affichage ds la liste 
                 $TT_AVMAJ="DJSN";// mise � jour auto de la date de creation
                 $LIBELLE="Date Creation";
                 }
               if (stristr ($NM_CHAMP,$usmaj)) 
                 {$TYPEAFF="STAL"; // affichage statique li� (non modifiable)
                 $TYPAFF_L=""; // pas d'affichage ds la liste
                 $VALEURS=$chpperlie;
                 $TT_AVMAJ="US";
                 $LIBELLE="MAJ par";
                 }
            } // fin si VALAUTO
            $val=$j; // force ordre d'aff sur 2 car
            if (strlen($val)==1) $val="0".$val;
            if (stristr(mysql_field_flags ($resf, $j),"auto_increment")) {
              $TYPEAFF="STA";
              $COMMENT=addslashes("Valeur auto incr�ment�e, impossible � changer par l'utilisateur");
              } // fin si champ auto incr�ment�
      
            msq("INSERT INTO $TBDname set NM_TABLE='$NM_TABLE', NM_CHAMP='$NM_CHAMP', LIBELLE='$LIBELLE', TYPEAFF='$TYPEAFF', VALEURS='$VALEURS', ORDAFF='$val', ORDAFF_L='$val', TYPAFF_L='$TYPAFF_L', TYP_CHP='$TYP_CHAMP', TT_AVMAJ='$TT_AVMAJ', COMMENT='$COMMENT'");
            }  // fin si champ cr�� dans la liste
      echo "<B>".$NM_CHAMP."</B>, ";
      echo" de type ".$row_table_def['Type'];
      $LIBELLE=RecupLib($TBDname,"NM_CHAMP","LIBELLE",$NM_CHAMP);
      if ($LIBELLE!=$NM_CHAMP) echo " - <I>$LIBELLE</I>";
      $TYPEAFF=RecupLib($TBDname,"NM_CHAMP","TYPEAFF",$NM_CHAMP);
      if ($TYPEAFF!="auto") echo "<small> Type aff.: $TYPEAFF</small>";
      $VALEURS=RecupLib($TBDname,"NM_CHAMP","VALEURS",$NM_CHAMP);
      if ($VALEURS!="") echo "<small> Valeurs: $VALEURS</small>";
      echo "<BR>";
      $row_table_def['True_Type'] = ereg_replace('\\(.*', '', $row_table_def['Type']);
         if ($AFFALL=="vrai") echo "Type epur�: ".$row_table_def['True_Type']."<BR>";
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
        } // fin si �num
         if ($AFFALL=="vrai") echo "Flags :".mysql_field_flags ($resf, $j)."<BR>";
      } // fin boucle sur les champs de la table
    
    // en MAJ on enl�ve les champs plus existants
    if ($CREATION=="MAJ") {
        $rqLCE=msq("SELECT NM_CHAMP from $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP!='$NmChDT'");
        while ($rpLCE=mysql_fetch_row($rqLCE)) {
            // si champ n'existe plus l'enl�ve
            if (!in_array($rpLCE[0],$tbNM_CHAMP)) { 
                msq("DELETE FROM $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP='$rpLCE[0]'");
                echo "<br>Champ <b>$rpLCE[0] <u>supprim�</u></b> de la table de description! <BR>";
                } // fin si a supprimer
            }
        } // fin si MAJ
    echo "</FONT></BLOCKQUOTE>";
    } // fin si pas table de definition des autres
    $i++;
    } // fin boucle sur les tables de la base
  ?>
  <span class"normalblack11px">
  <P>Cliquez <b><a href="LIST_TABLES.php?admadm=1&lc_DBName=<? echo $DBName; ?>">ICI</a></b> pour changer les propri�t�s d'EDITION des tables .....
  <P>Cliquez <b><a href="LIST_TABLES.php?lc_DBName=<? echo $DBName; ?>">ICI</a></b> pour �diter le CONTENU des tables.....<br>
  <?
  } // si nbre tables selectionnn�es >0
else echo "<H3> Vous devez s�lectionner au moins une table !</H3>";

} // fin tests sur step
?>
<a href="LIST_TABLES.php" class="fxbutton"> << RETOUR</A>
<H3>Infos Serveur <?=pinfserv()?></H3>
</body>
</html>

