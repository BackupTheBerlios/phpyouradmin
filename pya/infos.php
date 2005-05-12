<? //infos techniques PHPYOURADMIN
$debug=false;
$dbgn2=false;
include ("infos_conn_MySql.inc");
$infosparsed=true; // pour savoir si ce fichier a été parsé
//include("fonctions.php"); ceci est appelé en bas ...
$VerNum="0.892";
$MaxFSize=1000000; //taille max des fichiers téléchargés
$jsppwd="toto"; // mot de passe pour accéder aux pages protégées
// ceci est spécifié dans le fichier fonctions.php
//$CSpIC="`";
//$CSpIC=""; // caractère pour "isoler" les noms de champs merdiques
// ne fonctionne qu'avec des versions récentes de MySql
include_once("fonctions.php");

$def_lang="fr";
$tb_langs=array("fr"=>"#SEL#francais","en"=>"anglais");

$tb_dbtype=array("mysql"=>"#SEL#mysql","pgsql"=>"postGresql");

$admadm_color="#FF9900"; // couleur pour l'administration
// A POSITIONNER LORS DE LA CREATION (lancement de CREATE_DESC_TABLES)
$dtmaj="dtmaj"; // morceau de nom de champ, tel qu'à la creation de la definition des tables, le traitement de ce champ est affecté à une mise a jour automatique en fonction de la date
$dtcrea="dtcrea"; // idem, date de creation
$usmaj="coope";// idem, avec nom user effectuant la mise à jour
// table et champ personne pour affichage correct de l'usmaj
$chpperlie="INTRADRH;PERSONNE,PER_NUPERS,PER_LMTITREPER, !PER_LLPRENOMPERS, !@PER_LLNOMPERS";
// ceci ci-dessous utilisé tout le temps
// A POSITIONNER TOUT LE TEMPS
$VarNomUserMAJ="CO_USMAJ"; //nom de la variable contenant le code user
// définition du tableau de hachage des adresses de retour de chaque page par défaut
$def_adrr["LIST_BASES.php"]="index.php";
$def_adrr["LIST_TABLES.php"]="LIST_BASES.php";
$def_adrr["req_table.php"]="LIST_TABLES.php";
$def_adrr["list_table.php"]="LIST_TABLES.php";
$def_adrr["edit_table.php"]="list_table.php";


// fonction qui renvoie l'adresse de retour d'une page
// si la variable de session correspondante est définie
// la renvoie, sinon renvoie celle par défaut.
function ret_adrr($adr,$echImg=false,$lb_butt="BT_retour") {
global $ss_adrr,$def_adrr;
       // l'adresse vient de $PHP_SELF qui peut contenir le chemin : on l'enlève
       if (strrchr($adr,"/")) $adr=substr(strrchr($adr,"/"),1);
       if (strrchr($adr,"\\")) $adr=substr(strrchr($adr,"\\"),1);
       if (!$echImg) { // 1=true ie si on ne doit pas renvoyer le lien mais seulement une URL
          return (isset($ss_adrr[$adr]) ? $ss_adrr[$adr] : $def_adrr[$adr]);}
       else {
            if ($ss_adrr[$adr]!="0") return ("<a class=\"fxbutton\" href=\"".(isset($ss_adrr[$adr]) ? $ss_adrr[$adr] : $def_adrr[$adr])."\">".trad($lb_butt)."</A>");
            }
}

//fonction qui connecte à la base de données
function DBconnect($seldb=true) {
include ("globvar.inc");
if (isset($ss_parenv[MySqlUser])) $DBUser=$ss_parenv[MySqlUser];
if (isset($ss_parenv[MySqlPasswd])) $DBPass=$ss_parenv[MySqlPasswd];
// connecton au serveur
if ($debug) echo ("Connection au serveur $DBHost (user: $DBUser, passwd: $DBPass), base $DBName");
db_connect($DBHost,$DBUser, $DBPass,$DBName) or die ($mesdb);
// selection de la base (sauf si $seldb=false
}

// fonction qui démarre la session, et qui regarde si certainses variables sont OK
function sess_start() {
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
  //session_destroy(); // détruit la session
  //session_unset();
  } //détruit toutes les variables de session couramment enregistrées

if (isset($lc_adrr)) { // tableau des adresses de retour de page
   // l'adresse de retour au sommaire générale de la liste des forcémment la meme que celle de la grille de requete
   if (isset($lc_adrr["req_table.php"])) $lc_adrr["list_table.php"]=$lc_adrr["req_table.php"];
   $ss_adrr=$lc_adrr;
   $_SESSION["ss_adrr"]=$ss_addr; //session_register("ss_adrr");
}

if (isset($lc_parenv)) { // tableau des paramètres d'environnement passés en get ou formulaire
   foreach ($lc_parenv as $key=>$val) {
      // ne maj que ceux qui sont passés
      $ss_parenv[$key]=$val;
      echovar("ss_parenv");
      }
   $_SESSION["ss_parenv"]=$ss_parenv; //session_register("ss_parenv");
}

if (!isset($ss_parenv[lang])) {
	$ss_parenv[lang]=$def_lang;
	$_SESSION["ss_parenv"]["lang"]=$ss_parenv[lang];
	//session_register("ss_parenv[lang]");
	}

require "lang_".$ss_parenv[lang].".inc";

if (!isset($ss_parenv[db_type])) {
	$ss_parenv[db_type]="mysql";
	$_SESSION["ss_parenv"]["db_type"]=$ss_parenv[db_type];
	//session_register("ss_parenv[lang]");
	}
$_SESSION[db_type]=$ss_parenv[db_type];

if ($lc_CO_USMAJ!=""){
  $$VarNomUserMAJ=$lc_CO_USMAJ;
  $_SESSION[$VarNomUserMAJ]=$$VarNomUserMAJ; //session_register($VarNomUserMAJ);
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

if ($$VarNomUserMAJ=="") { // verifie que util déclaré
  header ("location: ./index.php?lc_clean=1"); // sinon renvoie en page d'accueil et détruit la session
  }
}

// fonction d'affichage de débogage
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
