<?
require_once("infos.php");
sess_start();
//print_r($_REQUEST);
//include_once("reg_glob.inc");
DBconnect();

$ult=rtb_ultchp(); // tableau des noms de champs sensibles �la casse (�cause de pgsql...)
// On compte le nombre d'enregistrement total correspondant �la table
// on realise la requ�e
if ($_REQUEST['lc_NM_TABLE']) $_SESSION['NM_TABLE'] = $_REQUEST['lc_NM_TABLE'];
$NM_TABLE = $_SESSION['NM_TABLE'];

if ($_REQUEST['lc_FirstEnr']!="") {
  $FirstEnr=$_REQUEST['lc_FirstEnr'];
  $_SESSION["FirstEnr"]=$FirstEnr; //session_register("FirstEnr");
}

if (!isset($FirstEnr) || $_REQUEST['cfopl']!="" ) // && $cfp=="" on vient d'une autre page que de celle la
  {
  $_SESSION["tbchptri"]=$tbchptri=array(); //unregvar ("tbchptri");
  $_SESSION["tbordtri"]=$tbordtri=array(); //unregvar ("tbordtri");
  $_SESSION["FirstEnr"]=$FirstEnr=0; //session_register("FirstEnr");

  }  

  
if ($chptri!="") {
  
// test si champ existant d��dans les tri secondaires
  if ($tbchptri[2]==$chptri) {
    $tbchptri[2]=$tbchptri[3];
    $tbordtri[2]=$tbordtri[3];
    }
  if ($tbchptri[3]==$chptri) {
    $tbchptri[3]="";
    $tbordtri[3]="";
    }
  
  if ($tbchptri[1]!=$chptri) {
  // d�ale les ordres de tri si le champ de tri est diff�ent  
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

if ($_REQUEST['lc_nbligpp']!="") {
  $nbligpp=$_REQUEST['lc_nbligpp'];
  $_SESSION["nbligpp"]=$nbligpp; //session_register("nbligpp");
} else if ($nbligpp==0 || $nbligpp=="")
  {$nbligpp=$nbligpp_def;
   $_SESSION["nbligpp"]=$nbligpp; //session_register("nbligpp");
}

if (isset($_REQUEST['lc_PgReq'])) {
    $PgReq=$_REQUEST['lc_PgReq'];
    $_SESSION["PgReq"]=$PgReq; //session_register("PgReq");
} elseif (!isset($PgReq)) $PgReq=0;

if (isset($_REQUEST['lc_PgReq'])) { // on vient d'une autre page que celle-la donc il faut recalculer la req
	// on est pas en requ�e custom
	if ($NM_TABLE!="__reqcust") {
		// recup libell�et commentaire de la table
		$_SESSION["LB_TABLE"] = RecLibTable($NM_TABLE,0);
		$COM_TABLE=RecLibTable($NM_TABLE,1);

		// constitution du where et des colonnes �afficher en fonction des crit�es de requetes �entuels
		$rqrq=db_query("select NM_CHAMP,TYPAFF_L from $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP!='$NmChDT'");
		// on balaye les noms de champs de cette table
		$condexists=false;
		$afcexists=false;

		$first = true;
		while ($rwrq=db_fetch_row($rqrq)) {
			// reconstitution nom de la var du Type Requ�e
			$NomChp = $rwrq[0];
			$NomChpSql = $NM_TABLE.".".$rwrq[0]; // on adpopte la vraie syntaxe sql
			$NomChpHtml = $NM_TABLE.sepNmTableNmChp.$rwrq[0]; // on adpopte la vraie syntaxe sql
			$nmvarTR="tf_".$NomChpHtml;
			$nmvarVR="rq_".$NomChpHtml; // Valeur de la Requete
// 			trafikPtsInRequestVar($nmvarTR); // remet les variables en syntaxe en rq_NomTable.NomChamp
// 			trafikPtsInRequestVar($nmvarVR);	

			if (isset($_REQUEST[$nmvarTR]) && $_REQUEST[$nmvarVR] != "") { // si ces var non nulles, il y a forc�ent une condition
				if ($first) {
					unset ($_SESSION['memFilt']); // nettoie le filtre au premier coup   		
					$first = false;
				}
				$condexists=true;
				$cond="";
				$nmvarNEG="neg_".$NomChpHtml; // Negation
//				trafikPtsInRequestVar($nmvarNEG);	

				if ($dbgn2) {
					echovar ("nmvarTR");
					echovar ($nmvarTR);
					echovar ("nmvarVR");
					echovar ("nmvarNEG");
				}

				$tbCIL[$NomChp]=new PYAobj(); // instancie un nouvel objet en tableau pour chaque champ
				$tbCIL[$NomChp]->NmBase=$DBName;
				$tbCIL[$NomChp]->NmTable=$NM_TABLE;
				$tbCIL[$NomChp]->NmChamp=$NomChp;
				$tbCIL[$NomChp]->DirEcho=false;
				$tbCIL[$NomChp]->InitPO(); // pour récuperer le type de champ et savoir s'il est numerique
				
				// verrue pour avoir une requete égalité au lieu de recherche en like %val% comme avant
				if ($$nmvarTR=="LDM" && ($tbCIL[$NomChp]->TypeAff!="LDLM" && $tbCIL[$NomChp]->TypeAff!="POPLM")) $$nmvarTR="LDMEG";

				$cond=SetCond ($_REQUEST[$nmvarTR],$_REQUEST[$nmvarVR],$_REQUEST[$nmvarNEG],$NomChpSql,$tbCIL[$NomChp]->TTC=="numeric");
				if ($cond!="") {
					$_SESSION['memFilt'][$nmvarVR] = $_REQUEST[$nmvarVR];
					$_SESSION['memFilt'][$nmvarNEG] = $_REQUEST[$nmvarNEG];
					$cond=str_replace("%%","%",$cond); // virer bizareries
					if ($where_sup!="") $where_sup.=" AND ";
					$where_sup.=$cond;
				}
			} // fin si il existe un crit�e sur ce champ
			// on teste maintenant l'existence de variables de colonnes �afficher
			$tbAfC[$NomChp]=($rwrq[1]!="" && $rwrq[1]!="HID"); // initialise tabeau des colonnes affich�s
			$nmvarAfC="AfC_".$NomChpHtml;
			if (isset($_REQUEST[$nmvarAfC])) {// si cette var existe, colonne s�ectionnable
				$afcexists=true;
				// si affichage selectionnable ne tient pas compte de TYPAFF_L
				$tbAfC[$NomChp]=($_REQUEST[$nmvarAfC] == "yes"); //on MAJ le tableau tableau associatif
				$_SESSION['memFilt'][$nmvarAfC] = $_REQUEST[$nmvarAfC];
			}
		} // fin boucle sur les champs
		// ne r�nregistre que si les variables ont ��d�inies ou chang�s
		if ($condexists) $_SESSION["where_sup"]=$where_sup; //session_register ("where_sup");
		if ($afcexists || !(isset($_SESSION["tbAfC"]))) { // enregistre si tableau n'existait pas, ou si a chang
			//  setcookie("cktbAfC",implode(";",$tbAfC),(time()+604800)); // m�orise la config une semaine
			$_SESSION["tbAfC"]=$tbAfC; //session_register ("tbAfC");
		}
		$where = ($where_sup=="" ? "" : "where ".$where_sup);
		$_SESSION['where'] = $where;
		$_SESSION['reqcust'];
	} // fin si pas req custom
	else { // req custom

		//echo ($_REQUEST['lc_reqcust']);

		$reqcust = $_REQUEST['lc_reqcust'];

		$tblvarrqc = explode(",",$_REQUEST['tblvarrqc']);

		print_r($tblvarrqc);

		$condexists=false;
		$afcexists=false;

		$first = true;

		foreach ($tblvarrqc as $varreq) {
			// reconstitution nom de la var du Type Requ�e
			/// ici test de si c'est un where ...
			$NomChp = $varreq;
			$nmvarTR = "tf_".$NomChp; // Type de filtre
			$nmvarVR = "rq_".$NomChp; // Valeur de la Requete
			$nmvarNuVarrqc = "nvc_".$NomChp; // N° de la variable


			if (isset($_REQUEST[$nmvarTR]) && $_REQUEST[$nmvarVR] != "") { // si ces var non nulles, il y a forc�ent une condition
				if ($first) {
					unset ($_SESSION['memFilt']); // nettoie le filtre au premier coup   		
					$first = false;
				}
				$condexists=true;
				$cond="";
				$nmvarNEG="neg_".$NomChpHtml; // Negation
//				trafikPtsInRequestVar($nmvarNEG);	

				if ($dbgn2) {
					echovar ("nmvarTR");
					echovar ($nmvarTR);
					echovar ("nmvarVR");
					echovar ("nmvarNEG");
				}

				$tbCIL[$NomChp]=new PYAobj(); // instancie un nouvel objet en tableau pour chaque champ
				$tbCIL[$NomChp]->NmBase=$DBName;
				$tbCIL[$NomChp]->NmTable=$NM_TABLE;
				$tbCIL[$NomChp]->NmChamp=$NomChp;
				$tbCIL[$NomChp]->DirEcho=false;
				$tbCIL[$NomChp]->InitPO(); // pour récuperer le type de champ et savoir s'il est numerique
				
				// verrue pour avoir une requete égalité au lieu de recherche en like %val% comme avant
				if ($$nmvarTR=="LDM" && ($tbCIL[$NomChp]->TypeAff!="LDLM" && $tbCIL[$NomChp]->TypeAff!="POPLM")) $$nmvarTR="LDMEG";

				$cond=SetCond ($_REQUEST[$nmvarTR],$_REQUEST[$nmvarVR],$_REQUEST[$nmvarNEG],$NomChpSql,$tbCIL[$NomChp]->TTC=="numeric");
				if ($cond!="") {
					$_SESSION['memFilt'][$nmvarVR] = $_REQUEST[$nmvarVR];
					$_SESSION['memFilt'][$nmvarNEG] = $_REQUEST[$nmvarNEG];
					$cond=str_replace("%%","%",$cond); // virer bizareries
				}
			} // fin si il existe un crit�e sur ce champ

			// on teste maintenant l'existence de variables de colonnes �afficher
			$tbAfC[$NomChp]=($rwrq[1]!="" && $rwrq[1]!="HID"); // initialise tabeau des colonnes affich�s
			$nmvarAfC="AfC_".$NomChpHtml;
			if (isset($_REQUEST[$nmvarAfC])) {// si cette var existe, colonne s�ectionnable
				$afcexists=true;
				// si affichage selectionnable ne tient pas compte de TYPAFF_L
				$tbAfC[$NomChp]=($_REQUEST[$nmvarAfC] == "yes"); //on MAJ le tableau tableau associatif
				$_SESSION['memFilt'][$nmvarAfC] = $_REQUEST[$nmvarAfC];
			}
			$reqcust = str_replace("###".$$nmvarNuVarrqc,$cond,$reqcust);
		} // fin boucle sur les var de requete cust
		// ne r�nregistre que si les variables ont ��d�inies ou chang�s
		if ($afcexists || !(isset($_SESSION["tbAfC"]))) { // enregistre si tableau n'existait pas, ou si a chang
			//  setcookie("cktbAfC",implode(";",$tbAfC),(time()+604800)); // m�orise la config une semaine
			$_SESSION["tbAfC"] = $tbAfC; //session_register ("tbAfC");
		}

		$_SESSION['reqcust'] = $reqcust;
		$_SESSION['where'] = "";
	}
}// fin si on dit recalculer la req

if ($NM_TABLE == "__reqcust") {
	$reqcust = $_SESSION['reqcust'];
	$result = db_query($reqcust);
	$nbrows = db_num_rows($result);
	$reqcust = addwherefORlimit($reqcust,$nbligpp,$FirstEnr);
	$LB_TABLE = $ss_parenv['lbreqcust'];
	$COM_TABLE="";
	$TitreHP=$LB_TABLE;
	$EchWher = "<br><small>$reqcust</small>";
} else { // pas req cust
	/**
	REMARQUE SUR LES CLAUSES LIMIT

	AVEC ORACLE c'EST LE BORDEL MONSTRE
	Voir l'explication du comportement de ROWNUM là http://www.oracle.com/technology/oramag/oracle/06-sep/o56asktom.html
	Comme oracle compte les résultats au fur et à mesure, une req "select * from t where ROWNUM > 1;" ne ramene JAMAIS rien

	L'astuce (pourrie) trouvée : on appelle la requete sans condition basse et on affiche pas les $FirstEnr premiers résultats

	$GLOBALS["NmChpOid"] est le champs masqué qui sert de clé/identifiant d'enregistrement
	**/

	switch ($_SESSION['db_type']) {
		case "mysql":
			$limitc =  " LIMIT $FirstEnr,$nbligpp";
			break;
		
		case "pgsql":
			$limitc =  " OFFSET $FirstEnr LIMIT $nbligpp";
			break;

		case "oracle" :
			//$wherelimit = " (ROWNUM >= $FirstEnr AND ROWNUM <= ".($FirstEnr + $nbligpp).") ";
			$wherelimit = " (ROWNUM <= ".($FirstEnr + $nbligpp).") "; // voire "REMARQUE SUR LES CLAUSES LIMIT au début de ce fichier"
			break;
	}

	$where =  $_SESSION['where'];
	$EchWher="<br><small>Condition: $where</small>";
	$nbrows =  db_count($CSpIC.$NM_TABLE.$CSpIC,$where);
	$where = $wherelimit!="" ? (trim($where)!="" ? "$where AND $wherelimit" : " where $wherelimit") : $where;
	$from = $CSpIC.$NM_TABLE.$CSpIC ;
	$result = db_query("SELECT 1 FROM $from $where");
	$TitreHP=($ss_parenv[ro]==true ? trad('com_consultation') : trad('com_edition')).trad('com_de_la_table'). $_SESSION['LB_TABLE'];
}



$title=trad('LR_title'). $NM_TABLE." , ".trad('com_database')." ". $DBName;
include ("header.php");
//echovar("_SESSION");
//echovar("_REQUEST");
?>

<a name="haut"></a>
<div align="center">
<?
//print_r($_SESSION["tbAfC"]);
if (!$ss_parenv['noinfos'] || true) { ?>
<H3><?=trad('com_database')." ".$DBName?></H3> <? }
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
if ($nbrows==0)// Si nbr�ultat = 0
    {
    ?>
    <H3><?=trad('LR_no_record')?></H3>
    <?
  }
else // si nbr�ultat>0
    {
    $s=($nbrows>1 ? "s" : "");
    ?>
  <script language="JavaScript">
  // boite de confirmation  de suppression d'un enregistrement
    function ConfSuppr(url) {
    if (<?=($NoConfSuppr!="No" ? "confirm('".trad('LR_confirm_del_message')."')": "true")?>)
        self.location.href=url;
    }
  </script>

  <H3><?=$nbrows.trad('com_record').$s.trad('LR_to_list')?> </H3>
  <H4><?=trad('LR_display_record').$s?> <em><?echo ($FirstEnr+1)." ".trad('com_to')." ".min($nbrows,($FirstEnr+$nbligpp));  ?> </em></H4>
  <?if ($ss_parenv[ro]!=true && $NM_TABLE!="__reqcust") {?>
  &nbsp;&nbsp;<a class="fxbutton" href="edit_table.php" title="<?=trad('LT_addrecord')?>"> <img src="new_r.gif"> <?=trad('LT_addrecord')?></a><?=nbsp(15)?>
  <?}?>
  <a href="#bas" class="fxbutton" title="<?=trad('com_vers_enbas_bulle')?>"><img src="flbas.png"> <?=trad('com_vers_enbas')?></a><br>
  <? if ($orderb!="" && !$ss_parenv[noinfos])
    echo "<small>".str_replace ("ORDER BY", trad('LR_orderby'),$orderb)."</small><BR>";
    ?>
    <!--On affiche les colonnes qui correspondent aux champs selectionn�-->
    <TABLE>
    <TR class="THEAD" valign="top">
  <TD class="th" align="center">
  <? echo (($ss_parenv[ro]==true || $NM_TABLE=="__reqcust") ? trad('com_details') : trad('LR_del_mod_cop')); ?>
  </TD>
  <?
  if ($NM_TABLE!="__reqcust") {
     $rq1=msq("select * from $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP!='$NmChDT' ORDER BY ORDAFF_L, LIBELLE");
     $nbcol=0; // n de colonne
     while ($res0=db_fetch_assoc($rq1)) {
     	 $tblallchps[] = $res0[$ult['NM_CHAMP']]; // on fait un tableau avec TOUS les champs à cause des clés multiples qui peuvent être allées chercher dans des champs masqués
         $tbobjCC[$nbcol] = $res0[$ult['NM_CHAMP']];
         if ($_SESSION["tbAfC"][$res0[$ult['NM_CHAMP']]]) {
         	$nbcol++; // la condition n'est true que si champ a afficher et case cochee 
         }
     }
     $nbcol=($nbcol-1);
     
     $tbCIL=array(); // reinitialise le tableau
     for ($i=0;$i<=$nbcol;$i++){
          $NomChamp=$tbobjCC[$i];
//          if (!isset($tbCIL[$NomChamp])) { // si pas deja existant !! on le fait pas sinon ca modifie l'ordre des colonnes
		$tbCIL[$NomChamp]=new PYAobj(); // instancie un nouvel objet en tableau pour chaque champ
		$tbCIL[$NomChamp]->NmBase=$DBName;
		$tbCIL[$NomChamp]->NmTable=$NM_TABLE;
		$tbCIL[$NomChamp]->NmChamp=$NomChamp;
		$tbCIL[$NomChamp]->DirEcho = false;
		$tbCIL[$NomChamp]->InitPO();
//	} //
     } // fin boucle sur les champs
    // rajoute le champ rowid au début pour les éditions,etc...
	$lctd = ($_SESSION['db_type'] == "oracle" ? "ROWIDTOCHAR(ROWID)," : ""). implode(",",$tblallchps);

	
    	//$from = $_SESSION['db_type'] != "oracle" ? $CSpIC.$NM_TABLE.$CSpIC  : " (select ROWNUM,$lctd from $CSpIC$NM_TABLE$CSpIC ) ";
    	$from = $CSpIC.$NM_TABLE.$CSpIC  ;
    	
     $reqcust='select '.$lctd.($GLOBALS["NmChpOid"]!="" ? ",".$GLOBALS["NmChpOid"] : "")." from $from";
  }
  else { // requete custom (perd l'ordre d'affichage sinon)
       $tbCIL=InitPOReq($reqcust,$DBName,false); // construction ey initialisation du tableau d'objets
		/// TODO TODO il faut ds le cas d'oracle faire un espece de hash plutot en tableau assoc des rowid avec toutes les colonnes du from passé en param
  	  ///$reqcust = ($_SESSION['db_type'] == "oracle" ? str_ireplace("select","select ROWIDTOCHAR(sdm_saillie.ROWID),",$reqcust) : $reqcust);
  }

  $lb_orderasc=trad('LR_order_asc');
  $lb_orderdesc=trad('LR_order_desc');

  foreach($tbCIL as $CIL) {
      $NomChamp=$CIL->NmChamp;
      
      if (($CIL->Typaff_l!="" && $CIL->Typaff_l!="HID") || ($_SESSION["tbAfC"][$NomChamp] && $NM_TABLE=="__reqcust") ) { // rajout a cause des req custom
         echo "<TD>";
         DispFlClasst($NomChamp); // affiche fleches de classement existant
         echo "<A HREF=\"list_table.php?chptri=$NomChamp&ordtri=asc\" title=\"$lb_orderasc\"><IMG SRC=\"flasc.gif\" border=\"0\"></A>&nbsp;";
         echo "<A HREF=\"list_table.php?chptri=$NomChamp&ordtri=desc\" title=\"$lb_orderdesc\"><IMG SRC=\"fldesc.gif\" border=\"0\"></A>&nbsp;";
         echo "<BR>".$CIL->Libelle;
         echo "</TD>\n";
         }
     else unset ($tbCIL[$NomChamp]); // sinon efface l'objet
      } // fin boucle sur les colonnes affich�s
  ?>
  </TR>
  <?
  echo "<!--Req sql = $reqcust $where $orderb $limitc -->";
  $req=db_query("$reqcust $where $orderb $limitc"); // dans le cas
  /* pour la cl�:
  - s'il y a une cl�primaire, on la constitue;  ds ce cas $pk=true
  - sinon, la cl�est constitu� de tous les champs
  */
  if ($_SESSION[db_type]=="mysql") {
	$nbpk=0;  // nbre de champs cl� primaires
	for($Idf=0;$Idf<db_num_fields($req);$Idf++) {
	//echo mysql_field_flags($req,$Idf)." <BR/>";
	if (stristr(mysql_field_flags($req,$Idf),"primary")) {
		$tbpk[$nbpk]=mysql_field_name($req,$Idf);
		$nbpk++;
		} // fin si champ est une cl�primaire
	}  // fin boucle sur les champs
  }
//  $chp0=mysql_field_name($req,0);
//  $chp1=mysql_field_name($req,1);
//  if (mysql_num_fields($req)>2) $chp2=mysql_field_name($req,2);
  $nolig = $noligoracle = 0;
  $lb_recedit=trad("LR_record_edit");
  $lb_recdel=trad("LR_record_delete");
  $lb_reccopy=trad("LR_record_copy");
  $lb_recshow=trad("LR_record_show");

// 
	while ($tbValChp=db_fetch_assoc($req)) {
		$noligoracle++;
		if ($_SESSION['db_type'] != "oracle" || $noligoracle>=$FirstEnr) {  // voire "REMARQUE SUR LES CLAUSES LIMIT au début de ce fichier"
			$nolig++;
			// premi�e colonne: modifier / supprimer
			echo "<TR class=\"".($nolig % 2==1 ? "backwhiten" : "backredc")."\"><TD class=\"LRcoledit\" align=\"center\">";
			// gestion cl�
			$key=""; // reinit
			if ($_SESSION[db_type]=="mysql") {
				if ($nbpk>0) { // cl�primaire existe : on la construit (elle peut �re multiple)
				for ($Idf=0;$Idf<$nbpk;$Idf++)
					$key.=$tbpk[$Idf]."='".$tbValChp[$tbpk[$Idf]]."' AND ";
				}
				else { // pas de cl�primaire: on prend ts les champs
				foreach ($tbValChp as $Chp=>$Val) // for ($Idf=0;$Idf<mysql_num_fields($req);$Idf++) $key.=mysql_field_name($req,$Idf)."='".$tbValChp[$Idf]."' AND ";
					$key.="$Chp='$Val' AND ";          
					}
				$key=vdc($key,5); // enl�e le dernier " AND "
			} elseif ($_SESSION['db_type']=="pgsql") {
				$key="oid=".$tbValChp['oid'];
			} elseif ($_SESSION['db_type']=="oracle" &&  $NM_TABLE!="__reqcust"){ /// TODO pour l'instant on gère pas l'affichage fiche en req custom
				$key="ROWIDTOCHAR(ROWID)='".$tbValChp['ROWIDTOCHAR(ROWID)']."'";
			}
			$url=addslashes("amact_table.php?key=".$key."&modif=-1");
			$key=urlencode($key);
			if ($ss_parenv[ro]!=true && $NM_TABLE!="__reqcust") { // bouton supprimer et duppliquer que quand read only false ou req custom
				echo "<A HREF=\"javascript:ConfSuppr('".$url."');\" TITLE=\"$lb_recdel\"><IMG SRC=\"del.png\" border=\"0\" height=\"18\"></A>&nbsp;";
				echo "<A HREF=\"edit_table.php?key=".$key."&modif=1\" TITLE=\"$lb_recedit\"><IMG SRC=\"edit.png\" border=\"0\" height=\"18\"></A>&nbsp;";
				echo "<A HREF=\"edit_table.php?key=".$key."&modif=2\" TITLE=\"$lb_reccopy\"><IMG SRC=\"copie.png\" border=\"0\" height=\"18\"></A>";
				}
			//    else { // affichage en read only (loupe et d�ails de l'enregistrement)
				echo "<A HREF=\"edit_table.php?key=".$key."&modif=C\"  TITLE=\"$lb_recshow\" ><IMG SRC=\"loupe.gif\" border=\"0\"></A>";
			//    }
				echo "</TD>\n";
			// colonnes suivantes
				foreach ($tbCIL as $objCIL){ // boucle sur le tableau d'objets colonnes
					echo("<TD>");
					$NomChamp=$objCIL->NmChamp;
					$objCIL->AffVal($tbValChp); // affecte valeur; un peu traps dans le cas des clés multiple
					if (VerifAdMail($tbValChp[$NomChamp])) $MailATous.=$tbValChp[$NomChamp].",";
					echo $objCIL->EchoVCL(); // affiche Valeur Champ ds Liste
					echo("</TD>");
					}  // fin boucle sur les colonnes
			echo "</TR>";
		} // fin si on affiche les lignes d'avant Oracle
	} // fin while = fin boucle sur les lignes
  } // fin si nbrows>0
  ?>
<br>
</table>
<a name="bas">
<br>

  <?if ($ss_parenv[ro]!=true && $NM_TABLE!="__reqcust") {?>
    <a class="fxbutton" href="edit_table.php" title="<?=trad('LT_addrecord')?>"> <img src="new_r.gif"> <?=trad('LT_addrecord')?></a><?=nbsp(15)?>
  <?}?>
  <a href="#haut" class="fxbutton" title="<?=trad('com_vers_enhaut_bulle')?>"><img src="flhaut.png"> <?=trad('com_vers_enhaut')?></a>

  <?
  if ($FirstEnr>0) {
    echo "&nbsp;&nbsp;&nbsp;<A class=\"fxbutton\" style=\"padding-top: 7px\" HREF=\"list_table.php?lc_FirstEnr=".max(0,$FirstEnr-$nbligpp)."\" title=\"".trad('LT_display_the').$nbligpp.trad('LT_preced_recs')."\"> <img src=\"preced.png\" border=\"0\"> </A>&nbsp;&nbsp;&nbsp;";
    }
  if (($FirstEnr+$nbligpp)<$nbrows) {
    echo "&nbsp;&nbsp;&nbsp;<A class=\"fxbutton\" style=\"padding-top: 7px\"  HREF=\"list_table.php?lc_FirstEnr=".($FirstEnr+$nbligpp)."\" title=\"".trad('LT_display_the').$nbligpp.trad('LT_follow_recs')."\"> <img src=\"suivant.png\" border=\"0\"> </A>";
    }
	?>
  &nbsp;&nbsp;&nbsp;<?=trad(LT_nblig_aff_ppage)?> <input type="text" name="lc_nbligpp" size="3" maxlength="3" value="<? echo ($nbligpp>0 ? $nbligpp : $nbligpp_def) ?>">
  <br><br><?=ret_adrr($_SERVER["PHP_SELF"],true)?>
    <? if ($PgReq==1) { ?>
       &nbsp;&nbsp;&nbsp;<a class="fxbutton" title="<?=trad('LR_query_back_bulle')?>" href="req_table.php?lc_NM_TABLE=<?=$NM_TABLE?>"> <?=trad('LR_query_back')?> </a>
<?  } if ($nbrows>0) {?>
  <img src="shim.gif" height="1" width="50"><a class="fxbutton" href="extraction.php?whodb=<?=urlencode($where." ".$orderb)?>" title="<?=trad('LR_download_bulle')?>"><img src="filesave.png"> <?=trad('LR_download')?></A> &nbsp;&nbsp;&nbsp; <a class="fxbutton" href="extraction.php?encod=iso&whodb=<?=urlencode($where." ".$orderb)?>" title="<?=trad('LR_download_bulle')?> EN ISO FORMAT"> <img src="filesave.png">Extract. ISO</A>
  <? if ($MailATous!="") {
	$MailATous=vdc($MailATous,1); // d�age la derniere virgule
  ?> &nbsp;&nbsp;&nbsp;<a href="mailto:<?=$MailATous?>" title="<?=trad('LR_mail_to_all_bulle')?>"><img src="mail_send.png"> <?=trad('LR_mail_to_all')?></A>
  <? 	} // fin MailATous
   } // fin nblig>0
   ?>

</div>
<? include ("footer.php");

function DispFlClasst($NmChamp) { // affiche fleches de classement
global $tbchptri, $tbordtri;
for ($l=1;$l<=3;$l++) {
  if ($tbchptri[$l]==$NmChamp) {
    echo "<IMG SRC=\"fl".$l.$tbordtri[$l].".gif\" alt=\"la flèche indique le sens et le n° d'ordre de clé du classement de ce champ\">&nbsp;";
    break;
    }
  }
}
?>
