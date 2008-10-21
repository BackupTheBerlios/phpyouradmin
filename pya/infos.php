<? //infos techniques PHPYOURADMIN
$debug=false;
$dbgn2=false;
include ("infos_conn_MySql.inc");
$infosparsed=true; // pour savoir si ce fichier a ��pars�//include("fonctions.php"); ceci est appel�en bas ...
$VerNum="0.895";
//$MaxFSizeDef=1000000; //taille max des fichiers t��harg� // mis dans fonctions.php
$jsppwd="toto"; // mot de passe pour acc�er aux pages prot��s
// ceci est sp�ifi�dans le fichier fonctions.php
//$CSpIC="`";
//$CSpIC=""; // caract�e pour "isoler" les noms de champs merdiques
// ne fonctionne qu'avec des versions r�entes de MySql
include_once("fonctions.php");
$ldajaxdynsize=10; //taille en nbre d'éléments des liste de sélection ajax dynamiques des popl
$ldajaxdynwidth=250; //idem largeur en px
$def_lang="fr";
$tb_langs=array("fr"=>"#SEL#francais","en"=>"anglais");
$tb_noLangs=array("fr"=>"0","en"=>"1"); // PYA se sert d'un n°

$tb_encodes=array("utf-8"=>"#SEL#utf8","iso-8859-1"=>"iso-8859-1");


$tb_dbtype=array("mysql"=>"#SEL#mysql","pgsql"=>"postGresql");

$admadm_color="#FF9900"; // couleur pour l'administration
// A POSITIONNER LORS DE LA CREATION (lancement de CREATE_DESC_TABLES)
$dtmaj="DTMAJ"; // morceau de nom de champ, tel qu'�la creation de la definition des tables, le traitement de ce champ est affect��une mise a jour automatique en fonction de la date
//$dtmaj="tstamp";
//$usmaj="USMAJ";// idem, avec nom user effectuant la mise �jour
$usmaj="COOPE"; 
$dtcrea="DTCREA"; // idem, date de creation
//$dtcrea="crdate";
$uscrea="USCREA";// idem, avec nom user effectuant la creation
//$uscrea="cruser_id";

// table et champ personne pour affichage correct de l'usmaj
$chpperlie="PERSONNE,PER_LCIDLDAP,PER_LLPRENOMPERS,PER_LLNOMPERS, -id=!PER_LCIDLDAP, no=!PER_NUPERS";
//$chpperlie="fe_users,uid,first_name, !lastname";
// ceci ci-dessous utilis�tout le temps
// A POSITIONNER TOUT LE TEMPS
$VarNomUserMAJ="CO_USMAJ"; //nom de la variable contenant le code user
// d�inition du tableau de hachage des adresses de retour de chaque page par d�aut
$def_adrr["LIST_BASES.php"]="index.php";
$def_adrr["LIST_TABLES.php"]="LIST_BASES.php";
$def_adrr["req_table.php"]="LIST_TABLES.php";
$def_adrr["list_table.php"]="LIST_TABLES.php";
$def_adrr["edit_table.php"]="list_table.php";


// fonction qui renvoie l'adresse de retour d'une page
// si la variable de session correspondante est d�inie
// la renvoie, sinon renvoie celle par d�aut.
function ret_adrr($adr,$echImg=false,$lb_butt="BT_retour") {
global $def_adrr;
 	//print_r($_SESSION['ss_adrr']);
       // l'adresse vient de $PHP_SELF qui peut contenir le chemin : on l'enl�e
       if (strrchr($adr,"/")) $adr=substr(strrchr($adr,"/"),1);
       if (strrchr($adr,"\\")) $adr=substr(strrchr($adr,"\\"),1);
       if (!$echImg) { // 1=true ie si on ne doit pas renvoyer le lien mais seulement une URL
          return (isset($_SESSION['ss_adrr'][$adr]) ? $_SESSION['ss_adrr'][$adr] : $def_adrr[$adr]);}
       else {
            if ($_SESSION['ss_adrr'][$adr]!="0") return ("<a class=\"fxbutton\" href=\"".(isset($_SESSION['ss_adrr'][$adr]) ? $_SESSION['ss_adrr'][$adr] : $def_adrr[$adr])."\">".trad($lb_butt)."</A>");
            }
}

//fonction qui connecte �la base de donn�s
function DBconnect($DB=false) {
include ("globvar.inc");
if ($DB) $DBName=$DB;
if (isset($ss_parenv['MySqlDB'])) $DBName=$ss_parenv['MySqlDB'];
if (isset($ss_parenv['MySqlUser'])) $DBUser=$ss_parenv['MySqlUser'];
if (isset($ss_parenv['MySqlPasswd'])) $DBPass=$ss_parenv['MySqlPasswd'];
// connecton au serveur
if ($debug) echo ("Connection au serveur $DBHost (user: $DBUser, passwd: $DBPass), base $DBName");
$ret=db_connect($DBHost,$DBUser, $DBPass,$DBName);

if ($_SESSION["ss_parenv"]["encoding"] == "utf-8" && $_SESSION['db_type']=="mysql") {
	@mysql_query("SET NAMES 'utf8'");
	@mysql_query("SET CHARACTER SET utf8");
}

if (!db_case_sens()) { // si base de donn�s insensible �la casse sur les noms de tables et champ
	$TBDname=strtolower($TBDname);
	$NM_TABLE=strtolower($NM_TABLE);
	}
// selection de la base (sauf si $seldb=false
return($ret);
}

// fonction qui d�arre la session, et qui regarde si certainses variables sont OK
function sess_start($verifUserisConnected=true) {
session_start();
include ("globvar.inc");

// simulation register_globals=On
foreach( $_REQUEST as $a => $b)
{
if ($a!="PHPSESSID")
	{
//	global $a;
	$$a = $b;
	}
}

foreach( $_SESSION as $a => $b)
{
$$a = $b;
}
if ($lc_clean==1)
  {
  $_SESSION=array();
  //session_destroy(); // d�ruit la session
  //session_unset();
  } //d�ruit toutes les variables de session couramment enregistr�s

if (isset($lc_adrr)) { // tableau des adresses de retour de page
   // l'adresse de retour au sommaire g��ale de la liste des forc�ment la meme que celle de la grille de requete
   if (isset($lc_adrr["req_table.php"])) $lc_adrr["list_table.php"]=$lc_adrr["req_table.php"];
   $_SESSION['ss_adrr']=$lc_adrr;
}

if (isset($lc_parenv)) { // tableau des param�res d'environnement pass� en get ou formulaire
   foreach ($lc_parenv as $key=>$val) {
      // ne maj que ceux qui sont pass�
      $ss_parenv[$key]=$val;
      //echovar("ss_parenv");
      if (($key=="ro") && $val!="true") unset($ss_parenv[$key]);
      }
   $_SESSION["ss_parenv"]=$ss_parenv; //session_register("ss_parenv");
}

if (!isset($ss_parenv['lang'])) {
	$ss_parenv['lang']=$def_lang;
	$_SESSION["ss_parenv"]["lang"] = $ss_parenv['lang'];
	//session_register("ss_parenv[lang]");
	}
$_SESSION["NoLang"] = $tb_noLangs[$ss_parenv['lang']];

require "lang_".$ss_parenv['lang'].".inc";

if (!isset($ss_parenv['db_type'])) {
	$ss_parenv['db_type']="mysql";
	$_SESSION["ss_parenv"]["db_type"]=$ss_parenv[db_type];
	//session_register("ss_parenv[lang]");
	}
$_SESSION['db_type']=$ss_parenv['db_type'];

if ($lc_CO_USMAJ!=""){
  $$VarNomUserMAJ=$lc_CO_USMAJ;
  $_SESSION[$VarNomUserMAJ]=$$VarNomUserMAJ; //session_register($VarNomUserMAJ);
  }
if ($$VarNomUserMAJ == "" || $_SESSION[$VarNomUserMAJ] =="") {
	$$VarNomUserMAJ = $ss_parenv['MySqlUser'];
	$_SESSION[$VarNomUserMAJ] =  $ss_parenv['MySqlUser'];
}

if ($lc_DBName!=""){
  $DBName=$lc_DBName;
  $_SESSION["DBName"]=$DBName; //session_register("DBName");
  }

if ($lc_where_sup!="" || $lc_NM_TABLE!="") {
  $where_sup=$lc_where_sup;
  $NM_TABLE=$lc_NM_TABLE;
  $_SESSION["NM_TABLE"]=$NM_TABLE; //session_register("where_sup", "NM_TABLE");
  }

if ($verifUserisConnected && $_SESSION[$VarNomUserMAJ] =="") { // verifie que util d�lar�  // en fait vérifie plus
	header ("location: ./index.php?lc_clean=1"); // sinon renvoie en page d'accueil et d�ruit la session
  }
}

// fonction qui transforme les *cl�* d'un tableau qui ne sont pas en majuscule en majuscule
function case_kup($tb) {
	foreach ($tb as $cle=>$val) {
		$ret[strtoupper($cle)]=$val;
	}
	return($ret);
}
// fonction d'affichage de d�ogage
function DispDebug() {
  global $where_sup, $tbchptri,$tbordtri,$FirstEnr,$tbAfC;
  echo "<B>! MODE DEBOGGAGE ! - </b> <a href=\"./phpinfo.php\" target=\"blank\">phpinfo</a><BR>";
  echovar ("HTTP_REFERER");
  echovar ("where_sup");
  echovar ("lc_nbligpp");
  echovar("tbchptri");
  echovar("tbordtri");
  echovar("FirstEnr");
  echovar("tbAfC","yes");
  echovar("ss_parenv","yes");
  echovar("ss_adrr","yes");
  echo "<PRE><u>Chaine de session:</u> ".session_encode()."</PRE>";
  }
?>
