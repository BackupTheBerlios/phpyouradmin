<html>
<head>
<title>Selection</title>
</head>
<body>
Test: Valeurs=<?=$_REQUEST['Valeurs']?><br>
Valeur Champ=<?=$_REQUEST['ValChp']?><br>
<?
require("infos.php");
sess_start();
DBconnect();
//include_once("reg_glob.inc");
$tbv2c=ttChpLink($_REQUEST['Valeurs']);
DispLD($tbv2c,"LDV2C","yes");
?>
</body>
</html>
