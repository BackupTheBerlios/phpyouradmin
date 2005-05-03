<?
/* fichiers de fonctions necessaires à l'authentification unique */
$MOD_AUTH="mysql"; // on pourra bientot utiliser le ldap
// infos de connection serveur,base,table mysql
$HOSTREF="localhost"; // serveur hébergeant le référentiel
$MYUSEREF="root"; 
$MYPWDREF="";
$DBREF="INTRADRH";
$TBREF="PERSONNE";

// 
// fonction renvoyant un tableau associatif, contenant le n° de user, son nom, son code profil, etc ... en fonction de l'appli
function auth_req($USER_ID,$PASSWD,$APPLI) {
	global $MOD_AUTH,$HOSTREF,$MYUSEREF,$MYPWDREF, $DBREF, $TBREF;
	$lnkdbref=mysql_connect($HOSTREF,$MYUSEREF,$MYPWDREF) or die ("Impossible de se connecter au serveur $Host avec le user $User, passwd: ***** ");
	mysql_select_db($DBREF) or die ("Impossible d'ouvrir la base de données $DBREF.");
	$tbas_sso[PER_NUPERS]=RecupLib($TBREF, "PER_LCIDPERS", "PER_NUPERS", $USER_ID);
	if ($tbas_sso[PER_NUPERS]!=false) {
		$rq=msq("select * from $TBREF where PER_LCIDPERS='$USER_ID'");
		$tbas_sso=mysql_fetch_array($rq);
		//echo "Salut $tbas_sso[PER_LLNOMPERS] ! <BR>"; // Debogage
		//$tbas_sso[name]=RecupLib($TBREF, "PER_LCIDPERS", "PER_NOMPER", $USER_ID);
		}
	mysql_close($lnkdbref); // ferme la connexion à cette base
	return ($tbas_sso);
	}
?>
