<?php 
require("infos.php");
sess_start();
//include_once("reg_glob.inc");
DBconnect();
$NM_TABLE = $_SESSION['NM_TABLE'];
//print_r($_REQUEST);
// reponse a un ajout, modif ou suppression d'un enregistrement
//if ($debug) echovar("_FILES");
// s'il existe au moins 1 champ fichier-photo,
// on calcule la CLE POUR LE NOM DE STOCKAGE DES FICHIERS ATTACHES EVENTUELS
// uniquement en cas autre que modif: ds ce cas c'est pas la peine, $keycopy=$key

if ($_REQUEST['modif']=="1" && $_REQUEST['key']!="") {
	$keycopy=$keyfich."_";
} else {
	$rpfl=msq("SELECT TYPEAFF from $TBDname where NM_TABLE='$NM_TABLE' AND TYPEAFF='FICFOT'");
	if (db_num_rows($rpfl)>0) { 
	//echovar("_FILES");
	// dermination champ cle pour stockage fichier ou image
	// on prend oid + 1; si c'est pas le bon, pas tr� grave
	if ($_SESSION[db_type]=="pgsql") {
		$rp1=msq("SELECT oid from $CSpIC$NM_TABLE$CSpIC order by oid DESC LIMIT 1");
			$rp2=db_fetch_row($rp1);
			$keycopy=$rp2[0]+1;
			$keycopy=$keycopy."_";
		}
	else {
		// on recupere les noms des 2 1er champs (idem aux variables)
		$rqkc=msq("SELECT NM_CHAMP from $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP!='$NmChDT' ORDER BY ORDAFF, LIBELLE LIMIT 2");
		$nmchp=db_fetch_row($rqkc); 
		$chp=$nmchp[0];
		$mff=mysqff ($chp,$NM_TABLE);
		// dans mff on a les caract. de cle primaire, auto_increment, etc ... du 1er champ
		if (stristr($mff,"primary_key")) { // si 1er champ est une cl�primaire
			// on regarde si c'est un auto incr�ent
			if (stristr($mff,"auto_increment") && (($_REQUEST['modif']==0) || ($_REQUEST['modif']==2)))
				{ // si auto increment et nouvel enregistrement ou copie
				$rp1=msq("SELECT $chp from $CSpIC$NM_TABLE$CSpIC order by $chp DESC LIMIT 1");
				$rp2=mysql_fetch_row($rp1);
				$keycopy=$rp2[0]+1;
				$keycopy=$keycopy."_";
				}
			else 
				{ // si pas auto increment ou modif, on recup la valeur
				$keycopy=$$nmchp[0]."_"; // VALEUR du premier champ  
				}
			
			}
		else // si 1er champ pas cle primaire, elle est forcement constituee des 2 autres
			{ 
			$keycopy=$$nmchp[0]; // VALEUR du premier champ
			$nmchp=mysql_fetch_row($rqkc);
			$keycopy=$keycopy."_".$$nmchp[0]."_";// VALEUR du deuxieme champ
			}
		} // fin si pas session pgsql
	// echo "Keycopy: $keycopy <BR>";
	} // fin s'il y a au moins un champ fichier attach�
} // fin si autre que modif
  
// construction du set, necessite uniquement le nom du champ ..
$rq1=msq("SELECT NM_CHAMP from $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP!='$NmChDT' AND (TYPEAFF!='HID' OR TYPEAFF!='' OR ( TT_PDTMAJ!='' AND TT_PDTMAJ!= NULL)) ORDER BY ORDAFF, LIBELLE");


$tbset=array();
while ($res1=db_fetch_row($rq1))
  {
  $PYAoMAJ=new PYAobj(); // recréée l'objet pour RAZ ses propriétés !!!
  $PYAoMAJ->NmBase=$DBName;
  $PYAoMAJ->NmTable=$NM_TABLE;
  $PYAoMAJ->TypEdit=$_REQUEST['modif'];
  $NOMC=$res1[0]; // nom variable=nom du champ
  $PYAoMAJ->NmChamp=$NOMC;
  $PYAoMAJ->InitPO();
  $PYAoMAJ->ValChp = $_REQUEST[$NOMC]; // issu du formulaire

  if ($PYAoMAJ->TypeAff=="HR_MN") {
	$NOMC_mn=$NOMC."_mn";
	$NOMC_hr=$NOMC."_mn";
	  $PYAoMAJ->ValChp= $$NOMC_mn + $$NOMC_hr*100;
  } elseif ($PYAoMAJ->TypeAff=="FICFOT") {
     // en php5, la variable $this->ValChp=$_REQUEST[toto], quand le champ est de type fichier a uploader, contient en fait $_FILES[toto] qui est un tableau...
     // et ca peut foutre la zouille...
     //$PYAoMAJ->ValChp=(is_array($$NOMC) ? $$NOMC['name'] : $$NOMC); 
     if ($_FILES[$NOMC][name]!="" && $_FILES[$NOMC][error]!="0") die ("error: impossible de joindre le fichier ".$_FILES[$NOMC][name]."; sa taille est peut-etre trop importante");
     $VarFok="Fok".$NOMC;
     //print_r($_FILES);
     
     //$PYAoMAJ->ValChp=($_FILES[$NOMC]['tmp_name']!="" ? $_FILES[$NOMC]['tmp_name'] : $PYAoMAJ->ValChp);
     $PYAoMAJ->ValChp=$_FILES[$NOMC]['tmp_name'];
     $PYAoMAJ->Fok=$$VarFok;
     //$VarFname=$NOMC."_name"; // ancienne m�hode
     //$PYAoMAJ->Fname=($$VarFname !="" ? $$VarFname : $_FILES[$NOMC]['name']);
     $PYAoMAJ->Fname=$_FILES[$NOMC]['name'];
     //$VarFsize=$NOMC."_size";// ancienne m�hode
     //$PYAoMAJ->Fsize=($$VarFsize!="" ? $$VarFsize : $_FILES[$NOMC]['size']);
     $PYAoMAJ->Fsize=$_FILES[$NOMC]['size'];
     $VarOldFName="Old".$NOMC;
     $PYAoMAJ->OFN=$$VarOldFName;
     if ($_REQUEST['modif']==-1) { // suppression de l'enregistrement
        $rqncs=msq("select ".$PYAoMAJ->NmChamp." from ".$PYAoMAJ->NmTable." where ".stripslashes($_REQUEST['key']));
        $rwncs=db_fetch_row($rqncs);
        $PYAoMAJ->Fname=$rwncs[0];
        }
  }
  $tbset=array_merge($tbset,$PYAoMAJ->RetSet($keycopy,true)); // key copy sert �la gestion des fichiers li�
  // la gestion des fichiers est faite aussi l�dedans
} // fin boucle sur les champs

//echovar("tbset");

$_REQUEST['key']=stripslashes($_REQUEST['key']);
//echo "Cl� $_REQUEST['key'] <BR>";

// GROS BUG  $where=" where ".$_REQUEST['key'].($where_sup=="" ? "" : " and $where_sup");
$where=" where ".$_REQUEST['key'];
if ($_REQUEST['modif']==1) // Si on vient d'une �ition
  {
  $strqaj="UPDATE $CSpIC$NM_TABLE$CSpIC SET ".tbset2set($tbset)." $where";
  }
else if ($_REQUEST['modif']==-1) // // Si on vient d'une suppression
  {
  $strqaj="DELETE FROM $CSpIC$NM_TABLE$CSpIC $where";

  }
else // Si on vient de nv enregistrement
  {
  // Ajout dans la table Mysql
  $strqaj="INSERT INTO $CSpIC$NM_TABLE$CSpIC ".tbset2insert($tbset);
  }
//echo "requete sql: $strqaj";
db_query($strqaj);
header ("location:".($lc_adrramact ? $lc_adrramact : ret_adrr("edit_table.php")."?cfp=amact")); // lc_adrramact=sp�ial e-toil*/
?>