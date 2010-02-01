<? /*

Fonctions Utiles à l'environnement PYA et PYAObj
*/
// Fonction de definition de condition
// appelé par les listes qui récupères des critères générés par EchoFilt
function SetCond ($TypF,$ValF,$NegF,$NomChp,$typChpNum=false) {
 
 if ($ValF!=NULL && $ValF!="%") {
 	if (!is_array($ValF)) $ValF = $typChpNum ? ($ValF + 0) : addslashes($ValF);
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

// fonction qui renvoie un tableau de chaines contenant des couples Libellé.":|".valeurs
// si valeur significative
// fonction d'une requete, le tout étant dépendant de PYA biensur..
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
// $ignorErrInitPO : si true ne renvoie pas d'erreur si champ non trouvé, fait comme pr les req custom
// $hashwnmtb : false, l'indice du tableau de hasch est le NomChamp, true c'est NomTable|NomChamp
function InitPOReq($req,$Base="",$DirEcho=true,$TypEdit="",$limit=1,$co_user="",$ignorErrInitPO=false,$hashwnmtb=false) {
global $debug, $DBName;
  if ($Base=="") $Base=$DBName;
  $resreq = db_query($req.($limit==1 ? " limit 1 " : ($limit!="no" ? " limit $limit " : "")));
  if ($limit==1) {
  	$tbValChp = db_fetch_array($resreq); // tableau des valeurs de l'enregistrement
	if ($CIL['db_num_rows']== 0 && !($_SESSION['db_type'] == "oracle")) return (false); // le oci_num_rows ne fonctionne pas avec Oracle !!
  } else {
  	$CIL['db_num_rows'] = db_num_rows($resreq);
  	$CIL['db_resreq'] = $resreq;
  }
  
//  print_r($tbValChp);
  for ($i=0;$i<db_num_fields($resreq);$i++) {
      $NmChamp=db_field_name($resreq,$i);
      $NTBL=db_field_table($resreq,$i);
	$NmChp4hash = $hashwnmtb ? $NTBL."|".$NmChamp : $NmChamp;
      $CIL[$NmChp4hash]=new PYAobj(); // nouvel objet
      $CIL[$NmChp4hash]->NmBase=$Base;
      $CIL[$NmChp4hash]->NmTable=$NTBL;
      $CIL[$NmChp4hash]->NmChamp=$NmChamp;
      $CIL[$NmChp4hash]->TypEdit=$TypEdit;
 	// requetes custom : initialise pas le PO si mot clé ou nom de champ est un entier
	//echo  $CIL[$NmChp4hash]->NmChamp.":".preg_match("/^[0-9]+$/",$CIL[$NmChp4hash]->NmChamp)."<br/>";
      if (!(preg_match("/sum\(|count\(|min\(|max\(|avg\(/i",$CIL[$NmChp4hash]->NmChamp) || preg_match("/^[0-9]+$/",$CIL[$NmChp4hash]->NmChamp))) {
		$CIL[$NmChp4hash]->retBooInitPO = $ignorErrInitPO;
      	$rpo = $CIL[$NmChp4hash]->InitPO();
		if (!$rpo) $CIL[$NmChp4hash]->Libelle = $NmChp4hash; // si champ pas trouvé (table non définie) on garde son libellé
      } else {
      	 $CIL[$NmChp4hash]->Libelle = $NmChp4hash;
      }
      if ($DirEcho!=true) $CIL[$NmChp4hash]->DirEcho=false;
      if ($TypEdit!="N" && $TypEdit!="" && $limit==1) {
      	$CIL[$NmChp4hash]->ValChp=$tbValChp[$NmChamp];
      	//echo $NmChamp."->".$tbValChp[$NmChamp];
      }
      if ($co_user!="" && $TypEdit!="C") $CIL[$NmChp4hash]->InitAvMaj($co_user);
	$strdbgIPOR.=$NmChp4hash.", ";
    } // fin boucle sur les champs du r�ultat
  if ($debug) echo("Champs traites par la fct InitPOReq :".$strdbgIPOR."<br/>\n");
  return($CIL);
}

// fonction renvoyant un tableau d'objets PYA initialisé d'une table
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

// fonction qui met à jour l'enregistrement d'une table; inclut la MAJ des fichiers
function PYATableMAJ($DB,$table,$typedit,$tbKeys=array()) {
	// construction du set, necessite uniquement le nom du champ ..
	$rq1=db_query("SELECT * from DESC_TABLES where NM_TABLE='$table' AND NM_CHAMP!='TABLE0COMM' AND (TYPEAFF!='HID' OR ( TT_PDTMAJ!='' AND TT_PDTMAJ!= NULL)) ORDER BY ORDAFF, LIBELLE");

	$key = implode("_",$tbKeys)."_";
	$PYAoMAJ=new PYAobj();
	$PYAoMAJ->NmBase=$DB;
	$PYAoMAJ->NmTable=$table;
	$PYAoMAJ->TypEdit=$typedit;
	if (MaxFileSize>0) $PYAoMAJ->MaxFSize=MaxFileSize;

	$tbset = array();
	$tbWhK = array();
	while ($res1 = db_fetch_array($rq1)) {
		$NOMC = $res1['NM_CHAMP']; // nom variable=nom du champ
		$PYAoMAJ->NmChamp = $NOMC;
		$PYAoMAJ->InitPO($_REQUEST[$NOMC],$res1); // init l'objet, sa valeur, lui passe le tableau d'infos du champ pr éviter une requete suppl.
		if (array_key_exists($NOMC,$tbKeys)) {
			$tbWhK = array_merge($tbWhK,$PYAoMAJ->RetSet($key."_",true));
		} 
		$PYAoMAJ->ValChp=$_REQUEST[$NOMC]; // sinon en new ca bugue
		if ($PYAoMAJ->TypeAff=="FICFOT") {
			if ($_FILES[$NOMC]['name']!="" && $_FILES[$NOMC]['error']!="0") {
				$error=$_FILES[$NOMC]['error'];
				$err_lbl="Erreur sur le champ $NOMC de type http";
			}
			$PYAoMAJ->ValChp=($_FILES[$NOMC]['tmp_name']!="" ? $_FILES[$NOMC]['tmp_name'] : $PYAoMAJ->ValChp);
			$PYAoMAJ->Fok=$_REQUEST["Fok".$NOMC];
			$PYAoMAJ->Fname=$_FILES[$NOMC]['name'];
			$PYAoMAJ->Fsize=$_FILES[$NOMC]['size'];
			$PYAoMAJ->OFN=$_REQUEST["Old".$NOMC];
			// recup infos pour les pj dans le mail     
			$size=($PYAoMAJ->Fsize >0 ? " (".round($PYAoMAJ->Fsize / 1000)."Ko) " : "");
			$chemfich="http://".$_SERVER["SERVER_NAME"].str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']).$PYAoMAJ->Valeurs;
			$fich=($PYAoMAJ->Fname != "" ? $key."_".$PYAoMAJ->Fname : $PYAoMAJ->ValChp);
		}
		$tbset=array_merge($tbset,$PYAoMAJ->RetSet($key."_",true)); // key sert �la gestion des fichiers li�
		if ($PYAoMAJ->error) {
			$error=true;
			$err_lbl="Erreur sur le champ $NOMC genree par pya :".$PYAoMAJ->error;
			$PYAoMAJ->error="";
		}
	} // fin boucle sur les champs
	
	if (count($tbWhK)>0) {
		foreach ($tbWhK as $chp=>$val) $lchp[]=$chp."=$val";
		
		$where= " where ".implode(" AND ",$lchp);
	}
	// GROS BUG  $where=" where ".$key.($where_sup=="" ? "" : " and $where_sup");
	//echovar("_REQUEST['typeditrcr']");
	if ($typedit=="M") { // UPDATE
		$strqaj="UPDATE $table SET ".tbset2set($tbset)." $where";
	} elseif ($typedit==-1) { // SUPPRESSION
		$strqaj="DELETE FROM $table $where";
	} elseif ($typedit=="N") { //INSERTION
		$strqaj="INSERT INTO $table ".tbset2insert($tbset)." ON DUPLICATE KEY UPDATE ".tbset2set($tbset);
	}
//	echo "requete sql: $strqaj";
	db_query($strqaj);
	if ($typedit == "N") return(mysql_insert_id());
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
// gestion condition AND depuis PYA
$valb2 = explode("[[",$lntable);
if (count($valb2)>1) {
	$lntable=$valb2[0];
	if ($reqsup!="") {
		$reqsup= "(".$this->Val2." AND ".$valb2[1].")";
	} else $reqsup = $valb2[1];
}
// si une seule valeur a chercher, on ignore $reqsup sinon ça met la merde
if ($valc != "") $reqsup = "";

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

// fonction qui r�up�e les champ libell�(0) ou commentaire(1) d'une table
function RecLibTable($NM_TABLE,$offs) {
global $TBDname,$NmChDT;
$req="SELECT LIBELLE,COMMENT FROM $CSpIC$TBDname$CSpIC WHERE NM_TABLE='$NM_TABLE' AND NM_CHAMP='$NmChDT'";
$reqRL=db_query($req) or die("Requete SQL de RecLibTable invalide : <I>$req</I>");
$resRL=db_fetch_row($reqRL);
return($resRL[$offs]);
}

// fonction servant pr les req sql custom avec paramètres
// dans une chaine de type xxxx [arg1] yyyy [arg2] zzzz [arg3]
// retourne array(arg1,arg2,arg3)
// la chaine est transformée en xxxx ###1 yyyy ###2 zzzz ###3

function parseArgsReq(&$req) {
	$loop = true;
	while ($loop) {
		$i++;
		if ($ret = ret1Arg($req,$i)) {
			$tbarg[] = $ret;
		} else $loop = false;
	}
	return ($tbarg);
}
// dans une chaine de type xxxx [arg1] yyyy [arg2] zzzz [arg3] arg1
// retourne arg1, et dans str met   xxxx ###i yyyy [arg2] zzzz [arg3] arg1

function ret1Arg(&$str,$i,$chm="###",$ch1="[",$ch2="]") {
	
	if ($np =  strpos($str,$ch1)) {
		$ret = substr($str,$np+1);
		$str1 = substr($str,0,$np);
		if ($np =  strpos($ret,"]")) {
			$str = $str1.$chm.$i.substr($ret,$np+1);
			$ret = substr($ret,0,$np);
			return $ret;
		} else $ret = false;
	} else $ret = false;
	return($ret);
}
?>