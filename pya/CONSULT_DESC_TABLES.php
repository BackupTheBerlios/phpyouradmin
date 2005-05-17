<?
// utilitaire permettant de générer la table DESC_TABLE qui décrit les autres pour l'édition
include_once("reg_glob.inc");
require("infos.php");
include("globvar.inc");

DBconnect(false);
$title="$DBName: Etat complet";
$admadm=1; // titre avec les !!
//mysql_select_db($DBName) or die ("Impossible d'ouvrir la base de données $DBName.");
include ("header.php"); ?>
<H1>Super Administration de phpYourAdmin</H1>
<H2>Définitions complètes de la base <?=$DBName?></H2>
<Table border="1">
<?
$rqT=msq("select * from $TBDname where NM_CHAMP='$NmChDT' ORDER BY ORDAFF_L,NM_TABLE");
while ($rpT=db_fetch_array($rqT)) {
      echothead($simpl);
      $nolig=0;
      echo "<tr class=\"alertered14px\"><td>".$rpT[NM_TABLE]."</td><td>".$rpT[LIBELLE]."&nbsp;</td>";
      if ($simpl!=1) {echo "<td>".$rpT[ORDAFF_L]."&nbsp;</td><td colspan=8>";}
         else echo "<td colspan=2>";
      echo $rpT[COMMENT]."&nbsp;</td></tr>\n";
      // recup les  caract. des champs

    
    
      $table_def= db_table_defs($rpT[NM_TABLE]);

      $rqC=msq("select * from $TBDname where NM_CHAMP!='$NmChDT' AND NM_TABLE='$rpT[NM_TABLE]' ORDER BY ORDAFF,NM_CHAMP");
      while ($rpC=db_fetch_assoc($rqC)) {
            $nolig++;
            echo "<TR class=\"".($nolig % 2==1 ? "backwhiten" : "backredc")."\">";
            echo "<td><b>$rpC[NM_CHAMP]</b>";
            $NM_CHAMP=$rpC[NM_CHAMP];
            echo "<BR><span style=\"font: 9px\">".$table_def[$NM_CHAMP][FieldType]."&nbsp;; ".$table_def[$NM_CHAMP][FieldValDef]."&nbsp;; ".$table_def[$NM_CHAMP][FieldNullOk]."&nbsp;;".$table_def[$NM_CHAMP][FieldKey]."&nbsp;; ".$table_def[$NM_CHAMP][FieldExtra]."\n";
	     // auto
            echo "</td>";
            echo "<td>$rpC[LIBELLE]&nbsp;</td>";
            if ($simpl!=1) {
                 echo "<td>$rpC[TYPAFF_L] ($rpC[ORDAFF_L])&nbsp;</td>";
                 echo "<td>$rpC[TYPEAFF] ($rpC[ORDAFF])&nbsp;</td>"; }
            echo "<td>$rpC[VALEURS]&nbsp;</td>";
            if ($simpl!=1) { echo "<td>$rpC[VAL_DEFAUT]&nbsp;</td>";
               echo "<td>$rpC[TYP_CHP]&nbsp;</td>";
               echo "<td>$rpC[TT_AVMAJ]&nbsp;</td>";
               echo "<td>$rpC[TT_PDTMAJ]&nbsp;</td>";
               echo "<td>$rpC[TT_APRMAJ]&nbsp;</td>";
               }
            echo "<td>$rpC[COMMENT]&nbsp;</td></tr>\n";
      } // fin boucle sur les champs
   }
?>
</table>
<input class="fxbutton" type="submit" value="<< RETOUR" onclick="history.back()">
<H3>Infos Serveur <?=pinfserv()?></H3>
</body>
</html>
<? function echothead($simpl) {
?>
<thead>
<th>TABLE/CHAMP<BR><span style="font: 9px">Type&nbsp;; Val. déf.&nbsp;; Null OK&nbsp;; Clé/index&nbsp;; Extra</th><th>LIBELLE</th>
<? if ($simpl!=1) { ?>
   <th>Aff. etat</th><th>Aff. Edit</th>
   <? } ?>
<th>VALEURS</th>
<? if ($simpl!=1) { ?>
<th>Filtre</th><th>AFS</th><th>TT_AVMAJ</th><th>TT_PDTMAJ</th><th>TT_APRMAJ</th>
<? } ?>
<th>Commentaire</th>
</thead>
<?} ?>


