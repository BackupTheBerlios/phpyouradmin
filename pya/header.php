<HTML>
<HEAD>
<TITLE><?=($admadm==1 ? "!P!Y!A! ":"PYA ").$title?></TITLE>
<link href="styles.css" rel="styleSheet" type="text/css">
<script src="functions.js" type="text/javascript" language="javascript"></script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</HEAD>
<BODY>
<DIV class="PYA">

<? if (!$ss_parenv[noinfos]) {
   echo '<span class="infoserv">';
   $TEST=(stristr($ListTest,$_SERVER["HTTP_HOST"])? " <BIG>TEST</BIG> " : "");
   echo "Serveur ".$_SERVER["HTTP_HOST"]." $TEST (IP=".gethostbyname($_SERVER["HTTP_HOST"]).")<BR>\n";
   echo '</span>';
}
if ($debug) DispDebug();
?>
