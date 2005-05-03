<?
require_once("fonctions.php");
include("infos_conn_rp.inc");

$rp_lnkdb=DBDRH_conn_sel(); // connection au serveur et sélection de la base

// fonction renvoyant la fiche de coordonnées d'une UF
function rp_RetFichCoordUF($Id,$Phot=false,$PlanAcc=false) {
if ($Phot) $FCUF=rp_RetCoord($Id,"Phot","UF");
$FCUF.="<B>".rp_RetCoord($Id,"Nom","UF")."</B>";
$FCUF.=rp_RetCoord($Id,"Adr1","UF");
$FCUF.=rp_RetCoord($Id,"Adr2","UF");
$FCUF.=rp_RetCoord($Id,"CDPST","UF");
$FCUF.=rp_RetCoord($Id,"Ville","UF");
$FCUF.=rp_RetCoord($Id,"TelFix","UF");
$FCUF.=rp_RetCoord($Id,"Fax","UF");
$FCUF.=rp_RetCoord($Id,"Mail","UF");
if ($PlanAcc) $FCUF.=rp_RetCoord($Id,"Plan","UF");
return($FCUF);
}

/* fonction qui renvoie un champ de contact
- Id: clé de la table (n° personne, id UF, ID société etc ..)
- Coord: champ demandé: Titre, Nom, Prenom, Adr1, Adr2, CDPST, Ville, Pays, TelFix, TelAbr, TelMob, Fax, Mail, Web, Phot, Plan,
- TypCtct: type contact: P=personne, UF=Unité fonctionnelle, S=Société
- $DPL= display libellé (tel; web, mail...)
- $DPHtml= display en html, et met un BR ou esp en suivant si non vide */
function rp_RetCoord($Id,$Coord,$TypCtct="P",$DPL=true,$DPHtml=true) {
global $rp_lnkdb; // variable identifiant la connection avec le référentiel

if (!isset($rp_lnkdb)) $rp_lnkdb=DBDRH_conn_sel(); // se connecte à la base si pas déjà

if ($Id!="") {
switch ($TypCtct) {
    case "P":
        $NMTable="PERSONNE";
        $ChpCle="PER_NUPERS";
        switch ($Coord) {
               case "Titre":
                    $NMChp="PER_LMTITREPER";
                    $CS=" ";
                    break;
               case "Nom":
                    $NMChp="PER_LLNOMPERS";
                    $CS=" ";
                    break;
               case "Prenom":
                    $NMChp="PER_LLPRENOMPERS";
                    $CS=" ";
                    break;
               case "Adr1":
                    $NMChp="";
                    $CS="<BR>\n";
                    break;
               case "Adr2":
                    $NMChp="";
                    $CS="<BR>\n";
                    break;
               case "CDPST":
                    $NMChp="";
                    $CS=" ";
                    break;
               case "Ville":
                    $NMChp="";
                    $CS="<BR>\n";
                    break;
               case "Pays":
                    $NMChp="";
                    $CS="<BR>\n";
                    break;
               case "TelFix":
                    $NMChp="PER_TELFIXE";
                    $CS="<BR>\n";
                    $Lib="Tel: ";
                    break;
               case "TelAbr":
                    $NMChp="PER_LCABREGE";
                    $Lib="N° abrégé";
                    $CS="<BR>\n";
                    break;
               case "TelMob":
                    $NMChp="PER_PORPERS";
                    $CS="<BR>\n";
                    $Lib="Mobile: ";
                    break;
               case "Fax":
                    $NMChp="PER_FAX";
                    break;
               case "Mail":
                    $NMChp="PER_MAILPERS";
                    $ttt="CustHT"; // type traitement custom pour avoir le lien mailto !!
                    $CS="<BR>\n";
                    $Lib="Mél: ";
                    break;
               case "Web" :
                    $ttt="CustHT"; // type traitement custom pour avoir le lien HREF !!
                    $NMChp="";
                    $CS="<BR>\n";
                    $Lib="Site ";
                    break;
               case "Phot":
                    $NMChp="PER_PHOTO";
                    $ttt="img"; // type traitement image pour affichage
                    break;
               case "Plan":
                    $NMChp="";
                    break;
               default:
                    break;
               } // fin switch coord
        break; // fin traitement contact de type P (personne)

    case "UF":
        $NMTable="UNITE_FONCTION";
        $ChpCle="UFO_NUUNITE";
        switch ($Coord) {
               case "Titre":
                    $NMChp="UFO_COTYUNITE";
                    $CS="<BR>\n";
                    break;
               case "Nom":
                    $NMChp="UFO_LLUNITE";
                    $CS="<BR>\n";
                    break;
               case "Prenom":
                    $NMChp="";
                    $CS="";
                    break;
               case "Adr1":
                    $NMChp="UFO_LLADRES";
                    $CS="<BR>\n";
                    break;
               case "Adr2":
                    $NMChp="UFO_LLADRES2";
                    $CS="<BR>\n";
                    break;
               case "CDPST":
                    $NMChp="UFO_COPOSTAL";
                    $CS=" ";
                    break;
               case "Ville":
                    $NMChp="UFO_LLCOMMU";
                    $CS="<BR>\n";
                    break;
               case "Pays":
                    $NMChp="UFO_COPAYS";
                    $CS="<BR>\n";
                    break;
               case "TelFix":
                    $NMChp="UFO_TELUNITE";
                    $CS="<BR>\n";
                    $Lib="Tel: ";
                    break;
               case "TelAbr":
                    $NMChp="UFO_LCABREGE";
                    $Lib="N° abrégé";
                    $CS="<BR>\n";
                    break;
               case "TelMob":
                    $NMChp="UFO_PORUNITE";
                    $CS="<BR>\n";
                    $Lib="Mobile: ";
                    break;
               case "Fax":
                    $NMChp="UFO_FAXUNITE";
                    $CS="<BR>\n";
                    $Lib="Fax: ";
                    break;
               case "Mail":
                    $NMChp="UFO_MAILUNITE";
                    $ttt="CustHT"; // type traitement custom pour avoir le lien mailto !!
                    $CS="<BR>\n";
                    $Lib="Mél: ";
                    break;
               case "Web" :
                    $ttt="CustHT"; // type traitement custom pour avoir le lien HREF !!
                    $NMChp="";
                    $CS="<BR>\n";
                    $Lib="Site ";
                    break;
               case "Phot":
                    $NMChp="UFO_FICHASS";
                    $ttt="img"; // type traitement image pour affichage
                    break;
               case "Plan":
                    $NMChp="UFO_PLANACC";
                    $ttt="img"; // type traitement image pour affichage
                    break;
               default:
                    break;
               } // fin switch coord
        break; // fin traitement contact de type P (personne)

    default:
        break;
    } // fin switch sur type contact
if ($NMTable=="" || $NMChp=="") {
   return ("le type coordonnée <b>$Coord</b> n'existe pas pour le type de contact <b>$TypCtct</b><br>\n");
   }
else {
     $valret=RecupLib($NMTable,$ChpCle,$NMChp,$Id,$lnkdb);
     switch ($ttt) {
            case "CustHT":
                 if ($DPHtml) $valret=DispCustHT($valret);
            default:
                    break;
            } // fin switch sur type traitement
     if ($DPL && $valret!="") $valret=$Lib.$valret;
     if ($DPHtml && $valret!="") $valret.=$CS;
     return($valret);
    }
} // fin $Id!=""
else return ("l'Id du contact n'est pas renseigné !");
} // fin fonction
?>
