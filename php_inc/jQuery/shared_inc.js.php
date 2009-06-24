<? 
// fichier shared_inc.js.php
// inclusions de fichiers partagés placés dans php_inc
// fctions principales de jQuery
include ("jquery.min.js");
// calendrier sur input date
include ("ui.datepicker.js");
// francisation du calendrier
include ("ui.datepicker-fr.js");
// RTE
include ("jquery.rte.js");
// Toolbars du RTE
include ("jquery.rte.tb.js");
// script de customisation
if ($_REQUEST['custJS']) {
	echo urldecode($_REQUEST['custJS']);}
// init diverses JS dlcube
include ("initjqdl3.js");
//include ("runonload.js");

?>
/* test activation/désactivation entrée de type LDandTxt*/
function CheckLDandTxt(theId) {
	if (document.getElementById('assLD4Txt' + theId).value == 'OTH') {
		document.getElementById(theId).type = 'text';
		document.getElementById(theId).value = '';
	} else {
		document.getElementById(theId).type = 'hidden';
		document.getElementById(theId).value = document.getElementById('assLD4Txt' + theId).value;
	}
}

/* contrôle de formulaire; appelé par onsubmit de form */
var nbInput2test = 0;
var tbId2Verif= new Array();
var tbTypeVerif = new Array();
var tbLibChp2Verif = new Array();

// messages: a traduires
var errOnField = "Erreur sur le champ ";
var mustNoBeNull = " : il ne doit pas être vide";
var mustBeEmail = " : ce devrait être une adresse email valide";
var mustBeTel = " : ce devrait être un n° de tel valide (+)00 00 00 00 00";
var mustBeNumber = " : ce devrait être un nombre >0";

function testJSValChp() {
	var formOk = true;
	var theValue ='';
	var condNN = true;
	regMail = new RegExp( "^\\w[\\w+\.\-]*@[\\w\-]+\.\\w[\\w+\.\-]*\\w$", "gi" );
// 	 Explication du modèle: \\w: un caractère au début de cet email. [\\w+\.\-]*: autant de caractères que l’on veut après, plus point et tiret. @:un arobase [\\w\-]: au moins un caractère, plus tiret. \.: un point \\w: un caractère après le point [\\w+\.\-]*: autant de caractères que l’on veut après, plus point et tiret. \\w: un caractère après. $: fin de l’email.
	regTel = new RegExp( "^[\\d+\+][\\d+ ]{9,16}$", "gi" );
// 	 Explication du modèle: [\\d+\+]: un digit ou + au début; [\\d+ ]{9,16}: de 9 à 16 digit ou espace; $: fin du tel
	regNum = new RegExp( "^[\\d][\\d+\.]*$", "gi" );


	if (typeof oPopupWin != "undefined") closepop();	
	for (i=1; i<=nbInput2test; i++) {
		theValue = '';
		if (document.getElementById(tbId2Verif[i]).type =="checkbox" || document.getElementById(tbId2Verif[i]).type =="radio") {
			for( j=0; j < document.getElementsByName(tbId2Verif[i]).length; j++) { /* tt ça pr les boutons radio ou les checkbox */
				theValue = theValue + document.getElementsByName(tbId2Verif[i]).item(j).value;
			}
		} else { theValue =  document.getElementById(tbId2Verif[i]).value;}
//		alert (tbLibChp2Verif[i] + theValue);
		switch (tbTypeVerif[i]) {
			case 'notNull':
				if (theValue == '') {
					alert (	errOnField + tbLibChp2Verif[i] + mustNoBeNull);
					formOk = false;
				}
			break;
			
			case 'email':
				condNN = !(theValue == '' || theValue == null);
			case 'emailNN':
	  			if(theValue.search(regMail) == -1 && condNN) {
					alert (	errOnField + tbLibChp2Verif[i] + ' (' + theValue + ') ' + mustBeEmail);
					formOk = false;
				}
			break;
			
			
			case 'tel':
				condNN = !(theValue == '' || theValue == null);
			case 'telNN':
	  			if(theValue.search(regTel) == -1 && condNN) {
					alert (	errOnField + tbLibChp2Verif[i] + ' (' + theValue + ') ' + mustBeTel);
					formOk = false;
				}
			break;
			
			case 'number':
				condNN = !(theValue == 0 || theValue == '' || theValue == null);
			case 'numberNN':
	  			if(theValue.search(regNum) == -1 && condNN) {
					alert (	errOnField + tbLibChp2Verif[i] + ' (' + theValue + ') ' + mustBeNumber);
					formOk = false;
				}
			break;
			
		}
	}
	return(formOk);
}
