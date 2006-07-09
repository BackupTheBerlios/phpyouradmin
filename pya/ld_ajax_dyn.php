<?
require("infos.php");
sess_start();
//include_once("reg_glob.inc");
DBconnect();
//echo "chp_lnk=".$_REQUEST['chp_lnk']."<br/>";
//echo "txt2srch=".$_REQUEST['txt2srch']."<br/>";
$tbres=ttChpLink($_REQUEST['chp_lnk'],"",'__str2f__'.$_REQUEST['txt2srch']);
echo '<select size="'.$ldajaxdynsize.'" name="srcList" multiple="multiple" id="srcList" style="width:'.$ldajaxdynwidth.'">';
foreach ($tbres as $k=>$v) {
	echo '<OPTION value="'.$k.'">'.$v.'</OPTION>';
	}
echo "</select>";

// DispLD($tbres,"srcList",$Mult="yes","LDF"); on l'utilise pas pour que la liste ai tjrs 10 de hauteur

?>
