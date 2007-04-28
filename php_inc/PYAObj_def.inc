<?// objet PYA, permettant l'affichage de filtres, de champ dans les listes et de controle d'�ition
class PYAobj {

  var $NbCarMxCust=0;
  var $NmBase;
  var $NmTable;
  var $NmChamp;
  var $Libelle;
  var $Typaff_l; // type affichage en mode liste (tableau de plusieurs enregistrements)
  var $FieldType;
  var $TTC; 
  var $FieldValDef="";
  var $ValChp;
  var $FieldNullOk; // YES ou rien
  var $FieldKey; // cl�PRI, index=MUL, unique=UNI
  var $FieldExtra; // auto_increment
  var $TypeAff;
  var $Fccr=""; // force cases �cocher/radio en place des LD. rien ou
                // si qqchose, force. Si contient 'BR' force avec retour de lignes
                // si contient RAD force boutons radios
  var $Valeurs;
  var $Val2=""; // valeurs en plus, �alu�s en fonction de l'enregistrement
  var $nmfpopl="popl.php"; // nom du fichier popup
  var $chempopl=""; // chemin par défaut pour la popup, incluant les /
  var $TypFilt;
  var $TypCSA;
  var $Tt_AvMaj;
  var $Tt_PdtMaj;
  var $Tt_AprMaj;
  var $TypEdit; // important de savoir si on est en cr�tion ("" ou N), copie(2), �ition(1 ou M) ou consultation (C), effacement (-1)
  var $DirEcho=true; // Direct Echo: pour savoir si on echoise direct ou renvoie la valeur
  var $Comment;
  var $MaxFSize; // taille max des fichiers joints !!
  var $error;

// m�hode qui initialise toutes les propri�� de l'objet pour un champ
// simplement �partir de la d�inition du nom du champ, de la table et de la base
function InitPO($Val2assign="")  {

// Nom de la table de description des autres
// nom du champ contenant les caract�istiques globales �la table
// morceua de chaine identifiant une table virtuelle
global $TBDname,$NmChDT,$id_vtb;
$retVal="";
    if ($this->NmChamp!="" && $this->NmTable!="" && $this->NmBase!="") {
    	$ult=rtb_ultchp(); // tableau des noms de champs sensibles �la casse (�cause de pgsql...)
    	// normallement on est d��connect�
        //mysql_select_db($this->NmBase) or die ("Impossible d'ouvrir la base de donn�s $this->NmBase.");
        $rpDF=msq("select * from $TBDname where NM_TABLE='$this->NmTable' AND NM_CHAMP='$this->NmChamp'");
        if (db_num_rows($rpDF)>0) {
              $CcChp=db_fetch_assoc($rpDF);
              $this->Libelle=stripslashes($CcChp[$ult['LIBELLE']]);
              $this->Typaff_l=$CcChp[$ult['TYPAFF_L']];
              $this->TypeAff=$CcChp[$ult['TYPEAFF']];
              $this->Valeurs=$CcChp[$ult['VALEURS']];
	      if (substr($this->Valeurs,"@@")) $this->Fccr="LDF"; // si hi�archique, force les listes d�oulante
              $this->Tt_AvMaj=$CcChp[$ult['TT_AVMAJ']];
              $this->Tt_PdtMaj=$CcChp[$ult['TT_PDTMAJ']];
              $this->Tt_AprMaj=$CcChp[$ult['TT_APRMAJ']];
              $this->TypFilt=$CcChp[$ult['VAL_DEFAUT']];
              $this->TypCSA=$CcChp[$ult['TYP_CHP']];
              $this->Comment=stripslashes($CcChp[$ult['COMMENT']]);
	      
	      if (!stristr($this->NmTable,$id_vtb)) { // on cherche a caract�iser le champ que s'il n'est pas dans une table virtuelle
		$TbDescChp=db_table_defs($this->NmTable,$this->NmChamp);
//		print_r($TbDescChp);
		$this->FieldType=$TbDescChp[$this->NmChamp]['FieldType'];
		$TTC= ereg_replace('\\(.*', '', $this->FieldType); // Type de champ �ur�sans les valeurs (enum, varchar, etc ...)
		if (stristr($TTC,"blob") || stristr($TTC,"text")) $TTC="txtblob"; 
		if (stristr($TTC,"varchar") || stristr($TTC,"char")) $TTC="char";
		if (stristr($TTC,"int") || stristr($TTC,"decimal")|| stristr($TTC,"float")|| stristr($TTC,"double")) $TTC="numeric";
		$this->TTC=$TTC;
		$this->FieldValDef=$TbDescChp[$this->NmChamp]['FieldValDef'];
		// si nouvel enregistrement, affecte la valeur par d�au
		$this->FieldNullOk=strtoupper($TbDescChp[$this->NmChamp]['FieldNullOk']); // YES ou rien
		$this->FieldKey=$TbDescChp[$this->NmChamp]['FieldKey']; // cl�PRI, index=MUL, unique=UNI
		$this->FieldExtra=$TbDescChp[$this->NmChamp]['FieldExtra']; // auto_increment
		// si champ auto increment et pas cach� on le met en statique
		if (stristr($this->FieldExtra,"auto_increment") && $this->TypeAff!="HID") $this->TypeAff="STA";
		/*
					$ret[$NM_CHAMP]['FieldValDef']=($row_table_def['Default']!="" ? $row_table_def['Default'] : "" );
					// si nouvel enregistrement, affecte la valeur par d�aut
					$ret[$NM_CHAMP]['FieldNullOk']=($row_table_def['Null']=="YES" ? "yes" : "no"); // YES ou rien
					$ret[$NM_CHAMP]['FieldKey']=($row_table_def['Key']!="" ? $row_table_def['Key'] : ""); // cl�PRI, index=MUL, unique=UNI
					$ret[$NM_CHAMP]['FieldExtra']=$row_table_def['Extra']; // auto_increment 
		
		$TbDescChp=ShowField($this->NmChamp,$this->NmTable);
		$this->FieldType=$TbDescChp['Type'];
		$TTC= ereg_replace('\\(.*', '', $this->FieldType); // Type de champ �ur�sans les valeurs (enum, varchar, etc ...)
		if (stristr($TTC,"blob") || stristr($TTC,"text")) $TTC="txtblob"; 
		if (stristr($TTC,"varchar") || stristr($TTC,"char")) $TTC="char";
		$this->TTC=$TTC;
		$this->FieldValDef=$TbDescChp['Default'];
		// si nouvel enregistrement, affecte la valeur par d�aut
		if ($this->TypEdit=="") { $this->ValChp=$TbDescChp['Default']; }
		$this->FieldNullOk=$TbDescChp['Null']; // YES ou rien
		$this->FieldKey=$TbDescChp['Key']; // cl�PRI, index=MUL, unique=UNI
		$this->FieldExtra=$TbDescChp['Extra']; // auto_increment
		// si champ auto increment et pas cach� on le met en statique
		if (stristr($this->FieldExtra,"auto_increment") && $this->TypeAff!="HID") $this->TypeAff="STA";
		*/
	      } // fin si pas champ dans une table virtuelle
	    if ($this->TypEdit=="" || $this->TypEdit=="N") {
		$this->ValChp=$this->FieldValDef; 
	    } else $this->ValChp=$Val2assign;
	      
            } // fin si champ trouv�
        else $retVal= "Champ $this->NmChamp (table :$this->NmTable, base: $this->NmBase) non trouve dans la table de description";
        }
    else $retVal = "Appel de la methode InitPO de l'objet PYAObj incorrecte: au moins une des propriete de base n'a pas definie"; 
    if ($this->DirEcho) {
    	echo $retVal;
    } else {
    	return($retVal);
    }
} // fin m�hode d'initialisation$DH

// m�hode d'affichage de filtre de requete
  function EchoFilt($bDN=true,$bDL=true) { // booleens affiche n�ation et affiche libell�ie baratins sur les dates e
   global $VSLD;
    switch ($this->TypFilt) {
      case "INPLIKE":
        $retVal= "<INPUT TYPE=\"hidden\" NAME=\"tf_".$this->NmChamp."\" VALUE=\"INPLIKE\">";
        $retVal.= "<INPUT TYPE=\"text\" NAME=\"rq_".$this->NmChamp."\" VALUE=\"\">";
        break;
  
  //! ttes les listes sont �choix multiple (condition OR sur les valeurs)
      case "LDC": // Liste d�oulante affichant les diff�entes valeurs du champ  
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
  
      case "LDF": // Liste d�oulante affichant les diff�entes valeurs fixes list�s dans le champ valeur, ou les valeurs du set ou enum
        $retVal= "<INPUT TYPE=\"hidden\" NAME=\"tf_".$this->NmChamp."\" VALUE=\"LDM\">";
        // $tbval["%"]=$VSLD."%"; fait apr�
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
				// ci-dessus � reconstruit les indices...
				$tbval=array("%"=>$VSLD."(tous)")+$tbval;

        $retVal.=DispLD($tbval,"rq_".$this->NmChamp,"yes",$this->Fccr,false);
        break;
  
      case "LDL": // Liste d�oulante affichant les diff�entes valeurs li�s par le champ valeurs
        $retVal= "<INPUT TYPE=\"hidden\" NAME=\"tf_".$this->NmChamp."\" VALUE=\"LDM\">";
        $tbval=ttChpLink($this->Valeurs,$this->Val2);
	if ($this->TypeAff=="LDLM") // si liaison multiple, la cle de recherche est %,cl�%
            {
            foreach ($tbval as $key=>$val)
               {
               $key="%,$key,%";
               $tabval[$key]=$val;
               }
            }
        else $tabval=$tbval;
				//$tabval["%"]=$VSLD."(tous)"; // avant on faisait � le % se retrouve �la fin ..
        $tbjok[000]=$VSLD."(tous)"; // on fait ca pour ne pas decaler les indices dans le cas o le champ est num
                              // quand c'est un champ num, on a 0, sinon 000
			        //$tabval=array_merge($tbjok,$tabval);
				// mais cette fonction reindexe le tableau et donc fout une merdre pas croyable!!
				//astuce ci-dessous trouv� et mise par un anonyme sur internet !!!
				$tabval=$tbjok + $tabval;
// 				$retVal.= "<!-- desbog";
// 				$retVal.= $tabval[103];
// 				$retVal.= "-->";
//        asort($tbval); // pas de tri de la liste (sp�ifi�dans la liaison)
        $retVal.=DispLD($tabval,"rq_".$this->NmChamp,"yes",$this->Fcrr,false);
        break;
        
      case "DANT": // criteres sur date ant�ieure ou post�ieure
      case "DPOST":
        $retVal= "<INPUT TYPE=\"hidden\" NAME=\"tf_".$this->NmChamp."\" VALUE=\"$this->TypFilt\">";
        if ($bDL) $retVal.= "Enregistrements pour lequels $this->Libelle est ". ($this->TypFilt=="DANT" ? "ant&eacute;rieure" : "post&eacute;rieure") ." &agrave;";
        $retVal.= " <INPUT TYPE=\"text\" NAME=\"rq_".$this->NmChamp."\" VALUE=\"\">";
        break;
        
      case "DATAP": // criteres sur date ant�ieure ET post�ieure
        $retVal= "<INPUT TYPE=\"hidden\" NAME=\"tf_".$this->NmChamp."\" VALUE=\"DATAP\">";
        if ($bDL) {
             $retVal.= "Enregistrements pour lequels $this->Libelle est <br/>post&eacute;rieure &agrave; <INPUT TYPE=\"text\" NAME=\"rq_".$this->NmChamp."[0]\" VALUE=\"%\"> et ant&eacute;rieure &agrave; <INPUT TYPE=\"text\" NAME=\"rq_".$this->NmChamp."[1]\" VALUE=\"%\">";
             }
        else {
             $retVal.= "$this->Libelle > <INPUT TYPE=\"text\" NAME=\"rq_".$this->NmChamp."[0]\" VALUE=\"%\"> && < <INPUT TYPE=\"text\" NAME=\"rq_".$this->NmChamp."[1]\" VALUE=\"\">";
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
  } // fin m�hode EchoFilt
//
// M�hode qui affiche la case de n�ation
function DispNeg($DE2=true) {
        $retVal.= "<br/><span class=\"FdR\"><INPUT TYPE=\"checkbox\" NAME=\"neg_".$this->NmChamp."\" VALUE=\"neg\"> N&eacute;gation</span>";
   if ($this->DirEcho && $DE2) {
    	echo $retVal;
    } else {
    	return($retVal);
    }

        }

// m�hode d'affichage de case �cocher pour affichage optionnel du champ
  function EchoCSA() {
    switch ($this->TypCSA) { // type d'affichage s�ectionnable
      case "OCD":
      case "ONCD":
        $nmvarAfC="AfC_".$this->NmChamp;
        $retVal.= "<input type=\"hidden\" name=\"$nmvarAfC\" value=\"no\">"; // obligatoire
        if (isset($_SESSION[$nmvarAfC])) {
        	$cs=($_SESSION[$nmvarAfC]=="yes" ? ' checked="checked" ' : "");
        } else $cs=($this->TypCSA=="OCD" ? ' checked="checked" ' : "");
        $retVal.= "<input type=\"checkbox\" name=\"$nmvarAfC\" value=\"yes\" $cs>";
         
      default: 
        $retVal.= "&nbsp;";
        break;
      } // fin switch sur affichage oui ou non
    
    if ($this->DirEcho) {
    	echo $retVal;
    } else {
    	return($retVal);
    }
  } // fin m�hode EchoCSA



// m�hode affichage champ dans liste
  function EchoVCL() {
     if ($this->DirEcho) {
    	echo $this->RetVCL()."\n";
    } else {
    	return($this->RetVCL()."\n");
    }
  }

  function RetVCL($HT=true) {  // affiche valeur champ dans Liste
      // le param HT indique si l'on g��e du HTML ou du texte brut (telechargement)
      global $nbcarmxlist;
      $EiS=($HT ? "&nbsp;" : ""); // espace ins�able  HTML ou rien

      if ($this->TTC=="date") $this->ValChp=DateF($this->ValChp); // convertit dates

      switch ($this->Typaff_l) {

      case "LNK": // si champ li�a une autre table, ou liste compos�
        return $this->RVLS($EiS);  // m�hode qui retourne valeur statique (non �itable) li� externe ou pas
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
               return "<A HREF=\"".$ChemFF.$this->ValChp."\" ".(TestNFImg($this->ValChp) ? "target=\"_blank\"" : "").">".$this->ValChp."</A>";}
          else
              {return "Fichier attache".$ChemFF.$this->ValChp;}
  
          }
        break;
        
      default: 
        // nbre de car max par d�aut, ou pass�en propri��
        $NbCarMax=($this->NbCarMxCust>0 ? $this->NbCarMxCust : $nbcarmxlist);
        $Val2Af=stripslashes($this->ValChp);
        $Val2Af=tronqstrww ($Val2Af,$NbCarMax); // + malin,
        //$Val2Af=substr($Val2Af, 0, $NbCarMax); // au max xx caract�es
        if ($HT) { 
        	$Val2Af=DispCustHT($Val2Af);
	  	$Val2Af=($Val2Af=="" ? "$EiS" : $Val2Af);
        }
        return ($Val2Af);
        break;
       } // fin du switch
  } // fin methode EchoVCL

// m�hode commune aux 2 m�hodes liste et �ition, qui affiche les valeurs li�s en statique
function RVLS($EiS) {
        // LnkTb=true si liste li� �une table, false si liste de valeurs fixes
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
          if (strstr($cle,",")) { // plusieurs valeurs s�ar�s par des virgules (type pseudo-set multiple)
            $tabcle=explode(",",$cle);
            foreach ($tabcle as $cle) {
                if ($cle!="") { // ignore valeurs vides
                   if ($LnkTb) {
                      $valsuiv=ttChpLink($this->Valeurs,"",$cle); 
                      }
                   else $valsuiv=$tbval[$cle];
                   $valaff.=($valsuiv ? "$valsuiv, ": "NC[$cle], "); // affiche si trouv�, ou erreur
                } // fin si valeur bnon vide
              }
            $valaff=vdc($valaff,2); // vire la derni�e virgule et esp
            }
          else { 
              if ($LnkTb) {
                 $valsuiv=ttChpLink($this->Valeurs,"",$cle); }
              else $valsuiv=$tbval[$cle];
            $valaff=($valsuiv ? "$valsuiv": "NC[$cle]"); // n'affiche si trouv�
            }
          } // fin si $cle!=""
        $valaff=($valaff!=""? $valaff : $EiS);
        return $valaff;
   } // fin fonction



// m�hode qui affiche le controle
// on a s�ar�les auto et manuels
function EchoEditAll($DH=true) {
  if ($this->TypeAff!="AUT") {
    return($this->EchoEdit($DH));}
  else return($this->EchoEditAuto());
  }

// m�hode qui affiche le controle d�ition
// pour tes les type d'aff <> de AUT  
function EchoEdit($DH=true) {
  global $VSLD,$nbrtxa,$nbctxa;
  
  if (!strstr($this->FieldType,'date')) { 
    if ($this->TypeAff!="HID") $this->ValChp=stripslashes($this->ValChp); }
  else {
  	$this->ValChp=DateF($this->ValChp);
  	$size=' SIZE="10" ';
  	$mxsize=' maxlength="10" ';
  	}

  if ($this->TypEdit=="C") { // si on est en consultation, on passe tout en STA ou STAL ou rien

   if ($this->ValChp=="00/00/2000") $this->ValChp="-";
   switch ($this->TypeAff) {
   //chgt en statique normale
        case "AUT": // AUto
	case "TXT": // boite texte normale
        case "TXA": // texte area
           $this->TypeAff="STA";
           break;

   //chgt en statique normale forc� (on recherche les paires cl�: valeurs dans this->Valeurs
        case "LD": // liste d�oulante non li�
        case "LDM": // liste d�oulante non li� MULTIPLE
        case "LDL":
        case "LDLM":
	case "POPL":
	case "POPLM":
        // on est en consultation forc�.
        // Ici on regarde si dans les valeurs s'il y a : et, ce qui implique que la valeur �afficher
        // est certainement �piocher dans une liste cl�valeurs et non �afficher telle quelle
           $Val2Af=DispCustHT($this->RVLS("&nbsp;"));
           $retVal.= $Val2Af."<INPUT TYPE=\"hidden\" NAME=\"".$this->NmChamp."\" ID=\"".$this->NmChamp."\" VALUE=\"".$this->ValChp."\">";
           $this->TypeAff="STAF"; // pour ne plus rien faire ...
           break;

     // le reste on change pas, les photos sont trait�s dans la proc elle-meme
     } // fin switch
   }   // fin si on est en consultation

   // traitement Ev�ements pendant mise �jour
   if ($this->Tt_PdtMaj!="") {
      $tbeven=explode(":",$this->Tt_PdtMaj);
      $evJS=" ".$tbeven[0]."=\"".$tbeven[1]."\"";
      }
   else $evJS="";

   switch ($this->TypeAff) {
    case "": // on affiche rien
      break;
      
    case "HID": // on affiche un champ cach�que si $DH=true, sinon rien
      if ($DH) $retVal.= "<INPUT TYPE=\"hidden\" NAME=\"".$this->NmChamp."\" ID=\"".$this->NmChamp."\" VALUE=\"".htmlspecialchars($this->ValChp)."\">";
      break;
  
    // pour indiquer qu'in ��ent est s�ectionn� met VSLD devant (#SEL#)
    case "LD": // liste d�oulante non li�
      $defl=explode(',',$this->Valeurs);
      if ($this->FieldNullOk=="YES")   
        {  // si null autoris�
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
        $tbval[$key]=stripslashes($val); // au cas ou ' dans les libelles
        $tbkey[]=$key;
      }
      if (!in_array($this->ValChp,$tbkey) && $this->ValChp!="")
        $retVal.= "<H6>Erreur : la valeur $this->ValChp contenue dans le champ n'est pas dans la liste ! </H6>";
      $retVal.=DispLD($tbval,$this->NmChamp,"no",$this->Fccr,false); // lise der. non multiple
      break;

    case "LDM": // liste d�oulante non li� MULTIPLE
      $defl=explode(',',$this->Valeurs);
      $tbvalm=explode(',',$this->ValChp);
      
      if ($this->Fccr=="" || $this->Fccr=="LDF") $tbval["NULL"]="Aucun(e)"; // valeur vide
      $Am1S=false; // Init au moins une valeur s�ectionn�
      foreach ($defl as $val) {
        if (strstr($val,":")) {
           $valtb=explode(":",$val);
		$key=$valtb[0];
           $val=$valtb[1];
           }
        else $key=$val;
        if (in_array($key, $tbvalm)) {
              $val=$VSLD.$val; // indique les s�ectionn�
              $Am1S=true; // Init au moins une valeur s�ectionn�
              }
        $tbval[$key]=$val;
        $tbkey[]=$key;
          }
      // controle d'erreur

      // si aucun de s�ectionn�...
      if (!$Am1S && ($this->Fccr=="" || $this->Fccr=="LDF")) $tbval["NULL"]=$VSLD."Aucun(e)";

      unset($err);
      foreach ($tbvalm as $valm) {
        if (!in_array($valm,$tbkey) && $valm!="") $err.=$valm.", ";
        }
      if ($err!="") {
        $err=vdc($err,2); // enl�e dernier", ";
        $retVal.= "<H6>Erreur : valeur(s) $err contenue(s) non presente(s) dans la liste ! </h6>";
        }
      $retVal.=DispLD($tbval,$this->NmChamp,"yes",$this->Fccr,false);
      break;
      
    case "LDL": // liste d�oulante li�
      /* Syntaxe
      (base,user,passwd);table,chpcl�chp_aff1,chpaff2,...,chpaffn)
      */      
      $tabCorlb=ttChpLink($this->Valeurs,$this->Val2);
      if (strstr($this->Valeurs,"@@")) {  // si hierachique
	$valnul=($this->TTC=="char" ? "''" : 0);
        $val=(($this->ValChp==$valnul) ? $VSLD.$valnul : $valnul);
        $tbval[$valnul]="Aucun, elle-meme";
        }
      elseif ($this->FieldNullOk=="YES") {  // si null autorise
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

    case "LDLM": // liste d�oulante li� MULTIPLE
      /* Syntaxe de TYPA_VAL
      (base,user,passwd);table,chpcl�chp_aff1,chpaff2,...,chpaffn)
      */
      $tabCorlb=ttChpLink($this->Valeurs,$this->Val2);
      //dans le champ lui m�e, les valeurs cl� sont stock�s s�ar�s par des virgules
      $tbvalm=explode(',',$this->ValChp);

     if (strstr($this->Valeurs,"@@")) $valsh=" ou elle-meme";
      $valnul=($this->TTC=="char" ? "''" : 0);
      $tbval[$valnul]="Aucun(e)".$valsh;


      $tberrck=array(); // clear tableau de controle d'erreur
      $Am1S=false; // Init au moins une valeur s�ectionn�
      foreach ($tabCorlb as $key=>$val) {
        $tberrck[]=$key;
        if (in_array($key, $tbvalm))
           { $val=$VSLD.$val; // raj les caract indiquant la s�ection
           $Am1S=true; // au mis une valeur s�ectionn�
           }
        $tbval[$key]=$val;        
        }

      // si aucun de s�ectionn�...
      if (!$Am1S) $tbval[$valnul]=$VSLD."Aucun(e)".$valsh;
      // controle d'erreur
      if (is_array($tbvalm)) {
        unset($err);
        foreach ($tbvalm as $valm) {
          if (!in_array($valm,$tberrck) && $valm!="") $err.=$valm.", ";
          }
        if ($err!="") {
          $err=vdc($err,2); // enl�e dernier", ";
          $retVal.= "<H6>Erreur : valeur(s) $err contenue(s) dans le champ non pr�ente(s) dans la liste ! </h6>";
          }
        }        
      $retVal.=DispLD($tbval,$this->NmChamp,"yes",$this->Fccr,false); // liste d�oulante, multiple
      break;
    
    $Mult=false;
    case "POPLM": // popup de s�ection
    $Mult=true;
    case "POPL": // popup de s�ection
		//$retVal.= "Valeur du lien : ".$this->Valeurs.", valeur du champ : ".$this->ValChp;
	
	if ($this->ValChp) { // si valeur existe
		if (strstr($this->ValChp,",")) {
			$tabk=explode(",",$this->ValChp);
//			print_r ($tabk);
			foreach($tabk as $k) {
				if ($k !="") $tabVS[$k]=ttChpLink($this->Valeurs,"",$k);
			}
		} else {
		$tabVS[$this->ValChp]=ttChpLink($this->Valeurs,"",$this->ValChp);
		}
	}
	
	$retVal.='<SELECT ID="'.$this->NmChamp.'" NAME="'.$this->NmChamp.($Mult ? "[]" : "").'" ID="'.$this->NmChamp.'" SIZE="'.(($Mult ? max(1,count($tabVS)) : 1)).'" MULTIPLE=\"MULTIPLE\" >';

	if ($tabVS) {
		foreach ($tabVS as $k=>$v) {
		$retVal.="<OPTION VALUE=\"$k\" SELECTED=\"SELECTED\">$v</OPTION>";
		}
	} else $tabVS=Array();
	$retVal.='</SELECT>';
	
	$retVal.= '<input type="button" class="fxbutton" onclick="popup(\''.$this->chempopl.$this->nmfpopl.'?Valeurs='.urlencode($this->Valeurs).'&NmChp='.$this->NmChamp.'&Mult='.($Mult ? 10 : 1).'\',500,500)" value=" M " />';

	break;
    		
    case "FICFOT":
      if ($this->TypEdit==2) $this->ValChp=""; // annule si copie
      $retVal.= "<INPUT TYPE=\"hidden\" NAME=\"Old".$this->NmChamp."\"  ID=\"Old".$this->NmChamp."\" VALUE=\"".$this->ValChp."\">";
        if ($this->ValChp!="") {  // regarde si chemin param�r�correctement
          if (strpos ($this->Valeurs,"#")!=1 && $this->Valeurs!="") {
              $ChemFF=$this->Valeurs;}
          else {
               $ChemFF="./".$this->NmBase."_".$this->NmTable."_".$this->NmChamp."/";}

        $NCF=$ChemFF.$this->ValChp;
        if (TestNFImg($this->ValChp)) // si c'est un fichier de type image
          { $retVal.= "<P><IMG SRC=\"".$NCF."\"></P>";
          }
        else
          { $retVal.= "Lien vers le fichier associe : <A HREF=\"".$NCF."\" ".(TestNFImg($this->ValChp) ? "TARGET=\"_blank\"" : "").">".$this->ValChp."</A><br/>";}

        if ($this->TypEdit!="C") $retVal.= "<INPUT TYPE=\"checkbox\" NAME=\"Fok".$this->NmChamp."\" VALUE=\"".$this->ValChp."\" CHECKED>".$this->ValChp."&nbsp;";
        }// Fin si il y a un fichier associe
        if ($this->TypEdit!="C") { // saisie que si pas en �ition
           $retVal.= "<INPUT TYPE=\"file\" NAME=\"".$this->NmChamp."\" SIZE=\"30\" MAXLENGTH=\"200\">";
           if ($this->ValChp!="") $retVal.= "<FONT SIZE=\"-2\"><br/>Pour supprimer l'image ou le fichier, decocher la case, ou choisissez-en un(e) autre. Sinon ne touchez a rien ...</font>";
      }
      break; // fin champ de type fichier-photo

      case "STAF": // statique forc�
           // on ne fait rien, on l'a d��fait en haut
      break;

      case "STA": // statique  simple
     // remplace caract�e merdiques et converti adresses mail en cliquables pour le HTML
      $Val2Af=DispCustHT($this->ValChp);
      $retVal.= $Val2Af."<INPUT TYPE=\"hidden\" NAME=\"".$this->NmChamp."\" ID=\"".$this->NmChamp."\" VALUE=\"".htmlspecialchars($this->ValChp)."\">";
      break;

    case "STAL": // statique li�
      $retVal.= (ttChpLink($this->Valeurs,"",$this->ValChp));
      $retVal.= "<INPUT TYPE=\"hidden\" NAME=\"".$this->NmChamp."\" ID=\"".$this->NmChamp."\" VALUE=\"".$this->ValChp."\">";

      break;

    case "TXA":
        $defl=explode(',',$this->Valeurs);
        // pour les dimensions, on prend les chiffres dans le champ valeurs (nblignes, nbcolonnes)
        $ligs=(($defl[0]>0) ? $defl[0] : $nbrtxa);
        $cols=(($defl[1]>0) ? $defl[1] : $nbctxa);
        $retVal.= "<TEXTAREA COLS=\"$cols\" ROWS=\"$ligs\" NAME=\"".$this->NmChamp."\" ID=\"".$this->NmChamp."\">".$this->ValChp."</TEXTAREA>";
      break;

    case "TXT":
        $defl=explode(',',$this->Valeurs);
        // pour la size et maxlength , on prend les chiffres dans le champ valeurs (size,maxlength)
        $size=(($defl[0]>0) ? " SIZE=\"$defl[0]\" ": "");
        $mxsize=(($defl[1]>0) ? " maxlength=\"$defl[1]\" " : "");
    default :
        $retVal.= "<INPUT TYPE=\"text\" NAME=\"".$this->NmChamp."\" ID=\"".$this->NmChamp."\" VALUE=\"".htmlspecialchars($this->ValChp)."\" $size $mxsize $evJS>";
      break;
     } // fin switch sur type d'affichage
    
    if ($this->DirEcho) {
    	echo $retVal;
    } else {
    	return($retVal);
    }

  } // fin m�hode EchoEdit

// m�hode qui affiche le controle d�ition
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
	$retVal.= "<input type=\"radio\" name=\"$this->NmChamp\" ID=\"".$this->NmChamp."\" value=\"TRUE\" $ck>vrai (true)\n";
	$retVal.= "<input type=\"radio\" name=\"$this->NmChamp\" ID=\"".$this->NmChamp."\" value=\"FALSE\" $ck>faux (false)\n";
	break;

     case "enum" : // liste de valeurs
       // la juste dessous pomp�de phpMyAdmin sans tout bien comprendre
       $enum = str_replace('enum(', '', $this->FieldType);
            $enum = ereg_replace('\\)$', '', $enum);
            $enum = explode('\',\'', substr($enum, 1, -1));
            $enum_cnt = count($enum);
       if ($enum_cnt<=$nValRadLd || $this->Fccr) { // si - de 3, cases �option
         for ($l=0;$l<$enum_cnt;$l++) {
           $ck=($enum[$l]==$this->ValChp ? "checked" : "");
           $retVal.= "<input type=\"radio\" name=\"$this->NmChamp\" ID=\"".$this->NmChamp."\" value=\"$enum[$l]\" $ck>$enum[$l]\n";
               }
         } // fin si cases �options 
       else { // si + de 3, liste d�oulante
         $retVal.= "<select name=\"$this->NmChamp\" ID=\"".$this->NmChamp."\">";
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
       if ($enum_cnt<=$nValRadLd || $this->Fccr) { // si - de 3, cases �cocher
         for ($l=0;$l<$enum_cnt;$l++) {
           $ck=(stristr($this->ValChp,$enum[$l]) ? "checked" : "");
           $retVal.= "<input type=\"checkbox\" name=\"".$this->NmChamp."[]\"  ID=\"".$this->NmChamp."\" value=\"".$enum[$l]."\" $ck>".$enum[$l]."&nbsp;&nbsp;&nbsp;";
               }
         }
       else { // liste d�oulante �choix multiples (variable tableau)
         $retVal.= "<select name=\"".$this->NmChamp."[]\"  ID=\"".$this->NmChamp."\" multiple size=\"".min($enum_cnt ,4)."\">";
         for ($l=0;$l<$enum_cnt;$l++) {
           $ck=(stristr($this->ValChp,$enum[$l]) ? "selected" : "");
           $retVal.= "<option value=\"$enum[$l]\" $ck>$enum[$l]</option>\n";
               }
         $retVal.= "</select>";
         }
       break;

     case "date" : // on passe la date en fran�is
         $retVal.= "<INPUT TYPE=\"text\" NAME=\"".$this->NmChamp."\"  ID=\"".$this->NmChamp."\" VALUE=\"".DateF($this->ValChp)."\" size=\"10\" maxlength=\"10\">";
       break;

     case "txtblob" : // textarea
         $retVal.= "<TEXTAREA COLS=\"$nbctxa\" ROWS=\"$nbrtxa\" NAME=\"".$this->NmChamp."\"  ID=\"".$this->NmChamp."\">".$this->ValChp."</TEXTAREA>";
       break;

     case "char" : // char ou varchar

       $lg=strrchr($this->FieldType, "(");
       $lg= substr($lg,1,-1); // ne r�up�e que l'entier
       $size=min(intval($lg*1.5),100);
         $retVal.= "<INPUT TYPE=\"text\" NAME=\"".$this->NmChamp."\"  ID=\"".$this->NmChamp."\" VALUE=\"".htmlspecialchars($this->ValChp)."\" ".($lg > 0 ? "maxlength=\"$lg\" size=\"$size\"": "").">";
       break;

     default :
         $retVal.= "<INPUT TYPE=\"text\" NAME=\"".$this->NmChamp."\"  ID=\"".$this->NmChamp."\" VALUE=\"".htmlspecialchars($this->ValChp)."\">";
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
         if ($this->TypEdit==1 || $this->TypEdit=="M") {
            if ($this->TypeAff=="LDL" || $this->TypeAff=="LDLM"){
               $this->TypeAff="STAL";
               }
            else $this->TypeAff="STA";
         }
    default :
      break;
    } // fin switch sur type de MAJ avant
  } // fin m�hode d'init ava MAJ

// m�hode retourne un set pour les mises �jour
// et g�e aussi les fichiers joints (!)

function RetSet($keycopy,$rttb=false) {
global $MaxFSizeDef;
$keycopy=str_replace("'","",$keycopy);
if ($this->MaxFSize==0 || $this->MaxFSize=="") $this->MaxFSize=$MaxFSizeDef ;

  if ($this->TTC=="date") // date
    {$this->ValChp=DateA($this->ValChp);}
  elseif  (is_array($this->ValChp))  // tableau
    { // on vient d'un set ou d'une liste d�oulante multiple (li� ou pas) et la var est un tableau
		if($this->TTC!="set") $valset=","; // Nouveau: avant rien
		foreach($this->ValChp as $val){
			if ($val!="NULL" && $val!="") $valset.=addslashes($val).",";
			}
		if($this->TTC=="set")	$valset=substr($valset,0,-1); // enl�e la derni�e virgule �la fin
				$this->ValChp=$valset; // laisse la derni�e virgule �la fin
    } // fin si valeur est un tableau
  elseif ($this->TTC=="numeric") {
	$this->ValChp=$this->ValChp + 0; // astuce pour convertir en nombre si pas bon
   }
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
    $value=$this->OFN; // valeur du set par d�aut

    if (strpos ($this->Valeurs,"#")!=1 && $this->Valeurs!="") {
          $ChemImg=$this->Valeurs;}
    else {
          $ChemImg="./".$this->NmBase."_".$this->NmTable."_".$this->NmChamp."/";}

	//print_r($this);
    if ($this->TypEdit==1 || $this->TypEdit=='M') { // gestion effacement anciens fichiers attach�
      // test decoche ou changement en modif
    // pour effacement de l'ancien fichier attach�
       if ($this->OFN!="" && ($this->Fok=="" || ( $this->ValChp!="none" && $this->ValChp!="" && $keycopy.$this->Fname!= $keycopy.$this->OFN)))
       // condition: ancien fichier attach�existe ($$VarOldFichImg!="")
       // ET
          // la case a ��d�och�  ($$VarFok$FichImg=="")
       // OU
           // un nouveau fichi�a ��entr�($FichImg!="none")
            // ET
            // le nom de ce fichier n'est pas le m�e que l'ancien
          {
           delfich($ChemImg.$this->OFN);
           $value="";
          }
    }   // fin gestion effacement anciens fichiers attach�
    
    // si nouveau ou modif avec fichier attach�
    if ($this->TypEdit!=-1 && $this->ValChp!="none" && $this->ValChp!="") {
	// remplacement des caracteres Zarbi ds les noms de fichier cause bugs
	$this->Fname=addslashes(strtr($this->Fname," àéèûêçïûüùîâäôö'","_aeeueciuuuiaaoo_"));
	
	// variable determinant fichier avec son chemin complet
	$ChemFich=$ChemImg.(strstr($this->Fname,$keycopy) ? "" : $keycopy).$this->Fname; // ne remet la clé que si elle y est pas déjà.
	
	/*    $retVal.= "debug:";
	$retVal.= "valeur (ftemp): $this->ValChp<br/>";
	$retVal.= "Chemin : $ChemImg<br/>";
	$retVal.= "nom fichier: $this->Fname<br/>";
	$retVal.= "Fok: $this->Fok<br/>";
	$retVal.= "Ancien nom: $this->OFN<br/>";
	$retVal.= "taille: $this->Fsize<br/>"; */
	
	// test taille du fichier attach�    
	if (($this->Fsize>$this->MaxFSize) && ($this->ValChp!="none")) {
		$retVal.= "<B>Impossible de joindre le fichier !</B><br/>La taille des fichiers attaché est limitée à  ".$this->MaxFSize.", ";
		unlink ($this->ValChp);
		$this->error=$retVal." et le fichier attache ".$this->Fname." a une taille de ".$this->Fsize." octets. <br/>Cliquez sur le bouton retour arriere de votre navigateur";
		$this->ValChp=$value=""; // annule le nom de fichier pour qu'il ne soit mis en base...
	} else { // fin si fichier trop gros
		$retcopy=copy($this->ValChp,$ChemFich);
		if (!$retcopy) { 
			$this->error="Impossible de copier le fichier". $this->Fname." depuis le fichier temporaire $this->ValChp ! (chemin complet : $ChemFich<br/>";
			delfich ($this->ValChp);
		}
	}
	// affecte le nouveau nom de la variable �stocker
	if (!$this->error) $value=(strstr($this->Fname,$keycopy) ? "" : $keycopy).$this->Fname;// ne remet la clé que si elle y est pas déjà.
	// la valeur �stocker dans la base de donn� est (�priori) le fichier pr���de la cl�
    } // fin si nouveau ou modif avec fichier attach�
    if ($this->TypEdit==-1 && $this->Fname!="") {// si suppression d'enregistrement on efface tous les fichiers eventuels
        $ChemFich=$ChemImg.$this->Fname;
        delfich($ChemFich);
    } // fin si suppression enregistrement
    $this->ValChp=$value;
} // fin si type de champ=fichier attach�

 // fonction qui retourn�eventuellement ' fonction du type de champ
// avec PostGres, le ' autour des champs num fout la merde
$cpstr=rt_carpstr($this->TTC);
//$cpstr="'";

  if ($this->ValChp !="") {
	$this->ValChp=$cpstr.$this->ValChp.$cpstr;
	}
  else	$this->ValChp=(($_SESSION['db_type']!="mysql" && isset($_SESSION['db_type'])) ? "DEFAULT" : ($this->FieldNullOk!="NO" ? "NULL" : "''"));
	
  return ($rttb ? Array($this->NmChamp=>$this->ValChp) : $this->NmChamp."=".$this->ValChp.", "); // retourne le set ou un tableau associatif...;
} // fin m�hode RetSet


} // fin objet PYAobj
?>