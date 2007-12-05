<? global $DBHost,$DBUser,$DBPass,$ChemFA,$MaxFSize,$TBDname,$NmChDT,$VarNomUserMAJ,$$VarNomUserMAJ,$lc_adrr, $ss_adrr, $lc_CO_USMAJ,
$lc_DBName,$DBName,$lc_clean,$debug,$lc_where_sup,$where_sup,$lc_NM_TABLE,$NM_TABLE,$where_sup,$lc_parenv,$ss_parenv,$def_lang,$id_vtb;

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
