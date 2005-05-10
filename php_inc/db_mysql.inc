<?
// fonctions d'accès base MySql

function db_connect($Host,$User,$Pwd,$DB="") {
	$link=mysql_connect($Host,$User,$Pwd) or die ("Impossible de se connecter au serveur MYSQL $Host avec le user $User, passwd: ***** ");
	if ($DB!="") mysql_select_db($DB) or die ("Impossible d'ouvrir la base de données $DB.");
	return($link);
}

function db_close($lnk) {
	mysql_close($lnk);
}

function db_query($req,$lnkid="",$mserridrq=""){
  $messret="<BR><BR><a href=\"javascript:history.back()\">RETOUR</A> à la page précédente";
  if ($lnkid=="") { // connection par la connexion courante
    $ret=mysql_query($req) or die("<U>Requete mysql invalide</U> : <I>$req</I><BR>$mserridrq<BR><U>Erreur mysql</U>:     <I>".mysql_error()."</I>".$messret);}  
		else
    $ret=mysql_query($req,$lnkid) or die("<U>Requete mysql invalide</U> : Id de connection =$lnkid,requête= <I>$req</I><BR>$mserridrq<BR><U>Erreur mysql</U>:<I>".mysql_error()."</I>".$messret);
	return $ret;
}

function db_num_rows($res) { 
	return(mysql_num_rows($res));
}

function db_num_fields($res) { 
	return(mysql_num_fields($res));
}

function db_field_name($res,$i) { 
	return(mysql_field_name($res,$i));
}

function db_field_type($res,$i) { 
	return(mysql_field_type($res,$i));
}

function db_field_table($res,$i) { // attention pas d'equivalent en postgresq
	return(mysql_field_table($res,$i));
}

function db_fetch_rows($res) { 
	return(mysql_fetch_rows($res));
}
?>