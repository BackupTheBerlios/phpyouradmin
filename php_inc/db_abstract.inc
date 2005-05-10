<?
// fonctions d'accès aux BDD

function db_connect($Host,$User,$Pwd,$DB="",$portIP="") {
	switch($_SESSION['db_type']) {
	case "pgsql":
		if ($DB=="") $DB="template1";
		if ($portIP=="") $portIP=5432;
		$link=pg_connect("host=$Host port=$portIP user=$User password=$Pwd dbname=$DB") or die ("Impossible to connect to PgSql server $Host, user $User, passwd: *****, db=$DB");
		break;
		
	case "mysql":
	default:
		if ($portIP=="") $portIP=3306;
		$link=mysql_connect($Host,$User,$Pwd) or die ("Impossible de se connecter au serveur MYSQL $Host avec le user $User, passwd: ***** ");
		if ($DB!="") mysql_select_db($DB) or die ("Impossible d'ouvrir la base de données $DB.");
		break;
	}
	return($link);
}

function db_close($lnk) {
	switch($_SESSION['db_type']) {
	case "pgsql":
		pg_close($lnk);
		break;
		
	case "mysql":
		default:
		mysql_close($lnk);
		break;
	}
}

function db_query($req,$lnkid="",$mserridrq=""){
	$messret="<BR><BR><a href=\"javascript:history.back()\">RETOUR</A> à la page précédente";
	switch($_SESSION['db_type']) {
	case "pgsql":
	  if ($lnkid=="") { // connection par la connexion courante
    $ret=pg_query($req) or die("<U>Requete PgSql invalide</U> : <I>$req</I><BR>$mserridrq<BR><U>Erreur mysql</U>:     <I>".pg_last_error()."<br/>".pg_result_error()."</I>".$messret);}  
		else
    $ret=pg_query($lnkid,$req) or die("<U>Requete mysql invalide</U> : Id de connection =$lnkid,requête= <I>$req</I><BR>$mserridrq<BR><U>Erreur mysql</U>:<I>".pg_last_error()."<br/>".pg_result_error()."</I>".$messret);
		break;
		
	case "mysql":
	default:	
		if ($lnkid=="") { // connection par la connexion courante
			$ret=mysql_query($req) or die("<U>Requete mysql invalide</U> : <I>$req</I><BR>$mserridrq<BR><U>Erreur mysql</U>:     <I>".mysql_error()."</I>".$messret);}  
			else
			$ret=mysql_query($req,$lnkid) or die("<U>Requete mysql invalide</U> : Id de connection =$lnkid,requête= <I>$req</I><BR>$mserridrq<BR><U>Erreur mysql</U>:<I>".mysql_error()."</I>".$messret);
		break;
	}
	return $ret;
}

function db_num_rows($res) { 
	switch($_SESSION['db_type']) {
	case "pgsql":
		return(pg_num_rows($res));
		break;
		
	case "mysql":
	default:	
		return(mysql_num_rows($res));
		break;
	}
}

function db_num_fields($res) { 
	switch($_SESSION['db_type']) {
	case "pgsql":
		return(pg_num_fields($res));
		break;
		
	case "mysql":
	default:
		return(mysql_num_fields($res));
		break;
	}
}

function db_field_name($res,$i) { 
	switch($_SESSION['db_type']) {
	case "pgsql":
		return(pg_field_name($res,$i));
		break;
		
	case "mysql":
	default:
		return(mysql_field_name($res,$i));
		break;
	}
}

function db_field_type($res,$i) { 
	switch($_SESSION['db_type']) {
	case "pgsql":
		return(pg_field_type($res,$i));
		break;
		
	case "mysql":
	default:
		return(mysql_field_type($res,$i));
		break;
	}
}

function db_field_table($res,$i) { // !! attention pas d'equivalent en postgresql !!
	switch($_SESSION['db_type']) {
	case "pgsql": // dur dur à déterminer
	
	// !! cette fonction de fonctionne que s'il n'existe pas 2 champs portant le même nom dans la table !!

		$rep=db_qr_res("select pg_class.relname from pg_class,pg_attribute where pg_class.oid=pg_attribute.attrelid and pg_attribute.attname='".db_field_name($res,$i)."' and pg_class.relkind='r'");
		return($rep[0]);
		break;
		
	case "mysql":
	default:
		return(mysql_field_table($res,$i));
		break;
	}
}

function db_fetch_row($res) { 
	switch($_SESSION['db_type']) {
	case "pgsql":
		return(pg_fetch_row($res));
		break;
		
	case "mysql":
	default:
		return(mysql_fetch_row($res));
		break;
	}
}
function db_fetch_assoc($res) { 
	switch($_SESSION['db_type']) {
	case "pgsql":
		return(pg_fetch_assoc($res));
		break;
		
	case "mysql":
	default:
		return(mysql_fetch_assoc($res));
		break;
	}
}
?>