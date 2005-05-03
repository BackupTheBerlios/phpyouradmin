<?
require("infos.php");	
InitPage(true); // initialise en envoyant les balises de début <HTML> etc ...

// Moulinette qui rajoute des , en début et fin de champs pseudo set
$TABLE="ENV_MENUS";
$CHP_SET="MEN_COPROFILS";
$CHP_CLE="MEN_NUMENUS";

$rpmoul=msq("select * from $TABLE");
while ($rw=mysql_fetch_array($rpmoul)) {
	if ($rw[$CHP_SET]!='' || $rw[$CHP_SET]!=',' || $rw[$CHP_SET]!=',,') {
		if (substr($rw[$CHP_SET],0,1)!=",") $rw[$CHP_SET]=",".$rw[$CHP_SET];
		if (substr($rw[$CHP_SET],-1,1)!=",") $rw[$CHP_SET].=",";
		msq("update $TABLE set $CHP_SET='$rw[$CHP_SET]' where $CHP_CLE='$rw[$CHP_CLE]'"); 
	}
?>
</body>
</html>



