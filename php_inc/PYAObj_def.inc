<?// objet PYA, permettant l'affichage de filtres, de champ dans les listes et de controle d'édition
class PYAobj {
  var $NbCarMxCust=0;
  var $NmBase;
  var $NmTable;
  var $NmChamp;
  var $Libelle;
  var $Typaff_l;
  var $FieldType;
  var $TTC; 
  var $FieldValDef;
  var $ValChp;
  var $FieldNullOk; // YES ou rien
  var $FieldKey; // clé=PRI, index=MUL, unique=UNI
  var $FieldExtra; // auto_increment
  var $TypeAff;
  var $Fccr=""; // force cases à cocher/radio en place des LD. rien ou
                // si qqchose, force. Si contient 'BR' force avec retour de lignes
                // si contient RAD force boutons radios
  var $Valeurs;
  var $Val2=""; // valeurs en plus, évaluées en fonction de l'enregistrement
  var $adpopl="popl.php"; // url par défaut de la popup d'édition
  var $TypFilt;
  var $TypCSA;
  var $Tt_AvMaj;
  var $Tt_PdtMaj;
  var $Tt_AprMaj;
  var $TypEdit; // important de savoir si on est en création ("" ou N), copie(2), édition(1) ou consultation (C)
  var $DirEcho=true; // Direct Echo: pour savoir si on echoise direct ou renvoie la valeur
  var $Comment;

// méthode qui initialise toutes les propriétés de l'objet pour un champ
// simplement à partir de la définition du nom du champ, de la table et de la base
function InitPO()  {
// Nom de la table de description des autres
// nom du champ contenant les caractéristiques globales à la table
// morceua de chaine identifiant une table virtuelle
global $TBDname,$NmChDT,$id_vtb;
$retVal="";
    if ($this->NmChamp!="" && $this->NmTable!="" && $this->NmBase!="") {
    	$ult=rtb_ultchp(); // tableau des noms de champs sensibles à la casse (à cause de pgsql...)
    	// normallement on est déjà connecté
        //mysql_select_db($this->NmBase) or die ("Impossible d'ouvrir la base de données $this->NmBase.");
        $rpDF=msq("select * from $TBDname where NM_TABLE='$this->NmTable' AND NM_CHAMP='$this->NmChamp'");
        if (db_num_rows($rpDF)>0) {
              $CcChp=db_fetch_assoc($rpDF);
              $this->Libelle=stripslashes($CcChp[$ult['LIBELLE']]);
              $this->Typaff_l=$CcChp[$ult['TYPAFF_L']];
              $this->TypeAff=$CcChp[$ult['TYPEAFF']];
              $this->Valeurs=$CcChp[$ult['VALEURS']];
	      if (substr($this->Valeurs,"@@")) $this->Fccr="LDF"; // si hiérarchique, force les listes déroulante
              $this->Tt_AvMaj=$CcChp[$ult['TT_AVMAJ']];
              $this->Tt_PdtMaj=$CcChp[$ult['TT_PDTMAJ']];
              $this->Tt_AprMaj=$CcChp[$ult['TT_APRMAJ']];
              $this->TypFilt=$CcChp[$ult['VAL_DEFAUT']];
              $this->TypCSA=$CcChp[$ult['TYP_CHP']];
              $this->Comment=stripslashes($CcChp[$ult['COMMENT']]);
	      
	      if (!stristr($this->NmTable,$id_vtb)) { // on cherche a caractériser le champ que s'il n'est pas dans une table virtuelle
	      
		$TbDescChp=db_table_defs($this->NmTable,$this->NmChamp);
		$this->FieldType=$TbDescChp[$this->NmChamp]['FieldType'];
		$TTC= ereg_replace('\\(.*', '', $this->FieldType); // Type de champ épuré sans les valeurs (enum, varchar, etc ...)
		if (stristr($TTC,"blob") || stristr($TTC,"text")) $TTC="txtblob"; 
		if (stristr($TTC,"varchar") || stristr($TTC,"char")) $TTC="char";
		$this->TTC=$TTC;
		$this->FieldValDef=$TbDescChp[$this->NmChamp]['FieldValDef'];
		// si nouvel enregistrement, affecte la valeur par défaut
		if ($this->TypEdit=="" || $this->TypEdit=="N") $this->ValChp=$this->FieldValDef; 
		$this->FieldNullOk=$TbDescChp[$this->NmChamp]['FieldNullOk']; // YES ou rien
		$this->FieldKey=$TbDescChp[$this->NmChamp]['FieldKey']; // clé=PRI, index=MUL, unique=UNI
		$this->FieldExtra=$TbDescChp[$this->NmChamp]['FieldExtra']; // auto_increment
		// si champ auto increment et pas caché, on le met en statique
		if (stristr($this->FieldExtra,"auto_increment") && $this->TypeAff!="HID") $this->TypeAff="STA";
		/*
					$ret[$NM_CHAMP]['FieldValDef']=($row_table_def['Default']!="" ? $row_table_def['Default'] : "ø" );
					// si nouvel enregistrement, affecte la valeur par défaut
					$ret[$NM_CHAMP]['FieldNullOk']=($row_table_def['Null']=="YES" ? "yes" : "no"); // YES ou rien
					$ret[$NM_CHAMP]['FieldKey']=($row_table_def['Key']!="" ? $row_table_def['Key'] : "ø"); // clé=PRI, index=MUL, unique=UNI
					$ret[$NM_CHAMP]['FieldExtra']=$row_table_def['Extra']; // auto_increment 
		
		$TbDescChp=ShowField($this->NmChamp,$this->NmTable);
		$this->FieldType=$TbDescChp['Type'];
		$TTC= ereg_replace('\\(.*', '', $this->FieldType); // Type de champ épuré sans les valeurs (enum, varchar, etc ...)
		if (stristr($TTC,"blob") || stristr($TTC,"text")) $TTC="txtblob"; 
		if (stristr($TTC,"varchar") || stristr($TTC,"char")) $TTC="char";
		$this->TTC=$TTC;
		$this->FieldValDef=$TbDescChp['Default'];
		// si nouvel enregistrement, affecte la valeur par défaut
		if ($this->TypEdit=="") { $this->ValChp=$TbDescChp['Default']; }
		$this->FieldNullOk=$TbDescChp['Null']; // YES ou rien
		$this->FieldKey=$TbDescChp['Key']; // clé=PRI, index=MUL, unique=UNI
		$this->FieldExtra=$TbDescChp['Extra']; // auto_increment
		// si champ auto increment et pas caché, on le met en statique
		if (stristr($this->FieldExtra,"auto_increment") && $this->TypeAff!="HID") $this->TypeAff="STA";
		*/
	      } // fin si pas champ dans une table virtuelle
            } // fin si champ trouvé
        else $retVal= "Champ $this->NmChamp (table :$this->NmTable, base: $this->NmBase) non trouvé dans la table de description";
        }
    else $retVal = "Appel de la méthode InitPO de l'objet PYAObj incorrecte: au moins une des propriétés de base n'a pas été définie"; 
    if ($this->DirEcho) {
    	echo $retVal;
    } else {
    	return($retVal);
    }
} // fin méthode d'initialisation$DH

// méthode d'affichage de filtre de requete
  function EchoFilt($bDN=true,$bDL=true) { // booleens affiche négation et affiche libellé ie baratins sur les dates e
   global $VSLD;
    switch ($this->TypFilt) {
      case "INPLIKE":
        $retVal= "<INPUT TYPE=\"hidden\" NAME=\"tf_".$this->NmChamp."\" VALUE=\"INPLIKE\">";
        $retVal.= "<INPUT TYPE=\"text\" NAME=\"rq_".$this->NmChamp."\" VALUE=\"\">";
        break;
  
  //! ttes les listes sont à choix multiple (condition OR sur les valeurs)
      case "LDC": // Liste déroulante affichant les différentes valeurs du champ  
        $retVal= "<INPUT TYPE=\"hidden\" NAME=\"tf_".$this->NmChamp."\" VALUE=\"LDM\">";
        $rqldcu=msq("SELECT $this->NmChamp from $CSpIC$this->NmTable$CSpIC group by $this->NmChamp order by $this->NmChamp");
        //$retVal.= "<SELECT NAME=\"rq_".$this->NmChamp."[]\" MULTIPLE title=\"cliquez sur une ou plusieurs valeurs en appyant sur la touche Ctrl\">";
        $tbval["%"]=$VSLD."(tous)";
        while ($rwsq=db_fetch_row($rqldcu)) {
          $tbval[$rwsq[0]]=$rwsq[0];
          }
        asort($tbval); // tri de la liste
        $retVal.=DispLD($tbval,"rq_".$this->NmChamp,"yes",$this->Fccr,false);
        break;
  
      case "LDF": // Liste déroulante affichant les différentes valeurs fixes listées dans le champ valeur, ou les valeurs du set ou enum
        $retVal= "<INPUT TYPE=\"hidden\" NAME=\"tf_".$this->NmChamp."\" VALUE=\"LDM\">";
        // $tbval["%"]=$VSLD."%"; fait après
        if ($this->TTC=="enum" || $this->TTC=="set") {
            $tabval = str_replace('set(', '', $this->FieldType);
            $tabval = str_replace('enum(', '', $tabval);
            $tabval = ereg_replace('\\)$', '', $tabval);
            $tabval = explode('\',\'', substr($tabval, 1, -1));}
        else  $tabval=explode(",",$this->Valeurs);
        foreach ($tabval as $val) {
           if (strstr($val,":")) {
                $valtb=explode(":",$val);
                $key=$valtb[0];
                $val=$valtb[1];
                }
           else $key=$val;
           
           if ($this->TypeAff=="LDM") $key="%,$key,%"; // filtre change si multiple (faux set)
           $tbval[$key]=$val;
           }
        asort($tbval); // tri de la liste
        //$tbval=array_merge(array("%"=>$VSLD."(tous)"),$tbval);
				// ci-dessus ça reconstruit les indices...
				$tbval=array("%"=>$VSLD."(tous)")+$tbval;

        $retVal.=DispLD($tbval,"rq_".$this->NmChamp,"yes",$this->Fccr,false);
        break;
  
      case "LDL": // Liste déroulante affichant les différentes valeurs liées par le champ valeurs
        $retVal= "<INPUT TYPE=\"hidden\" NAME=\"tf_".$this->NmChamp."\" VALUE=\"LDM\">";
        $tbval=ttChpLink($this->Valeurs);
	if ($this->TypeAff=="LDLM") // si liaison multiple, la cle de recherche est %,clé,%
            {
            foreach ($tbval as $key=>$val)
               {
               $key="%,$key,%";
               $tabval[$key]=$val;
               }
            }
        else $tabval=$tbval;
				//$tabval["%"]=$VSLD."(tous)"; // avant on faisait ça le % se retrouve à la fin ..
        $tbjok[000]=$VSLD."(tous)"; // on fait ça pour ne pas décaler les indices dans le cas où le champ est num
                              // quand c'est un champ num, on a 0, sinon 000
        //$tabval=array_merge($tbjok,$tabval);
				// mais cette fonction reindexe le tableau et donc fout une merdre pas croyable!!
				//astuce ci-dessous trouvée et mise par un anonyme sur internet !!!
				$tabval=$tbjok+$tabval;
				$retVal.= "<!-- desbog";
				$retVal.= $tabval[103];
				$retVal.= "-->";
//        asort($tbval); // pas de tri de la liste (spécifié dans la liaison)
        $retVal.=DispLD($tabval,"rq_".$this->NmChamp,"yes",$this->Fcrr,false);
        break;
        
      case "DANT": // criteres sur date antérieure ou postérieure
      case "DPOST":
        $retVal= "<INPUT TYPE=\"hidden\" NAME=\"tf_".$this->NmChamp."\" VALUE=\"$this->TypFilt\">";
        if ($bDL) $retVal.= "Enregistrements pour lequels $this->Libelle est ". ($this->TypFilt=="DANT" ? "antérieure" : "postérieure") ." à ";
        $retVal.= "<INPUT TYPE=\"text\" NAME=\"rq_".$this->NmChamp."\" VALUE=\"\">";
        break;
        
      case "DATAP": // criteres sur date antérieure ET postérieure
        $retVal= "<INPUT TYPE=\"hidden\" NAME=\"tf_".$this->NmChamp."\" VALUE=\"DATAP\">";
        if ($bDL) {
             $retVal.= "Enregistrements pour lequels $this->Libelle est <br>postérieure à <INPUT TYPE=\"text\" NAME=\"rq_".$this->NmChamp."[0]\" VALUE=\"%\"> et antérieure à <INPUT TYPE=\"text\" NAME=\"rq_".$this->NmChamp."[1]\" VALUE=\"%\">";
             }
        else {
             $retVal.= "$this->Libelle post <INPUT TYPE=\"text\" NAME=\"rq_".$this->NmChamp."[0]\" VALUE=\"%\"> & ante <INPUT TYPE=\"text\" NAME=\"rq_".$this->NmChamp."[1]\" VALUE=\"\">";
             }
        break;
  
      default:
        $retVal= "&nbsp;";
	$bDN=false;
        break;
	
      } // fin switch sur type de filtre
      // negation
   if ($bDN) $retVal.=$this->DispNeg(false);
   
   if ($this->DirEcho) {
    	echo $retVal;
    } else {
    	return($retVal);
    }
  } // fin méthode EchoFilt
//
// Méthode qui affiche la case de négation
function DispNeg($DE2=true) {
        $retVal.= "<BR><span class=\"FdR\"><INPUT TYPE=\"checkbox\" NAME=\"neg_".$this->NmChamp."\" VALUE=\"neg\"> Négation</span>";
   if ($this->DirEcho && $DE2) {
    	echo $retVal;
    } else {
    	return($retVal);
    }

        }

// méthode d'affichage de case à cocher pour affichage optionnel du champ
  function EchoCSA() {
    switch ($this->TypCSA) { // type d'affichage sélectionnable
      case "OCD":
      case "ONCD":
        $nmvarAfC="AfC_".$this->NmChamp;
        $retVal.= "<input type=\"hidden\" name=\"$nmvarAfC\" value=\"no\">"; // obligatoire
        $cs=($this->TypCSA=="OCD" ? "checked" : "");
        $retVal.= "<input type=\"checkbox\" name=\"$nmvarAfC\" value=\"yes\" $cs> Affiché";
         
      default: 
        $retVal.= "&nbsp;";
        break;
      } // fin switch sur affichage oui ou non
    
    if ($this->DirEcho) {
    	echo $retVal;
    } else {
    	return($retVal);
    }
  } // fin méthode EchoCSA



// méthode affichage champ dans liste
  function EchoVCL() {
     if ($this->DirEcho) {
    	echo $this->RetVCL()."\n";
    } else {
    	return($this->RetVCL()."\n");
    }
  }

  function RetVCL($HT=true) {  // affiche valeur champ dans Liste
      // le param HT indique si l'on génère du HTML ou du texte brut (telechargement)
      global $nbcarmxlist;
      $EiS=($HT ? "&nbsp;" : ""); // espace insécable  HTML ou rien

      if ($this->TTC=="date") $this->ValChp=DateF($this->ValChp); // convertit dates

      switch ($this->Typaff_l) {

      case "LNK": // si champ lié a une autre table, ou liste composée
        return $this->RVLS($EiS);  // méthode qui retourne valeur statique (non éditable) liée externe ou pas
        break;
        
      case "AHREF": // si lien HTML
        if ($this->ValChp=="") {
          return $EiS;}
        else {
          if (strpos ($this->Valeurs,"#")!=1 && $this->Valeurs!="") {
              $ChemFF=$this->Valeurs;}
          else {
               $ChemFF="./".$this->NmBase."_".$this->NmTable."_".$this->NmChamp."/";}

          if ($HT) {
               return "<A HREF=\"".$ChemFF.$this->ValChp."\" target=\"_blank\">".$this->ValChp."</A>";}
          else
              {return "Fichier attaché:".$ChemFF.$this->ValChp;}
  
          }
        break;
        
      default: 
        // nbre de car max par défaut, ou passé en propriété
        $NbCarMax=($this->NbCarMxCust>0 ? $this->NbCarMxCust>0 : $nbcarmxlist);
        $Val2Af=stripslashes($this->ValChp);
        $Val2Af=substr($Val2Af, 0, $NbCarMax); // au max xx caractères
        if ($HT) { 
            $Val2Af=DispCustHT($Val2Af);
              if ($this->DirEcho) {
    	echo $retVal;
    } else {
    	return($retVal);
    }
  $Val2Af=($Val2Af=="" ? "$EiS" : $Val2Af);
            }
        return ($Val2Af);
        break;
       } // fin du switch
  } // fin methode EchoVCL

// méthode commune aux 2 méthodes liste et édition, qui affiche les valeurs liées en statique
function RVLS($EiS) {
        // LnkTb=true si liste liée à une table, false si liste de valeurs fixes
        $LnkTb=!($this->TypeAff=="LD" || $this->TypeAff=="LDM");
        if (!$LnkTb) { // si liste de valeurs, construit directement un tableau de hachage cle=>valeurs
           $defl=explode(',',$this->Valeurs);
           foreach ($defl as $val) {
             if (strstr($val,":")) {
                $valtb=explode(":",$val);
                $key=$valtb[0];
                $val=$valtb[1];
                }
             else $key=$val;
             $tbval[$key]=$val;
             }
           } // fin si valeurs statiques
        $cle=$this->ValChp;
        $valaff="";
        if ($cle!=""){
          if (strstr($cle,",")) { // plusieurs valeurs séparées par des virgules (type pseudo-set multiple)
            $tabcle=explode(",",$cle);
            foreach ($tabcle as $cle) {
                if ($cle!="") { // ignore valeurs vides
                   if ($LnkTb) {
                      $valsuiv=ttChpLink($this->Valeurs,"",$cle); }
                   else $valsuiv=$tbval[$cle];
                   $valaff.=($valsuiv ? "$valsuiv, ": "NC[$cle], "); // affiche si trouvée, ou erreur
                } // fin si valeur bnon vide
              }
            $valaff=vdc($valaff,2); // vire la dernière virgule et esp
            }
          else { 
              if ($LnkTb) {
                 $valsuiv=ttChpLink($this->Valeurs,"",$cle); }
              else $valsuiv=$tbval[$cle];
            $valaff=($valsuiv ? "$valsuiv": "NC[$cle]"); // n'affiche si trouvée
            }
          } // fin si $cle!=""
        $valaff=($valaff!=""? $valaff : "$EiS");
        return $valaff;
   } // fin fonction



// méthode qui affiche le controle
// on a séparé les auto et manuels
function EchoEditAll($DH=true) {
  if ($this->TypeAff!="AUT") {
    return($this->EchoEdit($DH));}
  else return($this->EchoEditAuto());
  }

// méthode qui affiche le controle dédition
// pour tes les type d'aff <> de AUT  
function EchoEdit($DH=true) {
  global $VSLD,$nbrtxa,$nbctxa;
  
  if (!strstr($this->FieldType,'date')) { 
    if ($this>TypeAff!="HID") $this->ValChp=stripslashes($this->ValChp); }
  else $this->ValChp=DateF($this->ValChp);

  if ($this->TypEdit=="C") { // si on est en consultation, on passe tout en STA ou STAL ou rien

   switch ($this->TypeAff) {
   //chgt en statique normale
        case "AUT": // AUto
	case "TXT": // boite texte normale
        case "TXA": // texte area
           $this->TypeAff="STA";
           break;

   //chgt en statique normale forcée (on recherche les paires clés: valeurs dans this->Valeurs
        case "LD": // liste déroulante non liée
        case "LDM": // liste déroulante non liée MULTIPLE
        case "LDL":
        case "LDLM":
	case "POPL":
        // on est en consultation forcée.
        // Ici on regarde si dans les valeurs s'il y a : et, ce qui implique que la valeur à afficher
        // est certainement à piocher dans une liste clé-valeurs et non à afficher telle quelle
           $Val2Af=DispCustHT($this->RVLS("&nbsp"));
           $retVal.= $Val2Af."<INPUT TYPE=\"hidden\" NAME=\"".$this->NmChamp."\" VALUE=\"".$this->ValChp."\">";
           $this->TypeAff="STAF"; // pour ne plus rien faire ...
           break;

     // le reste on change pas, les photos sont traitées dans la proc elle-meme
     } // fin switch
   }   // fin si on est en consultation

   // traitement Evènements pendant mise à jour
   if ($this->Tt_PdtMaj!="") {
      $tbeven=explode(":",$this->Tt_PdtMaj);
      $evJS=" ".$tbeven[0]."=\"".$tbeven[1]."\"";
      }
   else $evJS="";

   switch ($this->TypeAff) {
    case "": // on affiche rien
      break;
      
    case "HID": // on affiche un champ caché que si $DH=true, sinon rien
      if ($DH) $retVal.= "<INPUT TYPE=\"hidden\" NAME=\"".$this->NmChamp."\" VALUE=\"".htmlspecialchars($this->ValChp)."\">";
      break;
  
    // pour indiquer qu'in élément est sélectionné, met VSLD devant (#SEL#)
    case "LD": // liste déroulante non liée
      $defl=explode(',',$this->Valeurs);
      if ($this->FieldNullOk=="YES")   
        {  // si null autorisé
        $val=(($this->ValChp=="NULL") ? $VSLD."NULL" : "NULL");
        $tbval["NULL"]="vide(null)";
        }
      foreach ($defl as $val) {
        if (strstr($val,":")) {
           $valtb=explode(":",$val);
           $key=$valtb[0];
           $val=$valtb[1];
           }
        else $key=$val;
        $val=(($key==$this->ValChp) ?  $VSLD.$val: $val);
        $tbval[$key]=$val;
        $tbkey[]=$key;
      }
      if (!in_array($this->ValChp,$tbkey) && $this->ValChp!="")
        $retVal.= "<H6>Erreur : la valeur $this->ValChp contenue dans le champ n'est pas dans la liste ! </H6>";
      $retVal.=DispLD($tbval,$this->NmChamp,"no",$this->Fccr,false); // lise der. non multiple
      break;

    case "LDM": // liste déroulante non liée MULTIPLE
      $defl=explode(',',$this->Valeurs);
      $tbvalm=explode(',',$this->ValChp);
      
      $tbval["NULL"]="Aucun(e)"; // valeur vide
      $Am1S=false; // Init au moins une valeur sélectionnée
      foreach ($defl as $val) {
        if (strstr($val,":")) {
           $valtb=explode(":",$val);
		$key=$valtb[0];
           $val=$valtb[1];
           }
        else $key=$val;
        if (in_array($key, $tbvalm)) {
              $val=$VSLD.$val; // indique les sélectionnés
              $Am1S=true; // Init au moins une valeur sélectionnée
              }
        $tbval[$key]=$val;
        $tbkey[]=$key;
          }
      // controle d'erreur

      // si aucun de sélectionné ...
      if (!$Am1S) $tbval["NULL"]=$VSLD."Aucun(e)";

      unset($err);
      foreach ($tbvalm as $valm) {
        if (!in_array($valm,$tbkey) && $valm!="") $err.=$valm.", ";
        }
      if ($err!="") {
        $err=vdc($err,2); // enlève dernier", ";
        $retVal.= "<H6>Erreur : valeur(s) $err contenue(s) non présente(s) dans la liste ! </h6>";
        }
      $retVal.=DispLD($tbval,$this->NmChamp,"yes",$this->Fccr,false);
      break;
      
    case "LDL": // liste déroulante liée
      /* Syntaxe
      (base,user,passwd);table,chpclé,chp_aff1,chpaff2,...,chpaffn)
      */
      $tabCorlb=ttChpLink($this->Valeurs,$this->Val2);
      if (strstr($this->Valeurs,"@@")) {  // si hierachique
	$valnul=($this->TTC=="char" ? "NULL" : 0);
        $val=(($this->ValChp==$valnul) ? $VSLD.$valnul : $valnul);
        $tbval[$valnul]="Aucun, Elle-meme(self)";
        }
      elseif ($this->FieldNullOk=="YES") {  // si null autorisé
        $val=(($this->ValChp=="NULL") ? $VSLD."NULL" : "NULL");
        $tbval["NULL"]="Aucun(e)";
        }
      $tberrck=array(); // clear tableau de controle d'erreur
      foreach ($tabCorlb as $key=>$val) {
        $tberrck[]=$key;
        $val=(($key==$this->ValChp) ? $VSLD.$val : $val);
        $tbval[$key]=$val;        
          }
      if (!in_array($this->ValChp,$tberrck) && $this->ValChp!="") 
        $retVal.= "<H6>Erreur : la valeur $this->ValChp contenue dans le champ n'est pas dans la liste ! </H6>";
      $retVal.=DispLD($tbval,$this->NmChamp,"no",$this->Fccr,false);
      break;

    case "LDLM": // liste déroulante liée MULTIPLE
      /* Syntaxe de TYPA_VAL
      (base,user,passwd);table,chpclé,chp_aff1,chpaff2,...,chpaffn)
      */
      $tabCorlb=ttChpLink($this->Valeurs,$this->Val2);
      //dans le champ lui même, les valeurs clés sont stockées séparées par des virgules
      $tbvalm=explode(',',$this->ValChp);

     if (strstr($this->Valeurs,"@@")) $valsh=" ou elle-meme";
      $valnul=($this->TTC=="char" ? "NULL" : 0);
      $tbval[$valnul]="Aucun(e)".$valsh;


      $tberrck=array(); // clear tableau de controle d'erreur
      $Am1S=false; // Init au moins une valeur sélectionnée
      foreach ($tabCorlb as $key=>$val) {
        $tberrck[]=$key;
        if (in_array($key, $tbvalm))
           { $val=$VSLD.$val; // raj les caract indiquant la sélection
           $Am1S=true; // au mis une valeur sélectionnée
           }
        $tbval[$key]=$val;        
        }

      // si aucun de sélectionné ...
      if (!$Am1S) $tbval[$valnul]=$VSLD."Aucun(e)".$valsh;
      // controle d'erreur
      if (is_array($tbvalm)) {
        unset($err);
        foreach ($tbvalm as $valm) {
          if (!in_array($valm,$tberrck) && $valm!="") $err.=$valm.", ";
          }
        if ($err!="") {
          $err=vdc($err,2); // enlève dernier", ";
          $retVal.= "<H6>Erreur : valeur(s) $err contenue(s) dans le champ non présente(s) dans la liste ! </h6>";
          }
        }        
      $retVal.=DispLD($tbval,$this->NmChamp,"yes",$this->Fccr,false); // liste déroulante, multiple
      break;
    
    $Mult=false;
    case "POPLM": // popup de sélection
    $Mult=true;
    case "POPL": // popup de sélection
		$retVal.= "Valeur du lien : ".$this->Valeurs.", valeur du champ : ".$this->ValChp;
	
	$retVal.='<SELECT ID="'.$this->NmChamp.'" NAME="'.$this->NmChamp.($Mult ? "[]" : "").'" SIZE="'.(($Mult ? 10 : 1)).'" MULTIPLE>';
	if ($this->ValChp) { // si valeur existe
		if (strstr($this->ValChp,",")) {
			$tabk=explode(",",$this->ValChp);
			foreach($tabk as $k) {
				$tabVS[$k]=ttChpLink($this->Valeurs,"",$this->ValChp);
			}
		} else {
		$tabVS[$this->ValChp]=ttChpLink($this->Valeurs,"",$this->ValChp);
		}
	}

	if ($tabVS) {
		foreach ($tabVS as $k=>$v) {
		$retVal.="<OPTION VALUE=\"$k\">$v</OPTION>";
		}
	}
	$retVal.='</SELECT>';
	
	$retVal.= '<a href="#" onclick="popup(\''.$this->adpopl.'?Valeurs='.urlencode($this->Valeurs).'&ValChp='.urlencode($this->ValChp).'&NmChp='.$this->NmChamp.'&Mult='.($Mult ? 10 : 1).'\',500,500)">Test Popup</a>';

	break;
    		
    case "FICFOT":
      if ($this->TypEdit==2) $this->ValChp=""; // annule si copie
      $retVal.= "<INPUT TYPE=\"hidden\" NAME=\"Old".$this->NmChamp."\" VALUE=\"".$this->ValChp."\"><B>";
        if ($this->ValChp!="") {  // regarde si chemin paramétré correctement
          if (strpos ($this->Valeurs,"#")!=1 && $this->Valeurs!="") {
              $ChemFF=$this->Valeurs;}
          else {
               $ChemFF="./".$this->NmBase."_".$this->NmTable."_".$this->NmChamp."/";}

        $NCF=$ChemFF.$this->ValChp;
        if (TestNFImg($this->ValChp)) // si c'est un fichier de type image
          { $retVal.= "<P><IMG SRC=\"".$NCF."\"></P>";
          }
        else
          { $retVal.= "Lien vers le fichier associé <A HREF=\"".$NCF."\" TARGET=\"_blank\">".$this->ValChp."</A><BR>";}

        if ($this->TypEdit!="C") $retVal.= "<INPUT TYPE=\"checkbox\" NAME=\"Fok".$this->NmChamp."\" VALUE=\"".$this->ValChp."\" CHECKED>".$this->ValChp."&nbsp;";
        }// Fin si il y a un fichier associe
        if ($this->TypEdit!="C") { // saisie que si pas en édition
           $retVal.= "</B><INPUT TYPE=\"file\" NAME=\"".$this->NmChamp."\" SIZE=\"30\" MAXLENGTH=\"200\">";
           if ($this->ValChp!="") $retVal.= "<FONT SIZE=\"-2\"><br>Pour désactiver l'image ou le fichier, décocher la case, ou choisissez-en un(e) autre. Sinon ne touchez à rien ...</font>";
      }
      break; // fin champ de type fichier-photo

      case "STAF": // statique forcée
           // on ne fait rien, on l'a déjà fait en haut
      break;

      case "STA": // statique  simple
     // remplace caractère merdiques et converti adresses mail en cliquables pour le HTML
      $Val2Af=DispCustHT($this->ValChp);
      $retVal.= $Val2Af."<INPUT TYPE=\"hidden\" NAME=\"".$this->NmChamp."\" VALUE=\"".htmlspecialchars($this->ValChp)."\">";
      break;

    case "STAL": // statique liée
      $retVal.= (ttChpLink($this->Valeurs,"",$this->ValChp));
      $retVal.= "<INPUT TYPE=\"hidden\" NAME=\"".$this->NmChamp."\" VALUE=\"".$this->ValChp."\">";

      break;

    case "TXA":
        $defl=explode(',',$this->Valeurs);
        // pour les dimensions, on prend les chiffres dans le champ valeurs (nblignes, nbcolonnes)
        $ligs=(($defl[0]>0) ? $defl[0] : $nbrtxa);
        $cols=(($defl[1]>0) ? $defl[1] : $nbctxa);
        $retVal.= "<TEXTAREA COLS=\"$cols\" ROWS=\"$ligs\" NAME=\"".$this->NmChamp."\">".$this->ValChp."</TEXTAREA>";
      break;

    case "TXT":
        $defl=explode(',',$this->Valeurs);
        // pour la size et maxsize , on prend les chiffres dans le champ valeurs (size,maxsize)
        $size=(($defl[0]>0) ? " SIZE=\"$defl[0]\" ": "");
        $mxsize=(($defl[1]>0) ? " MAXSIZE=\"$defl[1]\" " : "");
    default :
        $retVal.= "<INPUT TYPE=\"text\" NAME=\"".$this->NmChamp."\" VALUE=\"".htmlspecialchars($this->ValChp)."\" $size $mxsize $evJS>";
      break;
     } // fin switch sur type d'affichage
    
    if ($this->DirEcho) {
    	echo $retVal;
    } else {
    	return($retVal);
    }

  } // fin méthode EchoEdit

// méthode qui affiche le controle dédition
// pour le type d'aff AUTOMATIQUE uniquement
function EchoEditAuto() {
  global $VSLD,$nbrtxa,$nbctxa;

  if (!strstr($this->FieldType,'date')) $this->ValChp=stripslashes($this->ValChp);

  if ($this->TypEdit=="C") { // en consult, on appelle la manuelle
     $this->TypeAff="STA";
     return($this->EchoEdit());
     } // fin edition consult (ro)

  else {

   switch ($this->TTC) { // switch sur type de champ
     case "boolean": // specifique postgres
	$retVal.= "<input type=\"radio\" name=\"$this->NmChamp\" value=\"TRUE\" $ck>vrai (true)\n";
	$retVal.= "<input type=\"radio\" name=\"$this->NmChamp\" value=\"FALSE\" $ck>faux (false)\n";
	break;

     case "enum" : // liste de valeurs
       // la juste dessous pompé de phpMyAdmin sans tout bien comprendre
       $enum = str_replace('enum(', '', $this->FieldType);
            $enum = ereg_replace('\\)$', '', $enum);
            $enum = explode('\',\'', substr($enum, 1, -1));
            $enum_cnt = count($enum);
       if ($enum_cnt<=$nValRadLd || $this->Fccr) { // si - de 3, cases à option
         for ($l=0;$l<$enum_cnt;$l++) {
           $ck=($enum[$l]==$this->ValChp ? "checked" : "");
           $retVal.= "<input type=\"radio\" name=\"$this->NmChamp\" value=\"$enum[$l]\" $ck>$enum[$l]\n";
               }
         } // fin si cases à options 
       else { // si + de 3, liste déroulante
         $retVal.= "<select name=\"$this->NmChamp\">";
         for ($l=0;$l<$enum_cnt;$l++) {
           $ck=($enum[$l]==$this->ValChp ? "selected" : "");
           $retVal.= "<option value=\"$enum[$l]\" $ck>$enum[$l]</option>\n";
               }
         $retVal.= "</select>";
         } // fin si + de 3
       break;

     case "set" : // liste de valeurs
       $enum = str_replace('set(', '', $this->FieldType);
            $enum = ereg_replace('\\)$', '', $enum);
            $enum = explode('\',\'', substr($enum, 1, -1));
            $enum_cnt = count($enum);
       if ($enum_cnt<=$nValRadLd || $this->Fccr) { // si - de 3, cases à cocher
         for ($l=0;$l<$enum_cnt;$l++) {
           $ck=(stristr($this->ValChp,$enum[$l]) ? "checked" : "");
           $retVal.= "<input type=\"checkbox\" name=\"".$this->NmChamp."[]\" value=\"".$enum[$l]."\" $ck>".$enum[$l]."&nbsp;&nbsp;&nbsp;";
               }
         }
       else { // liste déroulante à choix multiples (variable tableau)
         $retVal.= "<select name=\"".$this->NmChamp."[]\" multiple size=\"".min($enum_cnt ,4)."\">";
         for ($l=0;$l<$enum_cnt;$l++) {
           $ck=(stristr($this->ValChp,$enum[$l]) ? "selected" : "");
           $retVal.= "<option value=\"$enum[$l]\" $ck>$enum[$l]</option>\n";
               }
         $retVal.= "</select>";
         }
       break;

     case "date" : // on passe la date en français
         $retVal.= "<INPUT TYPE=\"text\" NAME=\"".$this->NmChamp."\" VALUE=\"".DateF($this->ValChp)."\" size=\"10\" maxlength=\"10\">";
       break;

     case "txtblob" : // textarea
         $retVal.= "<TEXTAREA COLS=\"$nbctxa\" ROWS=\"$nbrtxa\" NAME=\"".$this->NmChamp."\">".$this->ValChp."</TEXTAREA>";
       break;

     case "char" : // char ou varchar

       $lg=strrchr($this->FieldType, "(");
       $lg= substr($lg,1,-1); // ne récupère que l'entier
       $size=min(intval($lg*1.5),100);
         $retVal.= "<INPUT TYPE=\"text\" NAME=\"".$this->NmChamp."\" VALUE=\"".htmlspecialchars($this->ValChp)."\" ".($lg > 0 ? "maxlength=\"$lg\" size=\"$size\"": "").">";
       break;

     default :
         $retVal.= "<INPUT TYPE=\"text\" NAME=\"".$this->NmChamp."\" VALUE=\"".htmlspecialchars($this->ValChp)."\">";
       break;
     } // fin switch type de champ
     
    if ($this->DirEcho) {
    	echo $retVal;
    } else {
    	return($retVal);
    }

    } // fin si pas en lecture seule
  } // fin Methode EchoEditAuto

  
Function InitAvMaj($User="") {
  switch ($this->Tt_AvMaj) {
    case "DJ": // date du jour
      $this->ValChp=date("Y-m-d");
      break;  
    case "DJSN": // date du jour si nul
      if ($this->ValChp=="" || $this->ValChp==0) $this->ValChp=date("Y-m-d");
      break;  
    case "DJP2MSN": // date du jour plus 2 mois si nul
      if ($this->ValChp=="" || $this->ValChp==0) $this->ValChp=date("Y-m-d",time()+5270400);
      break;  
    case "US": // code User
      if ($User!="") {
        $this->ValChp=$User; // ici CORES
      }else{
         $this->ValChp="_INC";}
      break;  
    case "USSN": // code User si nul avant (pour les nouvelles uniquement par e.)
      if ($this->ValChp=="") {
         if ($User!="") {
           $this->ValChp=$User; // ici CORES
         }else{
            $this->ValChp="_INC";}
            }
      break;
    case "EDOOFT" : // edition seulement la premiere fois
         if ($this->TypEdit==1) {
            if ($this->TypeAff=="LDL" || $this->TypeAff=="LDLM"){
               $this->TypeAff="STAL";
               }
            else $this->TypeAff="STA";
         }
    default :
      break;
    } // fin switch sur type de MAJ avant
  } // fin méthode d'init ava MAJ

// méthode retourne un set pour les mises à jour
// et gère aussi les fichiers joints (!)

function RetSet($keycopy,$rttb=false) {
global $MaxFSize,$MaxFSizeDef; // taille max des fichiers joints !!
if ($MaxFSize==0 || $MaxFSize=="") $MaxFSize=$MaxFSizeDef ;

  if ($this->TTC=="date") // date
    {$this->ValChp=DateA($this->ValChp);}
  else if  (is_array($this->ValChp))  // tableau
    { // on vient d'un set ou d'une liste déroulante multiple (liée ou pas) et la var est un tableau
		if($this->TTC!="set") $valset=","; // Nouveau: avant rien
		foreach($this->ValChp as $val){
			if ($val!="NULL" && $val!="") $valset.=addslashes($val).",";
			}
		if($this->TTC=="set")	$valset=substr($valset,0,-1); // enlève la dernière virgule à la fin
				$this->ValChp=$valset; // laisse la dernière virgule à la fin
    } // fin si valeur est un tableau
  else { // pas tableau ni date
    $this->ValChp=addslashes($this->ValChp);
    }
  
    switch ($this->Tt_AprMaj) {
      default:
      // encore rien de fait
      break;
    }

// gestion des champs type fichier joint
if ($this->TypeAff=="FICFOT") {

    // dans rep courant, chemindestockage=
    // Le champ valeur s'il est correct (commence pas par # et non vide
    // sion: NomBase_NomTable_NomChamp
    //print_r($_FILES);
    $value=$this->OFN; // valeur du set par défaut

    if (strpos ($this->Valeurs,"#")!=1 && $this->Valeurs!="") {
          $ChemImg=$this->Valeurs;}
    else {
          $ChemImg="./".$this->NmBase."_".$this->NmTable."_".$this->NmChamp."/";}

	//print_r($this);
    if ($this->TypEdit==1) { // gestion effacement anciens fichiers attachés
      // test decoche ou changement en modif
    // pour effacement de l'ancien fichier attaché
       if ($this->OFN!="" && ($this->Fok=="" || ( $this->ValChp!="none" && $this->ValChp!="" && $keycopy.$this->Fname!= $keycopy.$this->OFN)))
       // condition: ancien fichier attaché existe ($$VarOldFichImg!="")
       // ET
          // la case a été décochée  ($$VarFok$FichImg=="")
       // OU
           // un nouveau fichié a été entré ($FichImg!="none")
            // ET
            // le nom de ce fichier n'est pas le même que l'ancien
          {
           delfich($ChemImg.$this->OFN);
           $value="";
          }
    }   // fin gestion effacement anciens fichiers attachés
    
    // si nouveau ou modif avec fichier attaché
    if ($this->TypEdit!=-1 && $this->ValChp!="none" && $this->ValChp!="") {
    // remplacement des caracteres Zarbi ds les noms de fichier cause bugs
    $this->Fname=strtr($this->Fname," àéèùêçïüûîâ","_aeeueciuuia");

    // variable determinant fichier avec son chemin complet
    $ChemFich=$ChemImg.$keycopy.$this->Fname;

/*    $retVal.= "debug:";
    $retVal.= "valeur (ftemp): $this->ValChp<br>";
    $retVal.= "Chemin : $ChemImg<br>";
    $retVal.= "nom fichier: $this->Fname<br>";
    $retVal.= "Fok: $this->Fok<br>";
    $retVal.= "Ancien nom: $this->OFN<br>";
    $retVal.= "taille: $this->Fsize<br>"; */

    // test taille du fichier attaché     
    if (($this->Fsize>$MaxFSize) && ($this->ValChp<>"none")) {
      $retVal.= "<B>Impossible de joindre le fichier !</B><BR>La taille des fichiers attachés est limitée à $MaxFSize, ";
      unlink ($this->ValChp);
      die ($retVal." et le fichier attaché $this->Fname a une taille de $this->Fsize octets. <BR>Appuyez sur la touche retour arrière de votre navigateur");
      } // fin si fichier trop gros

      copy($this->ValChp,$ChemFich) or die("Impossible de copier le fichier". $this->Fname." depuis le fichier temporaire $this->ValChp ! (chemin complet : $ChemFich<BR>");
      delfich ($this->ValChp);
      // affecte le nouveau nom de la variable à stocker
      $value=$keycopy.$this->Fname;
      // la valeur à stocker dans la base de donnée est (à priori) le fichier précédé de la clé_
      } // fin si nouveau ou modif avec fichier attaché
    if ($this->TypEdit==-1 && $this->Fname!="")
      {// si suppression d'enregistrement on efface tous les fichiers eventuels
      $ChemFich=$ChemImg.$this->Fname;
      delfich($ChemFich);
      } // fin si suppression enregistrement
    $this->ValChp=$value;
    } // fin si type de champ=fichier attaché

 // fonction qui retourné eventuellement ' fonction du type de champ
// avec PostGres, le ' autour des champs num fout la merde
$cpstr=rt_carpstr($this->TTC);
//$cpstr="'";

  if ($this->ValChp !="") {
	$this->ValChp=$cpstr.$this->ValChp.$cpstr;
	}
  else	$this->ValChp="DEFAULT";
	
  return ($rttb ? Array($this->NmChamp=>$this->ValChp) : $this->NmChamp."=".$this->ValChp.", "); // retourne le set ou un tableau associatif...;
} // fin méthode RetSet


} // fin objet PYAobj
?>
