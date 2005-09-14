<?
// fonction qui renvoie le tableau des champs de PYA
// en fonction de la sensibilité à la casse du sgbd
function rtb_ultchp() {
if (db_case_sens()) {
	return array(
	"NM_TABLE"=>"NM_TABLE",
	"NM_CHAMP"=>"NM_CHAMP",
	"LIBELLE"=>"LIBELLE",
	"ORDAFF_L"=>"ORDAFF_L",
	"TYPAFF_L"=>"TYPAFF_L",
	"ORDAFF"=>"ORDAFF",
	"TYPEAFF"=>"TYPEAFF",
	"VALEURS"=>"VALEURS",
	"VAL_DEFAUT"=>"VAL_DEFAUT",
	"TT_AVMAJ"=>"TT_AVMAJ",
	"TT_PDTMAJ"=>"TT_PDTMAJ",
	"TT_APRMAJ"=>"TT_APRMAJ",
	"TYP_CHP"=>"TYP_CHP",
	"COMMENT"=>"COMMENT");
	}
else return array(
	"NM_TABLE"=>"nm_table",
	"NM_CHAMP"=>"nm_champ",
	"LIBELLE"=>"libelle",
	"ORDAFF_L"=>"ordaff_l",
	"TYPAFF_L"=>"typaff_l",
	"ORDAFF"=>"ordaff",
	"TYPEAFF"=>"typeaff",
	"VALEURS"=>"valeurs",
	"VAL_DEFAUT"=>"val_defaut",
	"TT_AVMAJ"=>"tt_avmaj",
	"TT_PDTMAJ"=>"tt_pdtmaj",
	"TT_APRMAJ"=>"tt_aprmaj",
	"TYP_CHP"=>"typ_chp",
	"COMMENT"=>"comment");
}
// fonction qui renvoie ou non le caractère de protection
// des strings: avec pgsql si on le met avec des entiers ou des numériques, ça merde
/* en fait ce qui merde, c'est si on met '' pour un champnum
function rt_carpstr($ttc) {
switch($_SESSION['db_type']) {
	case "pgsql":
		switch($ttc) {
			//case "integer":
			case "int5":
				return("");
				break;

			default:
			return("'");
			break;
		}
		
		
	case "mysql":
		default:
		return("'");
		break;
	}
}*/

// fonctions d'accès aux BDD
// fonctions qui renvoie true si la bdd est sensible à la case sur les noms de champs/tables
function db_case_sens() {
switch($_SESSION['db_type']) {
	case "pgsql":
		return(false);
		break;
		
	case "mysql":
		default:
		return(true);
		break;
	}
}

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
		if ($DB!="") {mysql_select_db($DB) or die ("Impossible d'ouvrir la base de données $DB.");}
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
    $ret=pg_query($req) or die("<PRE><U>Requete PgSql invalide</U> : <I>$req</I><BR>$mserridrq<BR><U>Erreur PgSql</U>:     <I>".pg_last_error()."<br/>".pg_result_error()."</I></PRE>".$messret);}  
		else
    $ret=pg_query($lnkid,$req) or die("<PRE><U>Requete pgsql invalide</U> : Id de connection =$lnkid,requête= <I>$req</I><BR>$mserridrq<BR><U>Erreur PgSql</U>:<I>".pg_last_error()."<br/>".pg_result_error()."</I></PRE>".$messret);
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
// fonction qui renvoie un tableau à 2 dim des caractéristiques du ou des champs d'une table

function db_table_defs($NM_TABLE,$NM_CHAMPD="*") {
	switch($_SESSION['db_type']) {
	case "pgsql":
		$table_def= db_query("select $NM_CHAMPD from $NM_TABLE LIMIT 1");
		for ($i=0;$i<db_num_fields($table_def);$i++) {
			$NM_CHAMP=db_field_name($table_def,$i);
			$ret[$NM_CHAMP]['FieldType']=db_field_type($table_def,$i)."(".db_field_size($table_def,$i).")";
			// non encore fait pour postgresql
			$ret[$NM_CHAMP]['FieldValDef']="";
			$ret[$NM_CHAMP]['FieldNullOk']="YES"; // YES ou rien
			$ret[$NM_CHAMP]['FieldKey']=""; // clé=PRI, index=MUL, unique=UNI
			$ret[$NM_CHAMP]['FieldExtra']=""; // auto_increment 
			
			}
		break;
		
	case "mysql":
	default:	
		$table_def = msq("SHOW FIELDS FROM $NM_TABLE");
		while ($row_table_def = db_fetch_array($table_def)) {
			if ($NM_CHAMPD=="*" || $NM_CHAMPD==$row_table_def['Field']) {
				$NM_CHAMP=$row_table_def['Field'];
				$ret[$NM_CHAMP]['FieldType']=$row_table_def['Type'];
				//$ret[$NM_CHAMP]['FieldValDef']=($row_table_def['Default']!="" ? $row_table_def['Default'] : "ø" );
				$ret[$NM_CHAMP]['FieldValDef']=$row_table_def['Default'];
				$ret[$NM_CHAMP]['FieldNullOk']=($row_table_def['Null']=="YES" ? "yes" : "no"); // YES ou rien
				$ret[$NM_CHAMP]['FieldKey']=($row_table_def['Key']!="" ? $row_table_def['Key'] : "ø"); // clé=PRI, index=MUL, unique=UNI
				$ret[$NM_CHAMP]['FieldExtra']=$row_table_def['Extra']; // auto_increment 
			}
		} 
		break;
	}
	return($ret);
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

// renvoie un tableau de la liste des bases
function db_show_bases() {
	switch($_SESSION['db_type']) {
	case "pgsql":
		$sql = "SELECT pdb.datname AS Database, pu.usename AS datowner, pg_encoding_to_char(encoding) AS datencoding, 
					(SELECT description FROM pg_description pd WHERE pdb.oid=pd.objoid) AS datcomment 
					FROM pg_database pdb, pg_user pu
					WHERE pdb.datdba = pu.usesysid 
					AND pdb.datname NOT LIKE 'template%'
					ORDER BY pdb.datname";
		break;
	
	case "mysql":
	default:
		$sql="SHOW DATABASES";
		break;
	}
	$rep=db_query($sql);
	if (db_num_rows($rep) > 0) {
		while ($res=db_fetch_row($rep)) $ret[]=$res[0];
		return($ret);
	} else return (false);
}

function db_show_tables($DB) {
	switch($_SESSION['db_type']) {
	case "pgsql":
		$sql = "select relname from pg_stat_user_tables order by relname";
		break;
	
	case "mysql":
	default:
		$sql="SHOW TABLES FROM $DB";
		break;
	}
	$rep=db_query($sql);
	if (db_num_rows($rep) > 0) {
		while ($res=db_fetch_row($rep)) $ret[]=$res[0];
		return($ret);
	} else return (false);
}

function db_show_fields($table,$dispview=false) {
	switch($_SESSION['db_type']) {
	case "pgsql":
		$sql="
		select c.relname, a.attname, t.typname||'('||a.attlen||')', d.adsrc
		from
		pg_class c , pg_attribute a, pg_type t, pg_attrdef d
		where (c.relkind='r' or c.relkind='v') 
		AND a.attrelid = c.oid
		AND t.oid=a.atttypid
		AND d.adrelid=c.oid
		AND d.adnum=a.attnum";
		break;
	
	case "mysql":
	default:
		$sql="SHOW FIELDS FROM $table";
		break;
	}
	$rep=db_query($sql);
	if (db_num_rows($rep) > 0) {
		while ($res=db_fetch_row($rep)) $ret[]=$res[0];
		return($ret);
	} else return (false);
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

function db_field_size($res,$i) { 
	switch($_SESSION['db_type']) {
	case "pgsql":
		return(pg_field_size($res,$i));
		break;
		
	case "mysql":
	default:
		return(mysql_field_len($res,$i));
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

// renvoie les tables d'un champ lors d'une requête
function db_field_table($res,$i) { // !! attention pas d'equivalent en postgresql !!
	switch($_SESSION['db_type']) {
	case "pgsql": // dur dur à déterminer
	
	// !! cette fonction de fonctionne que s'il n'existe pas 2 champs portant le même nom dans la base !!

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


function db_fetch_array($res) { 
	switch($_SESSION['db_type']) {
	case "pgsql":
		return(pg_fetch_array($res));
		break;
		
	case "mysql":
	default:
		return(mysql_fetch_array($res));
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

function db_fetch_object($res) { 
	switch($_SESSION['db_type']) {
	case "pgsql":
		return(pg_fetch_object($res));
		break;
		
	case "mysql":
	default:
		return(mysql_fetch_object($res));
		break;
	}
}
?>
