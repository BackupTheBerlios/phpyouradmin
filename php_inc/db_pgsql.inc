<?
// fonctions d'accès base PostGresql

function db_connect($Host,$User,$Pwd,$DB="template1") {
	$link=pg_connect($Host,$User,$Pwd,$DB) or die ("Impossible to connect to PgSql server $Host, user $User, passwd: *****, db=$DB");
	return($link);
}

function db_close($lnk) {
	pg_close($lnk);
}

function db_query($req,$lnkid="",$mserridrq=""){
  $messret="<BR><BR><a href=\"javascript:history.back()\">RETOUR</A> à la page précédente";
  if ($lnkid=="") { // connection par la connexion courante
    $ret=pg_query($req) or die("<U>Requete PgSql invalide</U> : <I>$req</I><BR>$mserridrq<BR><U>Erreur mysql</U>:     <I>".pg_last_error()."<br/>".pg_result_error()."</I>".$messret);}  
		else
    $ret=pg_query($lnkid,$req) or die("<U>Requete mysql invalide</U> : Id de connection =$lnkid,requête= <I>$req</I><BR>$mserridrq<BR><U>Erreur mysql</U>:<I>".pg_last_error()."<br/>".pg_result_error()."</I>".$messret);
	return $ret;
}

function db_num_rows($res) { 
	return(pg_num_rows($res));
}

function db_num_fields($res) { 
	return(pg_num_fields($res));
}

function db_field_name($res,$i) { 
	return(pg_field_name($res,$i));
}

function db_field_type($res,$i) { 
	return(pg_field_type($res,$i));
}

function db_field_table($res,$i) { // attention pas d'equivalent en postgresq

	//return(pg_field_table($res,$i));
}

function db_fetch_rows($res) { 
	return(pg_fetch_rows($res));
}
?>