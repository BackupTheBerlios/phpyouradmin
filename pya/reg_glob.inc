<? // simulation register_globals=On
if (is_array($_REQUEST)) {
	foreach( $_REQUEST as $a => $b)
	{
	if ($a!="PHPSESSID" && strlen($$a)<255) // strlen pour éviter les attaques
		{
	//	global $a;
		$$a = $b;
		}
	}
}

if (is_array($_SESSION)) {
	foreach( $_SESSION as $a => $b)
	{
	$$a = $b;
	}
}
?>