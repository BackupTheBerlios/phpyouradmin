<? require("infos.php");
require "lang_".$def_lang.".inc";
unset($_SESSION);
session_start();
session_destroy();
unset($_SESSION);
/*
echovar("_SESSION");
echovar("_GLOBALS");
echovar("_GET");
echovar("_POST");
echovar("ss_parenv");
print_r("ss_parenv");
*/
$title="phpYourAdmin";
include ("header.php"); ?>
<H1><?=trad("IND_title")?></H1>
<blockquote>
<form action="./LIST_BASES.php" method="post">
<?=trad("IND_txtacc")?>
<H4><? echo trad("IND_bddtype")."<br/>";
DispLD($tb_dbtype,"lc_parenv[db_type]")?></H4>
<br><br>
<H4><?=trad("IND_bdduser")?><br>
<input type="text" name="lc_parenv[MySqlUser]" value="<?=(isset($ss_parenv[MySqlUsero]) ? $ss_parenv[MySqlUsero] : $DBUser)?>"></H4>
<H4><?=trad("IND_bddpasswd")?> <br>
<input type="password" name="lc_parenv[MySqlPasswd]" value="<?=(isset($ss_parenv[MySqlPasswd]) ? $ss_parenv[MySqlPasswd] : $DBPass)?>"></H4>
<H4><?=trad("IND_pyauser")?><br>
<input type="text" name="lc_CO_USMAJ" value="<?=$$VarNomUserMAJ;?>"></H4>
<H4><? echo trad("IND_choilang")."<br/>";
DispLD($tb_langs,"lc_parenv[lang]")?></H4>
<br><br>
<input class="fxbutton" type="submit" value="<?=trad("BT_valider")?>">
</form>
</blockquote>
<? include ("footer.php"); ?>
</BODY>
</html>

