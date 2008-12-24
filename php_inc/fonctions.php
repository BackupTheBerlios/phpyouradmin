<?
/* FICHIER DE FONCTIONS */
// PARTAGE PAR TOUTES LES APPLIS
// quelques variables globales
$nbcarmxlist=50; // nbre de caract�es max affich� par cellules dans les tableaux liste
$nbligpp_def=15; // nbre de lignes affich�s par page par d�aut
// servant ds les progs d'�ition
$nbrtxa=5; // nbre de lignes des textarea
$nbctxa=40; // nbre de colonnes des textarea
$nValRadLd=4; // nbre de valeurs passage liste d�oulante/boutons radio case �cocher
$SzLDM=6; // parmetre size pour les listes d�oulantes multiples
$DispMsg=true; // affichage par d�aut du message "appuyez sur control pour s�ectionner plusieurs valeurs
$VSLD="#SEL#"; // caract�es ins��en d�ut de valeur de listes indiquant la s�ection
$maxrepld=2000;
$carsepldef="-"; // caract�e par d�aut s�arant les valeur dans les listes d�oulantes
$maxprof=10; // prof max des hi�archies
$CSpIC=""; // caract�e pour "isoler" les noms de champs merdiques
// ne fonctionne qu'avec des versions r�entes de MySql
$MaxFSizeDef = 5000000; // taille max des fichiers joints par d�aut!!

// Nom de la table de description des autres
$TBDname="DESC_TABLES";
// nom du champ contenant les caract�istiques globales �la table
$NmChDT="TABLE0COMM";
// id contenu ds les tables virtuelles ie celles qui n'existent pas en base
$id_vtb="_vtb_";

// tableau des évenements verification de formulaire auto
$tbEvenmtVFAutoJS = array ("notNull" => "Non vide",
    	 	"email" => "Adresse email",
    	 	"emailNN" => "Adresse email non vide",
    	 	"tel" =>"N° téléphone",
    	 	"telNN" => "N°téléphone non vide",
    	 	"number" => "Nombre",
    	 	"numberNN" => "Nombre >0",
    	 	);

$ListTest="linux xsir-intralinux 126.0.26.2";
$ListDev="linuxk6 192.168.0.20 192.168.0.30";

// abstraction BDD
require_once("db_abstract.inc");
// abstraction BDD
require_once("ajaxtools.inc");

// NECESSITE D'IMPLEMENTER LES FONCTIONS D'ACCES A L'ANNUAIRE
// et tt ce qui concerne l'objet PYA
require_once("PYAObj_def.inc");
require_once("debug_tools.inc"); // fonctions servant au debogage


// fonction qui retourne une ext fonction de l'adresse de l'H�e
function RetEFAH($UC=false) {
global $ListTest,$ListDev,$HTTP_HOST;
$HostName=($HTTP_HOST=="" ? $_SERVER["HTTP_HOST"] : $HTTP_HOST);
if (stristr($ListTest,$HostName)) {
   return ($UC ? "!TEST_" : "_test"); }
else if (stristr($ListDev,$HostName)) {
   return ($UC ? "!LOC_" : "_loc"); }
else return ("");
}

// test si une chaine correspond �un fichier image
// ie si son nom contient l'extension .gif, .jpeg, etc ...
function TestNFImg($Nmf){
return(strstr(strtolower($Nmf),".gif") or
     strstr(strtolower($Nmf),".jpg") or
     strstr(strtolower($Nmf),".png") or
     strstr(strtolower($Nmf),".jpeg"));
}

// remise en forme d'une date en fran�is j/m
function rmfDateF($DateOr){
$tab=explode("/",$DateOr);
$tab[0]=$tab[0]+0; // conversions de type
$tab[1]=$tab[1]+0;
if ($tab[2]=="") $tab[2]=date("Y");
if ($tab[2]<70) $tab[2]+=2000;
if ($tab[2]>70 && $tab[2]<100) $tab[2]+=1900;
if ($tab[1]<10) $tab[1]="0".$tab[1];
return(implode("/",$tab));
}
// conversion d'une date en fran�is jj/mm/aa vers anglais aa-mm-jj
function DateA($DateOr){
if (strstr($DateOr,"/")) { // si pas de /, fait rien elle est peut-etre deja au bon format
	$tab=explode("/",$DateOr);
	$tab[0]=$tab[0]+0;
	$tab[1]=$tab[1]+0;
	if ($tab[2]=="") $tab[2]=date("Y");
	if ($tab[2]<70) $tab[2]+=2000;
	if ($tab[2]>70 && $tab[2]<100) $tab[2]+=1900;
	$DateOr=$tab[2]."-".$tab[1]."-".$tab[0];
}
return($DateOr);
}
// fonction inverse (anglais vers fran�is)
function DateF($DateOr){
if (strstr($DateOr,"-")) { // si pas de -, fait rien elle est peut-etre deja au bon format
	$tab=explode("-",$DateOr);
	$tab[0]=$tab[0]+0;
	$tab[1]=$tab[1]+0;
	if ($tab[1]<10) $tab[1]="0".$tab[1];
	$DateOr=$tab[2]."/".$tab[1]."/".$tab[0];
}
return($DateOr);
}
function DateF2tstamp($DateStr) {
if (trim($DateStr)=="" || !strstr($DateStr,"/")) return (0);
$tab=explode("/",$DateStr);
$tab[0]=$tab[0]+0;
$tab[1]=$tab[1]+0;
$tab[2]=$tab[2]+0;
if ($tab[2]=="") $tab[2]=date("Y");
if ($tab[2]<70) $tab[2]+=2000;
if ($tab[2]>70 && $tab[2]<100) $tab[2]+=1900;
if ($tab[2]>=2038) $tab[2]=2037; // bug an 2038
return(mktime(0,0,0,$tab[1],$tab[0],$tab[2]));
}
function DateA2tstamp($DateStr) {
if (trim($DateStr)=="" || !strstr($DateStr,"-")) return (0);
$tab=explode("-",$DateStr);
$tab[0]=$tab[0]+0;
$tab[1]=$tab[1]+0;
$tab[2]=$tab[2]+0;
if ($tab[0]=="") $tab[0]=date("Y");
if ($tab[0]<70) $tab[0]+=2000;
if ($tab[0]>70 && $tab[0]<100) $tab[0]+=1900;
if ($tab[0]>=2038) $tab[0]=2037; // bug an 2038
return(mktime(0,0,0,$tab[1],$tab[2],$tab[0]));
}

// force affichage d'un nombre a 2 chiffres (pr affichage des minutes et heures) 
function c2c($nb) {
	if ($nb<10) $nb="0".$nb;
	return ($nb);
}

/// affichage heure : min à partir entier (webcalendar)
function DispHRMN($hr) {
	return (c2c(floor($hr/100))."h".c2c($hr % 100)."mn");
}

function readfile2tb($file,$cs=";") {
// lecture d'un fichier csv : première ligne contient les clés, suivantes les valeurs des clés
// renvoie un tableau à deux dimensions $tb[col][ligne]=
// utilisé dans GRH pour les profils
if (file_exists($file)) {
	$h=fopen($file,'r');
	while (!feof($h)) {
		$lig = fgets($h);
		if ( (substr($lig,0,1) != "#")) { // pas commentaire
			$i++;
			if ($i == 1) { // ligne entête
				$tbhead = explode($cs,$lig);
				$j=0;
				foreach($tbhead as $head) {
					if ($j>0) $tb[trim($head)] =  array();
					$j++;
				}
			} else { // ligne "normale"
				$tblig = explode($cs,$lig);
				$j =  0;
				foreach($tbhead as $head) {
					if ($j>0) $tb[$head][trim($tblig[0])] = trim($tblig[$j]);
					$j++;
				}
			}
		}
	}
	fclose($h);
	return ($tb);
} else {
	echo "Erreur : impossible de lire $file";
	return (false);
	}
}
// fonction qui vire les x derniers car d'une chaine
function vdc($strap,$nbcar) {
return (substr($strap,0,strlen($strap)-$nbcar));
}

// fonction qui convertit le contenu d'une valeur geree par LDLM dans pya ( ,toto,titi,
// en valeur mettable dans un IN sql
function cvldlm2in ($strap,$addsl=false) {
	if ($addsl) $adsl="'";
	if (strstr($strap,",")) {
		$tbuids=explode(",",$strap);
		foreach($tbuids as $uid) {
			if ($uid!="") $tbuids2[]=$adsl.$uid.$adsl;
		}
		$uidin=implode(",",$tbuids2);
	} else $uidin=$adsl.$strap.$adsl;
	return($uidin);
}

// fonction qui renvoie x espaces ins�ables
function nbsp($i=1){
return(str_repeat("&nbsp;",$i));
}

// fonction qui coupe une chaine �la longueur d�ir�, sans couper les mots
function tronqstrww ($strac,$long=50,$strsuite=" [...]") {
         if (strlen($strac)<=$long) return $strac;
         $strac = wordwrap($strac,$long,"coucou");
         $tbstrac= explode("coucou",$strac);
         return($tbstrac[0].$strsuite);
}

// fonction qui echoise un texte dans un style
function echspan($style,$text,$DirEcho=true) {
    $retVal.= "<span class=\"$style\">$text</span>";
    if ($DirEcho) {
    	echo $retVal;
    } else {
    	return($retVal);
    }
}
function toggleAffDiv($theid,$thecontent,$theclass="fxbutton",$thetitle="afficher/masquer",$initDisp=false) {
	$initDisp = $initDisp || $_SESSION['hidPosOf'.$theid];
	$ret = '<a name="AncOfTgAf'.$theid.'"/>';
	$ret .= '<input type="hidden" name="hidPosOf'.$theid.'" id="hidPosOf'.$theid.'" value="'.$initDisp.'"/>';
	$ret .= '<input title="'.$thetitle.'" type="button" id="butOf'.$theid.'" value="'.(!$initDisp ? "+" : "-").'" class="'.$theclass.'" onclick="if (document.getElementById(\''.$theid.'\').style.display==\'none\') { document.getElementById(\'butOf'.$theid.'\').value=\'-\';document.getElementById(\''.$theid.'\').style.display=\'block\'; document.getElementById(\'hidPosOf'.$theid.'\').value=\'1\'; } else {document.getElementById(\'butOf'.$theid.'\').value=\'+\';document.getElementById(\''.$theid.'\').style.display=\'none\';document.getElementById(\'hidPosOf'.$theid.'\').value=\'0\';}"> <br/>';	
// 	$ret .= '<a href="#AncOfTgAf'.$theid.'" onclick="document.getElementById(\''."theb".'\').value=\'P\';document.getElementById(\''.$theid.'\').style.display=\'block\'" class="'.$theclass.'" title="'.$thetitle.'">+</a>&nbsp;';
// 	$ret .= '<a href="#AncOfTgAf'.$theid.'" onclick="document.getElementById(\''.$theid.'\').style.display=\'none\'" class="'.$theclass.'" title="'.$thetitle.'">&nbsp;-&nbsp;</a><br/>';
	
	$ret .= '<div id="'.$theid.'" style="display:'.($initDisp ? "block" : "none").'">'.$thecontent.'</div>';
	return($ret);
}

function makelink($url,$obj,$title="",$class="",$target="") {
	return ('<a href="'.$url.'" '.( $target !="" ? 'target="'.$target.'" ' : ""). ( $title !="" ? 'title="'.$title.'" ' : ""). ( $class !="" ? 'class="'.$class.'" ' : "").'>'.$obj.'</a>');
}

// fonction qui echoise un champ hidden
function echochphid($NmC,$ValC,$DirEcho=true) {
    $retVal.= "<input type=\"hidden\" name=\"$NmC\" value=\"$ValC\">\n";
    if ($DirEcho) {
    	echo $retVal;
    } else {
    	return($retVal);
    }

}

// finction qui v�ifie qu'une adresse mail est valide
// ie un @, pas d'espaces et pas de retour chariots
function VerifAdMail($admail) {
         if (strstr($admail,"@") && !strstr($admail," ")  && !strstr($admail,"\n"))
                  { return (true) ;}
         else return(false);
}
function tel_format($tel) {
  $tel = str_replace(array(" ",".",","),array(),$tel);
  $tel_format = chunk_split  ( $tel  , 2 ," " );
  return $tel_format; 
}
// fonction qui convertit une chaine avec des accents en ascii pur
function cv2purascii($text,$from_enc="UTF-8") {
	setlocale(LC_CTYPE, 'fr_FR');
	return(iconv('UTF-8','ASCII//TRANSLIT', $text));
}

// fonction qui encrypte les mails en JS
function encJSmail ($admail,$DirEcho=true) {
	$retVal='
<script language="javascript">document.write(\'<a href="mailto:'.$admail.'">'.$admail.'</a>\');</script><noscript>'.str_replace("@","[/at\]",$admail).'</noscript>';
 if ($DirEcho) {
    	echo $retVal;
    } else {
    	return($retVal);
    }
}
// fonction qui affiche du HTML customis�fonction d'une chaine de car
function DispCustHT($Val2Af) {
   // si dans la chaine il y a un @, pas d'espaces ni de retour chariot, alors c'est une adressemail 
   if (VerifAdMail($Val2Af))
      {
      $Val2Af="<A HREF=\"mailto:".$Val2Af."\">".$Val2Af."</a>";
      }
  else if (strstr($Val2Af,"http://")  && !strstr($Val2Af,"\n"))
      {
      $Val2Af="<A HREF=\"".$Val2Af."\" target=\"_blank\">".$Val2Af."</a>";
      }
  else if (strstr($Val2Af,"www.")  && !strstr($Val2Af," ") && !strstr($Val2Af,"\n"))
      {
      $Val2Af="<A HREF=\"http://".$Val2Af."\" target=\"_blank\">".$Val2Af."</a>";
      }
  else {  // sinon traitement divers
      $Val2Af=ereg_replace("<","&lt;", $Val2Af);
      $Val2Af=ereg_replace(">","&gt;", $Val2Af);
      $Val2Af=ereg_replace("\n","<br/>", $Val2Af);
      $Val2Af=($Val2Af=="" ? "&nbsp;" : $Val2Af);
      }
return ($Val2Af);
}

// fonction d'effacement d'un fichier s'il existe
function delfich($ChemFich) {
  // echo "Chemin complet du fichier a effacer :$ChemFich<br/>";
  if (file_exists($ChemFich)) unlink ($ChemFich);
  }

// connection et s�ection �entuelle d'une base
function msq_conn_sel($Host,$User,$Pwd,$DB="") {
     return(db_connect($Host,$User,$Pwd,$DB)) or die ("Impossible de se connecter au serveur $Host avec le user $User, passwd: ***** ");
	}

// fonction qui effectue une requete sql, et affiche une erreur avec la requete si necessaire
function msq($req,$lnkid="",$mserridrq="") {
	return (db_query($req,$lnkid="",$mserridrq=""));
}

// compte ligne dans table
function db_count($table,$where="",$lnkid="") {
$where = ($where !="" ? " where $where ": "");
$res= db_query("select count(*) from $table $where",$lnkid);
$ret = db_fetch_row($res);
return ($ret[0]);
}

// fonction qui effectue une requ�e et renvoie toutes les lignes dans un tableau 
// les lignes sont index�s num�iquement
// les colonnes aussi
function db_qr_compres($req,$lnkid="") {
$res=db_query($req,$lnkid);
$i=0;
	$ret = false;
//if (db_num_rows($res)) { // car oci_num_rows ne fonctionne pas avec Oracle !!
	while ($rep = db_fetch_row($res)) {
		$ret[$i]=$rep;
		$i++;
		}
	return($ret);
}

// fonction qui effectue une requ�e et renvoie toutes les lignes dans un tableau 
// les lignes sont index�s num�iquement
// les colonnes sont indexe par les noms des colonnes
function db_qr_comprass($req,$lnkid="") {
$res=db_query($req,$lnkid);
$i=0;
$ret = false;
//if (db_num_rows($res)) { // car oci_num_rows ne fonctionne pas avec Oracle !!
	while ($rep=db_fetch_assoc($res)) {
		$ret[$i]=$rep;
		$i++;
		}
	return($ret);
//	}
//else return (false);
}

// fonction qui effecture une requete et renvoie la premi�e ligne de r�onse sous forme d'un tableau indic�numeriquement
function db_qr_res($req,$lnkid="") {

$res=db_query($req,$lnkid);
	$ret=db_fetch_row($res);
	if (!$ret) 	{
		$ret[0]="error or no record found";
	}
	return ($ret);
}
// fonction qui effecture une requete et renvoie la premi�e ligne de r�onse sous forme d'un tableau ASSOCIATIF
function db_qr_rass($req,$lnkid="") {
	$res=db_query($req,$lnkid);
	$ret=db_fetch_assoc($res);
	if (!$ret) {
		$ret[0]="error or no record found";
	}
	return ($ret);
}
// fonction qui transforme un tableau tb[nomchp]=valchp en instruction SQL INSERT 
function tbset2insert($set) {
foreach ($set as $chp=>$val) {
	$lchp[]=$chp;
	$vchp[]=$val;
	//$vchp[]="'$val'";
	}
return("(".implode(",",$lchp).") VALUES (".implode(",",$vchp).")");
}
// fonction qui transforme un tableau tb[nomchp]=valchp en instruction SQL SET 
function tbset2set($set) {
foreach ($set as $chp=>$val) {
	$lchp[]=$chp."=$val";
	}
return(" ".implode(",",$lchp)." ");
}
// doubles les cotes pour insert ds Oracle
function dblslashes($str) {
	return(str_replace("'","''",$str));
}

// fonction qui r�up�e un libell�dans une table fonction de la cl�// sert aussi �tester si un enregistrement existe (renvoie faux sinon)
function RecupLib($Table, $ChpCle, $ChpLib, $ValCle,$lnkid="",$wheresup="",$trace=false) {
$wheresup=($wheresup!="" ? " AND ".$wheresup : "");
$req="SELECT $ChpCle, $ChpLib FROM $CSpIC$Table$CSpIC WHERE $ChpCle='$ValCle' $wheresup";
if ($trace) echo $req;
$reqRL=db_query($req,$lnkid) or die("Requete sql de RecupLib invalide : <I>$req</I>".($lnkid=="" ? "":$lnkid));
$resRL=db_fetch_row($reqRL);
if ($resRL) {
  return($resRL[1]);
  }
else return (false);
}

// fonction qui r�up�e les champ libell�(0) ou commentaire(1) d'une table
function RecLibTable($NM_TABLE,$offs) {
global $TBDname,$NmChDT;
$req="SELECT LIBELLE,COMMENT FROM $CSpIC$TBDname$CSpIC WHERE NM_TABLE='$NM_TABLE' AND NM_CHAMP='$NmChDT'";
$reqRL=db_query($req) or die("Requete SQL de RecLibTable invalide : <I>$req</I>");
$resRL=db_fetch_row($reqRL);
return($resRL[$offs]);
}

/* fonction de traitement des champs li�
 arg1: chaine brute de liaison, arg2: valeur cherch� (optionnelle)
 la chaine de liaison comporte 2 parties:
 Nom_base,nom_serveur,nom_user,passwd;0: table, 1: champ li�(cl�; 2: ET SUIVANTS champs affich�

retourne un tableau associatif si valc="", une valeur sinon
$reqsup est utilise par DRH2 et GDP1
*/
function ttChpLink($valb0,$reqsup="",$valc=""){
//echo $reqsup;
global $DBHost,$DBUser,$DBName,$DBPass,$carsepldef,$TBDname,$maxrepld;
//$valb0=str_replace (' ','',$valb0); // enl�e espaces ind�irables
$valbrut=explode(';',$valb0);
/// en cas de modif de la syntaxe, checker aussi PYAObj
/// methode echoFilt, case LDLLV qui se sert de la chaine valb0 pour une requete imbriquée  
if (count($valbrut)>1) { // connection �une base diff�ente
  $lntable=$valbrut[1];
  $defdb=explode(',',$valbrut[0]);
  $newbase=true;
 // si user et/ou hote d'acc� �la Bdd est diff�ent, on etablit une nvlle connexion
 // on fait une nouvelle connection syst�atiquement pourt etre compatioble avec pg_sql
   //if (($defdb[1]!="" && $defdb[1]!=$DBHost)||($defdb[2]!="" && $defdb[2]!=$DBUser)) {
     $lnc=db_connect($defdb[1],$defdb[2],$defdb[3],$defdb[0]) or die ("Impossible de se connecter au serveur $defdb[1], user: $defdb[2], passwd: $defdb[3]");
	 $newconnect=true;
     //}
   //mysql_select_db($defdb[0]) or die ("Impossible d'ouvrir la base de donn�s $defdb[0].");
  }
else { //commme avant
   $lntable=$valbrut[0];
   $newbase=false;
   $newconnect=false;
   }
// 0: table, 1: champ li�(cl�; 2: ET SUIVANTS champs affich�
$defl=explode(',',$lntable);
$nbca=0; // on regarde les suivants pour construire la requete
$rcaf="";
/* si le 1er �afficher champ comporte un & au d�ut, il faut aller cherche les valeurs dans une 
table; les param�res sont  indiqu� dans les caract�istiques d'�ition de CE champ dans la table  de d�inition*/

/*if (strstr($defl[2],"&")) { // si chainage
    $nmchp=substr ($defl[2],1); // enl�e le &
       if (strstr($nmchp,"@")) { // si classement sur ce champ
         $nmchp=substr ($nmchp,1); // enl�e le @
         $orderby=" order by $nmchp ";
         }
     $rcaf=$nmchp;
     $rqvc=msq("select VALEURS from $TBDname where NM_CHAMP='$nmchp' AND NM_TABLE='$defl[0]'");
     $resvc=db_fetch_row($rqvc);
     $valbchain=$resvc[0];
    }*/
//else {
     while ($defl[$nbca+2]!="") {
       $nmchp=$defl[$nbca+2];
       $c2aff=true; // champ �afficher effectivement
       if (strstr($nmchp,"!")) { // caract�e sp�ateur d�ini
         $nmchp=explode("!",$nmchp);
       	 $tbcs[$nbca+1]=$nmchp[0]; // s�arateur avant le "!"
       	 $nmchp=$nmchp[1];
        }
       	if (strstr($nmchp,"&")) { // si chainage
   	 $nmchp=substr ($nmchp,1); // enl�e le &
		if (strstr($nmchp,"~@")) { // si classement inverse en plus sur ce champ
		$nmchp=substr ($nmchp,2); // enl�e le @
		$orderby=" order by $nmchp DESC "; 
		} elseif (strstr($nmchp,"@")) { // si classement en plus sur ce champ
		$nmchp=substr ($nmchp,1); // enl�e le @
		$orderby=" order by $nmchp "; 
		}
     	 $rqvc=db_query("select VALEURS from $TBDname where NM_CHAMP='$nmchp' AND NM_TABLE='$defl[0]'");
      	 $resvc=db_fetch_row($rqvc);
     	 $valbchain[$nbca+1]=$resvc[0];
    	}

       if (strstr($nmchp,"@@")) { // si ce champ indique un champ de structure hi�achique avec la cl�de type pid= parent id
         $cppid=substr ($nmchp,2); // enl�e le @@
	 $c2aff=false;
	 }	 
       elseif (strstr($nmchp,"~@")) { // si classement inverse sur ce champ
         $nmchp=substr ($nmchp,2); // enl�e le ~@
         $orderby=" order by $nmchp DESC"; 
        }
       elseif (strstr($nmchp,"@")) { // si classement sur ce champ
         $nmchp=substr ($nmchp,1); // enl�e le @
         $orderby=" order by $nmchp "; 
        }
	 
       if ($c2aff) {	 
       	  $rcaf=$rcaf.",".$nmchp;
       	  $tbc2a[]=$nmchp; // tableau des champs ou chercher
	  }
       $nbca++;
       } // fin boucle
       if ($cppid) $nbca=$nbca-1;  
/*}*/
 // soit on cherche 1 et 1 seule valeur, ou plusieurs : $valc est un tableau
if  ($valc!="") {
    if (is_array($valc)) {
	foreach($valc as $uval) {
		$whsl.=" $defl[1]='$uval' OR ";
	}
	$whsl=" where ".vdc($whsl,3);
    } elseif (strstr($valc,'__str2f__')) { // on cherche une chaine parmi les champs
    	$val2s=str_replace('__str2f__','',$valc);
    	foreach($tbc2a as $chp) {
    		$whsl.=" $chp LIKE '%$val2s%' OR ";
    	}
    	$whsl=" where ".vdc($whsl,3);
    	
    } else {
    	$whsl=" where $defl[1]='$valc'";
    }
    if ($reqsup!="") $whsl="(".$whsl.") AND ".$reqsup;
}
// soit la liste est limit� par une clause where suppl�entaire
else {
     $whsl= ($reqsup != "" ? "WHERE ".$reqsup : "");
     }

if ($cppid && $valc=="") { //on a une structure h�archique et plus d'une valeur �chercher
	// on cherche les parents initiaux, ie ceux dont le pid est null ou egal a la cle du meme enregistrement
	if ($reqsup!="") {
		$whreqsup=" AND $reqsup ";
	}
	$rql=msq("SELECT $defl[1] , $cppid $rcaf from $defl[0] WHERE ($cppid IS NULL OR $cppid=$defl[1] OR $cppid=0) $whreqsup $orderby");
	while ($rw=db_fetch_row($rql)) {
		if($rw[0] !="") { // si cle valide
			$resaf=tradLib($rw[2]);
			for ($k=2;$k<=$nbca;$k++) {
				$cs=($tbcs[$k]!="" ? $tbcs[$k] : $carsepldef);
				if ($valbchain[$k]!="") {
					$resaf=$resaf.$cs.ttChpLink($valbchain[$k],"",$rw[$k + 1]);
				} else $resaf=$resaf.$cs.tradLib($rw[$k +1]);
			} // boucle sur chps �entuels en plus
			$tabCorlb[$rw[0]]=$resaf;
			rettarbo($tabCorlb,$rw[0],$defl,$cppid,$rcaf,$orderby,$nbca,$tbcs,0,$whreqsup); 
			//print_r($tabCorlb);				
		} // fin si cl�valide
	} // fin boucle r�onses
	if (!is_array($tabCorlb)) { // pas de reponses
		$tabCorlb[err]="Error ! impossible construire l'arbre ";
	}
	
		
} else 	{ // pas hi�archique => normal     
	$sqln="SELECT $defl[1] $rcaf from $defl[0] $whsl $orderby LIMIT $maxrepld";
	//echo $sqln;
	$rql=msq($sqln);
	// constitution du tableau associatif �2 dim de corresp code ->lib
	//echo "<!--debug2 rql=SELECT $defl[1] $rcaf from $defl[0] $whsl $orderby <br/>-->";
	$tabCorlb=array();
	while ($resl=db_fetch_row($rql)) {
		//$cle=strtoupper($resl[0]);
		$cle=$resl[0];
		$resaf="";
		for ($k=1;$k<=$nbca;$k++) {
			$cs=($tbcs[$k]!="" ? $tbcs[$k] : ($k!=1 ? $carsepldef : ""));
			if ($valbchain[$k]!="") {
				$resaf=$resaf.$cs.ttChpLink($valbchain[$k],"",$resl[$k]);
			} else $resaf=$resaf.$cs.tradLib($resl[$k]);
		}
		$tabCorlb[$cle]=stripslashes($resaf); // tableau associatif de correspondance code -> libell�		
		//echo "<!--debug2 cle: $cle; val: $resaf ; valverif:   ".$tabCorlb[$cle]."-->\n";  
	} 
	// fin boucle sur les r�ultats
} // fin si pas hi�archique  

// retablit les param�res normaux si n��saire
if ($newconnect || $newbase) {
	db_close($lnc);
	db_connect($DBHost,$DBUser,$DBPass,$DBName);// r�uvre la session normale
	}
//if ($newbase) mysql_select_db($DBName) or die ("Impossible d'ouvrir la base de donn�s $DBName.");
if ($valc!="" && !strstr($valc,'__str2f__')) {
  if ($resaf=="") $resaf="N.C.";
  return ($resaf);
  }
else {
	return($tabCorlb); // retourne le tableau associatif
	}
}
// fonction compl�entaire r�ntrante pour la gestion hi�archique
// !! le tableau pricipal est pass�par argument !
function rettarbo(&$tabCorlb,$valcppid,$defl,$cppid,$rcaf,$orderby,$nbca,$tbcs,$niv=0,$whreqsup="") {
	global $carsepldef,$maxprof;
	//if ($niv==3) die("SELECT $defl[1],$cppid $rcaf from $defl[0] where $cppid='$valcppid' $orderby");
	$niv=$niv+1;
	if ($niv>$maxprof) {
		$tabCorlb[errprogf]="ERREUR Profond max de l'arbre ($maxprof) depassee !";
		return;
		}
	$rqra=db_query("SELECT $defl[1],$cppid $rcaf from $defl[0] where ($cppid='$valcppid' AND $defl[1]!='$valcppid') $whreqsup $orderby");
	//echo ("SELECT $defl[1],$cppid $rcaf from $defl[0] where $cppid='$valcppid' $orderby, nbrep:".db_num_rows($rqra).", niv=$niv<br/>");
	// constitution du tableau associatif �2 dim de corresp code ->lib
	while ($resra=db_fetch_row($rqra)) {
		//$cle=strtoupper($rera[0]);
		$cle=$resra[0];
		//echo "<!--debug2: $cle\n-->";
		$resaf=$resra[2];
		for ($k=2;$k<=$nbca;$k++) {
			$cs=($tbcs[$k]!="" ? $tbcs[$k] : $carsepldef);
			$resaf=$resaf.$cs.$resra[$k + 1];
			}
		$tabCorlb[$cle]=str_repeat("&nbsp;|&nbsp;&nbsp;",$niv-1)."&nbsp;|--".$resaf; // tableau associatif de correspondance code -> libell�
		rettarbo($tabCorlb,$cle,$defl,$cppid,$rcaf,$orderby,$nbca,$tbcs,$niv,$whreqsup);
	} // fin boucle sur les r�onses
	return;
}
// qq fonctions taleaau arrangées qui déclenchent une erreur si l'argument n'est pas un tableau..
function is_arr_implode($glue,$pieces) {
	if (is_array($pieces)) 	return(implode($glue,$pieces));
}

function is_arr_in_array($needle,$haystack) {
	if (is_array($haystack)) {
		return(in_array($needle,$haystack));
	} else return(false);
}

function array_concat($tb1,$tb2) {
if (!$tb2) return($tb1);
foreach ($tb1 as $k=>$v) {
	$tb3[$k]=$v;
}
foreach ($tb2 as $k=>$v) {
	$tb3[$k]=$v;
}
return($tb3);
}
// FIN ENSEMBLE DE FONCTIONS NECESSAIRES A ttChpLink
// convertit une chaine de type 0:Non demandé,1:Refusé,2:Accepté,3:A revoir en tableau de hachage
function hash_explode($string,$sep1=":",$sep2=",") {
	$tbvrac=explode($sep2,$string);
	foreach($tbvrac as $tbelem) {
		$tbe=explode($sep1,$tbelem);
		$res[$tbe[0]]=$tbe[1];
	}
	return($res);
}
// info serveur
function pinfserv() {
//  echo gethostbyaddr ("127.0.0.1");
  echo gethostbyname ("localhost");
  /*getmxrr("localhost",$mxhosts) ;
  effectue une recherche DNS pour obtenir les MX de l`h�e hostname. Retourne TRUE si des 
enregistrements sont trouv�, et FALSE si une erreur est rencontr�, ou si la recherche �houe.
La liste des enregistrements MX est plac� dans le tableau mxhosts.
	foreach ($mxhosts as $nameh)
     	{ echo $nameh." " ;} */   
}


// fonction qui retourne le type d'un champ
// Utiliser plutot la fonction ShowField qui retourne un tableau avec beaucoup plus d'infos
function mysqft ($NOMC,$NM_TABLE)
{
$resf=msq("select $NOMC from $CSpIC$NM_TABLE$CSpIC LIMIT 0");
return (db_field_type($resf,0));
}
// fonction qui retourne les flags d'un champ
// Utiliser plutot la fonction ShowField qui retourne un tableau avec beaucoup plus d'infos
function mysqff ($NOMC,$NM_TABLE)
{
$resf=msq("select $NOMC from $CSpIC$NM_TABLE$CSpIC LIMIT 0");
return (mysql_field_flags($resf,0)); 
}
// fonction qui retourne un tableau de hachage des caracteristiques d'un champ
function ShowField($NOMC,$NM_TABLE) {
$table_def = msq("SHOW FIELDS FROM $CSpIC$NM_TABLE$CSpIC LIKE '$NOMC'");
return (mysql_fetch_array($table_def));
}

// retourne une liste déroulante et une boite texte
function DispLDandTxt ($tbval,$nmC,$valC="",$DirEcho=true,$idC="") {
global $VSLD;
if ($idC=="") $idC=$nmC;
$kex = false;
foreach ($tbval as $k=>$v) {
	if ($k == $valC) {
		$tbval[$k] = $VSLD.$v;
		$kex = true;
	}
}
$tbval = array('OTH' => (!$kex ? $VSLD : "").($_SESSION["NoLang"] >  0 ? "Other" : "Autre")) + $tbval;
// CheckLDandTxt est dans php_inc/jQuery/shared_inc.js.php
$retVal .= str_replace("<SELECT",'<SELECT onchange="CheckLDandTxt(\''.$idC.'\');"',DispLD($tbval,"assLD4Txt".$idC,"no","LDF",false));
if ($kex) {
	$type = 'hidden';
} else $type = 'text';
$retVal .= '<input type="'.$type.'" value="'.$valC.'" name="'.$nmC.'" id="'.$idC.'">';
if ($DirEcho) { echo $retVal; } else return $retVal;
}
// DispLD fonction qui affiche une liste deroulante, ou des boutons radio ou cases a cocher
// ceci fonction du nombre de valeurs specifie  dans la variable globale $nValRadLd
// les valeurs selectionnées sont precedées  de la chaine $VSLD
// arguments :
// - un tableau associatif clé>valeur
// - le nom du controle
// - s'il est multiple ou non (non par défaut)
// - 4ème argument (optionel) force  les cases à cocher ou boutons radio (=RAD) ou liste d�oulante (=LDF) qqsoit le nbre de valeur
// - DirEcho: true: echo la liste ;; sinon renvoie la chaine de caractere 
// idc= valeur de l'id au sens du terme
function DispLD($tbval,$nmC,$Mult="no",$Fccr="",$DirEcho=true,$idC="") {
global $nValRadLd,$VSLD,$SzLDM,$DispMsg;
if ($idC=="") $idC=$nmC;
if (count($tbval)==0) {
   if ($DispMsg) $retVal.= "<h6>Aucune liste de valeurs disponible <br/></h6>";
   $retVal.= "<INPUT TYPE=\"hidden\" ID=\"".$idC."\"  name=\"".$nmC.($Mult!="no" ? "[]" : "")."\" value=\"\">";
   }
elseif ((count($tbval)>$nValRadLd && $Fccr=="") || $Fccr=="LDF") { 
// liste déroulante: nbre val suffisantes et pas de forcage 
   if ($Mult!="no") $title = "Appuyez sur la touche Ctrl pour s&eacute;lectionner plusieurs valeurs";
  $retVal.= "<SELECT ondblclick=\"document.theform.submit();\" TITLE=\"$title\" ID=\"".$idC."\" NAME=\"".$nmC;
  $SizeLDM=min($SzLDM,count($tbval));
  $retVal.= ($Mult!="no" ? "[]\" MULTIPLE=\"MULTIPLE\" SIZE=\"$SizeLDM\">" : "\">");
  foreach ($tbval as $key =>$val) {
    $retVal.= "<OPTION VALUE=\"$key\" ";
    $niv=count(explode("|",$val));
    $retVal.=' class="optld'.$niv.'" ';
    if (strstr($val,$VSLD)) {
      $sel=' selected="selected" ';
      $val=str_replace ($VSLD, "", $val); // retourne la chaine ss le car de s�ection
      }
    else $sel="";
    $retVal.= $sel.">$val</OPTION>";
    } // fin boucle sur les valeurs
  $retVal.= "</SELECT>";
  //$retVal.= (($Mult!="no" && $DispMsg) ? "<br/><small>Appuyez sur Ctrl pour s&eacute;lectionner plusieurs valeurs</small>" : "");
  } // fin liste deroulante
else if ($Mult!="no" && !stristr($Fccr,"RAD") ) // cases �cocher si multiple ou pas de for�ge en radio
  { 
  foreach ($tbval as $key =>$val) {
    if ($key!="") {
      $retVal.= "<INPUT TYPE=\"CHECKBOX\" NAME=\"".$nmC."[]\" VALUE=\"$key\" ";
      if (strstr($val,$VSLD)) {
        //$sel="checked";
        $sel=' checked="checked" '; // XHTML
        $val=str_replace ($VSLD, "", $val); // retourne la chaine ss le car de s�ection
        }
      else $sel="";
      $retVal.= $sel.">".$val;
      $retVal.= (stristr($Fccr,"BR") ? "<br/>" : " &nbsp;&nbsp;");
      } // fin si valeur non nulle    
    } // fin boucle sur les valeurs
  } // fin cases �cocher
else {// boutons radio
  foreach ($tbval as $key =>$val) {
    $retVal.= "<INPUT TYPE=\"RADIO\" NAME=\"$nmC\"".($Mult!="no" ? "[]" :"" )." VALUE=\"$key\" ";
    if (strstr($val,$VSLD)) {
      //$sel="checked";
      $sel=' checked="checked" '; // XHTML
      $val=str_replace ($VSLD, "", $val); // retourne la chaine ss le car de s�ection
      }
    else $sel="";
    $retVal.= $sel.">".$val;
    $retVal.= (stristr($Fccr,"BR") ? "<br/>" : " &nbsp;&nbsp;");
    } // fin boucle sur les valeurs
  }// fin boutons radio
  if ($DirEcho) {
 	echo $retVal;
  } else {
    	return($retVal);
  }
} // fin fonction

/// fonction utilisée pour le multilinguisme
// les lib contiennent liblang0£liblang1£liblang3 ...
// le n° de langue est stocké dans la var de session $_SESSION['NoLang']
function tradLib($lib) {
	if (isset($_SESSION['NoLang']) && strstr($lib,"£")) {
		$tblib = explode("£",$lib);
		if ($tblib[$_SESSION['NoLang']] !="") {
			return($tblib[$_SESSION['NoLang']]);
		} else return($lib);
	} else return($lib);
}
// fonction qui efface une variable de session si elle existe
// et la d�ruit par d�aut
function unregvar($var,$annvar=true) {
global $$var;
//if (isset($var)) {
  session_unregister($var);
  $_SESSION[$var] = null;
  unset($_SESSION[$var]);
  if ($annvar) {
  	$$var = null;
	$_SESSION[$var] = null;
  	unset($$var); // d�ruit par d�aut ensuite
  	}
//  }
}

// fonction qui met les bonnes balises Javascript
function outJS($myjs) {
return('
<script type="text/javascript">
	/*<![CDATA[*/
<!--
'.$myjs.'
// -->
	/*]]>*/
</script>
');
}

// fonction JAVASCRIPT qui remplace un caract�e a par b dans une chaine
// le js est a mettre dans le onclick plutot que dans le href, sinon on voit tout dans la barre d'�at
function JSstr_replace() {
?>
<script type="text/javascript">
	/*<![CDATA[*/
<!--
function str_replace(a,b,expr) {
      var i=0
      while (i!=-1) {
         i=expr.indexOf(a,i);
         if (i>=0) {
            expr=expr.substring(0,i)+b+expr.substring(i+a.length);
            i+=b.length;
         }
      }
      return expr
   }
// -->
	/*]]>*/
</script>
<?
}

// fonction qui permet de rentrer de prot�er un lien par une boite JS ou il faut rentrer un mot de passe
// le js est a mettre dans le onclick plutot que dans le href, sinon on voit tout dans la barre d'�at
// ce n'est biensur pas tres secure, mais bon on est pas encore chez Sarko donc ca va...
function JSprotectlnk() {
?>
<script type="text/javascript">
	/*<![CDATA[*/
<!--
function protectlnk(url,passwd,message) {
	if (passwd==prompt(message,'')) {
	   location=url;}
	else alert ('Mot de passe incorrect');
}
// -->
	/*]]>*/
</script><?
}
// colle le code javascript d'ouverture d'une popup
function JSpopup($wdth=500,$hght=400,$nmtarget="Intlpopup",$DirEcho=true) {
global $HTTP_HOST;
$HostName=($HTTP_HOST=="" ? $_SERVER["HTTP_HOST"] : $HTTP_HOST); // because diff�entes versions
// on change le nom de target des popups internet (externes) pour ne pas foutre la merde dans les popups ouvertes sur l'intranet
$nmtarget=(strstr($HostName,"haras-nationaux.fr")!=false ? "Ext".$nmtarget : $nmtarget);
$ret='
<script type="text/javascript">
	/*<![CDATA[*/
<!--
// ouverture d\'une Popup
var oPopupWin; // stockage du handle de la popup
function popup(page, width, height) {
    NavVer=navigator.appVersion;
	HostName=\''.$HostName.'\' // sert au debogage;
    NavVer=navigator.appVersion;
    if (NavVer.indexOf(\'MSIE 5.5\',0) >0  ) {
        var undefined;
        undefined=\'\';
        }
  closepop();
  if (width==undefined)
  width='.$wdth.';
  if (height==undefined)
  height='.$hght.';
    oPopupWin = window.open(page, \''.$nmtarget.'\', "alwaysRaised=1,dependent=1,height=" + height + ",location=0,menubar=0,personalbar=0,scrollbars=1,status=0,toolbar=0,width=" + width + ",resizable=1");
	oPopupWin.focus();
	// valeur de retour diff�ente suivant navigateur (merdique a souhait) !!!
	var bAgent = window.navigator.userAgent;
	var bAppName = window.navigator.appName;
	if ((bAppName.indexOf("Explorer") >= 0) && (bAgent.indexOf("Mozilla/3") >= 0) && (bAgent.indexOf("Mac") >= 0))
		return true; // dont follow link
	else return false; // dont follow link
	//return !oPopupWin;

}
function closepop() {
    if (oPopupWin) {
        var tmp;
        // Make sure oPopupWin is empty before
        // calling .close() or we could throw an
        // exception and never set it to null.
        tmp = oPopupWin;
        oPopupWin = null;
        // Only works in IE...  Netscape crashes
        // if you have previously closed it by hand
        tmp.close();
        //if (navigator.appName != "Netscape") tmp.close();
      }

}
// -->
	/*]]>*/
</script>
';
if ($DirEcho) {
	echo $ret;
} else return $ret;
}
/* colle le code javascript d'ouverture d'une popup Loupe de photo qui se redimensionne automatiquement
Utilisation: appel de cette fonction en php au d�ut du fichier dans l'entete <HEAD> pas ex
<? JSPopLoup();?>
ensuite: lien du type <a href="#" onclick="poploup(image_avec_chemin_relatif,titre,commentaire)">
A noter que le chemin relatif de l'image est donn�par rapport au fichier appelant (comme pour une image normale)
*/
function JSPopLoup($nmtarget="Intlpopup") {
// pour assurer compat. avec vieilles versions de php
$doc_root_vm=($_SERVER["DOCUMENT_ROOT"]=="" ? "/home/httpd/html" : $_SERVER["DOCUMENT_ROOT"]);
// on calcule le chemin du fichier appeleant pour pouvoir utiliser des liens relatifs
// i.e. on enl�e du chemin absolu (getcwd) la racine du serveur
$chemcour=str_replace ( $doc_root_vm,"" , getcwd());
//echo "test chemin:".getcwd()."<br/>";
?>
<script type="text/javascript">
	/*<![CDATA[*/
<!--
// ouverture d'une Popup Loupe auto redimensionnante
var oPopupWin; // stockage du handle de la popup
function poploup(image,titre,commentaire) {
    NavVer=navigator.appVersion;
    if (NavVer.indexOf("MSIE 5.5",0) == -1 && NavVer.indexOf("MSIE 6.",0) == -1) {
        var undefined;
        undefined='';
        }

    var tmp; // issu d'un copier/coller antediluvien
    if (oPopupWin) {
        // Make sure oPopupWin is empty before
        // calling .close() or we could throw an
        // exception and never set it to null.
        tmp = oPopupWin;
        oPopupWin = null;
        // Only works in IE...  Netscape crashes
        // if you have previously closed it by hand
        if (navigator.appName != "Netscape") tmp.close();
      }
  
    oPopupWin = window.open("", "<?=$nmtarget?>", "alwaysRaised=1,dependent=1,height=200,location=0,menubar=0,personalbar=0,scrollbars=no,status=0,toolbar=0,width=200,resizable=1");    
	oPopupWin.document.open();
	if (titre=="") {titre="Loupe";}
	oPopupWin.document.write("<HTML><HEAD><TITLE>"+titre+"</TITLE></HEAD>\n<BODY>\n");
	oPopupWin.document.write("<CENTER>\n");
	oPopupWin.document.write("<IMG SRC=\"<?=$chemcour?>/" + image+"\"><br/>\n");
	if (commentaire!="") {oPopupWin.document.write("<small><I>"+commentaire+"</I></small><br/>\n");}
	oPopupWin.document.write("<br/><a href=\"javascript:self.close()\" ><IMG SRC=\"/hn0700/partage/IMAGES/fermer.gif\" border=\"0\"></a>\n");
	oPopupWin.document.write("</CENTER>\n"); 
	oPopupWin.document.write("<script language=\"JavaScript\">\n");
	// la fonction d'ajustement n'est pas appel� directement, mais toutes les 5 sec pour laisser
	// le temps aux images de se charger ;-)
	oPopupWin.document.write("function ajuste() {\n");
	//oPopupWin.document.write("alert('coucou');"); DEBUG
   oPopupWin.document.write("var H = document.body.scrollHeight+50;\n");
	oPopupWin.document.write("var W = document.body.scrollWidth+30;\n");
	oPopupWin.document.write("var SH = screen.height;\n");
	oPopupWin.document.write("var SW = screen.width;\n");
	oPopupWin.document.write("window.moveTo((SW-W)/2,(SH-H)/2);\n");
	oPopupWin.document.write("window.resizeTo(W,H);\n");
	oPopupWin.document.write("} \najuste();"); // appel au premier coup
	oPopupWin.document.write(" \nsetTimeout(\"ajuste()\",2000);");
		oPopupWin.document.write("</sc"+"r"+"ipt>\n"); // astuce sinon � arrete le script courant 
	oPopupWin.document.write("</bo"+"d"+"y></HT"+"M"+"L>\n"); // idem
	oPopupWin.document.close();
	oPopupWin.focus();    
	return !oPopupWin;
}
// -->
	/*]]>*/
</script>
<?
}
// Fonction de definition de condition
// appel� pour les def de liste
 function SetCond ($TypF,$ValF,$NegF,$NomChp,$typChpNum=false) {
 
 if ($ValF!=NULL && $ValF!="%") {
    switch ($TypF) { // switch sur type de filtrage
      case "EGAL" : // special
        $ValF=trim($ValF);
        $cond=$typChpNum ? "$NomChp = $ValF" : "$NomChp = '".$ValF."'";
        break;

      case "INPLIKE" : // boite d'entr�
        $ValF=trim($ValF);
        if (substr($ValF,-1,1)!="%" && !$typChpNum) $ValF.="%";
        $cond=$typChpNum ? "$NomChp = $ValF" : "$NomChp LIKE '".$ValF."'";
        break;

      case "LDM" : // liste �choix multiples de valeurs ds ce cas la valeur est un tableau
                 // la condition r�ultante est omChp LIKE '%Val1%' or NomChp LIKE '%Val2%' etc ...
        if (is_array($ValF)) {  // teste Valf est un tabelau
           foreach ($ValF as $valf) {
             if ($valf=="%" || $valf=="000") {
                $cond="";
                break; // pas de condition s'il y a %
                }
             else
                $cond.=$typChpNum ? "$NomChp = $valf OR " : "$NomChp LIKE '%".addslashes($valf)."%' OR "; // on av vire les % puis les a remis
             }
           if ($cond!="") $cond="(".substr($cond,0,strlen($cond)-4).")"; // vire le dernier OR
                                                          // et rajoute () !!
           } // si ValF pas tableau
        else {
        	if ($ValF=="%" || $ValF=="000") {
        		$cond="";
        	} else {
        		if (!$typChpNum) $gi="'";
        		$cond="($NomChp = $gi$ValF$gi)";
        	}
        }
        break;
        
      case "LDMEG" : // liste �choix multiples de valeurs ds ce cas la valeur est un tableau
       // la condition r�ultante est un NomChp ='Val1' or NomChp ='Val2' etc ...

        if (is_array($ValF)) {  // teste Valf est un tabelau
           foreach ($ValF as $valf) {
             if ($valf=="%" || $valf=="000") {
             	// ya un bug avec les enum qui contiennent '0'; arrive pas à le résoudre
       		//if ($NomChp == "ECR_POINTAGE") { echo "$NomChp , the val ($TypF) : " ; print_r($ValF);}
                $cond="";
                break; // pas de condition s'il y a %
                }
             else
                $cond.=$typChpNum ? "$NomChp = $valf OR " : "$NomChp='".addslashes($valf)."' OR ";
             }
           if ($cond!="") $cond="(".substr($cond,0,strlen($cond)-4).")"; // vire le dernier OR  
	   // et rajoute () !!          
	   } // si ValF pas tableau
        else {
        	if ($ValF=="%" || $ValF=="000") {
        		$cond="";
        	} else {
        		if (!$typChpNum) $gi="'";
        		$cond="($NomChp = $gi$ValF$gi)";
        	}
        }

        break;
// special pour liaison multiple: solutionne le pb qui fait que typo met pas les , au debut et a la fin
	case "LDM_SPL":
        if (is_array($ValF)) {  // teste Valf est un tabelau
           foreach ($ValF as $valf) {
             if ($valf=="%" || $valf=="000") {
                $cond="";
                break; // pas de condition s'il y a %
                }
             else
                $cond.="($NomChp LIKE '%,".addslashes($valf).",%' OR $NomChp LIKE '".addslashes($valf).",%' OR $NomChp LIKE '%,".addslashes($valf)."' OR $NomChp='".addslashes($valf)."') OR "; 
             }
           if ($cond!="") $cond="(".substr($cond,0,strlen($cond)-4).")"; // vire le dernier OR
                                                          // et rajoute () !!
           } // si ValF pas tableau
        else $cond="";

	break;
      case "DANT" : // date ant�ieure �      
      case "DPOST" : // date ant�ieure 
      if ($ValF=="%" || $ValF=="") break; // pas de condition
        $oprq=($TypF=="DANT" ? "<=" : ">="); // calcul de l'op�ateur
        if ($typChpNum) { // alors c un tstamp
        	$cond="$NomChp $oprq ".DateF2tstamp($ValF)."";
        } else
        	$cond="$NomChp $oprq '".DateA($ValF)."'";
        break;

      case "DATAP" : // date inf et sup
        if ($ValF[0]!="%" && $ValF[0]!="") {
        	 if ($typChpNum) { // alors c un tstamp
        	 	$cond="$NomChp >= ".DateF2tstamp($ValF[0])."";
        	 } else 
        		$cond="$NomChp >= '".DateA($ValF[0])."'";
        }

        if ($ValF[1]!="%" && $ValF[1]!="") {
           $cond=($cond=="" ? "" : $cond." AND ");
           if ($typChpNum) { // alors c un tstamp
        	 	$cond="$NomChp <= ".DateF2tstamp($ValF[1])."";
        	 } else 
        		$cond.="$NomChp <= '".DateA($ValF[1])."'";
           }
        break;
         
      case "VINF" : // inf
      case "VSUP" : // sup 
      if ($ValF=="%" || $ValF=="") break; // pas de condition
        $oprq=($TypF=="VINF" ? "<=" : ">="); // calcul de l'op�ateur
        if ($typChpNum) { //
        	$cond="$NomChp $oprq $ValF";
        } else // marche avec alpha
        	$cond="$NomChp $oprq '".$ValF."'";
        break;

      case "VIS" : // inf et sup
        if ($ValF[0]!="%" && $ValF[0]!="") {
        	 if ($typChpNum) { // alors c un tstamp
        	 	$cond="$NomChp >= ".$ValF[0]."";
        	 } else // marche avec alpha
        		$cond="$NomChp >= '".$ValF[0]."'";
        }

        if ($ValF[1]!="%" && $ValF[1]!="") {
           $cond=($cond=="" ? "" : $cond." AND ");
           if ($typChpNum) { //
        	 	$cond.="$NomChp <= ".$ValF[1]."";
        	 } else 
        		$cond.="$NomChp <= '".$ValF[1]."'";
           }
        break;

      case "NOTNUL" : // inf et sup
      	$cond = "(".$NomChp ." IS NOT NULL AND ".($typChpNum ? "$NomChp!=0" : "$NomChp!=''"). ") ";
        
      break;
      
      default :
        $cond="";
        break;
      } // fin switch
  } // fin ValF a une valeur coh�ente
  else $cond="";


  if ($cond!="" && $NegF!="") $cond="NOT(".$cond.")"; // negationne �entuellement
  return($cond);
} // fin fonction SteCond

// fonction qui renvoie un tableau de chaine contenant des couples Libell�"|:".valeurs
// si valeur significative
// fonction d'une requete, le tout �ant d�endant de PYA biensur..
function RTbVChPO($req,$dbname="",$DirEcho=false) {
	$TbObj=InitPOReq($req,$dbname);
	foreach ($TbObj as $PO) {
		$PO->TypEdit="C";
		$PO->DirEcho=$DirEcho;
		if ($PO->ValChp !="" && $PO->ValChp !="NULL") $TbVO[$PO->NmChamp]=$PO->Libelle.":|".$PO->EchoEditAll(false);
	}
	return($TbVO);
}

// fonction renvoyant un tableau d'objets PYA initialis� en fonction d'une simple requ� SQL
// les objets sont initialis� �partir des noms de champs et des noms de base du resultat
function InitPOReq($req,$Base="",$DirEcho=true,$TypEdit="",$limit=1,$co_user="") {
global $debug, $DBName;
  if ($Base=="") $Base=$DBName;
  $resreq=msq($req.($limit==1 ? " limit 1 " : ($limit!="no" ? " limit $limit " : "")));
  if ($limit==1) {
  	$tbValChp = db_fetch_array($resreq); // tableau des valeurs de l'enregistrement
  } else {
  	$CIL['db_num_rows'] = db_num_rows($resreq);
  	$CIL['db_resreq'] = $resreq;
	if ($CIL['db_num_rows']== 0 && !($_SESSION['db_type'] == "oracle")) return (false); // le oci_num_rows ne fonctionne pas avec Oracle !!
  }
  
//  print_r($tbValChp);
  for ($i=0;$i<db_num_fields($resreq);$i++) {
      $NmChamp=db_field_name($resreq,$i);
      $NTBL=db_field_table($resreq,$i);
      $CIL[$NmChamp]=new PYAobj(); // nouvel objet
      $CIL[$NmChamp]->NmBase=$Base;
      $CIL[$NmChamp]->NmTable=$NTBL;
      $CIL[$NmChamp]->NmChamp=$NmChamp;
      $CIL[$NmChamp]->TypEdit=$TypEdit;
      if (!preg_match("/sum|count|min|max|avg/i",$CIL[$NmChamp]->NmChamp)) { // requetes custom
      	$CIL[$NmChamp]->InitPO();
      } else {
      	 $CIL[$NmChamp]->Libelle = $NmChamp;
      }
      if ($DirEcho!=true) $CIL[$NmChamp]->DirEcho=false;
      if ($TypEdit!="N" && $TypEdit!="") $CIL[$NmChamp]->ValChp=$tbValChp[$NmChamp];
      if ($co_user!="" && $TypEdit!="C") $CIL[$NmChamp]->InitAvMaj($co_user);
	$strdbgIPOR.=$NmChamp.", ";
    } // fin boucle sur les champs du r�ultat
  if ($debug) echo("Champs traites par la fct InitPOReq :".$strdbgIPOR."<br/>\n");
  return($CIL);
}

// fonction renvoyant un tableau d'objets PYA initialis� d'une table
function InitPOTable($table,$Base="",$DirEcho=true,$TypEdit="",$co_user="") {
	global $debug, $DBName;
  	if ($Base=="") $Base=$DBName;
	$reqt = db_qr_comprass("select NM_CHAMP FROM DESC_TABLES where NM_TABLE='$table' AND NM_CHAMP!='TABLE0COMM' ORDER BY ORDAFF");
	if (!$reqt) { 
		return(false);
	} else {
		foreach ($reqt as $chp) {
			$CIL[$chp['NM_CHAMP']]=new PYAobj(); // nouvel objet
			$CIL[$chp['NM_CHAMP']]->NmBase=$Base;
			$CIL[$chp['NM_CHAMP']]->NmTable=$table;
			$CIL[$chp['NM_CHAMP']]->NmChamp=$chp['NM_CHAMP'];
			$CIL[$chp['NM_CHAMP']]->TypEdit=$TypEdit;
			$CIL[$chp['NM_CHAMP']]->InitPO();
			if ($DirEcho!=true) $CIL[$chp['NM_CHAMP']]->DirEcho=false;
			if ($co_user!="" && $TypEdit!="C") $CIL[$chp['NM_CHAMP']]->InitAvMaj($co_user);
		}
		return($CIL);
	}
}

// fonction envoi de mail text+HTML, pomp�sur nexen et bricol�...
function mail_html($destinataire, $sujet , $messhtml,  $from, $encod="iso-8859-1")
{
$limite = "_parties_".md5 (uniqid (rand()));

$entete = "Reply-to: $from\n";
$entete .= "From:$from\n"; 
$entete .="Date:" . date("D, d M Y H:i:s")."\n"; 
//$entete .= "Date: ".date("l j F Y, G:i")."\n"; // sert certainement a rien et fout la merde
$entete .= "MIME-Version: 1.0\n";
$entete .= "Content-Type: multipart/alternative;\n";
$entete .= " boundary=\"----=$limite\"\n\n";

//Le message en texte simple pour les navigateurs qui
//n'acceptent pas le HTML
$texte_simple = "This is a multi-part message in MIME format.\n";
$texte_simple .= "Ceci est un message est au format MIME.\n";
$texte_simple .= "------=$limite\n";
$texte_simple .= "Content-Type: text/plain; charset=\"$encod\"\r\n";
$texte_simple .= "Content-Transfer-Encoding: 8bit\n\n";
//$texte_simple .=  "Procurez-vous un client de messagerie qui sait afficher le HTML !!";
$texte_simple .=  strip_tags(eregi_replace("<br/>", "\n", $messhtml)) ;
$texte_simple .= "\n\n";

//le message en html original
$texte_html = "------=$limite\n";
$texte_html .= "Content-Type: text/html; charset=\"$encod\"\r\n";
$texte_html .= "Content-Transfer-Encoding: 8bit\n\n";
$texte_html .= $messhtml;
$texte_html .= "\n\n\n------=$limite--\n";

return mail($destinataire, $sujet, $texte_simple.$texte_html, $entete);
}

// envoi de mail avec pi�e jointe
// pour l'instant utilis�seulement pour les messages anti-spam
function mail_fj($destinataire,$sujet,$message,$from,$file,$contentType) {
//----------------------------------
// Construction de l'ent�e
//----------------------------------
// On choisi g��alement de construire une fronti�e g��� aleatoirement
// comme suit. (REM: je n'en connais pas la raison profonde)
$boundary = "-----=".md5(uniqid(rand()));

// Ici, on construit un ent�e contenant les informations
// minimales requises.
// Version du format MIME utilis�
$header = "MIME-Version: 1.0\r\n";
// Type de contenu. Ici plusieurs parties de type different "multipart/mixed"
// Avec un fronti�e d�inie par $boundary
$header .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
$header .= "\r\n";

//--------------------------------------------------
// Construction du message proprement dit
//--------------------------------------------------

// Pour le cas, o le logiciel de mail du destinataire
// n'est pas capable de lire le format MIME de cette version
// Il est de bon ton de l'en informer
// REM: Ce message n'appara� pas pour les logiciels sachant lire ce format
$msg = "Je vous informe que ceci est un message au format MIME 1.0 multipart/mixed.\r\n";

//---------------------------------
// 1�e partie du message
// Le texte
//---------------------------------
// Chaque partie du message est s�ar�par une fronti�e
$msg .= "--$boundary\r\n";

// Et pour chaque partie on en indique le type
$msg .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
// Et comment il sera cod�
$msg .= "Content-Transfer-Encoding:8bit\r\n";
// Il est indispensable d'introduire une ligne vide entre l'ent�e et le texte
$msg .= "\r\n";
// Enfin, on peut �rire le texte de la 1�e partie

$msg .= $message."\r\n";
$msg .= "\r\n";

//---------------------------------
// 2nde partie du message
// Le fichier
//---------------------------------
// Tout d'abord lire le contenu du fichier
// le chenmin du fichier est relatif au script appelant cette fonction

if ($file!="" && file_exists($file)) { // si fichier est sp�ifi�et existe ....

// 	$fp = fopen($file, "rb");   // b c'est pour les windowsiens
// 	$attachment = fread($fp, filesize($file));
// 	fclose($fp);
	
	$attachment=file_get_contents($file);
	
	// puis convertir le contenu du fichier en une cha�e de caract�e
	// certe totalement illisible mais sans caract�es exotiques
	// et avec des retours �la ligne tout les 76 caract�es
	// pour �re conforme au format RFC 2045
	$attachment = chunk_split(base64_encode($attachment));
	$file = basename($file);
	// Ne pas oublier que chaque partie du message est s�ar�par une fronti�e
	$msg .= "--$boundary\r\n";
	// Et pour chaque partie on en indique le type
	$msg .= "Content-Type: $contentType; name=\"$file\"\r\n";
	//$msg .= "Content-Type: text/html; name=\"$file\"\r\n";
	// Et comment il sera cod�	
	$msg .= "Content-Transfer-Encoding: base64\r\n";
	// Petit plus pour les fichiers joints
	// Il est possible de demander �ce que le fichier
	// soit si possible affich�dans le corps du mail
//	$msg .= "Content-Disposition: inline; filename=\"$file\"\r\n";
	$msg .= "Content-Disposition: attachment; filename=\"$file\"\r\n";
	// Il est indispensable d'introduire une ligne vide entre l'ent�e et le texte
	$msg .= "\r\n";
	// C'est ici que l'on ins�e le code du fichier lu
	$msg .= $attachment . "\r\n";
	$msg .= "\r\n\r\n";
	
	// voil� on indique la fin par une nouvelle fronti�e
	$msg .= "--$boundary--\r\n";
} 
else { // le fichier attach�n'a pas ��trouv�	$msg.="Le fichier $file qui devait etre attach��ce ce message n\'a pas  ��trouv�;
	die("fichier $file introuvable; impossible d'envoyer le mail");
}

return mail($destinataire, $sujet, $msg,"Reply-to: $from\r\nFrom: $from\r\n".$header);
}
?>