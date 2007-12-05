<?
@ini_set("default_charset",($_SESSION["ss_parenv"]["encoding"]!="" ? $_SESSION["ss_parenv"]["encoding"] : "utf-8"));
header('Content-type: text/html; charset='.($_SESSION["ss_parenv"]["encoding"]!="" ? $_SESSION["ss_parenv"]["encoding"] : "utf-8")); 
?>
<HTML>
<HEAD>
<TITLE><?=($admadm==1 ? "!P!Y!A! ":"PYA ").$title?></TITLE>
<link href="styles.css" rel="styleSheet" type="text/css"><?// =($ss_parenv[css_ssf] ? $ss_parenv[css_ssf] : "styles.css")?> 
<link href="stylesCalendar.css.php" rel="stylesheet" type="text/css">
<script src="functions.js" type="text/javascript" language="javascript"></script>
<script type="text/javascript" src="CalendarPopup.js.php"></script>
<meta http-equiv="Content-Type" content="text/html; charset=<?=($_SESSION["ss_parenv"]["encoding"]!="" ? $_SESSION["ss_parenv"]["encoding"] : "utf-8")?>">
</HEAD>
<BODY>
<DIV ID="popupcalend" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;z-index : 0;"></DIV>
<SCRIPT LANGUAGE="JavaScript" ID="jscalStart">
var popcal = new CalendarPopup("popupcalend");
</SCRIPT>
<DIV class="PYA">

<? if (!$ss_parenv[noinfos]) {
   echo '<span class="infoserv">';
   $TEST=(stristr($ListTest,$_SERVER["HTTP_HOST"])? " <BIG>TEST</BIG> " : "");
   echo "Serveur ".$_SERVER["HTTP_HOST"]." $TEST (IP=".gethostbyname($_SERVER["HTTP_HOST"]).")<BR>\n";
   echo '</span>';
}
if ($debug) DispDebug();
?>
