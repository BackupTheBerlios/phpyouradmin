<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"><html>
<title>Assistant champ valeurs</title>
<link href="styles.css" rel="styleSheet" type="text/css"><?// =($ss_parenv[css_ssf] ? $ss_parenv[css_ssf] : "styles.css")?> 
<link href="css4sharedjs_inc.css.php" rel="stylesheet" type="text/css">
<script type="text/javascript" src="shared_inc.js.php"></script>
<script src="functions.js" type="text/javascript" language="javascript"></script>

<?
require("infos.php");
sess_start();
DBconnect();
define("nbchpaff",5); // nbre de champs affichés
echo echAjaxJSFunctions(); // sort les fonctions ajax qui vont bien
?>
</head>
<body>
<div align="center">
<form name="theform">
<h2>Assistant de définition des valeurs pour le champ <?=$_REQUEST['NM_CHAMP']?></h2>
<div id="formlm">
<h3>Liaisons inter-tables</h3>
<table border="0">
<TR class="backredc"><td class="th">Variables optionnelles</td><td class="th" colspan="4">Valeur</td></tr>
<TR><TD>Connexion à une autre base<br>
<i><small>Ces informations sont optionnelles, par défaut on se connecte sur la base courante</small></i>
</TD><TD colspan="4">Serveur : <input type="text" name="dbhost" id="dbhost"/><br/>
	Base : <input type="text" name="dbname" id="dbname"/><br/>
	User : <input type="text" name="dbuser" id="dbuser"/><br/>
	M.d;P : <input type="text" name="dbpwd" id="dbpwd"/><br/>
	</TD></TR>
<TR><TD>Champs FK (multiples), dans la table courante <?=$_REQUEST['NM_TABLE']?> <br>
<i><small>Cette information est optionnelle, par défaut c'est le nom du champ courant, <?=$_REQUEST['NM_CHAMP']?>, qui sera utilisé</small></i></TD>
<TD colspan="4">
<? // LD champs FK de la table locale
$lchptbloc = db_qr_comprass("SELECT NM_CHAMP,LIBELLE FROM $TBDname where NM_TABLE='".$_REQUEST['NM_TABLE']."' AND NM_CHAMP!='$NmChDT' order by ORDAFF");
foreach ($lchptbloc as $chp) {
	$tbchploc[$chp['NM_CHAMP']] = $chp['NM_CHAMP']." (".$chp['LIBELLE'].")";
}
DispLD($tbchploc,"locFKeys",$Mult="yes",$Fccr="LDF");
?>
</TD></TR>
<TR class="THEAD"><td class="th">Variables obligatoires</td><td class="th" colspan="4">Valeur</td></tr>

<? // list de stables 
$tbltab = db_show_tables($DBName);
foreach ($tbltab as $tab) {
	//$lb = RecupLib($TBDname,"NM_TABLE","LIBELLE",$tab,"","NM_CHAMP='$NmChDT'"); // trop long, ça le fait ramer
	$tbltab4ld[$tab] = $tab.($lb ? ' ('.$lb.')': '');
}

if ($_REQUEST['btv']) { // seulement si table virtuelle ?>
<TR class="backredc"><TD>Table physique</TD><TD colspan="4">
<?
DispLD($tbltab4ld,"physTable",$Mult="no",$Fccr="LDF");
?>
</TD></TR>
<? } ?>

<TR><TD>Table cible</TD><TD colspan="4">
<?
echo str_ireplace("<select",'<select onchange=" majLdsAjaxFctTable(this.value);" ',DispLD($tbltab4ld,"dTable",$Mult="no",$Fccr="LDF",false));
?>
</TD></TR>
<TR><TD>Champ(s) clé(s) dans la table cible</TD><TD colspan="4">
	<div id="iddKeys4ajax"></div>
</TD></TR>
<TR class="THEAD"><td class="th">Variable</td><td class="th">Valeur</td><TD class="th" title="Caractères affichés avant le champ">Séparateur</TD><TD class="th">Classement</TD><TD class="th">Chain.</TD></TR>
<? for ($i=1;$i<=nbchpaff;$i++) { // champs affichés?>
<TR><TD>Champ affiché n°<?=$i?></TD><TD>
	<div id="idd4ajaxChp<?=$i?>"></div>
<!--	<input type="text" name="dchp1" id="dchp1"/>-->
</TD><td>
<? if ($i != 1) { ?>
	<input type="text" name="sepchp<?=$i?>" id="sepchp<?=$i?>" size="2" value="-">
<? } else { ?>
	<input type="hidden" name="sepchp<?=$i?>" id="sepchp<?=$i?>" size="2" value="-">
<? } ?>
</td>
<td> v<input type="radio" name="classt" id="classta<?=$i?>" value="a<?=$i?>"> ^<input type="radio" name="classt" id="classtr<?=$i?>"  value="r<?=$i?>"></td>
<td> <input type="checkbox" name="chain<?=$i?>" id="chain<?=$i?>" value="yes"></td></TR>
<? } ?>
<TR><TD>Champ d'arborescence</TD><TD colspan="4">
	<div id="idd4ajaxChpArbo"></div>
</TD></TR>
<TR><TD>C° where supplémentaire</TD><TD colspan="4"><input type="text" name="wheresup" id="wheresup" size="50"/></TD></TR>
</table>
</div>
<div>Commentaires :
<textarea id="comments" name="comments" cols="80" rows="5"></textarea>
</div>
<div>
<br/>
<input type="button" value="Annuler" onclick="self.close();"  style="font-weight:bold">
<input type="button" value="OK" onclick="TestMajClose();"  style="font-weight:bold" id="btvalid" >
</div>
</form>
<script language="javascript">

// fonction qui met à jour les listes déroulantes de champs affichés en ajax quand on change de table...
function majLdsAjaxFctTable(tablename) {
	ahah(basedKeysUrl + tablename,'iddKeys4ajax'); // maj en ajax LD des champs clés
	for (var k = 1; k <=nbchpaff; k++) {
		setSelectdTable(k,tablename); // maj les LD des champs affichés, et selectionne la bonne val si elle existe
	}
	arboLDUrl = "ld_lchp_ajax_dyn.php?NMID=nmChpArbo&FIRSTEMPTY=true&MULT=0" + "&NM_CHPS=" + nmChpArbo + "&NMTABLE=";
	ahah( arboLDUrl + tablename,"idd4ajaxChpArbo");
}

var typeAff = window.opener.document.getElementById("typeAff<?=$_REQUEST['i']?>").value;
if (!( typeAff=='LDLM' || typeAff=='STAL' ||typeAff=='LDL' ||typeAff=='POPL' ||typeAff=='POPLM' )) {
	document.getElementById("btvalid").style.display="none";
	document.getElementById("formlm").style.display="none";
	alert ("L'assistant ne fonctionne pour l'instant que pour les liaisons de table. Désolé");
}

var orValeurs = new String(window.opener.document.getElementById("valeurs<?=$_REQUEST['i']?>").value); // valeurs d'origine
var valeurs = new String(orValeurs);
var comments = new String('');
var tbdchpUrls = new Array();
var tblaval = new Array();
var nm_champ = '<?=$_REQUEST['NM_CHAMP']?>';
var basedKeysUrl = "ld_lchp_ajax_dyn.php?NMID=dKeys&MULT=1&NMTABLE=";
var nmChpArbo = new String('');
var arboLDUrl = "ld_lchp_ajax_dyn.php?NMID=nmChpArbo&FIRSTEMPTY=true&MULT=0&NMTABLE=";
var nbchpaff = <?=nbchpaff?>;
valeurs = valeurs.replace("\r\n","\n");
valeurs = valeurs.replace("\r","\n");
tblvaleurs = valeurs.split("\n");

//for(var i = 0; i<lentblv; i++) {
for(var i in tblvaleurs) {
	tblvaleurs[i] = trim(tblvaleurs[i]); // function trim definie dans functions.js
	if (tblvaleurs[i].substr(0, 1) != '#' && tblvaleurs[i].substr(0, 1) != ";" &&  tblvaleurs[i].substr(0, 1) != "" ) {
		if (tblvaleurs[i].substr(0, 1) == '$') { // nvelle syntaxe
			tblvaleurs[i] = tblvaleurs[i].substr(1, tblvaleurs[i].length - 1);
			tblaval = tblvaleurs[i].split("=");
			if (tblaval[0] == "locFKeys") {
				setSelect("locFKeys",tblaval[1],",");
			<? if ($_REQUEST['btv']) { // seulement si table virtuelle ?>
			} else if (tblaval[0] == "physTable") {
				setSelect("physTable",tblaval[1],",");
			<? } ?>
			} //else document.getElementById(tblaval[0]).value = tblaval[1];
		} else {
			//alert ('la ligne ' + tblvaleurs[i] + ' est a l ancienne syntaxe');
			var tblaval = tblvaleurs[i].split("[[");
			if (tblaval.length >1) 	document.getElementById("wheresup").value = tblaval[1]; // cd° where suppl
			tblaval = tblaval[0];
			tblaval = tblaval.split(";"); // on détermine un serveur ou une base différente
			if (tblaval.length >1) {
				tbifb = tblaval[0].split(",");
				document.getElementById('dbhost').value = tbifb[0];
				document.getElementById('dbname').value = tbifb[1];
				document.getElementById('dbuser').value = tbifb[2];
				document.getElementById('dbpwd').value = tbifb[3];
				tblaval = tblaval[1];
			} else {
				tblaval = tblaval[0];
			}
			tblaval = tblaval.split(","); // explode la liste qui est sous la forme table,chpcle1[:chpcle2],chpaff1,chpaff2,
			if (tblaval.length > 0) {
				setSelect("dTable",tblaval[0],":"); // sélection la table sélectionnée dans la liste der. des tables
				var basedKeysUrl = "ld_lchp_ajax_dyn.php?NMID=dKeys&MULT=1&NM_CHPS=" + tblaval[1] + "&NMTABLE="; // prépare url
				// regarde si champ d'arbre. Si oui le met à la fin et décale les indices
				for (var i = 1; i <tblaval.length; i++) {
					// regarde si champ d'arbre. Si oui le met à la fin et décale les indices
					if (tblaval[i].substr(0, 2)  == "@@") {
						nmChpArbo = tblaval[i].substr(2, tblaval[i].length - 2); // vir le @@
						if (i != nbchpaff + 1) { // si c'est pas le dernier (5eme)
							// décale les indices
							for (var j = i; j < tblaval.length - 1; j++) {
								tblaval[j] = tblaval[j+1];
							}
							tblaval[tblaval.length - 1] = '';
						}
						//alert(tblaval);
					}
					// teste séparateur spécifique
     				if (tblaval[i].indexOf('!',0) > 0) {
						tblaval2 = tblaval[i].split('!');
						document.getElementById('sepchp' + (i-1)).value = tblaval2[0];
						tblaval[i] = tblaval2[1];
     				}
     				// teste chainage
     				if (tblaval[i].substr(0, 1)  == "&") {
						tblaval[i] = tblaval[i].substr(1, tblaval[i].length - 1); // vir le &
						document.getElementById('chain' + (i-1)).checked = true;
     				}
     				// teste classt inverse
     				if (tblaval[i].substr(0, 2)  == "~@") {
						tblaval[i] = tblaval[i].substr(2, tblaval[i].length - 2); // vir le ~@
						document.getElementById('classtr' + (i-1)).checked = true;
     				}
     				// teste classt normal
     				if (tblaval[i].substr(0, 1)  == "@") {
						tblaval[i] = tblaval[i].substr(1, tblaval[i].length - 1); // vir le @
						document.getElementById('classta' + (i-1)).checked = true;
     				}
				}
				majLdsAjaxFctTable(tblaval[0]); // met à jour ttes les LD ajax et leurs valeurs
			}
		}
	} else {
		comments = comments + tblvaleurs[i].substr(1,tblvaleurs[i].length - 1) + "\n";
	}
} // fin boucle sur les lignes de valeurs
document.getElementById('comments').value = comments;

// met à jour liste déroulante fonction de champ
function setSelectdTable(i,nmtable) {
	if (! tblaval[i + 1]) tblaval[i + 1] = '';
	if (i != 1) {
		var firstempty = '&FIRSTEMPTY=true';
	} else var firstempty = '';
	tbdchpUrls[i] =  "ld_lchp_ajax_dyn.php?NMID=dchp" + i + firstempty + "&NM_CHPS=" + tblaval[i + 1] + "&NMTABLE=";
	ahah( tbdchpUrls[i] + nmtable,"idd4ajaxChp" + i);
	//setSelect('dchp' + i,tblaval[i + 1]);
}

// met les valeurs sélectionnées d'une LD à partir du tableau valsel
function setSelect(idDest,valsel,chSep) {
	var destList  = document.getElementById(idDest);
	var len = destList.options.length;
	valsel = valsel.split(chSep);
	for(var i = (len-1); i >= 0; i--) {
		for(var j in valsel) {
			if (destList.options[i].value == valsel[j]) destList.options[i].selected = true;
		}
	}
}

// renvoie les valeurs sélectionnées d'une LD dans un champ avec separateur
function getSelect(idDest,sepchps) {
	var destList  = document.getElementById(idDest);
	var len = destList.options.length;
	var k = 0;
	var valsel = new Array();
	for(var i = (len-1); i >= 0; i--) {
		if (destList.options[i].selected ) {
			valsel[k] = destList.options[i].value;
			k++;
		}
	}
	valsel = valsel.join(sepchps);
	return(valsel);
}
// renvoie les valeurs sélectionnées d'un radio bouton
function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

// test, maj à jour cible et ferme
function TestMajClose() {
	//alert("classement:" + getCheckedValue(document.theform.classt));
	var theres = '';
	var boolOk = true;
	var locFKstr = getSelect('locFKeys',",");
	if (locFKstr.indexOf(",", 1) > 0) { // si clés multiples
		var tbFKs = locFKstr.split(",");
		// teste si même nombre de clés locale que distantes
		var nbdk = getSelect('dKeys',":").split(":").length;
		if ( nbdk != tbFKs.length ) {
			alert("Erreur : le nombre de clés locales (" + tbFKs.length + ") est différents du nombre de clés étrangères ("+ nbdk + ")...");
			boolOk = false;
		} else { // liste les autres champs
			var tbFKNs = new Array();
			var k = 0;
			for (var i=0;i<tbFKs.length;i++) {
				if (tbFKs[i] != nm_champ) { tbFKNs[k] = tbFKs[i]; k++; }
			}
			alert ('Vous avez choisi un champ FK multiple; vous devez mettre les propriétés d édition des autres champs FK de cette table (' + tbFKNs.join(",") + ') à " Aucun"');
		}
	}
	if (locFKstr != '') theres = theres + '$locFKeys=' + locFKstr + "\n";
	<? if ($_REQUEST['btv']) { // seulement si table virtuelle ?>
		if (document.getElementById('physTable').value != "")  theres = theres + '$physTable=' + document.getElementById('physTable').value + "\n";
	<? } ?>
	// chaine principale
	// si base/chp etc distant	
	if (document.getElementById('dbhost').value != '' || document.getElementById('dbname').value != '' || document.getElementById('dbuser').value != '' || document.getElementById('dbpwd').value != '') 
		 theres = theres + document.getElementById('dbhost').value  + "," +  document.getElementById('dbname').value + "," + document.getElementById('dbuser').value  + "," + document.getElementById('dbpwd').value +";";
	// table cible
	theres = theres + document.getElementById('dTable').value + ",";
	// champ(s) clé(s)
	theres = theres + getSelect('dKeys',":");
	var classt = getCheckedValue(document.theform.classt);
	// champs affichés
	for(var i = 1; i <= <?=nbchpaff?>; i++) theres = theres +  getChpAfInfo(i,classt);
	// chp arbo
	if ( document.getElementById('nmChpArbo').value != "") theres = theres + ",@@" + document.getElementById('nmChpArbo').value;
	// where sup
	if (document.getElementById("wheresup").value !="") theres = theres + "[[" + document.getElementById("wheresup").value + "\n";
	// commentaires
	var comments = document.getElementById('comments').value.split("\n");
	for(var i = 0; i < comments.length; i++) {
		if (trim(comments[i]) != "") theres = theres +  "\n#" + comments[i];
	}
	//	alert (theres);
	// maj fenetre origine
	if (boolOk) {
		window.opener.document.getElementById("valeurs<?=$_REQUEST['i']?>").value = theres;
		self.close();
	}
}

function getChpAfInfo(i,classt) {
	var ret = '';
	var valld =   document.getElementById('dchp' + i).value;
	if (valld != "") {
		ret= ",";
		if (document.getElementById('sepchp' + i).value != "-") ret = ret + document.getElementById('sepchp' + i).value + "!";
		if (document.getElementById('chain' + i).checked) ret = ret + "&";
		if (classt == "a" + i) ret = ret + "@";
		if (classt == "r" + i) ret = ret + "~@";
		ret = ret + valld;
	}
	return (ret);
}


</script>
</body>
</html>