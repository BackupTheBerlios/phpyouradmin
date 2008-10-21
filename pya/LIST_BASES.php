<? 
require("infos.php");
sess_start();
$_SESSION['where_sup']="";
$_SESSION['NM_TABLE']="";
$_SESSION['DBName']="";
include_once("reg_glob.inc");
$title=trad("LB_title"). $_SERVER["HTTP_HOST"] ."( IP=".gethostbyname($_SERVER["HTTP_HOST"]).")";
$lnkbdd=DBconnect(false);
//mysql_connect($DBHost,$DBUser, $DBPass) or die ("Impossible de se connecter au serveur $DBHost (user: $DBUser, passwd: $DBPass)");
?>
<? $resb=db_show_bases();
// liste toutes les bases
foreach ($resb as $tresb) {
	$DBName=$tresb;
	DBconnect($DBName);
	$dbg=db_show_tables("`".$tresb."`");
	//echovar ("dbg");
  	$admok=($dbg && in_array($TBDname,$dbg));
  // n'affiche le lien pour �ition que si la table d'admin existe dans la base
  if ($admok) {
  	$theecho.= "<LI> <A HREF=\"LIST_TABLES.php?lc_DBName=$tresb&cfLB=vrai\">$tresb</A></LI>";
  	$nbbases ++;
  }
}

if ($nbbases ==0) $theecho = "Aucune base actuellement paramétrée pour PhpYourAdmin; veuillez lancer l'utilitaire de configuration";
if ($nbbases != 1) { // si pas une seule base affiche écran
	include ("header.php"); ?>
	<H1><?=$title?></H1>
	<?=trad("LB_txtacc")?>
	<H2><?=trad("LB_baselist");?></H2>
	<UL>
	</UL>
	</span>
	<br>
	<a href="<?=ret_adrr($_SERVER["PHP_SELF"])?>" class="fxbutton"><?=trad("BT_retour")?></A>
	<br><br>
	<?= $theecho?>
	<br><br>
	<? if ($ss_parenv[blair]!="1"  && $ss_parenv[ro]!=true) {
	JSprotectlnk();?>
	<small><?=trad("BT_click")?> <a class="fxbutton" href="#" onclick="protectlnk('CREATE_DESC_TABLES.php','<?=$jsppwd?>','<?=trad(com_enter_password)?>');"><?=trad("BT_here")?></a> <?=trad("LB_createDT")?></small>
	<? }
	include ("footer.php"); 
} else {
	header ("location:LIST_TABLES.php?lc_DBName=$tresb&cfLB=vrai");
}
?>
