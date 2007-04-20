<?
require_once ("ajaxtools.inc");
$charset=($_SESSION['ss_parenv']['encoding']!="" ? $_SESSION['ss_parenv']['encoding'] : "utf-8");
@ini_set("default_charset", $charset);
header('Content-type: text/html; charset='.$charset); 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"><html>
<title>Selection</title>
<?=echAjaxJSFunctions(); ?>

<script language="javascript">

var ajaxurl="ld_ajax_dyn.php?chp_lnk=<?=$_REQUEST['Valeurs']?>&txt2srch=";

function searchdb(txt2srch) {
	
	//alert (txt2srch);
	if (txt2srch.length>2) {
	 ahah(ajaxurl + txt2srch,'ajaxdblist'); 
	 }
}

function SelectionnerTout(action, selectbox) {
  var srcList = document.getElementById(selectbox);
  for(var i=0;i<srcList.length;i++) {
    srcList.options[i].selected = action;
  }
}

function AddItem() {
  var srcList  = document.getElementById("srcList"); // il a pas de nom
  var destList  = document.getElementById("resList");
  var len = srcList.options.length;
  for(var i = (len-1); i >= 0; i--) {
    if ((srcList.options[i] != null) && (srcList.options[i].selected == true)) {
      <? if($_REQUEST['Mult']>1) { ?>                                                 
	find=false;
	for(var j=0;j<destList.length;j++) {
		if(destList.options[j].value == srcList.options[i].value) { find=true; }
	}
	if(find==false) {
		destList.options[destList.options.length] = new Option(srcList.options[i].text, srcList.options[i].value);
	}
    <? } else { // unique  ?>
    if (destList.options[0] != null) {
    	destList.options[0] = null;
    	}
    destList.options[0] = new Option(srcList.options[i].text, srcList.options[i].value);
  
    <? } ?>
    }
  }
}
function DelItem() {
  var destList  = document.getElementById("resList");
  var len = destList.options.length;
  for(var i = (len-1); i >= 0; i--) {
    if ((destList.options[i] != null) && (destList.options[i].selected == true)) {
      destList.options[i] = null;
    }
  }
}

function MajClose() {
  var srcList  = document.getElementById("resList");
  var destList  = window.opener.document.getElementById("<?=$_REQUEST['NmChp']?>"); 
  var lens = srcList.options.length;

  // efface toutes les valeurs dans la liste parente  
  var lend = destList.options.length;
  for(var i = (lend-1); i >= 0; i--) {
      destList.options[i] = null;
    }
    
  for(var i = (lens-1); i >= 0; i--) {
    if (srcList.options[i] != null) {
        destList.options[destList.options.length] = new Option(srcList.options[i].text, srcList.options[i].value);
	destList.options[destList.options.length-1].selected=true;
	//destList.options[ -1].selected=true;
    }
  }
  destList.size=lens;
  self.close();
}

function MajReslist() {
/* mise a jour de la liste en javascript */
  var destList  = document.getElementById("resList");
  var srcList  = window.opener.document.getElementById("<?=$_REQUEST['NmChp']?>"); 
  var lens = srcList.options.length;

 
  for(var i = (lens-1); i >= 0; i--) {
    if (srcList.options[i] != null) {
        destList.options[destList.options.length] = new Option(srcList.options[i].text, srcList.options[i].value);
	destList.options[destList.options.length-1].selected=true;
	//destList.options[ -1].selected=true;
    }
  }
}
</script>
</head>
<body>
<form>
<? 
$debug=false;
if ($debug) { ?>
Test: Valeurs=<?=$_REQUEST['Valeurs']?><br>
Valeur Champ=<?=urldecode($_REQUEST['ValChp'])?><br>
<? }
/* print_r($_REQUEST);
print_r(urldecode(unserialize($_REQUEST['ValChp'])))*/;
?>
<B>Entrez la chaine de caractères à rechercher :</B>
<input id="searchfield" name="searchfield" type="text" onkeyup="searchdb(this.value)" size="50"><br/>
<div style="border: 1px solid"><small><I>Entrer des caractères ci-dessus pour actualiser la liste ci-dessous</I></small></div>
<B>Résultat de la recherche : </B><br/>
<div id="ajaxdblist">
<select size="<?=($ldajaxdynsize>0 ? $ldajaxdynsize : 8)?>" style="width:<?=($ldajaxdynwidth>0 ? $ldajaxdynwidth : 250 ) ?>px">></select>
<!--<select name="srcList" id="srcList" multiple="multiple" size="10">
</select>-->
</div>

<br><input type="button" value="   V   " onclick="AddItem();" alt="ajouter" style="font-weight:bold"> <small><I>Ajouter les &eacute;l&eacute;ments selectionn&eacute;s</i></small></br>
<P><B>Selection :</B><br/><SELECT NAME="resList[]" id="resList" multiple="MULTIPLE" SIZE="<?=($ldpoplsel>0 ? $ldpoplsel : 8 )?>" style="width:<?=($ldajaxdynwidth>0 ? $ldajaxdynwidth : 250 ) ?>px; vertical-align:middle;">
<? /* on le fait en javascript maintenant
$tabVS=urldecode(unserialize($_REQUEST['ValChp'])); // en plus le unserialize ne marchait pas
if (count($tabVS)>0) {
	foreach ($tabVS as $k=>$v) {
		echo "<OPTION VALUE=\"$k\" SELECTED=\"SELECTED\">$v</OPTION>";
		}
	} 
*/ ?>

</SELECT>
<input type="button" value=" - " onclick="DelItem();" alt="Effacer l'element selectionne"  style="font-weight:bold"> <small><i>Effacer</i></small></p>
<div align="center">
<input type="button" value="Annuler" onclick="self.close();"  style="font-weight:bold">
<input type="button" value="OK" onclick="MajClose();"  style="font-weight:bold">
</div>
</form>
<script language="javascript">
MajReslist();
</script>
</body>
</html>
