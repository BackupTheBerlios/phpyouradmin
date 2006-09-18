<? // liste dÃ©roulane gÃ©nÃ©rique
// appellee par les popl en ajax..
$tbres=ttChpLink($_REQUEST['chp_lnk'],"",'__str2f__'.$_REQUEST['txt2srch']);
if (is_array($tbres)) {
echo '<select size="'.$ldajaxdynsize.'" name="srcList" multiple="multiple" id="srcList" style="width:'.$ldajaxdynwidth.'">';
foreach ($tbres as $k=>$v) {
	echo '<OPTION value="'.$k.'">'.$v.'</OPTION>';
	}
echo "</select>";
} else echo "Aucun enregistrement correspondant aux criteres..";

?>
