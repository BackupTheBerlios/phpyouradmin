<? // simulation register_globals=On
foreach( $_REQUEST as $a => $b)
{
if ($a!="PHPSESSID")
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