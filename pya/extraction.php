<?
include_once("reg_glob.inc");
require("infos.php");
sess_start();
DBconnect();
$whodb=stripslashes(urldecode($whodb));

// entetes http pour téléchargement
if (!$debug) {
header('Content-disposition: filename=extractPYA.tsv');
header('Content-type: application/octetstream');
header('Content-type: application/ms-excel');
header('Pragma: no-cache');
header('Expires: 0');
}

$tab="\t"; //tab en ascii
echo "Extraction de données de phpYourAdmin\n\n";
echo "Base $DBName\n\n";
if ($debug) {
   echovar("whodb");
   echovar("DBName");
   echovar("NM_TABLE");
   echovar("tbAfC");}
  
if ($NM_TABLE!="__reqcust") {
   // recup libellé et commentaire de la table
   $LB_TABLE=epurelongchp(RecLibTable($NM_TABLE,0));
   echo "Edition de la table $LB_TABLE ($NM_TABLE)\n\n";

   $result=msq("SELECT 1 FROM $CSpIC$NM_TABLE$CSpIC $whodb");
   // on compte le nombre de ligne renvoyée par la requête
   }
else { // req custom
   $result=msq($reqcust);
   $LB_TABLE=$ss_parenv[lbreqcust];
   $COM_TABLE="";
}

$nbrows=db_num_rows($result);

if ($nbrows==0) {
  echo "AUCUN ENREGISTREMENT !\n" ;} 

else {
  if ($NM_TABLE!="__reqcust") {
     $reqcust="select * from $CSpIC$NM_TABLE$CSpIC";

       $rq1=msq("select * from $TBDname where NM_TABLE='$NM_TABLE' AND NM_CHAMP!='$NmChDT' ORDER BY ORDAFF_L, LIBELLE");
       $j=0; // n° de colonne
       while ($res0=db_fetch_object($rq1)) {
         $tbobjCC[$j]=$res0;
         if ($tbAfC[$res0->NM_CHAMP]) $j++;
         }
       $nbcol=($j-1);

     // initialise les objets    
       for ($i=0;$i<=$nbcol;$i++) {
           $objCC=$tbobjCC[$i]; // objet contenant les caractéristiques du champ
           $NomChamp=$objCC->NM_CHAMP;
	   echo $NomChamp."<br>";
           $CIL[$NomChamp]=new PYAobj(); // instancie un nouvel objet pour chaque champ, stocké ds un tableau
           $CIL[$NomChamp]->NmBase=$DBName;
           $CIL[$NomChamp]->NmTable=$NM_TABLE;
           $CIL[$NomChamp]->NmChamp=$NomChamp;
           $CIL[$NomChamp]->InitPO();
         }
     } // fin si pas custom

  else { // requete custom (perd l'ordre d'affichage sinon)
       $CIL=InitPOReq($reqcust,$DBName); // construction ey initialisation du tableau d'objets
  }


// si   infos, affiche les vrais noms de champs
  if (!$ss_parenv[noinfos]) {
        echo "Noms des champs de la Bdd\t";
      foreach ($CIL as $objCIL){ // boucle sur le tableau d'objets colonnes
          if ($objCIL->Typaff_l!="" && $objCIL->Typaff_l!="") echo  $objCIL->NmChamp."\t";
        }
      echo "\n";
      echo "\n";
  } // fin si afichage des noms de champs  
  
  // affichage entêtes de lignes (noms des champs en clair)
  echo "N° ligne\t";
  
  foreach ($CIL as $objCIL){ // boucle sur le tableau d'objets colonnes
     $NomChamp=$objCIL->NmChamp;
     if ($objCIL->Typaff_l!="" && $objCIL->Typaff_l!="")
        {echo  $objCIL->Libelle."\t";}
     else unset($CIL[$NomChamp]); // en profite pour supprimer les champs non affichés
         
    }
  echo "\n";

  $req=msq("$reqcust $whodb");
  
  $i=1;
  while ($tbValChp=db_fetch_array($req)) {
    // colonnes 
      echo $i."\t"; // affiche n° de ligne
      $i++;
      foreach ($CIL as $objCIL){ // boucle sur le tableau d'objets colonnes
            $NomChamp=$objCIL->NmChamp;
            $objCIL->ValChp=$tbValChp[$NomChamp];
            echo epurelongchp($objCIL->RetVCL(false)).$tab; // affiche Valeur Champ ds Liste
            }  // fin boucle sur les colonnes
      echo "\n";
    } // fin while = fin boucle sur les lignes
  } // fin si nbrows>0

function epurelongchp($vchp)  
{  
  $vchp=str_replace("\n", ";",$vchp);
  $vchp=str_replace("\r", ";",$vchp);
  $vchp=str_replace("&nbsp;", " ",$vchp);
  $vchp=str_replace("<br>", ";",$vchp);
  $vchp=str_replace("<BR>", ";",$vchp);
  $vchp=substr($vchp,0,255) ;
  return($vchp);
}
?>

