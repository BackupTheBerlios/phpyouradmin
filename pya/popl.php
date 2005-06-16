<?
require("infos.php");
sess_start();
DBconnect();
?>
<html>
<head>
<title>Selection</title>
<script language="javascript">
function SelectionnerTout(action, selectbox) {
  var srcList = document.getElementById(selectbox);
  for(var i=0;i<srcList.length;i++) {
    srcList.options[i].selected = action;
  }
}

function AddItem() {
  var srcList  = document.forms[0].elements[0]; // il a pas de nom
  var destList  = document.getElementById("resList");
  var len = srcList.options.length;
  for(var i = (len-1); i >= 0; i--) {
    if ((srcList.options[i] != null) && (srcList.options[i].selected == true)) {
      find=false;
      for(var j=0;j<destList.length;j++) {
        if(destList.options[j].value == srcList.options[i].value) { find=true; }
      }
      if(find==false) {
        destList.options[destList.options.length] = new Option(srcList.options[i].text, srcList.options[i].value);
      }
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
  self.close();
}
</script>
</head>
<body>
<form>
<? if ($debug) { ?>
Test: Valeurs=<?=$_REQUEST['Valeurs']?><br>
Valeur Champ=<?=$_REQUEST['ValChp']?><br>
<? }?>
<table><TR><TD colspan="2">
<?
//include_once("reg_glob.inc");
$tbv2c=ttChpLink($_REQUEST['Valeurs']);
$SzLDM=20;
$DispMsg=false;
DispLD($tbv2c,"dbList","yes"); ?>
</TD><tr><TD colspan="2" align="center">
<input type="button" value="   V   " onclick="AddItem();" alt="ajouter"><br>
</TD></tr><tr><TD>
<?
?>
<SELECT NAME="resList[]" id="resList" MULTIPLE SIZE="10" style="width:300px">
</SELECT></TD>
<td valign="middle"><input type="button" value=" - " onclick="DelItem();" alt="Effacer l'élément sélectionné">
</td></tr></table>
<input type="button" value="OK" onclick="MajClose();">
</form>

</body>
</html>
