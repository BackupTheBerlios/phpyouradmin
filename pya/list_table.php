<?
include_once("reg_glob.inc");
require("infos.php");
sess_start();
DBconnect();
// On compte le nombre d'enregistrement total correspondant � la table
// on realise la requ�te

if ($lc_FirstEnr!="") {
  $FirstEnr=$lc_FirstEnr;
  $_SESSION["FirstEnr"]=$FirstEnr; //session_register("FirstEnr");
  }
else if ($FirstEnr=="" && $cfp=="") // on vient d'une autre page que de celle l� ou des pages de consult
  {$FirstEnr=0;
  unset($_SESSION["tbchptri"]); //unregvar ("tbchptri");
  unset($_SESSION["tbordtri"]); //unregvar ("tbordtri");
  unset($_SESSION["FirstEnr"]); //unregvar ("FirstEnr");
  $_SESSION["FirstEnr"]=$FirstEnr; //session_register("FirstEnr");
  }  

  
if ($chptri!="") {
  
// test si champ existant d�j� dans les tri secondaires
  if ($tbchptri[2]==$chptri) {
    $tbchptri[2]=$tbchptri[3];
    $tbordtri[2]=$tbordtri[3];
    }
  if ($tbchptri[3]==$chptri) {
    $tbchptri[3]="";
    $tbordtri[3]="";
    }
  
  if ($tbchptri[1]!=$chptri) {
  // d�cale les ordres de tri si le champ de tri est diff�rent  
    if ($tbchptri[2]!="") {
      $tbchptri[3]=$tbchptri[2];
      $tbordtri[3]=$tbordtri[2];
  
      }
    if ($tbchptri[1]!="") {
      $tbchptri[2]=$tbchptri[1];
      $tbordtri[2]=$tbordtri[1];
      }
    }
  $tbchptri[1]=$chptri;
  $tbordtri[1]=$ordtri;
  
  $FirstEnr=0;
  $_SESSION["tbchptri"]=$tbchptri; //session_register("tbchptri","tbordtri","FirstEnr");
  $_SESSION["tbordtri"]=$tbordtri;
  $_SESSION["FirstEnr"]=$FirstEnr;
  } // fin $chptri!=""

if ($tbchptri[1]!="") {
  $orderb="ORDER BY $tbchptri[1] $tbordtri[1]";
  if ($tbchptri[2]!="") $orderb.=", $tbchptri[2] $tbordtri[2]";
  if ($tbchptri[3]!="") $orderb.=", $tbchptri[3] $tbordtri[3]";
  }

if ($lc_nbligpp!="") {
  $nbligpp=$lc_nbligpp;
  $_SESSION["nbligpp"]=$nbligpp; //session_register("nbligpp");
  }

else if ($nbligpp==0 || $nbligpp=="")
  {$nbligpp=$nbligpp_def;
   $_SESSION["nbligpp"]=$nbligpp; //session_register("nbligpp");
  }

if (isset($lc_PgReq)) {
    $PgReq=$lc_PgReq;
    $_SESSION["PgReq"]=$PgReq; //session_register("PgReq");
    }
else if(!isset($PgReq)) $PgReq=0;

$limitc=" LIMIT $FirstEnr,$nbligpp";

// on est pas en requ�te custom
if ($NM_TABLE!="__reqcust") {
   // recup libell� et commentaire de la table
   $LB_TABLE=RecLibTable($NM_TABLE,0);
   $COM_TABLE=RecLibTable($NM_TABLE,1);

   // constitution du where et des colonnes � afficher en fonction des crit�res de requetes �ventuels
   $rqrq=msq("select NM_CHAMP,TYPAFF_L from $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP!='$NmChDT'");
   // on balaye les noms de champs de cette table
   $condexists=false;
   $afcexists=false;

   while ($rwrq=db_fetch_array($rqrq)) {
     // reconstitution nom de la var du Type Requ�te
     $NomChp=$rwrq[NM_CHAMP];
     $nmvarTR="tf_".$NomChp;
     $nmvarVR="rq_".$NomChp; // Valeur de la Requete
     if (isset($$nmvarTR) && $$nmvarVR!="") {// si ces var non nulles, il y a forc�ment une condition
       $condexists=true;
       $cond="";
       $nmvarNEG="neg_".$NomChp; // Negation
       if ($dbgn2) {
         echovar ("nmvarTR");
         echovar ($nmvarTR);
         echovar ("nmvarVR");
         echovar ("nmvarNEG");
         }

       $cond=SetCond ($$nmvarTR,$$nmvarVR,$$nmvarNEG,$NomChp);
       if ($cond!="") {
         if ($where_sup!="") $where_sup.=" AND ";
         $where_sup.=$cond;
         }
       } // fin si il existe un crit�re sur ce champ
       
     // on teste maintenant l'existence de variables de colonnes � afficher
     $tmp_tbAfC[$NomChp]=($rwrq[TYPAFF_L]!="" && $rwrq[TYPAFF_L]!="HID"); // initialise tabeau des colonnes affich�es
     $nmvarAfC="AfC_".$NomChp;
     if (isset($$nmvarAfC)) {// si cette var existe, colonne s�lectionnable
       $afcexists=true;
       if ($debug) echovar($nmvarAfC);  
       $tmp_tbAfC[$NomChp]=($tmp_tbAfC[$NomChp] && $$nmvarAfC=="yes"); //on MAJ le tableau tableau associatif     
       }
     } // fin boucle sur les champs

   // ne r�enregistre que si les variables ont �t� d�finies ou chang�es
   if ($condexists) $_SESSION["where_sup"]=$where_sup; //session_register ("where_sup");
   if ($afcexists || !(isset($tbAfC))) { // enregistre si tableau n'existait pas, ou si a chang�
     $tbAfC=$tmp_tbAfC;
   //  setcookie("cktbAfC",implode(";",$tbAfC),(time()+604800)); // m�morise la config une semaine
     $_SESSION["tbAfC"]=$tbAfC; //session_register ("tbAfC");
	 }

   $where=($where_sup=="" ? "" : "where ".$where_sup);
   $result=msq("SELECT 1 FROM $CSpIC$NM_TABLE$CSpIC $where");

   $EchWher="<br><small>Condition: $where</small>";
   $TitreHP=($ss_parenv[ro]==true ? trad(com_consultation) : trad(com_edition)).trad(com_de_la_table). $LB_TABLE;
   } // fin si pas req custom

else { // req custom
   $result=msq($reqcust);

   $LB_TABLE=$ss_parenv[lbreqcust];
   $COM_TABLE="";
   $TitreHP=$LB_TABLE;
   $EchWher="<br><small>$reqcust</small>";
}

// on compte le nombre de ligne renvoy�e par la requ�te
$nbrows=db_num_rows($result);

$title=trad(LR_title). $NM_TABLE." , ".trad(com_database)." ". $DBName;
include ("header.php");
//echovar("_SESSION");
//echovar("_REQUEST");
?>

<a name="haut"></a>
<div align="center">
<?
if (!$ss_parenv[noinfos]) { ?>
<H3><?=trad(com_database)." ".$DBName?></H3> <? }
?>
<H1><?=$TitreHP?></H1>
<?
if (!$ss_parenv[noinfos]) {
//   echo "($NM_TABLE)";
   echo ($COM_TABLE!="" ? "$COM_TABLE" : "");
   }
?>
<?
if (($where!="" || $NM_TABLE=="__reqcust") && !$ss_parenv[noinfos]) echo $EchWher;
// On affiche le resultat
if ($nbrows==0)// Si nbr�sultat = 0
    {
    ?>
    <H3><?=trad(LR_no_record)?></H3>
    <?
  }
else // si nbr�sultat>0
    {
    $s=($nbrows>1 ? "s" : "");
    ?>
  <script language="JavaScript">
  // boite de confirmation  de suppression d'un enregistrement
    function ConfSuppr(url) {
    if (<?=($NoConfSuppr!="No" ? "confirm('".trad(LR_confirm_del_message)."')": "true")?>)
        self.location.href=url;
    }
  </script>

  <H3><?=$nbrows.trad(com_record).$s.trad(LR_to_list)?> </H3>
  <H4><?=trad(LR_display_record).$s?> <em><?echo ($FirstEnr+1)." ".trad(com_to)." ".min($nbrows,($FirstEnr+$nbligpp));  ?> </em></H4>
  <?if ($ss_parenv[ro]!=true && $NM_TABLE!="__reqcust") {?>
  &nbsp;&nbsp;<a class="fxbutton" href="edit_table.php" title="<?=trad(LT_addrecord)?>"> <img src="new_r.gif"> <?=trad(LT_addrecord)?></a><?=nbsp(15)?>
  <?}?>
  <a href="#bas" class="fxbutton" title="<?=trad(com_vers_enbas_bulle)?>"><img src="flbas.png"> <?=trad(com_vers_enbas)?></a><br>
  <? if ($orderb!="" && !$ss_parenv[noinfos])
    echo "<small>".str_replace ("ORDER BY", trad(LR_orderby),$orderb)."</small><BR>";
    ?>
    <!--On affiche les colonnes qui correspondent aux champs selectionn�s-->
    <TABLE>
    <THEAD valign="top">
  <TH align="center">
  <? echo (($ss_parenv[ro]==true || $NM_TABLE=="__reqcust") ? trad(com_details) : trad(LR_del_mod_cop)); ?>
  </TH>
  <?
  if ($NM_TABLE!="__reqcust") {
     $rq1=msq("select * from $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP!='$NmChDT' AND TYPAFF_L!='' ORDER BY ORDAFF_L, LIBELLE");
     $j=0; // n� de colonne
     while ($res0=mysql_fetch_assoc($rq1)) {
         $tbobjCC[$j]=$res0;
         if ($tbAfC[$res0[NM_CHAMP]]) $j++; // la condition n'est true que si champ � afficher et case coch�e
         }
     $nbcol=($j-1);

     for ($i=0;$i<=$nbcol;$i++){
          $NomChamp=$tbobjCC[$i][NM_CHAMP];
          $tbCIL[$NomChamp]=new PYAobj(); // instancie un nouvel objet en tableau pour chaque champ
          $tbCIL[$NomChamp]->NmBase=$DBName;
          $tbCIL[$NomChamp]->NmTable=$NM_TABLE;
          $tbCIL[$NomChamp]->NmChamp=$NomChamp;
          $tbCIL[$NomChamp]->InitPO();
     } // fin boucle sur les champs
     
   // CHANGER LE * EN TABLEAU DES CHAMPS 
    $reqcust="select * from $CSpIC$NM_TABLE$CSpIC";

  }

  else { // requete custom (perd l'ordre d'affichage sinon)
       $tbCIL=InitPOReq($reqcust,$DBName); // construction ey initialisation du tableau d'objets
  }

  $lb_orderasc=trad(LR_order_asc);
  $lb_orderdesc=trad(LR_order_desc);

  foreach($tbCIL as $CIL) {
      $NomChamp=$CIL->NmChamp;
      if ($CIL->Typaff_l!="" && $CIL->Typaff_l!="") { // rajout� � cause des req custom
         echo "<TH>";
         DispFlClasst($NomChamp); // affiche fleches de classement existant
         echo "<A HREF=\"list_table.php?chptri=$NomChamp&ordtri=asc\" title=\"$lb_orderasc\"><IMG SRC=\"flasc.gif\" border=\"0\"></A>&nbsp;";
         echo "<A HREF=\"list_table.php?chptri=$NomChamp&ordtri=desc\" title=\"$lb_orderdesc\"><IMG SRC=\"fldesc.gif\" border=\"0\"></A>&nbsp;";
         echo "<BR>".$CIL->Libelle;
         echo "</TH>\n";
         }
     else unset ($tbCIL[$NomChamp]); // sinon efface l'objet
      } // fin boucle sur les colonnes affich�es
  ?>
  </THEAD>
  <?
  $req=msq("$reqcust $where $orderb $limitc");
  /* pour la cl� :
  - s'il y a une cl� primaire, on la constitue;  ds ce cas $pk=true
  - sinon, la cl� est constitu�e de tous les champs
  */
  $nbpk=0;  // nbre de champs cl�s primaires
  for($Idf=0;$Idf<db_num_fields($req);$Idf++) {
    if (stristr(mysql_field_flags($req,$Idf),"primary_key")) {
       $tbpk[$nbpk]=$Idf;
       $nbpk++;
       } // fin si champ est une cl� primaire
    }  // fin boucle sur les champs

//  $chp0=mysql_field_name($req,0);
//  $chp1=mysql_field_name($req,1);
//  if (mysql_num_fields($req)>2) $chp2=mysql_field_name($req,2);
  $nolig=0;
  $lb_recedit=trad(LR_record_edit);
  $lb_recdel=trad(LR_record_delete);
  $lb_reccopy=trad(LR_record_copy);
  $lb_recshow=trad(LR_record_show);

  while ($tbValChp=mysql_fetch_array($req, MYSQL_BOTH)) {
    $nolig++;
    // premi�re colonne: modifier / supprimer
      echo "<TR class=\"".($nolig % 2==1 ? "backwhiten" : "backredc")."\"><TD class=\"LRcoledit\" align=\"center\">";
    // gestion cl�
    $key=""; // reinit
    if ($nbpk>0) { // cl� primaire existe : on la construit (elle peut �tre multiple)
       for ($Idf=0;$Idf<$nbpk;$Idf++)
           $key.=mysql_field_name($req,$tbpk[$Idf])."='".$tbValChp[$tbpk[$Idf]]."' AND ";
       }
    else { // pas de cl� primaire: on prend ts les champs
       for ($Idf=0;$Idf<mysql_num_fields($req);$Idf++)
           $key.=mysql_field_name($req,$Idf)."='".$tbValChp[$Idf]."' AND ";
         }
    $key=vdc($key,5); // enl�ve le dernier " AND "
//    $key=($pk ? $chp0."='".$tbValChp[0]."'" : $chp0."='".$tbValChp[0]."' AND ".$chp1."='".$tbValChp[1]."' ");
    // si le 3�me existe, on le prend aussi
  //  if (!$pk && $chp2!="") $key.="AND ".$chp2."='".$tbValChp[2]."'";
    // onmouseover=\"self.status='Modifier l\'enregistrement';return true\"


    $url=addslashes("amact_table.php?key=".$key."&modif=-1");
    $key=urlencode($key);
    if ($ss_parenv[ro]!=true && $NM_TABLE!="__reqcust") { // bouton supprimer et duppliquer que quand read only false ou req custom
        echo "<A HREF=\"javascript:ConfSuppr('".$url."');\" TITLE=\"$lb_recdel\"><IMG SRC=\"del.png\" border=\"0\" height=\"18\"></A>&nbsp;";
        echo "<A HREF=\"edit_table.php?key=".$key."&modif=1\" TITLE=\"$lb_recedit\"><IMG SRC=\"edit.png\" border=\"0\" height=\"18\"></A>&nbsp;";
        echo "<A HREF=\"edit_table.php?key=".$key."&modif=2\" TITLE=\"$lb_reccopy\"><IMG SRC=\"copie.png\" border=\"0\" height=\"18\"></A>";
        }
    else { // affichage en read only (loupe et d�tails de l'enregistrement)
         echo "<A HREF=\"edit_table.php?key=".$key."&modif=1\"><IMG SRC=\"loupe.gif\" ALT=\"$lb_recshow\" border=\"0\"></A>";
    }
      echo "</TD>\n";
    // colonnes suivantes
      foreach ($tbCIL as $objCIL){ // boucle sur le tableau d'objets colonnes
            echo("<TD>");
            $NomChamp=$objCIL->NmChamp;
            $objCIL->ValChp=$tbValChp[$NomChamp];
			if (VerifAdMail($tbValChp[$NomChamp])) $MailATous.=$tbValChp[$NomChamp].",";
            $objCIL->EchoVCL(); // affiche Valeur Champ ds Liste
            echo("</TD>");
            }  // fin boucle sur les colonnes
    echo "</TR>";
    } // fin while = fin boucle sur les lignes
  } // fin si nbrows>0
  ?>
<br>
</table>
<a name="bas">
<br>

  <?if ($ss_parenv[ro]!=true && $NM_TABLE!="__reqcust") {?>
    <a class="fxbutton" href="edit_table.php" title="<?=trad(LT_addrecord)?>"> <img src="new_r.gif"> <?=trad(LT_addrecord)?></a><?=nbsp(15)?>
  <?}?>
  <a href="#haut" class="fxbutton" title="<?=trad(com_vers_enhaut_bulle)?>"><img src="flhaut.png"> <?=trad(com_vers_enhaut)?></a>

  <?
  if ($FirstEnr>0) {
    echo "&nbsp;&nbsp;&nbsp;<A class=\"fxbutton\" style=\"padding-top: 7px\" HREF=\"list_table.php?lc_FirstEnr=".max(0,$FirstEnr-$nbligpp)."\" title=\"".trad(LT_display_the).$nbligpp.trad(LT_preced_recs)."\"> <img src=\"preced.png\" border=\"0\"> </A>&nbsp;&nbsp;&nbsp;";
    }
  if (($FirstEnr+$nbligpp)<$nbrows) {
    echo "&nbsp;&nbsp;&nbsp;<A class=\"fxbutton\" style=\"padding-top: 7px\"  HREF=\"list_table.php?lc_FirstEnr=".($FirstEnr+$nbligpp)."\" title=\"".trad(LT_display_the).$nbligpp.trad(LT_follow_recs)."\"> <img src=\"suivant.png\" border=\"0\"> </A>";
    }
	?>
  <br><br><?=ret_adrr($_SERVER["PHP_SELF"],true)?>
    <? if ($PgReq==1) { ?>
       &nbsp;&nbsp;&nbsp;<a class="fxbutton" title="<?=trad(LR_query_back_bulle)?>" href="req_table.php?lc_NM_TABLE=<?=$NM_TABLE?>"> <?=trad(LR_query_back)?> </a>
<?  } if ($nbrows>0) {?>
  <img src="shim.gif" height="1" width="50"><a class="fxbutton" href="extraction.php?whodb=<?=urlencode($where." ".$orderb)?>" title="<?=trad(LR_download_bulle)?>"><img src="filesave.png"> <?=trad(LR_download)?></A>
  <? if ($MailATous!="") {
	$MailATous=vdc($MailATous,1); // d�gage la derniere virgule
  ?> &nbsp;&nbsp;&nbsp;<a href="mailto:<?=$MailATous?>" title="<?=trad(LR_mail_to_all_bulle)?>"><img src="mail_send.png"> <?=trad(LR_mail_to_all)?></A>
  <? 	} // fin MailATous
   } // fin nblig>0
   ?>

</div>
<? include ("footer.php");

function DispFlClasst($NmChamp) { // affiche fleches de classement
global $tbchptri, $tbordtri;
for ($l=1;$l<=3;$l++) {
  if ($tbchptri[$l]==$NmChamp) {
    echo "<IMG SRC=\"fl".$l.$tbordtri[$l].".gif\" alt=\"la fl�che indique le sens et le n� l'ordre de cl� du classement de ce champ\">&nbsp;";
    break;
    }
  }
}
?>
