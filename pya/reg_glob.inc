<? // simulation register_globals=On
if (is_array($_SESSION)) {
	foreach( $_SESSION as $a => $b)
	{
	$$a = $b;
//		echo $$a."=".$a."|";
	}
}
if (is_array($_REQUEST)) {
	foreach( $_REQUEST as $a => $b)
	{
	if ($a!="PHPSESSID" && strlen($$a)<10024) // strlen pour �iter les attaques
		{
	//	global $a;
		$$a = $b;
//		echo $$a."=".$a."|";
		}
	}
}
?>