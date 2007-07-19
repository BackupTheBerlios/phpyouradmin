<?
/* fichiers de fonctions necessaires �l'authentification unique */
$MOD_AUTH="mysql"; // on pourra bientot utiliser le ldap
// infos de connection serveur,base,table mysql
$HOSTREF="localhost"; // serveur h�ergeant le r��entiel
$MYUSEREF="root"; 
$MYPWDREF="";
$DBREF="INTRADRH";
$TBREF="PERSONNE";

// fonction qui convertit une chaine avec des accents en ascii pur
function cv2asciip_old($strcv) {
//         $batard=array("�,"�,"�,"�,"�,"�,"�,"�,"�,"�,"","","�,"");
//         $asciip=array("a","a","e","e","e","e","i","i","o","o","u","u","c","u");
//         return(str_replace($batard,$asciip,$strcv));
         }

// fonction qui convertit une chaine avec des accents en ascii pur
function cv2asciip($text,$from_enc="UTF-8") {
	setlocale(LC_CTYPE, 'fr_FR');
	return(iconv('UTF-8','ASCII//TRANSLIT', $text));
}


function cvNomPrenom2mail($Nom,$Prenom) {
         //$Nom=str_replace( "-", "" ,$Nom);
         $Nom=str_replace( " ", "" ,$Nom);
         $Nom=str_replace( "'", "" ,$Nom);
	 $Nom=cv2asciip($Nom);
	 $Prenom=cv2asciip($Prenom);
         return(strtolower($Prenom.".".$Nom."@haras-nationaux.fr"));
}

function cvNomPrenom2uid($Nom,$Prenom,$nbcp=1,$nbcar=8) {
         $Nom=str_replace( "-", "" ,$Nom);
         $Nom=str_replace( " ", "" ,$Nom);
         $Nom=str_replace( "'", "" ,$Nom);
	 $Nom=cv2asciip($Nom);
	 $Prenom=cv2asciip($Prenom);
         return(substr(strtolower(substr($Prenom,0,$nbcp).$Nom),0,$nbcar));
}

// 
// fonction renvoyant un tableau associatif, contenant le n de user, son nom, son code profil, etc ... en fonction de l'appli
// NE SERT PLUS CAR ON ACCEDE AU LDAP VIA UN SERVICE WEB
function auth_req($USER_ID,$PASSWD,$APPLI) {
  global $MOD_AUTH,$HOSTREF,$MYUSEREF,$MYPWDREF, $DBREF, $TBREF;
  $lnkdbref=mysql_connect($HOSTREF,$MYUSEREF,$MYPWDREF) or die ("Impossible de se connecter au serveur $Host avec le user $User, passwd: ***** ");
  mysql_select_db($DBREF) or die ("Impossible d'ouvrir la base de donnees $DBREF.");
  $tbas_sso[PER_NUPERS]=RecupLib($TBREF, "PER_LCIDPERS", "PER_NUPERS", $USER_ID);
  if ($tbas_sso[PER_NUPERS]!=false) {
    $rq=msq("select * from $TBREF where PER_LCIDPERS='$USER_ID'");
    $tbas_sso=mysql_fetch_array($rq);
    //echo "Salut $tbas_sso[PER_LLNOMPERS] ! <BR>"; // Debogage
    //$tbas_sso[name]=RecupLib($TBREF, "PER_LCIDPERS", "PER_NOMPER", $USER_ID);
    }
  mysql_close($lnkdbref); // ferme la connexion �cette base
  return ($tbas_sso);
  }
?>
