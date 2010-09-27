<?
// connection à la bdd
function db_connect($Host,$User,$Pwd,$DB="",$portIP="") {
	$GLOBALS["NmChpComment"] = $_SESSION['db_type'] == "oracle" ? "COMMENT2" : "COMMENT"; // nom de champ COMMENT interdit dans Oracle
	$GLOBALS["CisChpp"] = $_SESSION['db_type'] == "mysql" ? "`" : ""; // caractère d'isolation des champs péraves
	switch($_SESSION['db_type']) {
		case "pgsql":
			if ($DB=="") $DB="template1";
			if ($portIP=="") $portIP=5432;
			$link = pg_connect("host=$Host port=$portIP user=$User password=$Pwd dbname=$DB") or die ("Impossible to connect to PgSql server $Host, user $User, passwd: *****, db=$DB");
			$GLOBALS["NmChpOid"] = "oid";
			$GLOBALS["id_vtb"] = "TBAS_";
			$GLOBALS['sqllenstr0'] = " !='' ";
			break;
			
		case "oracle":
			$link = oci_connect($User,$Pwd, $DB) or die ("Impossible to connect to  oracle server, localhost, user $User, passwd: *****, db=$DB");
			// met le format date par défaut identique à celui de mysql et pgsql pour pas etre emmerdé
			db_query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'",$link);
			$GLOBALS["NmChpOid"] = "ROWID";
			$GLOBALS['sqllenstr0'] = " IS NOT NULL "; // ces blaireaux d'oracle considèrent une chaine vide comme NULL
			$GLOBALS["id_vtb"] = "TBAS_";
			break;
		
		case "mysql":
		default:
			if ($portIP=="") $portIP=3306;
			$link = @mysql_connect($Host,$User,$Pwd);// or die ("Impossible de se connecter au serveur MYSQL $Host avec le user $User, passwd: ***** ");
			if (!$link)  throw new Exception("Impossible de se connecter au serveur MYSQL $Host avec le user $User, passwd: ***** ");
			$GLOBALS['CSpIC'] = "`"; // caract�e pour "isoler" les noms de champs merdiques
			$GLOBALS['NmChpOid'] = "";
			$GLOBALS['sqllenstr0'] = "!=''";
			$GLOBALS["id_vtb"] = "_vtb_";
			if ($DB!="") mysql_select_db($DB) or die ("Impossible d'ouvrir la base de donnees $DB.");
			break;
	}
	return($link);
}

function db_close($lnk) {
	switch($_SESSION['db_type']) {
	case "pgsql":
		pg_close($lnk);
		break;
		
	case "oracle":
		oci_close($lnk);
		break;
	
	case "mysql":
		default:
		mysql_close($lnk);
		break;
	}
}
// execution requete
function db_query($req,$lnkid="",$mserridrq=""){
	//echo $req."\n";
	if ($_SESSION['db_lnkid'] != "" && $lnkid=="") $lnkid = $_SESSION['db_lnkid'];

	$messret="<br/><br/><a href=\"javascript:history.back()\">RETOUR</A> a la page precedente";
	switch($_SESSION['db_type']) {
	case "pgsql":
		if ($lnkid=="") { // connection par la connexion courante
    			$ret=pg_query($req) or die("<PRE><U>Requete PgSql invalide</U> : <I>$req</I><br/>$mserridrq<br/><U>Erreur PgSql</U>:     <I>".pg_last_error()."<br/>".pg_result_error()."</I></PRE>".$messret);
		} else
			$ret=pg_query($lnkid,$req) or die("<PRE><U>Requete pgsql invalide</U> : Id de connection =$lnkid,requ�e= <I>$req</I><br/>$mserridrq<br/><U>Erreur PgSql</U>:<I>".pg_last_error()."<br/>".pg_result_error()."</I></PRE>".$messret);
		break;
		
	case "oracle":
		$ret = oci_parse($lnkid,$req) or die ("parsing req oracle plante : $req") ;
		if (!oci_execute($ret)) { 
			print_r (oci_error($lnkid));
			die ("execute req oracle plante:  $req");
		}
		oci_commit($lnkid) or die ("commit sur $lnkid plante");
		$_SESSION['lastOciRq'][$ret] = $req; // memorise la req pour le db_num_rows qui ne marche pas avec oracle
		break;
	
	case "mysql":
	default:	
		if ($lnkid=="") { // connection par la connexion courante
			$ret=mysql_query($req) or die("<U>Requete mysql invalide</U> : <I>$req</I><br/>$mserridrq<br/><U>Erreur mysql</U>:     <I>".mysql_error()."</I>".$messret);}  
			else
			$ret=mysql_query($req,$lnkid) or die("<U>Requete mysql invalide</U> : Id de connection =$lnkid,requ�e= <I>$req</I><br/>$mserridrq<br/><U>Erreur mysql</U>:<I>".mysql_error()."</I>".$messret);
		break;
	}
	return $ret;
}
// fonction qui trafique une req sql pr lui rajouter une clause LIMIT
/** AVEC ORACLE c'EST LE BORDEL MONSTRE
Voir l'explication du comportement de ROWNUM là http://www.oracle.com/technology/oramag/oracle/06-sep/o56asktom.html
Comme oracle compte les résultats au fur et à mesure, une req "select * from t where ROWNUM > 1;" ne ramene JAMAIS rien

L'astuce (pourrie) trouvée : on appelle la requete sans condition basse et on affiche pas les $FirstEnr premiers résultats

$GLOBALS["NmChpOid"] est le champs masqué qui sert de clé/identifiant d'enregistrement
**/
function addwherefORlimit($sql,$nblimit,$offset=0) {
	switch ($_SESSION['db_type']) {
		case "mysql":
			if (!stristr($sql,"LIMIT")) { // si pas deja une clause de limite
				$limitc =  " LIMIT ".($offset>0 ? "$offset," : "").$nblimit;
				$sql .= $limitc; /// on simplifie, normalement il faudrait insérer la clause limit avant "PROCEDURE,FETCH,FOR"
			}
			break;
		
		case "pgsql":
			if (!stristr($sql,"LIMIT") && !stristr($sql,"OFFSET")) { // si pas deja une clause de limite
				$limitc =  " OFFSET $offset LIMIT $nblimit";
				$sql .= $limitc; /// on simplifie, normalement il faudrait insérer la clause limit avant "PROCEDURE,FETCH,FOR"
			}
			break;
	
		case "oracle" :
			if (!stristr($sql,"ROWNUM")) { // si pas deja une clause de limite

				//$wherelimit = " (ROWNUM >= $FirstEnr AND ROWNUM <= ".($FirstEnr + $nbligpp).") ";
				$wherelimit = " (ROWNUM <= ".($offset + $nblimit).") "; // voire "REMARQUE SUR LES CLAUSES LIMIT au début de ce fichier"

				if (stristr($sql,"where")) { // si deja "where toto" rajoute "where $wherelimit AND toto"
					$sql = str_ireplace('where','where'.$wherelimit.' AND ',$sql);
				} else { // si pas where
					$tbsqlkw = array(" group "," having "," window "," union "," intercept "," expect "," order ");
					// recherche les mots clés ci-dessus et rajoute where $wheerlimit avant
					foreach ($tbsqlkw as $sqlkw) {
					if (stristr($sql,$sqlkw)) {
						return (str_ireplace($sqlkw,' WHERE '.$wherelimit.$sqlkw,$sql));
						}
					}
					// si pas trouvé
					$sql .= ' WHERE '.$wherelimit;
				}
			}
			break;
	}
	return($sql);
}


// compte ligne dans table
function db_count($table,$where="",$lnkid="") {
$where = ($where !="" && !stristr($where,"where")) ? " where $where ": $where;
$res= db_query("select count(*) from $table $where",$lnkid);
$ret = db_fetch_row($res);
return ($ret[0]);
}


// fonction qui effectue une requ�e et renvoie toutes les lignes dans un tableau 
// les lignes sont index�s num�iquement
// les colonnes aussi
function db_qr_compres($req,$lnkid="") {
$res=db_query($req,$lnkid);
$i=0;
	$ret = false;
//if (db_num_rows($res)) { // car oci_num_rows ne fonctionne pas avec Oracle !!
	while ($rep = db_fetch_row($res)) {
		$ret[$i]=$rep;
		$i++;
		}
	return($ret);
}

// fonction qui effectue une requ�e et renvoie toutes les lignes dans un tableau 
// les lignes sont index�s num�iquement
// les colonnes sont indexe par les noms des colonnes
function db_qr_comprass($req,$lnkid="") {
$res=db_query($req,$lnkid);
$i=0;
$ret = false;
//if (db_num_rows($res)) { // car oci_num_rows ne fonctionne pas avec Oracle !!
	while ($rep=db_fetch_assoc($res)) {
		$ret[$i]=$rep;
		$i++;
		}
	return($ret);
//	}
//else return (false);
}

// fonction qui effecture une requete et renvoie la premi�e ligne de r�onse sous forme d'un tableau indic�numeriquement
function db_qr_res($req,$lnkid="") {

$res=db_query($req,$lnkid);
	$ret=db_fetch_row($res);
	if (!$ret) 	{
		$ret[0]="error or no record found";
	}
	return ($ret);
}
// fonction qui effecture une requete et renvoie la premi�e ligne de r�onse sous forme d'un tableau ASSOCIATIF
function db_qr_rass($req,$lnkid="") {
	$res=db_query($req,$lnkid);
	$ret=db_fetch_assoc($res);
	if (!$ret) {
		$ret[0]="error or no record found";
	}
	return ($ret);
}
// fonction qui effecture une requete et renvoie la premi�e ligne de r�onse sous forme d'un tableau ASSOCIATIF
function db_qr_rass2($req,$lnkid="") {
	$res=db_query($req,$lnkid);
	return (db_fetch_assoc($res));
}

// fonction qui transforme un tableau tb[nomchp]=valchp en instruction SQL INSERT 
function tbset2insert($set,$addquotes=false) {
foreach ($set as $chp=>$val) {
	$lchp[] = $chp;
	$vchp[] = $addquotes ? "'$val'" : $val;
	}
return("(".implode(",",$lchp).") VALUES (".implode(",",$vchp).")");
}
// fonction qui transforme un tableau tb[nomchp]=valchp en instruction SQL SET 
function tbset2set($set,$addquotes=false) {
foreach ($set as $chp=>$val) {
	$lchp[] = $chp. ($addquotes ? "='$val'" : "=$val");
	}
return(" ".implode(",",$lchp)." ");
}

// fonction qui génere une requete INSERT INTO xxxxx ON DUPLIACTE KEY UPDATE 
function insertondkupdate($table,$ins,$set="",$addquotes=false) {
        if ($set == "") $set = $ins;
        return("INSERT INTO $table ".tbset2insert($ins,$addquotes)." ON DUPLICATE KEY UPDATE ".tbset2set($set,$addquotes));
}


function db_escape_string($str) {
	switch($_SESSION['db_type']) {
	case "pgsql":
		return(pg_escape_string($str));
		break;
		
	case "oracle":
		return(dblslashes($str));
		break;
	
	case "mysql":
		default:
		return(mysql_real_escape_string($str));
		break;
	}
}

// doubles les cotes pour insert ds Oracle
function dblslashes($str) {
	return(str_replace("'","''",$str));
}

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

// fonction qui renvoie ou non le caractere de protection
// des strings: avec pgsql si on le met avec des entiers ou des numeriques, ca merde

function rt_carpstr($ttc) {
switch($_SESSION['db_type']) {
	case "pgsql":
		switch($ttc) {
			case "integer":
			case "int4":
			//case "bool":
			//case "timestamp":
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
}

// fonctions d'acc� aux BDD
// fonctions qui renvoie true si la bdd est sensible �la case sur les noms de champs/tables
function db_case_sens() {
switch($_SESSION['db_type']) {
	case "pgsql":
		return(false);
		break;
		
	case "mysql":
	case "oracle":
		default:
		return(true);
		break;
	}
}

// fonction qui retourne le type d'un champ
// Utiliser plutot la fonction ShowField qui retourne un tableau avec beaucoup plus d'infos
function mysqft ($NOMC,$NM_TABLE)
{
$resf=db_query("select $NOMC from $CSpIC$NM_TABLE$CSpIC LIMIT 0");
return (db_field_type($resf,0));
}
// fonction qui retourne les flags d'un champ
// Utiliser plutot la fonction ShowField qui retourne un tableau avec beaucoup plus d'infos
// et est compatible avec autre chose que MySql....
function mysqff ($NOMC,$NM_TABLE)
{
$resf=db_query("select $NOMC from $CSpIC$NM_TABLE$CSpIC LIMIT 0");
return (mysql_field_flags($resf,0)); 
}
// fonction qui retourne un tableau de hachage des caracteristiques d'un champ
function ShowField($NOMC,$NM_TABLE) {
$table_def = db_query("SHOW FIELDS FROM $CSpIC$NM_TABLE$CSpIC LIKE '$NOMC'");
return (db_fetch_array($table_def));

}
// fonction qui renvoie un tableau �2 dim des caract�istiques du ou des champs d'une table
function db_table_defs($NM_TABLE,$NM_CHAMPD="*",$owner="") {
	switch($_SESSION['db_type']) {
	case "pgsql":
		$table_def= db_query("select $NM_CHAMPD from $NM_TABLE LIMIT 1");
		for ($i=0;$i<db_num_fields($table_def);$i++) {
			$NM_CHAMP=db_field_name($table_def,$i);
			$ret[$NM_CHAMP]['FieldType']=db_field_type($table_def,$i)."(".db_field_size($table_def,$i).")";
			// non encore fait pour postgresql
			$ret[$NM_CHAMP]['FieldValDef']="";
			$ret[$NM_CHAMP]['FieldNullOk']="YES"; // YES ou rien
			$ret[$NM_CHAMP]['FieldKey']=""; // cl�PRI, index=MUL, unique=UNI
			$ret[$NM_CHAMP]['FieldExtra']=""; // auto_increment 
			
			}
		break;
		
	case "mysql":
		$table_def = db_query("SHOW FIELDS FROM $NM_TABLE");
		while ($row_table_def = db_fetch_array($table_def)) {
			if ($NM_CHAMPD=="*" || $NM_CHAMPD==$row_table_def['Field']) {
				$NM_CHAMP=$row_table_def['Field'];
				$ret[$NM_CHAMP]['FieldType']=$row_table_def['Type'];
				//$ret[$NM_CHAMP]['FieldValDef']=($row_table_def['Default']!="" ? $row_table_def['Default'] : "" );
				$ret[$NM_CHAMP]['FieldValDef']=$row_table_def['Default'];
				$ret[$NM_CHAMP]['FieldNullOk']=(strtoupper($row_table_def['Null'])=="YES" ? "yes" : "no"); // YES ou rien
				$ret[$NM_CHAMP]['FieldKey']=($row_table_def['Key']!="" ? $row_table_def['Key'] : ""); // cl�PRI, index=MUL, unique=UNI
				$ret[$NM_CHAMP]['FieldExtra']=$row_table_def['Extra']; // auto_increment 
			}
		} 
		break;

	case "oracle":
		if ($owner!="") $whown =  " AND TC.OWNER='$owner' ";
		$table_def = db_query("SELECT TC.COLUMN_NAME, TC.DATA_TYPE, TC.DATA_LENGTH, TC.DATA_PRECISION,
        TC.DATA_SCALE, TC.NULLABLE, TC.DATA_DEFAULT
      FROM ALL_TAB_COLUMNS TC
      WHERE TC.TABLE_NAME='$NM_TABLE'
      $whown
      ORDER BY COLUMN_ID");
		while ($row_table_def = db_fetch_array($table_def)) {
			if ($NM_CHAMPD=="*" || $NM_CHAMPD==$row_table_def['COLUMN_NAME']) {
				$NM_CHAMP=$row_table_def['COLUMN_NAME'];
				$ret[$NM_CHAMP]['FieldType']=$row_table_def['DATA_TYPE'];
				$ret[$NM_CHAMP]['FieldValDef']=$row_table_def['DATA_DEFAULT'];
				$ret[$NM_CHAMP]['FieldNullOk']=(strtoupper($row_table_def['NULLABLE'])=="Y" ? "yes" : "no"); // YES ou rien
				/*$ret[$NM_CHAMP]['FieldKey']=($row_table_def['Key']!="" ? $row_table_def['Key'] : ""); // cl�PRI, index=MUL, unique=UNI
				$ret[$NM_CHAMP]['FieldExtra']=$row_table_def['Extra']; // auto_increment

				Pour d infos sur les index faire un $tb = db_qr_comprass("select * from ALL_IND_COLUMNS WHERE TABLE_NAME='CHEVAL'");
					 [INDEX_OWNER] => HARASIRE
					[INDEX_NAME] => PK_CHEVAL
					[TABLE_OWNER] => HARASIRE
					[TABLE_NAME] => CHEVAL
					[COLUMN_NAME] => CHE_NUCHEVAL
					[COLUMN_POSITION] => 1
					[COLUMN_LENGTH] => 8
					[CHAR_LENGTH] => 8
					[DESCEND] => ASC
					*/
			}
		} 
		break;

	default:
		$ret=false;
		break;
		
	}
	return($ret);
}     
      
function db_num_rows($res) { 
	switch($_SESSION['db_type']) {
	case "pgsql":
		return(pg_num_rows($res));
		break;
		
	case "oracle":
	/*int oci_num_rows ( resource stmt ) oci_num_rows retourne le nombre de lignes affectées durant la dernière commande Oracle stmt
.	!!Note!! Cette fonction ne retourne pas le nombre de lignes sélectionnées. Pour les commandes de type SELECT, cette fonction va retourner le nombre de ligne qui ont été lues dans le buffer avec oci_fetch* */
	if ($_SESSION['lastOciRq'][$res] && stristr($_SESSION['lastOciRq'][$res],"select")) {
			$rqs = $_SESSION['lastOciRq'][$res];
			$ifrom = strripos($rqs,"from"); // DERNIERE occurrence de from, pour les select imbriqués
			//die("select count(*) ".substr($rqs,$ifrom));
			$rp = db_qr_compres("select count(*) ".substr($rqs,$ifrom));
			return($rp[0][0]);
		} else
			return(oci_num_rows($res));
		break;
	
	case "mysql":
	default:	
		return(mysql_num_rows($res));
		break;
	}
}

function db_last_id($res="") { 
	switch($_SESSION['db_type']) {
	case "pgsql":
		return(pg_last_oid($res));
		break;
		
	case "mysql":
	default:	
		return(mysql_insert_id());
		break;
	}
}

// renvoie un tableau de la liste des bases / Owners sous Oracle
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
		$sql="SHOW DATABASES";
		break;
	
	case "oracle":
		$sql="SELECT DISTINCT OWNER FROM ALL_TABLES
      ORDER BY OWNER";
		break;
}
	$rep=db_query($sql);
	if (db_num_rows($rep) > 0 || $_SESSION['db_type']=="oracle") {
		while ($res=db_fetch_row($rep)) $ret[]=$res[0];
		return($ret);
	} else return (false);
}

function db_show_tables($DB="",$showSysTables=false) {
	switch($_SESSION['db_type']) {
	case "pgsql":
		$sql = "select relname from pg_stat_user_tables order by relname";
		break;
	
	case "mysql":
		$sql="SHOW TABLES FROM $DB";
		break;

	case "oracle":
		//$DB="";
		//$sql="SELECT table_name FROM all_tables WHERE 1=1 ".($DB!="" ? " AND OWNER=upper('$DB') ": "")." ORDER BY table_name";
		if (!$showSysTables) $whereHST = " where table_name NOT LIKE '%$%' ";
		$sql="SELECT distinct table_name FROM all_tables $whereHST ORDER BY table_name";
		break;
	}

	
	$rep=db_query($sql);
	
	while ($res=db_fetch_row($rep)) {
		 $ret[]=$res[0];
			//if (!strstr($res[0],"$")) $ret[]=$res[0]; // special oracle
			
	}
	if (is_array($ret)) {
		return($ret);
	} else return (false);
}

function db_show_fields($table,$owner="") {
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
		$sql="SHOW FIELDS FROM $table";
		break;
	
	case "oracle":
		if ($owner != "") $whown = " AND OWNER='$owner' ";
		$sql = "SELECT TC.COLUMN_NAME, TC.DATA_TYPE, TC.DATA_LENGTH, TC.DATA_PRECISION,
        TC.DATA_SCALE, TC.NULLABLE, TC.DATA_DEFAULT
      FROM ALL_TAB_COLUMNS TC
      WHERE TC.TABLE_NAME='$table'
      $whown
      ORDER BY COLUMN_ID";
      
      $sql = "SELECT TC.*
      FROM ALL_TAB_COLUMNS TC
      WHERE TC.TABLE_NAME='$table'
      $whown
      ORDER BY COLUMN_ID";
      	return (db_qr_comprass($sql));

		break;
	}
	$rep=db_query($sql);
	if (db_num_rows($rep) > 0 || $_SESSION['db_type']=="oracle") {
		while ($res=db_fetch_row($rep)) $ret[]=$res[0];
		return($ret);
	} else return (false);
}
		
		

function db_num_fields($res) { 
	switch($_SESSION['db_type']) {
	case "pgsql":
		return(pg_num_fields($res));
		break;
	
	case "oracle":
		return (oci_num_fields($res));
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
	
	case "oracle":
		return(oci_field_name($res,($i + 1))); // dans oracle les index de champ commencent à 1
		break;
		
	case "mysql":
	default:
		return(mysql_field_name($res,$i));
		break;
	}
}
// renvoie un tableau contenant le nom de tous les champs
function db_list_fields($res) {
	for($i=0;$i<db_num_fields($res);$i++) {
		$listf[]=db_field_name($res,$i);
	}
	return($listf);
}

function db_field_size($res,$i) { 
	switch($_SESSION['db_type']) {
	case "pgsql":
		return(pg_field_size($res,$i));
		break;
		
	case "oracle":
		return(oci_field_size($res,($i + 1)));// dans oracle les index de champ commencent à 1
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
		
	case "oracle":
		return(oci_field_type($res,($i + 1)));// dans oracle les index de champ commencent à 1
		break;
	
	case "mysql":
	default:
		return(mysql_field_type($res,$i));
		break;
	}
}

// renvoie les tables d'un champ lors d'une requ�e
/// !! sur Oracle et pgsql, cette fonction ne fonctionne que s'il n'existe pas 2 champs portant le m�e nom dans la base !!
/// si ça renvoie la première réponse....
function db_field_table($res,$i) { // !! attention pas d'equivalent en postgresql ni en Oracle !!
	switch($_SESSION['db_type']) {
	case "pgsql": // dur dur �d�erminer
	
		$rep=db_qr_res("select pg_class.relname from pg_class,pg_attribute where pg_class.oid=pg_attribute.attrelid and pg_attribute.attname='".db_field_name($res,$i)."' and pg_class.relkind='r'");
		return($rep[0]);
		break;
	
	case "oracle":
		$rep = db_qr_compres("select TABLE_NAME from ALL_TAB_COLUMNS where COLUMN_NAME='".db_field_name($res,$i)."'");
		if (count($rep==1)) { // retour seulement si univoque..
			return($rep[0][0]);
		} else {
			return("");
		}
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
	
	case "oracle":
		return(oci_fetch_row($res));
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
	
	case "oracle":
		return(oci_fetch_array($res));
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
	
	case "oracle":
		return(oci_fetch_assoc($res));
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
	
	case "oracle":
		return(oci_fetch_object($res));
		break;
		
	case "mysql":
	default:
		return(mysql_fetch_object($res));
		break;
	}
}
?>