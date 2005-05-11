<? // simulation register_globals=On
foreach( $_REQUEST as $a => $b)
{
if ($a!="PHPSESSID" && strlen($$a)<255) // strlen pour éviter les attaques
	{
//	global $a;
	$$a = $b;
	}
}

foreach( $_SESSION as $a => $b)
{
$$a = $b;
}

?>