<? require("infos.php");
sess_start();
unset($_SESSION[where_sup]);
$title=trad("LB_title"). $_SERVER["HTTP_HOST"] ."( IP=".gethostbyname($_SERVER["HTTP_HOST"]).")";
include ("header.php");
DBconnect(false);
//mysql_connect($DBHost,$DBUser, $DBPass) or die ("Impossible de se connecter au serveur $DBHost (user: $DBUser, passwd: $DBPass)");

?>

<H1><?=$title?></H1>
<?=trad("LB_txtacc")?>
<H2><?=trad("LB_baselist");?></H2>
<UL>
<? $resb=msq("SHOW DATABASES");
// liste toutes les bases
while ($tresb=mysql_fetch_row($resb)) {
  $admok=false;
  $rest = msq("SHOW TABLES from ".addslashes($tresb[0]));
  while ($trest=mysql_fetch_row($rest))
    {
    // regarde si la table d'admin existe dans la base
    if (strtolower ($trest[0])==strtolower($TBDname)) $admok=true;
    }
  // n'affiche le lien pour édition que si la table d'admin existe dans la base
  if ($admok) echo "<LI> <A HREF=\"LIST_TABLES.php?lc_DBName=$tresb[0]&cfLB=vrai\">$tresb[0]</A></LI>";
  }
?>
</UL>
</span>
    <br>
      <a href="<?=ret_adrr($_SERVER["PHP_SELF"])?>" class="fxbutton"><?=trad("BT_retour")?></A>
      <br><br>

<br><br>
<? if ($ss_parenv[blair]!="1"  && $ss_parenv[ro]!=true) {
JSprotectlnk();?>
<small><?=trad("BT_click")?> <a class="fxbutton" href="#" onclick="protectlnk('CREATE_DESC_TABLES.php','<?=$jsppwd?>','<?=trad(com_enter_password)?>');"><?=trad("BT_here")?></a> <?=trad("LB_createDT")?></small>
<? }
include ("footer.php"); ?>
