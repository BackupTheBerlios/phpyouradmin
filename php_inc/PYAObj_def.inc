<?// objet PYA, permettant l'affichage de filtres, de champ dans les listes et de controle d'�ition
class PYAobj {

	var $NbCarMxCust=0;
	var $NmBase;
	var $NmTable;
	var $NmChamp;
	var $Libelle;
	var $Typaff_l = "NOR"; // type affichage en mode liste (tableau de plusieurs enregistrements)
	var $FieldType;
	var $TTC; 
	var $FieldValDef="";
	var $ValChp;
	var $ValChpAvMaj; // valeur champ avabt MAJ
	var $FieldNullOk; // YES ou rien
	var $FieldKey; // clePRI, index=MUL, unique=UNI
	var $FieldExtra; // auto_increment
	var $TypeAff = "AUT";
	var $Fccr=""; // force cases a cocher/radio en place des LD. rien ou
				// si qqchose, force. Si contient 'BR' force avec retour de lignes
				// si contient RAD force boutons radios
	var $Valeurs;
	var $Val2=""; // valeurs en plus, Statuts en fonction de l'enregistrement
	var $nmfpopl="popl.php"; // nom du fichier popup
	var $chempopl=""; // chemin par défaut pour la popup, incluant les /
	var $TypFilt;
	var $TypCSA;
	var $Tt_AvMaj;
	var $Tt_PdtMaj;
	var $Tt_AprMaj;
	var $TypEdit; // important de savoir si on est en creation ("" ou N), copie(2), edition(1 ou M) ou consultation (C), effacement (-1)
	var $DirEcho=true; // Direct Echo: pour savoir si on echoise direct ou renvoie la valeur
	var $Comment;
	var $MaxFSize; // taille max des fichiers joints !!
	var $MaxImgWidth=0; // largeur max des images
	var $dateInpAttribs=' class="dateinput" size="8" maxlength="10" ';
	var $error;
/// Méthode EchoCompField
// nom des classes par défaut utilisées pour EchoCompField
	var $classDivECF = "DivECF";
	var $classFieldECF = "FieldECF";
	var $classLabECF = "LabECF";
	var $classCSAECF = "CSAECF";
// propiriétés (par défaut) utilisées par EchoCompField
	var	$modTable=false ;// mode table / div
	var	$br=false;//  CR après libellé
	var	$dispComment=false;// affichage commentaire
	var	$altLibel="";//  autre libellé que celui stocké en base
	//Pour filtre uniquement: 
	var	$bDN = true ;// display négation
	var	$bDL=true ; // baratin libellés dates
	var	$choixMult="yes"; // 
	var	$dispCSA="yes"; // display champ sélectionnable ou n'importe quoi <> no
	// Pour Edit uniquement : 
	var	$DH = true; //display Hidden

  
  
	// methode qui initialise toutes les propriete de l'objet pour un champ
	// simplement a partir de la definition du nom du champ, de la table et de la base
	function InitPO($Val2assign="",$CcChp=false)  {
		// Nom de la table de description des autres
		// nom du champ contenant les caracteristiques globales a la table
		// morceua de chaine identifiant une table virtuelle
		global $TBDname,$NmChDT,$id_vtb;
		if (!$TBDname) $TBDname="DESC_TABLES";
		// nom du champ contenant les caract�istiques globales �la table
		if (!$NmChDT) $NmChDT="TABLE0COMM";
		// id contenu ds les tables virtuelles ie celles qui n'existent pas en base
		if (!$id_vtb) $id_vtb = $GLOBALS["id_vtb"] ? $GLOBALS["id_vtb"] : "_vtb_";
		$sepNmTableNmChp = "#";

		$ult=rtb_ultchp(); // tableau des noms de champs sensibles a la casse (a cause de pgsql...)
		$retVal="";
		// verrue pour les req custom: dans le cas d'un UNION, ce con de mysql est buggué, la fonction db_field_table($resreq,$i) ne marche pas !!
		// on cherche donc à retrouver le nom de la table via la table de description
		if ($this->NmTable=="") {
			$rpDF=db_query("select * from $TBDname where  NM_CHAMP='$this->NmChamp'");
			if (db_num_rows($rpDF) == 1) {
				$CcChp=db_fetch_assoc($rpDF);
				$this->NmTable = $CcChp[$ult['NM_TABLE']];
			} else {
				$retval = "Impossible de déterminer automatiquement la table du champ ".$this->NmChamp;
			}
		}
		if ($this->NmChamp!="" && $this->NmTable!="" && $this->NmBase!="") {
			if (!$CcChp) { // les args de champ ne sont pas passés
				$rqsh = "select * from $TBDname where NM_TABLE LIKE '$this->NmTable' AND NM_CHAMP LIKE '$this->NmChamp'";
				$CcChp = db_qr_rass2($rqsh);
				if (!$CcChp) $retVal .= "Champ $this->NmChamp (table :$this->NmTable, base: $this->NmBase) non trouve dans la table de description";// <br/>$rqsh<br/>
			}
			
			if ($CcChp) {
				$this->Libelle=tradLib(stripslashes($CcChp[$ult['LIBELLE']]));
				$this->Typaff_l=$CcChp[$ult['TYPAFF_L']];
				$this->TypeAff=$CcChp[$ult['TYPEAFF']];
				$this->Valeurs = $CcChp[$ult['VALEURS']];
				$this->ParseValeurs(); // parse la valeur avec la nouvelle syntaxe avancée..
				if (strstr($this->Valeurs,"@@")) $this->Fccr="LDF"; // si hiérarchique, force les listes déroulantes
				$this->Tt_AvMaj=$CcChp[$ult['TT_AVMAJ']];
				$this->Tt_PdtMaj=$CcChp[$ult['TT_PDTMAJ']];
				$this->Tt_AprMaj=$CcChp[$ult['TT_APRMAJ']];
				$this->TypFilt=$CcChp[$ult['VAL_DEFAUT']];
				$this->TypCSA=$CcChp[$ult['TYP_CHP']];
				$this->Comment=stripslashes($CcChp[$ult['COMMENT']]);
				if (!stristr($this->NmTable,$id_vtb)) { // on cherche a caracteriser le champ que s'il n'est pas dans une table virtuelle
					$TbDescChp=db_table_defs($this->NmTable,$this->NmChamp);
			//		print_r($TbDescChp);
					$this->FieldType=$TbDescChp[$this->NmChamp]['FieldType'];
					$TTC= ereg_replace('\\(.*', '', $this->FieldType); // Type de champ épuré sans les valeurs (enum, varchar, etc ...)
					if (stristr($TTC,"blob") || stristr($TTC,"text")) $TTC="txtblob";
					if (stristr($TTC,"date")) $TTC="date";  // ds oracle DATE est en maj et ça fout la merde
					if (stristr($TTC,"varchar") || stristr($TTC,"char")) $TTC="char";
					if (stristr($TTC,"int") || stristr($TTC,"decimal")|| stristr($TTC,"float") || stristr($TTC,"double") || stristr($TTC,"NUMBER"))  $TTC="numeric";
					$this->TTC=$TTC;
					$this->FieldValDef = $TbDescChp[$this->NmChamp]['FieldValDef'];
					// si nouvel enregistrement, affecte la valeur par d�au
					$this->FieldNullOk = strtoupper($TbDescChp[$this->NmChamp]['FieldNullOk']); // YES ou rien
					$this->FieldKey = $TbDescChp[$this->NmChamp]['FieldKey']; // cl�PRI, index=MUL, unique=UNI
					$this->FieldExtra = $TbDescChp[$this->NmChamp]['FieldExtra']; // auto_increment
					// si champ auto increment et pas caché on le met en statique
					if (stristr($this->FieldExtra,"auto_increment") && $this->TypeAff!="HID") $this->TypeAff="STA";
				} // fin si pas champ dans une table virtuelle
				if ($this->TypEdit=="" || $this->TypEdit=="N" || !$Val2assign) {
						$this->ValChp=$this->FieldValDef; 
				} else $this->ValChp=$Val2assign;
			// test l'existence de la cste qui dit si on met les noms de table dans les var html
				if(!defined("DefOutNmTblInHtmlVarName")) {
					define("DefOutNmTblInHtmlVarName",false);
				}
				if(!defined("sepNmTableNmChp")) { // car de séparation NOM_TABLE#NOM_CHAMP
					define("sepNmTableNmChp","#"); // sécurité
				}
				
				$this->NmChpHtml = (DefOutNmTblInHtmlVarName ? $this->NmTable.sepNmTableNmChp : "").$this->NmChamp; // si $this->outNmTblInHtmlVarName sort les var html ss la forme nomtable.nomchamp
			} else {
				$retVal .= "; champ non trouvé ou paramètres non passés en arguments";
			} // fin si champ trouvé ou renseigné
		} else {
			$retVal .= "Appel de la methode InitPO de l'objet PYAObj incorrecte: au moins une des propriete de base n'a pas definie; NmChamp=".$this->NmChamp."; NmTable=".$this->NmTable."; NmBase=".$this->NmBase."<br/>\n";
		}

		if ($this->DirEcho && $retVal) {
			echo "<p><small>$retVal</small></p>";
		}
		return($retVal); // retourne quoi qu'il arrive, normalement c'est vide, si ya qqchose c kya une erreur et ça la décrit
	} // fin m�hode d'initialisation$DH
	/// methode affichage de type <div class="classDiv"><label class="classLab"  for="theField">The Label </label>(<br/>)<span class="classField">The Field</span></div>
	/// ou "<tr class="classDivECF"><td><label class="classLabECF"  for="theField">The Label </span></td><td><span class="classFieldECF">The Field</span></td></tr>
	/// s'applique aux filtres, ou edition
	/// On peut passer les args en tableau, ou comme propriétés au choix

	function EchoCompField($type,$tbArgs=array()) {
		/* 
		$type = Edit | Filt
		$tbArgs : 
			$modTable=false mode table
			$br=false CR après libellé
			$dispComment=false
			$altLibel="": autre libellé que celui stocké en base
		Pour filtre uniquement: 
			$bDN (display négation)=true 
			$bDL=true; baratin libellé dates
			$choixMult="yes"
			$dispCSA="yes" ou n'importe quoi <> no
		Pour Edit uniquement : 
			$DH (display Hidden) = true
		*/
		$this->NmChpHtml4Edit = $this->sepNmTableNmChpInEdit ? $this->NmChpHtml : $this->NmChamp;
		foreach ($tbArgs as $c=>$v) $this->$c = $v;
		if ($this->modTable) {
			$retVal = '<tr class="'.$this->classDivECF.'"><td>';
		} else $retVal = '<div class="'.$this->classDivECF.'">';
		$retVal .= '<label class="'.$this->classLabECF.'" for="'.($type == "Filt" ? "rq_" : "").$this->NmChpHtml4Edit.'">'.($this->altLibel ? $this->altLibel : $this->Libelle);
		if ($this->Comment && $this->dispComment) $retVal .='<br/><small>'.tradLib($this->Comment).'</small>';
		$retVal .= '</label>';
		if ($this->br) $retVal .= '<br/>';
		if ($this->modTable) {
			$retVal .= '</td><td>';
		}
		$retVal .= '<span class="'.$this->classFieldECF.'">';
		// sinon les meth  EchoEditAll & EchoFilt echoisent et ça fout la merde
		$DirEchoSave = $this->DirEcho;
		$this->DirEcho = false;
		if ($type == "Edit") {
			$retVal .= $this->EchoEditAll($this->DH);
		} elseif ($type == "Filt") {
			$retVal .= $this->EchoFilt($this->bDN,$this->bDL,$this->choixMult);
		} else {
			$err = "<h3>type $type inconnu pour methode PYA->EchoCompField</h3>";
			echo $err;
			return($err);
		}
		$retVal .= '</span>';
		if ($type == "Filt" && $this->dispCSA!="no" && $this->TypCSA!="") {
			if ($this->modTable) {
				$retVal .= "<td>".$this->EchoCSA()."</td>";
			} else 	
				$retVal .= '<div class="'.$this->classCSAECF.'" >'.$this->EchoCSA(true)."</div>\n";
		}
		if ($this->modTable) {
			$retVal .= "</td></tr>\n";
		} else 	$retVal .= "</div>\n";
		$this->DirEcho = $DirEchoSave;
		if ($this->DirEcho) {
				echo $retVal;
			} else {
				return($retVal);
			}
	} // fin meth ec

	// methode d'affichage de filtre de requete
	function EchoFilt($bDN=true,$bDL=true,$choixMult="yes") { // booleens affiche n�ation et affiche libell�ie baratins sur les dates e
		global $VSLD,$id_vtb;
		if (!$VSLD) $VSLD = "#SEL#";
		$id_vtb = $id_vtb != "" ? $id_vtb : "_vtb_";
		if (stristr($this->NmTable,$id_vtb)) { // si table virtuelle alias, la vrai table doit être indiquée dans le champs VALEURS avec la nouvelle syntaxe cad $physTable=
			$nmtable = $this->physTable;
			if ($nmtable == "" && ($this->TypFilt == "LDC" || $this->TypFilt == "LDLLV")) die ("La table ".$this->NmTable." est une table virtuelle, le champ ".$this->NmChamp.' necessite que le nom de la VRAIE table soit mise dans le champ VALEURS sous la forme $physTable=NomDeLaTable');
		} else $nmtable = $this->NmTable; 
	// regarde si valeur déjà filtrée pour les listes 
		$valDjFilt = is_array($_SESSION['memFilt']['rq_'.$this->NmChpHtml]) && !(in_array("%",$_SESSION['memFilt']['rq_'.$this->NmChpHtml]));
		switch ($this->TypFilt) {
		case "INPLIKE":
		case "EGAL":
			$retVal= '<INPUT TYPE="hidden" NAME="tf_'.$this->NmChpHtml.'" VALUE="'.$this->TypFilt.'">';
			$retVal.= '<INPUT TYPE="text" NAME="rq_'.$this->NmChpHtml.'" VALUE="'.$_SESSION['memFilt']['rq_'.$this->NmChpHtml].'">';
			break;
		
		case "EGALHID": // special pour OSS
			$retVal= '<INPUT TYPE="hidden" NAME="tf_'.$this->NmChpHtml.'" VALUE="EGAL">';
			$retVal.= '<INPUT TYPE="hidden" NAME="rq_'.$this->NmChpHtml.'" VALUE="'.$_SESSION['memFilt']['rq_'.$this->NmChpHtml].'">'.$_SESSION['memFilt']['rq_'.$this->NmChpHtml];
			break;
		
	
	//! ttes les listes sont à choix multiple (condition OR sur les valeurs) sauf si spécifié
		case "LDC": // Liste d�oulante affichant les différentes valeurs du champ
			$retVal= '<INPUT TYPE="hidden" NAME="tf_'.$this->NmChpHtml.'" VALUE="LDM">';
		if ($this->Val2!="") $whsup=" AND ".$this->Val2;
		// affiche que les valeurs significatives
			$rqldcu=db_query("SELECT distinct $this->NmChamp from $nmtable WHERE $this->NmChamp".$GLOBALS['sqllenstr0']." $whsup order by $this->NmChamp");
			//$retVal.= "<SELECT NAME=\"rq_".$this->NmChamp."[]\" MULTIPLE title=\"cliquez sur une ou plusieurs valeurs en appyant sur la touche Ctrl\">";
			$tbval["%"]= (!$valDjFilt ? $VSLD : "").$this->trad("all");
			while ($rwsq=db_fetch_row($rqldcu)) {
			$tbval[$rwsq[0]] = (is_arr_in_array($rwsq[0],$_SESSION['memFilt']['rq_'.$this->NmChpHtml]) ? $VSLD : "").tradLib($rwsq[0]);
			}
			//asort($tbval); // tri de la liste // fout la merde, le % se retrouve au milieu
			$retVal.=DispLD($tbval,"rq_".$this->NmChpHtml,$choixMult,$this->Fccr,false);
			break;
	
		case "LDF": // Liste d�oulante affichant les diff�entes valeurs fixes list�s dans le champ valeur, ou les valeurs du set ou enum
			$retVal= '<INPUT TYPE="hidden" NAME="tf_'.$this->NmChpHtml.'" VALUE="'.(($this->TTC=="enum" || $this->TypeAff=="LD") ? "LDMEG" : "LDM").'">';
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
				$tbval[$key]=(is_arr_in_array($key,$_SESSION['memFilt']['rq_'.$this->NmChpHtml]) ? $VSLD : "").tradLib($val);
			}
			asort($tbval); // tri de la liste
			//$tbval=array_merge(array("%"=>$VSLD."(tous)"),$tbval);
		// ci-dessus � reconstruit les indices...
		$tbval=array("%"=>(!$valDjFilt ? $VSLD : "").$this->trad("all")) + $tbval;

			$retVal.=DispLD($tbval,"rq_".$this->NmChpHtml,$choixMult,$this->Fccr,false);
			break;
	
		case "LDLLV": // Liste d�oulante affichant les diff�entes valeurs li�s par le champ valeur limitées aux valeurs existantes
		$valbrut=explode(';',$this->Valeurs);
		if (count($valbrut)>1) { // connection �une base diff�ente
			$lntable=$valbrut[1];
		} else { //commme avant
			$lntable=$valbrut[0];
		}
		// gestion condition AND depuis PYA
		$valb2 = explode("[[",$lntable);
		if (count($valb2)>1) {
			$lntable=$valb2[0];
			if ($this->Val2!="") {
				$this->Val2 = "(".$this->Val2." AND ".$valb2[1].")";
			} else $this->Val2 = $valb2[1];
		}
		
		// 0: table, 1: champ(s) lié(s)(clé, éventuellement multiple); 2: ET SUIVANTS champs à afficher
		$defl=explode(',',$lntable);
		$retVal= '<INPUT TYPE="hidden" NAME="tf_'.$this->NmChpHtml.'" VALUE="'.$this->TypFilt.'">';
			$retVal.= '<INPUT TYPE="text" NAME="rq_'.$this->NmChpHtml.'" VALUE="'.$_SESSION['memFilt']['rq_'.$this->NmChpHtml].'">';

		$val2spcllv = "(".$defl[1]." IN (SELECT distinct $this->NmChamp from $nmtable $whsup order by $this->NmChamp)) ";
		case "LDL": // Liste d�oulante affichant les diff�entes valeurs li�s par le champ valeurs
			$retVal= "<INPUT TYPE=\"hidden\" NAME=\"tf_".$this->NmChpHtml."\" VALUE=\"".($this->TypeAff=="LDLM" ? "LDM_SPL" : "LDMEG")."\">";
			if ($val2spcllv!="" &&  $this->Val2!="") {
				$val2spcllv = $val2spcllv." AND ".$this->Val2;
			} else $val2spcllv.= $this->Val2;
			$tabval=ttChpLink($this->Valeurs,$val2spcllv);
	/*	if ($this->TypeAff=="LDLM") // si liaison multiple, la cle de recherche est %,cl�%
				{
				foreach ($tbval as $key=>$val)
				{
				$key="%,$key,%";
				$tabval[$key]=$val;
				}
				}
			else $tabval=$tbval;*/
		if (!is_array($tabval)) $tabval = array(); // ptit bug si la liste est vide
					//$tabval["%"]=$VSLD."(tous)"; // avant on faisait � le % se retrouve �la fin ..
		$tbjok[000]=(!$valDjFilt ? $VSLD : "").$this->trad("all"); // on fait ca pour ne pas decaler les indices dans le cas o le champ est num
			// quand c'est un champ num, on a 0, sinon 000
		//$tabval=array_merge($tbjok,$tabval);
		// mais cette fonction reindexe le tableau et donc fout une merdre pas croyable!!
		//astuce ci-dessous trouv� et mise par un anonyme sur internet !!!
		$tabval=$tbjok + $tabval;
	// 				$retVal.= "<!-- desbog";
	// 				$retVal.= $tabval[103];
	// 				$retVal.= "-->";
	//        asort($tbval); // pas de tri de la liste (sp�ifi�dans la liaison)
			if ($valDjFilt) { // filtre mémorisé, et pas bidon
			foreach ($tabval as $key=>$val) { // re-parcourt le tableau pour mémoriser les sélectionné
				$tabval[$key]=(is_arr_in_array($key,$_SESSION['memFilt']['rq_'.$this->NmChpHtml]) && $key!='0' ? $VSLD : "").$val;
			}
		}
			$retVal.=DispLD($tabval,"rq_".$this->NmChpHtml,$choixMult,$this->Fccr,false);
			break;
			
		case "DANT": // criteres sur date ant�ieure ou post�ieure,
		case "DPOST":
			$classinp = $this->dateInpAttribs;
		case "VINF": //valeurs inf et sup
		case "VSUP":
			
			switch ($this->TypFilt) {
			case "DANT": 
				$clause=$this->trad("ante");
				break;
			case "DPOST":
				$clause=$this->trad("poste");
				break;
			case "VINF":
				$clause=$this->trad("inf");
				break;
			case "VSUP":
				$clause=$this->trad("sup");
				break;
			}
		
			$retVal= "<INPUT TYPE=\"hidden\" NAME=\"tf_".$this->NmChpHtml."\" VALUE=\"$this->TypFilt\">";
			if ($bDL) $retVal.= $this->trad("Enrpq").$this->Libelle.$this->trad("is").$clause.$this->trad("agrave");
			$retVal.= ' <INPUT TYPE="text" NAME="rq_'.$this->NmChpHtml.'" ID="rq_'.$this->NmChpHtml.'" '.$classinp.' VALUE="'.$_SESSION['memFilt']['rq_'.$this->NmChpHtml].'">';
			break;
			
		case "DATAP": // criteres sur date ant�ieure ET post�ieure
		case "VIS" : // bornes sup et inf
			if ($this->TypFilt=="DATAP") {
				$classinp = $this->dateInpAttribs;
				$lbi=$this->trad("poste");
				$lbs=$this->trad("ante");
			} else {
				$lbi=$this->trad("sup");
				$lbs=$this->trad("inf");
			}
			$retVal= "<INPUT TYPE=\"hidden\" NAME=\"tf_".$this->NmChpHtml."\" VALUE=\"$this->TypFilt\">";
			if ($bDL) { // $bDL boolenn Display Libs
				$retVal.=  $this->trad("Enrpq"). $this->Libelle . $this->trad("is")." <br/>$lbi".$this->trad("agrave")." <INPUT TYPE=\"text\" NAME=\"rq_".$this->NmChpHtml."[0]\" ID=\"rq_".$this->NmChpHtml."0\" $classinp VALUE=\"".($_SESSION['memFilt']['rq_'.$this->NmChpHtml][0] != "" ? $_SESSION['memFilt']['rq_'.$this->NmChpHtml][0] : "%")."\"> <br/> ".$this->trad("and")." $lbs ".$this->trad("agrave")." <INPUT TYPE=\"text\" NAME=\"rq_".$this->NmChpHtml."[1]\" ID=\"rq_".$this->NmChpHtml."1\" $classinp VALUE=\"".($_SESSION['memFilt']['rq_'.$this->NmChpHtml][1] != "" ? $_SESSION['memFilt']['rq_'.$this->NmChpHtml][1] : "%")."\">";
				}
			else {
				$retVal.= $this->Libelle." &gt;  <INPUT TYPE=\"text\" NAME=\"rq_".$this->NmChpHtml."[0]\" ID=\"rq_".$this->NmChpHtml."0\" $classinp VALUE=\"".($_SESSION['memFilt']['rq_'.$this->NmChpHtml][0] != "" ? $_SESSION['memFilt']['rq_'.$this->NmChpHtml][0] : "%")."\"> ".$this->trad("and")." &lt; <INPUT TYPE=\"text\" NAME=\"rq_".$this->NmChpHtml."[1]\" ID=\"rq_".$this->NmChpHtml."1\" $classinp  VALUE=\"".($_SESSION['memFilt']['rq_'.$this->NmChpHtml][1] != "" ? $_SESSION['memFilt']['rq_'.$this->NmChpHtml][1] : "%")."\">";
				}
			break;
		
		case "NOTNUL":
			$retVal= '<INPUT TYPE="hidden" NAME="tf_'.$this->NmChpHtml.'" VALUE="'.$this->TypFilt.'">';
			$retVal.= '<INPUT TYPE="checkbox" NAME="rq_'.$this->NmChpHtml.'" VALUE="y" '.($_SESSION['memFilt']['rq_'.$this->NmChpHtml]!="" ? ' checked="checked" ': '').'> '.$this->trad("notnul");
			$bDN = false;
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
			$retVal.= "<br/><span class=\"FdR\"><INPUT TYPE=\"checkbox\" NAME=\"neg_".$this->NmChpHtml."\" VALUE=\"neg\" ".($_SESSION['memFilt']['neg_'.$this->NmChpHtml] ? ' checked="checked" ' : '').">".$this->trad("neg")."</span>";
	if ($this->DirEcho && $DE2) {
			echo $retVal;
		} else {
			return($retVal);
		}

			}

	// m�hode d'affichage de case �cocher pour affichage optionnel du champ
	function EchoCSA($dl = false) {
		switch ($this->TypCSA) { // type d'affichage s�ectionnable
		case "OCD":
		case "ONCD":
			$nmvarAfC="AfC_".$this->NmChpHtml;
			$retVal.= "<input type=\"hidden\" name=\"$nmvarAfC\" value=\"no\">"; // obligatoire
			if (isset($_SESSION[$nmvarAfC]) || isset($_SESSION['memFilt'][$nmvarAfC])) {
				$cs=(($_SESSION[$nmvarAfC]=="yes" || $_SESSION['memFilt'][$nmvarAfC]=="yes") ? ' checked="checked" ' : "");
			} else $cs=($this->TypCSA=="OCD" ? ' checked="checked" ' : "");
			$retVal.= "<input type=\"checkbox\" name=\"$nmvarAfC\" value=\"yes\" $cs>".($dl ? $this->trad("2disp") : "");
			
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
		
		if ($this->TypeAff == "HR_MN") $this->ValChp= DispHRMN($this->ValChp);
 	
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
				{return $this->trad("fat").$ChemFF.$this->ValChp;}
		
				}
			break;
			
			case "DT_TS":
				return date("d/m/Y",$this->ValChp);
			break;
		
			default: 
				if ($HT && $this->TypeAff == "CKBX") {
					$defl=explode(',',$this->Valeurs);
					$val0 = explode(':',$defl[0]); 
					return('<INPUT TYPE="checkbox" NAME="'.$this->NmChpHtml.'" ID="'.$this->NmChpHtml.'" VALUE="'.$val0[0].'" '.($this->ValChp == $val0[0] ? ' checked="checked" ' : '').' disabled="disabled" /> '); // .tradLib($val0[1]) met plus le libellé
				}
				// nbre de car max par d�aut, ou pass�en propri��
				$NbCarMax=($this->NbCarMxCust>0 ? $this->NbCarMxCust : $nbcarmxlist);
				$Val2Af =  $this->ValChp;
				$Val2Af=stripslashes($Val2Af);
				if ($NbCarMax<=50) $Val2Af =  str_replace("\n","",strip_tags($Val2Af)) ; // si peu de caractères autorisés, on affiche pas le HTML ni les sauts de ligne
				$Val2Af=tronqstrww ($Val2Af,$NbCarMax); // + malin,
				//$Val2Af=substr($Val2Af, 0, $NbCarMax); // au max xx caract�es
				if ($HT) { 
					$Val2Af=DispCustHT($Val2Af);
					$Val2Af=($Val2Af=="" ? "$EiS" : $Val2Af);
				} else 
					$Val2Af = strip_tags($Val2Af) ;
				return ($Val2Af);
			break;
		} // fin du switch
	} // fin methode EchoVCL

	// methode commune aux 2 methodes liste et edition, qui affiche les valeurs liées en statique
	function RVLS($EiS) {
		// LnkTb=true si liste liée à une table, false si liste de valeurs fixes
		$LnkTb =! ($this->TypeAff=="LD" || $this->TypeAff=="LDM" || $this->TypeAff=="CKBX");
		if (!$LnkTb) { // si liste de valeurs, construit directement un tableau de hachage cle=>valeurs
			$defl=explode(',',$this->Valeurs);
			foreach ($defl as $val) {
			if (strstr($val,":")) {
				$valtb=explode(":",$val);
				$key=$valtb[0];
				$val=$valtb[1];
				}
			else $key=$val;
			$tbval[$key]=tradLib($val);
			}
		} // fin si valeurs statiques
		$cle = $this->ValChp;
		$valaff="";
		if ($cle!="") {
		if (strstr($cle,",")) { // plusieurs valeurs séparées par des virgules (type pseudo-set multiple)
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
		} else { 
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

	function genEditJS() {
		$this->NmChpHtml4Edit = $this->sepNmTableNmChpInEdit ? $this->NmChpHtml : $this->NmChamp;
		global $tbEvenmtVFAutoJS;
		if (array_key_exists($this->Tt_PdtMaj,$tbEvenmtVFAutoJS)) { // $tbEvenmtVFAutoJS tableau des evenements auto gérés; stocké ds functions.php
			// incremente nbre de champs a tester
			return(outJS("nbInput2test ++;
			tbId2Verif[nbInput2test] = '".$this->NmChpHtml4Edit."';
			tbTypeVerif[nbInput2test] = '".$this->Tt_PdtMaj."';
			tbLibChp2Verif[nbInput2test] = '".$this->Libelle."';
			"));
		}
	}
	// fonction qui regarde si un champs est obligatoire dans les valeurs de $this->Tt_PdtMaj
	function testOblig () {
		if (($this->Tt_PdtMaj =="notNull" || stristr($this->Tt_PdtMaj, "NN")) && $this->TypEdit!="C")  return ('<span title="Champ a renseigner obligatoirement" class="notNullField">*</span>&nbsp;');
	}
	// m�hode qui affiche le controle d�ition
	// pour tes les type d'aff <> de AUT  
	function EchoEdit($DH=true) {
	global $VSLD,$nbrtxa,$nbctxa,$tbEvenmtVFAutoJS;
	if (!$nbrtxa) $nbrtxa=5; // nbre de lignes des textarea
	if (!$nbctxa) $nbctxa=40; // nbre de colonnes des textarea
	if (!$VSLD) $VSLD="#SEL#"; // caract�es ins��en d�ut de valeur de listes indiquant la s�ection

	$htmlspecialchars = true; // épuration du html par défaut en TypEdit= C
	$retVal .= $this->genEditJS();
	$retVal .= $this->testOblig();
	$this->NmChpHtml4Edit = $this->sepNmTableNmChpInEdit ? $this->NmChpHtml : $this->NmChamp;
	if ($this->TTC != 'date') {
		if ($this->TypeAff!="HID") $this->ValChp=stripslashes($this->ValChp);
	} else {
		$this->ValChp=DateF($this->ValChp);
		if ($this->ValChpAvMaj!="") $this->ValChpAvMaj=DateF($this->ValChpAvMaj);
		$size=' SIZE="10" ';
		$mxsize=' maxlength="10" ';
	}

	if ($this->TypEdit=="C") { // si on est en consultation, on passe tout en STA ou STAL ou rien

	if ($this->TTC == "date" && ($this->ValChp=="00/00/2000" || (strstr($this->ValChp,"/") && ($this->ValChp + 0) == 0))) $this->ValChp="-";
	switch ($this->TypeAff) {
	//chgt en statique normale
			case "TXARTE":
			$htmlspecialchars = false; // (surtout) pas d'épuration du html
				case "AUT": // AUto
			case "TXT": // boite texte normale
			case "LDTXT": // boite texte + liste déroulante
				case "TXA": // texte area
			case "TXARTE":
			$this->TypeAff="STA";
			break;

			case "DT_TSTAMP_ED" :
			$this->TypeAff="DT_TSTAMP_VS";
			break;

			case "HR_MN":
			$Val2Af= DispHRMN($this->ValChp);
			$retVal.= $Val2Af.($DH ? "<INPUT TYPE=\"hidden\" NAME=\"".$this->NmChpHtml4Edit."\" ID=\"".$this->NmChpHtml4Edit."\" VALUE=\"".$this->ValChp."\">" : "");
		// on sort direct, pour ne pas modifier la valeur de ->TypeAff
		// si on le fait, si on rappelle deux fois la m�hode pour le m�e champ ds une page
		// � plante 
			if ($this->DirEcho) {
				echo $retVal;
				return;
			} else {
				return($retVal);
			}
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
				$retVal.= $Val2Af.($DH ? "<INPUT TYPE=\"hidden\" NAME=\"".$this->NmChpHtml4Edit."\" ID=\"".$this->NmChpHtml4Edit."\" VALUE=\"".$this->ValChp."\">" : "");
			// on sort direct, pour ne pas modifier la valeur de ->TypeAff
			// si on le fait, si on rappelle deux fois la m�hode pour le m�e champ ds une page
			// � plante 
				if ($this->DirEcho) {
					echo $retVal;
					return;
				} else {
					return($retVal);
				}
				break;
		
		// le reste on change pas, les photos sont trait�s dans la proc elle-meme
		} // fin switch
	}   // fin si on est en consultation

	// traitement Ev�ements pendant mise �jour

	if ($this->Tt_PdtMaj!="" && !array_key_exists($this->Tt_PdtMaj,$tbEvenmtVFAutoJS)) { // $tbEvenmtVFAutoJS tableau des evenements auto gérés; stocké ds functions.php
		$tbeven=explode(":",$this->Tt_PdtMaj);
		$evJS=" ".$tbeven[0]."=\"".$tbeven[1]."\"";
		}
	else $evJS="";

	switch ($this->TypeAff) {
		case "": // on affiche rien
		case "NULL":
		break;
		
		case "HID": // on affiche un champ cach�que si $DH=true, sinon rien
		if ($DH) $retVal.= "<INPUT TYPE=\"hidden\" NAME=\"".$this->NmChpHtml4Edit."\" ID=\"".$this->NmChpHtml4Edit."\" VALUE=\"".htmlspecialchars($this->ValChp)."\">";
		break;

		case "CKBX": //check box
		$defl=explode(',',$this->Valeurs);
		$val0 = explode(':',$defl[0]);
		$retVal = '<INPUT TYPE="checkbox" NAME="'.$this->NmChpHtml4Edit.'" ID="'.$this->NmChpHtml4Edit.'" VALUE="'.$val0[0].'" '.($this->ValChp == $val0[0] ? ' checked="checked" ' : '').($this->TypEdit=="C" ? ' disabled="disabled" ' : '').'/> '.tradLib($val0[1]);
		break;

		// pour indiquer qu'in ��ent est s�ectionn� met VSLD devant (#SEL#)
		case "LD": // liste d�oulante non li�
		case "LDTXT": //liste déroulante + texte
		$defl=explode(',',$this->Valeurs);
		if ($this->FieldNullOk=="YES")
			{  // si null autoris�
			$val=(($this->ValChp=="NULL") ? $VSLD."NULL" : "NULL");
			$tbval["NULL"]=$this->trad("null");
			}
		foreach ($defl as $val) {
			if (strstr($val,":")) {
			$valtb=explode(":",$val);
			$key=$valtb[0];
			$val=$valtb[1];
			}
			else $key=$val;
			$val = tradLib($val);
			$val=(($key==$this->ValChp) ?  $VSLD.$val: $val);
			$tbval[$key]=stripslashes($val); // au cas ou ' dans les libelles
			$tbkey[]=$key;
		}
		if ($this->TypeAff == "LDTXT") {
				$retVal.=DispLDandTxt ($tbval,$this->NmChpHtml4Edit, $this->ValChp,false);
		} else {
		if (!in_array($this->ValChp,$tbkey) && $this->ValChp!="")
			$retVal.= str_replace("###",  $this->ValChp, $this->trad("err6"));
		$retVal.=DispLD($tbval,$this->NmChpHtml4Edit,"no",$this->Fccr,false); // lise der. non multiple
		}
		break;

		case "LDM": // liste d�oulante non li� MULTIPLE
		$defl=explode(',',$this->Valeurs);
		$tbvalm=explode(',',$this->ValChp);
		
		if ($this->Fccr=="" || $this->Fccr=="LDF") $tbval["NULL"]=$this->trad("NONE"); // valeur vide
		$Am1S=false; // Init au moins une valeur s�ectionn�
		foreach ($defl as $val) {
			if (strstr($val,":")) {
			$valtb=explode(":",$val);
			$key=$valtb[0];
			$val=$valtb[1];
			}
			else $key=$val;
			$val = tradLib($val);
			if (in_array($key, $tbvalm)) {
				$val=$VSLD.$val; // indique les s�ectionn�
				$Am1S=true; // Init au moins une valeur s�ectionn�
				}
			$tbval[$key]=$val;
			$tbkey[]=$key;
			}
		// controle d'erreur

		// si aucun de s�ectionn�...
		if (!$Am1S && ($this->Fccr=="" || $this->Fccr=="LDF")) $tbval["NULL"]=$VSLD.$this->trad("none");

		unset($err);
		foreach ($tbvalm as $valm) {
			if (!in_array($valm,$tbkey) && $valm!="") $err.=$valm.", ";
			}
		if ($err!="") {
			$err=vdc($err,2); // enl�e dernier", ";
			$retVal.= str_replace("###", $err, $this->trad("err7"));
			}
		$retVal.=DispLD($tbval,$this->NmChpHtml4Edit,"yes",$this->Fccr,false);
		break;
		
		case "LDL": // liste d�oulante li�
		/* Syntaxe
		[base,user,passwd;]table,chpcle1[:chpcle2:chpcle3],chp_aff1[,chpaff2,...,chpaffn]['[[' condiion where suppl]
		*/
		$tabCorlb=ttChpLink($this->Valeurs,$this->Val2);
		if (strstr($this->Valeurs,"@@")) {  // si hierachique
			$valnul=($this->TTC=="char" ? "" : 0);
			$val=(($this->ValChp==$valnul) ? $VSLD.$valnul : $valnul);
			$tbval[$valnul]=$this->trad("none");
		}	elseif ($this->FieldNullOk=="YES") {  // si null autorise
			$val=(($this->ValChp=="NULL") ? $VSLD."NULL" : "NULL");
			$tbval["NULL"]=$this->trad("none");
		}
		$tberrck=array(); // clear tableau de controle d'erreur
		foreach ($tabCorlb as $key=>$val) {
			$tberrck[]=$key;
			$val=(($key==$this->ValChp) ? $VSLD.$val : $val);
			$tbval[$key]=$val;
		}
		if (!in_array($this->ValChp,$tberrck) && $this->ValChp!="")
			$retVal.= str_replace("###",  $this->ValChp, $this->trad("err6"));
		$retVal.=DispLD($tbval,$this->NmChpHtml4Edit,"no",$this->Fccr,false);
		break;

		case "LDLM": // liste d�oulante li� MULTIPLE
		/* Syntaxe de TYPA_VAL
		(base,user,passwd);table,chpcl�chp_aff1,chpaff2,...,chpaffn)
		*/
		$tabCorlb=ttChpLink($this->Valeurs,$this->Val2);
		//dans le champ lui même, les valeurs clé sont stockées séparées par des virgules
		$tbvalm=explode(',',$this->ValChp);

		if (strstr($this->Valeurs,"@@")) $valsh=$this->trad("oris");
		$valnul=($this->TTC=="char" ? "" : 0);
		$tbval[$valnul]=$this->trad("none").$valsh;


		$tberrck=array(); // clear tableau de controle d'erreur
		$Am1S=false; // Init au moins une valeur s�ectionn�
		foreach ($tabCorlb as $key=>$val) {
			$tberrck[]=$key;
			if (in_array($key, $tbvalm)) {
				$val=$VSLD.$val; // raj les caract indiquant la s�ection
			$Am1S=true; // au mis une valeur s�ectionn�
			}
			$tbval[$key]=$val;
		}

		// si aucun de s�ectionn�...
		if (!$Am1S) $tbval[$valnul]=$VSLD.$this->trad("none").$valsh;
		// controle d'erreur
		if (is_array($tbvalm)) {
			unset($err);
			foreach ($tbvalm as $valm) {
			if (!in_array($valm,$tberrck) && $valm!="") $err.=$valm.", ";
			}
			if ($err!="") {
			$err=vdc($err,2); // enl�e dernier", ";
			$retVal.= str_replace("###", $err, $this->trad("err7"));
			}
			}
		$retVal.=DispLD($tbval,$this->NmChpHtml4Edit,"yes",$this->Fccr,false); // liste d�oulante, multiple
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
		
		$retVal.='<SELECT ID="'.$this->NmChpHtml4Edit.'" NAME="'.$this->NmChpHtml4Edit.($Mult ? "[]" : "").'" SIZE="'.(($Mult ? max(1,count($tabVS)) : 1)).'" MULTIPLE=\"MULTIPLE\" >';

		if ($tabVS) {
			foreach ($tabVS as $k=>$v) {
			$retVal.="<OPTION VALUE=\"$k\" SELECTED=\"SELECTED\">$v</OPTION>";
			}
		} else $tabVS=Array();
		$retVal.='</SELECT>';
		
		// pour la compat avec gdp2 multi-onglets dans FF et bientot PYA
		if (isset($_REQUEST['SESSION_NAME'])) $sessnreq = "&SESSION_NAME=".$_REQUEST['SESSION_NAME'];
		$retVal.= '<input type="button" class="fxbutton" onclick="popup(\''.$this->chempopl.$this->nmfpopl.'?Valeurs='.urlencode($this->Valeurs).'&NmChp='.$this->NmChpHtml4Edit.'&Mult='.($Mult ? 10 : 1).$sessnreq.'\',500,500)" value=" M " />';
		//$retVal.= '<a target="_blank" href="'.$this->chempopl.$this->nmfpopl.'?Valeurs='.urlencode($this->Valeurs).'&NmChp='.$this->NmChpHtml4Edit.'&Mult='.($Mult ? 10 : 1).'">M</a>';

		break;
				
		case "FICFOT":
		//if ($this->TypEdit==2) $this->ValChp=""; // annule si copie
		$retVal.= "<INPUT TYPE=\"hidden\" NAME=\"Old".$this->NmChpHtml4Edit."\"  ID=\"Old".$this->NmChpHtml4Edit."\" VALUE=\"".$this->ValChp."\">";
			if ($this->ValChp!="") {  // regarde si chemin param�r�correctement
			if (strpos ($this->Valeurs,"#")!=1 && $this->Valeurs!="") {
				$ChemFF=$this->Valeurs;}
			else {
				$ChemFF="./".$this->NmBase."_".$this->NmTable."_".$this->NmChpHtml4Edit."/";}

			$NCF=$ChemFF.$this->ValChp;
			if (TestNFImg($this->ValChp)) // si c'est un fichier de type image
			{ $retVal.= '<P><IMG SRC="'.$NCF.'" '.($this->MaxImgWidth > 0 ? ' width="'.$this->MaxImgWidth.'" ' : "").'></P>';
			}
			else
			{ $retVal.= $this->trad("linktof")." : <A HREF=\"".$NCF."\" ".(TestNFImg($this->ValChp) ? "TARGET=\"_blank\"" : "").">".$this->ValChp."</A><br/>";}

			if ($this->TypEdit!="C") $retVal.= "<INPUT TYPE=\"checkbox\" NAME=\"Fok".$this->NmChpHtml4Edit."\" VALUE=\"".$this->ValChp."\" CHECKED>".$this->ValChp."&nbsp;";
			}// Fin si il y a un fichier associe
			if ($this->TypEdit!="C") { // saisie que si pas en �ition
			$retVal.= "<INPUT TYPE=\"file\" NAME=\"".$this->NmChpHtml4Edit."\" SIZE=\"30\" MAXLENGTH=\"200\">";
			if ($this->ValChp!="") $retVal.= "<FONT SIZE=\"-2\"><br/>".$this->trad("mes1")."</font>";
		}
		break; // fin champ de type fichier-photo

		case "STAF": // statique forc�
			// on ne fait rien, on l'a d��fait en haut
		break;

		case "STA": // statique  simple
		// remplace caract�e merdiques et converti adresses mail en cliquables pour le HTML
		$Val2Af = DispCustHT($this->ValChp,$htmlspecialchars);
		if ($this->ValChpAvMaj!="" && $this->ValChpAvMaj!=$this->ValChp) $Val2Af = DispCustHT($this->ValChpAvMaj); // on affiche plus la valeur courante, on s'en branle
		$retVal.= $Val2Af."<INPUT TYPE=\"hidden\" NAME=\"".$this->NmChpHtml4Edit."\" ID=\"".$this->NmChpHtml4Edit."\" VALUE=\"".htmlspecialchars($this->ValChp)."\">";
		break;

		case "STAL": // statique li�
		$retVal.= (ttChpLink($this->Valeurs,"",$this->ValChp));
		if ($this->ValChpAvMaj!="" && $this->ValChpAvMaj!=$this->ValChp) $retVal = ttChpLink($this->Valeurs,"",$this->ValChpAvMaj); // on affiche plus la valeur courante, on s'en branle
		$retVal.= "<INPUT TYPE=\"hidden\" NAME=\"".$this->NmChpHtml4Edit."\" ID=\"".$this->NmChpHtml4Edit."\" VALUE=\"".$this->ValChp."\">";

		break;

		case "TXA":
		case "TXARTE":
			$defl=explode(',',$this->Valeurs);
			// pour les dimensions, on prend les chiffres dans le champ valeurs (nblignes, nbcolonnes)
			$ligs=(($defl[0]>0) ? $defl[0] : $nbrtxa);
			$cols=(($defl[1]>0) ? $defl[1] : $nbctxa);
			// dans le cas des TXA c'est des dim css
			$height=(($defl[0]>0) ? 'height:'.$defl[0].(ereg("[:alpha:]",$defl[0]) ? "" : "px") : "");
			$width=(($defl[1]>0) ? 'width:'.$defl[1].(ereg("em|px|%",$defl[1]) ? "" : "px") : "");

			$retVal.= "<TEXTAREA COLS=\"$cols\" ROWS=\"$ligs\" NAME=\"".$this->NmChpHtml4Edit."\" ID=\"".$this->NmChpHtml4Edit."\"".($this->TypeAff=="TXARTE" ? ' class="jqrte2" style="'.$height.';'.$width.'" ' :'').">".$this->ValChp."</TEXTAREA>";
		break;

		case "DT_TSTAMP_ED":
		$retVal.= "<INPUT TYPE=\"text\" NAME=\"".$this->NmChpHtml4Edit."\" ID=\"".$this->NmChpHtml4Edit."\" ".$this->dateInpAttribs." VALUE=\"".date("d/m/Y",$this->ValChp)."\" $evJS>";
			if ($this->ValChpAvMaj!="" && $this->ValChpAvMaj!=$this->ValChp) $retVal.= " (".$this->trad("avtmaj").": ".date("d/m/Y",$this->ValChpAvMaj).") ";
		break;
		
		case "DT_TSTAMP_VS":
		$retVal.= date("d/m/Y",$this->ValChp);
		$retVal.= "<INPUT TYPE=\"hidden\" NAME=\"".$this->NmChpHtml4Edit."\" ID=\"".$this->NmChpHtml4Edit."\" VALUE=\"".date("d/m/Y",$this->ValChp)."\">";
		if ($this->ValChpAvMaj!="" && $this->ValChpAvMaj!=$this->ValChp) $retVal.= " (".$this->trad("avtmaj").": ".date("d/m/Y",$this->ValChpAvMaj).") ";
		break;
		
		case "HR_MN":
		$retVal.= "<INPUT TYPE=\"text\" NAME=\"".$this->NmChpHtml4Edit."_hr\" ID=\"".$this->NmChpHtml4Edit."_hr\" VALUE=\"".floor($this->ValChp/100)."\" size=\"2\" maxsize=\"2\" $evJS> : <INPUT TYPE=\"text\" NAME=\"".$this->NmChpHtml4Edit."_mn\" ID=\"".$this->NmChpHtml4Edit."_mn\" VALUE=\"".c2c($this->ValChp % 100)."\" size=\"2\" maxsize=\"2\" $evJS>";
		break;
			
		case "TXT":
			$defl=explode(',',$this->Valeurs);
			// pour la size et maxlength , on prend les chiffres dans le champ valeurs (size,maxlength)
			$size=(($defl[0]>0) ? " SIZE=\"$defl[0]\" ": "");
			$mxsize=(($defl[1]>0) ? " maxlength=\"$defl[1]\" " : "");
		default :
			if (stristr($this->NmChpHtml4Edit,"_DT")) {
				$classi = $this->dateInpAttribs;
				$size=$mxsize=0;
			}
			$retVal.= "<INPUT TYPE=\"text\" NAME=\"".$this->NmChpHtml4Edit."\" ID=\"".$this->NmChpHtml4Edit."\" VALUE=\"".htmlspecialchars($this->ValChp)."\"  $classi $size $mxsize $evJS>";
			if ($this->ValChpAvMaj!="" && $this->ValChpAvMaj!=$this->ValChp) $retVal.= " (".$this->trad("avtmaj").": ".DispCustHT($this->ValChpAvMaj).") ";
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
	global $VSLD,$nbrtxa,$nbctxa,$nValRadLd;
	if (!$nValRadLd) $nValRadLd=4; // nbre de valeurs passage liste d�oulante/boutons radio case �cocher
	if (!$nbrtxa) $nbrtxa=5; // nbre de lignes des textarea
	if (!$nbctxa) $nbctxa=40; // nbre de colonnes des textarea
	if (!$VSLD) $VSLD="#SEL#"; // caract�es ins��en d�ut de valeur de listes indiquant la s�ection

	$retVal .= $this->genEditJS();
	$retVal .= $this->testOblig();
	$this->NmChpHtml4Edit = $this->sepNmTableNmChpInEdit ? $this->NmChpHtml : $this->NmChamp;

	if ($this->TTC != 'date') $this->ValChp=stripslashes($this->ValChp);

	if ($this->TypEdit=="C") { // en consult, on appelle la manuelle

		$this->TypeAff="STA";
		return($this->EchoEdit());
		} // fin edition consult (ro)

	else {
		//$retVal.=$this->TTC;
	switch ($this->TTC) { // switch sur type de champ
		case "boolean": // specifique postgres
		$ck = $this->ValChp=="true" ? ' checked="checked" ' : '';
		$retVal.= '<input type="radio" name="'.$this->NmChpHtml4Edit.'" ID="'.$this->NmChpHtml4Edit.'" value="TRUE" '.$ck.">vrai (true)\n";
		$ck = $this->ValChp=="false" ? ' checked="checked" ' : '';
		$retVal.= '<input type="radio" name="'.$this->NmChpHtml4Edit.'" ID="'.$this->NmChpHtml4Edit.'" value="FALSE"'." $ck >faux (false)\n";
		
		break;

		case "enum" : // liste de valeurs
		// la juste dessous pomp�de phpMyAdmin sans tout bien comprendre
		$enum = str_replace('enum(', '', $this->FieldType);
				$enum = ereg_replace('\\)$', '', $enum);
				$enum = explode('\',\'', substr($enum, 1, -1));
				$enum_cnt = count($enum);
		if ($enum_cnt<=$nValRadLd || $this->Fccr) { // si - de 3, cases �option
			for ($l=0;$l<$enum_cnt;$l++) {
			$ck = ($enum[$l] == $this->ValChp ? ' checked="checked" ' : '');
			$retVal.= '<input type="radio" name="'.$this->NmChpHtml4Edit.'" ID="'.$this->NmChpHtml4Edit.'" value="'.$enum[$l].'" '.$ck.">$enum[$l]\n";
				}
			} // fin si cases a options
		else { // si + de 3, liste deroulante
			$retVal.= "<select name=\"$this->NmChpHtml4Edit\" ID=\"".$this->NmChpHtml4Edit."\">";
			for ($l=0;$l<$enum_cnt;$l++) {
			$ck=($enum[$l]==$this->ValChp ? ' selected="selected" ' : '');
			$retVal.= '<option value="'.$enum[$l].'" '.$ck.'>'.$enum[$l]."</option>\n";
				}
			$retVal.= "</select>";
			} // fin si + de 3
		break;

		case "set" : // liste de valeurs
		$enum = str_replace('set(', '', $this->FieldType);
				$enum = ereg_replace('\\)$', '', $enum);
				$enum = explode('\',\'', substr($enum, 1, -1));
				$enum_cnt = count($enum);
		if ($enum_cnt<=$nValRadLd || $this->Fccr) { // si - de 3, cases a cocher
			for ($l=0;$l<$enum_cnt;$l++) {
			$ck=(stristr($this->ValChp,$enum[$l]) ? ' checked="checked" ' : '');
			$retVal.= "<input type=\"checkbox\" name=\"".$this->NmChpHtml4Edit."[]\"  ID=\"".$this->NmChpHtml4Edit."\" value=\"".$enum[$l]."\" $ck>".$enum[$l]."&nbsp;&nbsp;&nbsp;";
				}
			}
		else { // liste deroulante a choix multiples (variable tableau)
			$retVal.= "<select name=\"".$this->NmChpHtml4Edit."[]\"  ID=\"".$this->NmChpHtml4Edit."\" multiple size=\"".min($enum_cnt ,4)."\">";
			for ($l=0;$l<$enum_cnt;$l++) {
			$ck=(stristr($this->ValChp,$enum[$l]) ? ' selected="selected" ' : '');
			$retVal.= "<option value=\"$enum[$l]\" $ck>$enum[$l]</option>\n";
				}
			$retVal.= "</select>";
			}
		break;

		case "date" : // on passe la date en fran�is
			$retVal.= '<INPUT TYPE="text" NAME="'.$this->NmChpHtml4Edit.'"  ID="'.$this->NmChpHtml4Edit.'" VALUE="'.DateF($this->ValChp).'" '.$this->dateInpAttribs.'/>';
			if ($this->ValChpAvMaj!="" && $this->ValChpAvMaj!=$this->ValChp) $retVal.= "(Avt MAJ : ".DateF($this->ValChpAvMaj).") ";

		break;

		case "txtblob" : // textarea
			$retVal.= "<TEXTAREA COLS=\"$nbctxa\" ROWS=\"$nbrtxa\" NAME=\"".$this->NmChpHtml4Edit."\"  ID=\"".$this->NmChpHtml4Edit."\">".$this->ValChp."</TEXTAREA>";
		break;

		case "char" : // char ou varchar

		$lg=strrchr($this->FieldType, "(");
		$lg= substr($lg,1,-1); // ne r�up�e que l'entier
		$size=min(intval($lg*1.5),100);
			$retVal.= "<INPUT TYPE=\"text\" NAME=\"".$this->NmChpHtml4Edit."\"  ID=\"".$this->NmChpHtml4Edit."\" VALUE=\"".htmlspecialchars($this->ValChp)."\" ".($lg > 0 ? "maxlength=\"$lg\" size=\"$size\"": "").">";
			if ($this->ValChpAvMaj!="" && $this->ValChpAvMaj!=$this->ValChp) $retVal.= "(Avt MAJ : ".$this->ValChpAvMaj.") ";
			
		break;

		default :
			$retVal.= "<INPUT TYPE=\"text\" NAME=\"".$this->NmChpHtml4Edit."\"  ID=\"".$this->NmChpHtml4Edit."\" VALUE=\"".htmlspecialchars($this->ValChp)."\">";
			if ($this->ValChpAvMaj!="" && $this->ValChpAvMaj!=$this->ValChp) $retVal.= "(Avt MAJ : ".$this->ValChpAvMaj.") ";
		break;
		} // fin switch type de champ
		
		if ($this->DirEcho) {
			echo $retVal;
		} else {
			return($retVal);
		}

		} // fin si pas en lecture seule
	} // fin Methode EchoEditAuto

	
	function InitAvMaj($User="") {
	if ($this->TypEdit!="C") {
	unset($this->ValChpAvMaj);
	switch ($this->Tt_AvMaj) {
		case "DJ": // date du jour
		$this->ValChpAvMaj=$this->ValChp;
		$this->ValChp=(!strstr($this->TypeAff,"DT_TSTAMP") ? date("Y-m-d") : time());
		break;
		case "DJSN": // date du jour si nul
		if ($this->ValChp=="" || $this->ValChp==0) $this->ValChp=(!strstr($this->TypeAff,"DT_TSTAMP") ? date("Y-m-d") : time());

		break;
		case "DJP2MSN": // date du jour plus 2 mois si nul
		if ($this->ValChp=="" || $this->ValChp==0) $this->ValChp=(!strstr($this->TypeAff,"DT_TSTAMP") ? date("Y-m-d",time() + 5270400) : time() + 5270400);
		break;
		case "US": // code User
		$this->ValChpAvMaj=$this->ValChp;
		if ($User!="") {
			$this->ValChp=$User; // ici CORES
		}else{
			$this->ValChp="_INC";}
		break;
		case "USSN": // code User si nul avant (pour les nouvelles uniquement par e.)
		if ($this->ValChp=="" || $this->ValChp==NULL || $this->ValChp=="0") {
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
	} // fin Typedit !="C"
	} // fin m�hode d'init ava MAJ

	// m�hode retourne un set pour les mises �jour
	// et g�e aussi les fichiers joints (!)

	function RetSet($keycopy,$rttb=false) {
		global $MaxFSizeDef;
		$keycopy=str_replace("'","",$keycopy);
		if ($this->MaxFSize==0 || $this->MaxFSize=="") $this->MaxFSize=$MaxFSizeDef ;
		if ($this->TTC=="date") { // date
			$this->ValChp=DateA($this->ValChp);
		} elseif (strstr($this->TypeAff,"DT_TSTAMP")) {
			$this->ValChp=DateF2tstamp ($this->ValChp);
		} elseif  (is_array($this->ValChp)) { // tableau
			// on vient d'un set ou d'une liste d�oulante multiple (li� ou pas) et la var est un tableau
				if($this->TTC!="set") $valset=","; // Nouveau: avant rien
				foreach($this->ValChp as $val){
					if ($val!="NULL" && $val!="") $valset.=db_escape_string($val).",";
					}
				if($this->TTC=="set")	$valset=substr($valset,0,-1); // enl�e la derni�e virgule �la fin
						$this->ValChp=$valset; // laisse la derni�e virgule �la fin
		} // fin si valeur est un tableau
		elseif ($this->TTC=="numeric" && count($this->tblocfkeys) <=1) {
				$this->ValChp=$this->ValChp + 0; // astuce pour convertir en nombre si pas bon
		} else { // pas tableau ni date
			$this->ValChp=db_escape_string($this->ValChp);
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

		// en php5, la variable $this->ValChp=$_REQUEST[toto] contient en fait $_FILES[toto] qui est un tableau...
			if (is_array($this->ValChp)) {
				$this->ValChp = $this->ValChp['name'];
			}

			if (strpos ($this->Valeurs,"#")!=1 && $this->Valeurs!="") {
				$ChemImg=$this->Valeurs;}
			else {
				$ChemImg="./".$this->NmBase."_".$this->NmTable."_".$this->NmChamp."/";
			}

			//print_r($this);
			if ($this->TypEdit==1 || $this->TypEdit=='M' || $this->TypEdit==2) { // gestion effacement anciens fichiers attach�
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
			
			if ($this->TypEdit==2 && $this->OFN!="") { // copie de fiche => copie de fichier s'il y en a un
				$cok = copy($ChemImg.$value,$ChemImg."2".$value);
				$value = "2".$value;
				if (!$cok) echo "Copie du fichier $ChemImg$value vers $ChemImg"."2"."$value impossible <br/>Cliquez sur le bouton retour arriere de votre navigateur";
			} elseif ($this->TypEdit!=-1 && $this->ValChp!="none" && $this->ValChp!="") {// si nouveau ou modif avec fichier attach�
				// remplacement des caracteres Zarbi ds les noms de fichier cause bugs
				$this->Fname = cv2purascii($this->Fname);
				$this->Fname = db_escape_string(strtr($this->Fname," '*?$\"","______"));
				
				// variable determinant fichier avec son chemin complet
				$ChemFich=$ChemImg.(strstr($this->Fname,$keycopy) ? "" : $keycopy).$this->Fname; // ne remet la clé que si elle y est pas déjà.
				
				/* $retVal.= "debug:";
				$retVal.= "valeur (ftemp): $this->ValChp<br/>";
				$retVal.= "Chemin : $ChemImg<br/>";
				$retVal.= "nom fichier: $this->Fname<br/>";
				$retVal.= "Fok: $this->Fok<br/>";
				$retVal.= "Ancien nom: $this->OFN<br/>";
				$retVal.= "taille: $this->Fsize<br/>"; 
				die($retVal); */
				
				// test taille du fichier attach�    
				if (($this->Fsize>$this->MaxFSize) && ($this->ValChp!="none")) {
					$retVal.= str_replace("###",  $this->MaxFSize, $this->trad("err1")); 
					unlink ($this->ValChp);
					$this->error = $retVal.str_replace("###",  $this->Fsize, $this->trad("err2"));
					die($this->error);
					$this->ValChp=$value=""; // annule le nom de fichier pour qu'il ne soit mis en base...
				} else { // fin si fichier trop gros
					$retcopy=copy($this->ValChp,$ChemFich);
					//die("copy(".$this->ValChp.",".$ChemFich.",res=$retcopy");
					if (!$retcopy) { 
						$this->error="Impossible de copier le fichier". $this->Fname." depuis le fichier temporaire $this->ValChp ! (chemin complet : $ChemFich<br/>";
						delfich ($this->ValChp);
					}
				}
				// affecte le nouveau nom de la variable �stocker
				if (!$this->error) $value=(strstr($this->Fname,$keycopy) ? "" : $keycopy).$this->Fname;// ne remet la clé que si elle y est pas déjà.
				// la valeur �stocker dans la base de donn� est (�priori) le fichier pr���de la cl�
			} // fin si nouveau ou modif avec fichier attach�
			if ($this->TypEdit==-1 && $this->OFN!="") {// si suppression d'enregistrement on efface tous les fichiers eventuels
				$ChemFich=$ChemImg.$this->OFN;
				delfich($ChemFich);
			} // fin si suppression enregistrement
			$this->ValChp=$value;
		} // fin si type de champ=fichier attach�

		// fonction qui retourn�eventuellement ' fonction du type de champ
		// avec PostGres, le ' autour des champs num fout la merde
		// avec Mysql et Oracle, ça change rien donc on met les ' systématiquement
		$cpstr=rt_carpstr($this->TTC);
		
		if ($this->ValChp !== "") { /// le !=== est indispensable, sinon la valeur "" est identique à la valeur 0 et bonjour le bordel !!
			if (count($this->tblocfkeys) <=1) $this->ValChp=$cpstr.$this->ValChp.$cpstr;
		} else {
			$this->ValChp=(($_SESSION['db_type']!="mysql" && isset($_SESSION['db_type'])) ? "DEFAULT" : ($this->FieldNullOk != "NO" ? "NULL" : $cpstr.$this->FieldValDef.$cpstr));
		}
		
		// gestion des clés multiples
		if (count($this->tblocfkeys) > 1) {
			$tbvalchp = explode(":",$this->ValChp);
			$tbret = array();
			foreach ($this->tblocfkeys as $i=>$fk) {
				$this->NmChp4SqlRetSet = ($this->sepNmTableNmChpInRetSet ? $this->NmTable."." : "").$fk;
				if ($rttb) {
					$tbret[$this->NmChp4SqlRetSet] = $cpstr.$tbvalchp[$i].$cpstr;
				} else {
					$ret.=  $this->NmChp4SqlRetSet."=".$cpstr.$tbvalchp[$i].$cpstr.", ";
				}
			}
			return ($rttb ? $tbret: $ret); // retourne le set ou un tableau associatif...;
		} else { // clé simple "normale"
			// si on veut le nom complet
			$this->NmChp4SqlRetSet = $this->sepNmTableNmChpInRetSet ? $this->NmTable.".".$this->NmChamp : $this->NmChamp;
			return ($rttb ? Array($this->NmChp4SqlRetSet => $this->ValChp) : $this->NmChp4SqlRetSet."=".$this->ValChp.", "); // retourne le set ou un tableau associatif...;
		}
	} // fin methode RetSet

	function trad($lib) {
		global $labels;
		$nolang = (isset($_SESSION["NoLang"]) ? $_SESSION["NoLang"] : 0);
	//	return ($this->labels[$nolang][$lib] != "" ? $this->labels[$nolang][$lib] : "[! $lib !]");
		return ($labels[$nolang][$lib] != "" ? $labels[$nolang][$lib] : "[! $lib !]");
	}

	// méthode qui parse le champ valeurs avce la nvelle syntaxe
	function ParseValeurs() {
		$tblval = explode("\n",$this->Valeurs);
		foreach ($tblval as $lval) {
			$lval = trim($lval);
			$firstcar = substr($lval,0,1);
			if ($firstcar == "$") {
				$lval = substr($lval,1); // vire premier car = $
				$tbv = explode ("=",$lval);
				$this->$tbv[0] = $tbv[1];
			} elseif ($firstcar == "#" || $firstcar == ";" || $lval== "") { // commentaire ou ligne vide
			} else $valeurs = $lval;
		}
		if ($this->locFKeys != '') $this->tblocfkeys =  explode(",",$this->locFKeys) ; // tableau des clés locales
		$this->Valeurs = $valeurs;
	}
	// fonction qui affecte la valeur du champ.
	// $tbValChp est le tableau asso des valeurs de TOUT l'enregistrement; Si clés multiples, va chercher les valeurs dans les autres champs, et la valeur courante devient Valk1:valk2:valk3 ..
	function AffVal($tbValChp) {
		/// assez sioux : il se peut que le tableau asso des valeurs soit sous la forme $tbValChp[NomChp] = val ou $tbValChp[NmTable#NomChp] = val, dans le cas d'oss et des requetes Oracle faisant intervenir des alias...
		if (count($this->tblocfkeys)>1) { // FK multiple
			$tbv = array();
			foreach ($this->tblocfkeys as $fk) {
				$fk = trim($fk); // enlève espaces
				if (isset($tbValChp[$this->NmTable.'#'.$fk])) {
					$tbv[] = $tbValChp[$this->NmTable.'#'.$fk];
				} else $tbv[] = $tbValChp[$fk];
			}
			$this->ValChp = implode(":",$tbv);
		} else { // normal
			if (isset($tbValChp[$this->NmTable.'#'.$this->NmChamp])) {
				$this->ValChp = $tbValChp[$this->NmTable.'#'.$this->NmChamp];
			} else $this->ValChp = $tbValChp[$this->NmChamp];
		}
	}
} // fin objet PYAobj

$GLOBALS['labels'] = array (
	0 => array (
		"all" => "(tous)",
		"ante" => "ant&eacute;rieure",
		"poste" => "post&eacute;rieure",
		"inf" => "inf&eacute;rieur ou égal",
		"sup" => "sup&eacute;rieur ou égal",
		"Enrpq" => "Enregistrements pour lequels ",
		"is" => " est ",
		"agrave" => " &agrave; ",
		"and" => " ET ",
		"neg" => "Negation",
		"fat" => "Fichier attaché",
		"null" => "vide (null)",
		"err6" => "<H6>Erreur : la valeur ### contenue dans le champ n'est pas dans la liste ! </H6>",
		"none" => "Aucun(e)",
		"err7" => "<H6>Erreur : valeur(s) ### contenue(s) non presente(s) dans la liste ! </h6>",
		"oris" => " ou elle-meme",
		"linktof" => "Lien vers le fichier associe",
		"mes1" => "Pour supprimer l'image ou le fichier, decocher la case, ou choisissez-en un(e) autre. Sinon ne touchez a rien ...",
		"avtmaj" => "Avant MAJ",
		"err1" => "<B>Impossible de joindre le fichier !</B><br/>La taille des fichiers attaché est limitée à  ###, ",
		"err2" => " et le fichier attache a une taille de ### octets. <br/>Cliquez sur le bouton retour arriere de votre navigateur",
		"2disp" => "A afficher",
		"notnul" => "Significatif (Non nul, >0, !='')"
	),
	1 => array (
		"all" => "(all)",
		"ante" => "anterior with",
		"poste" => "posterior with",
		"inf" => "lower or equal than",
		"sup" => "greater or equal than",
		"Enrpq" => "Records of ",
		"is" => " wich are ",
		"agrave" => " ",
		"and" => " AND ",
		"neg" => "Negation",
		"fat" => "Attached vile",
		"null" => "empty (null)",
		"err6" => "<H6>Error : value ### contained in field is not in the list ! </H6>",
		"none" => "None",
		"err7" => "<H6>Erreur : value(s) ### contained in field are not in the list ! ! </h6>",
		"oris" => " or itself",
		"linktof" => "Link to attached file",
		"mes1" => "To delete image or file, uncheck the box, or select another file. If not, don't touch anything...",
		"avtmaj" => "Before update",
		"err1" => "<B>Impossible to join this file !</B><br/>File size is limted to ###, ",
		"err2" => " and attached file's size is ### bytes. <br/>Click on return button of your browser",
		"2disp" => "To display",
		"notnul" => "Significant (Not null, >0, !='')"
	)
); // fin tableau langues
?>
