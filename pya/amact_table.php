<? require("infos.php");
sess_start();
include_once("reg_glob.inc");
DBconnect();
// réponse a un ajout, modif ou suppression d'un enregistrement

// s'il existe au moins 1 champ fichier-photo,
// on calcule la CLE POUR LE NOM DE STOCKAGE DES FICHIERS ATTACHES EVENTUELS
$rpfl=msq("SELECT TYPEAFF from $TBDname where NM_TABLE='$NM_TABLE' AND TYPEAFF='FICFOT'");
if (mysql_num_rows($rpfl)>0) { 
  // on recupere les noms des 2 1er champs (idem aux variables)
  $rqkc=msq("SELECT NM_CHAMP from $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP!='$NmChDT' ORDER BY ORDAFF, LIBELLE LIMIT 2");
  $nmchp=mysql_fetch_row($rqkc); 
  $chp=$nmchp[0];
  
  // détermination champ cle pour stockage fichier ou image
  $mff=mysqff ($chp,$NM_TABLE);
  // dans mff on a les caract. de cle primaire, auto_increment, etc ... du 1er champ
  if (stristr($mff,"primary_key")) { // si 1er champ est une clé primaire
    // on regarde si c'est un auto incrément
    if (stristr($mff,"auto_increment") && (($modif==0) || ($modif==2))) 
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
  // echo "Keycopy: $keycopy <BR>";
} // fin s'il y a au moins un champ fichier attaché

  
// construction du set, necessite uniquement le nom du champ ..
$rq1=msq("SELECT NM_CHAMP from $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP!='$NmChDT' ORDER BY ORDAFF, LIBELLE");

$PYAoMAJ=new PYAobj();

$PYAoMAJ->NmBase=$DBName;
$PYAoMAJ->NmTable=$NM_TABLE;
$PYAoMAJ->TypEdit=$modif;

while ($res1=mysql_fetch_array($rq1))
  {
  $NOMC=$res1[NM_CHAMP]; // nom variable=nom du champ
  $PYAoMAJ->NmChamp=$NOMC;
  $PYAoMAJ->InitPO();
  $PYAoMAJ->ValChp=$$NOMC; // issu du formulaire
  if ($PYAoMAJ->TypeAff=="FICFOT") {
     $VarFok="Fok".$NOMC;
     $PYAoMAJ->Fok=$$VarFok;
     $VarFname=$NOMC."_name";
     $PYAoMAJ->Fname=$$VarFname;
     $VarFsize=$NOMC."_size";
     $PYAoMAJ->Fsize=$$VarFsize;
     $VarOldFName="Old".$NOMC;
     $PYAoMAJ->OFN=$$VarOldFName;
     if ($modif==-1) { // suppression de l'enregistrement
        $rqncs=msq("select ".$PYAoMAJ->NmChamp." from ".$PYAoMAJ->NmTable." where $key ");
        $rwncs=mysql_fetch_array($rqncs);
        $PYAoMAJ->Fname=$rwncs[0];
        }
     }
  $set.=$PYAoMAJ->RetSet($keycopy); // key copy sert à la gestion des fichiers liés
  // la gestion des fichiers est faite aussi là-dedans

  } // fin boucle sur les champs
  
$set= substr($set,0,-2); // enlève la dernière virgule et esp en trop à la fin

$key=stripslashes($key);
//echo "Clé: $key <BR>";

// GROS BUG  $where=" where ".$key.($where_sup=="" ? "" : " and $where_sup");

$where=" where ".$key;
if ($modif==1) // Si on vient d'une édition
  {
  $strqaj="UPDATE $CSpIC$NM_TABLE$CSpIC SET $set $where";
  }
else if ($modif==-1) // // Si on vient d'une suppression
  {
  $strqaj="DELETE FROM $CSpIC$NM_TABLE$CSpIC $where";

  }
else // Si on vient de nv enregistrement
  {
  // Ajout dans la table Mysql
  $strqaj="INSERT INTO $CSpIC$NM_TABLE$CSpIC SET $set";
  }

//echo "requete sql: $strqaj";
msq($strqaj);
header ("location:".ret_adrr("edit_table.php")."?cfp=amact");
?>
