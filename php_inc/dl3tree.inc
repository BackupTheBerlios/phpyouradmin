<? 
/*
Obet permettant de générer un arbre 
les paramètres $dbxxx sont utilises dans typo3 car il ne faut mettre les balises 
*/
class dltreeObj { 

var $imgfopen='folderOpen.gif'; // nom des fichiers images symoles
var $imgfcloseplus ='folderClosedplus.gif';
var $imgfclose ='folderClosed.gif';
var $indentpx=15; // identation arbre en px fct des niveaux
var $dispckbox=false; // affichage des cases à cocher
var $mesg1="Cliquez pour afficher le contenu de cette rubrique";
var $tbChilds;

// 
// fonction qui "echoisise" les styles css necessaires au fonctionnement de l'arbre
// ils peuvent biensur etre placés dans la css globale
function echDL3TStyles($dbsty=true) {
return ("
".($dbsty ? "<style>" : ""). "
.stDL3Tdisp a, .stDL3Tdisp a:hover, .stDL3Tdisp a:visited {
	font-size:11px;
	text-decoration:none;
	color:black;
	font-family:sans-serif;
	}

.stDL3Tdisp a:hover {
	color:red;
	}

.stDL3Thidd {
	visibility: collapse;
	line-height:0px;
	display: none;
}
.stDL3Tdisp {
	visibility:visible;
	line-height:auto;
	display:block;
}
.smDL3Tchecked {
    background-color : #d0d0d0;
    background-image : url('grey.gif');
  }


".($dbsty ? "</style>" : ""). "
");
}

// fonction qui echoise les variables JAVASCRIPT a initialiser
function echDL3TJSVarsInit($dbscr=true) {
return ("

".($dbscr ? "<script>" : ""). "

var imgfopen='".$this->imgfopen."';
var imgfcloseplus ='".$this->imgfcloseplus."';
var imgfclose ='".$this->imgfcloseplus."';
".($dbscr ? "</script>" : ""). "

");
}

// fonction qui echoise les FONCTIONS JAVASCRIPT a initialiser
function echDL3TJSFunctions($dbscr=true) {
return ("
".($dbscr ? "<script>" : ""). "

// fonction de cochage/ decochage des cases
function togckb(obj) {
	//alert(obj.className);
	var ipid;
	var ipid=obj.id.substr(3,20); // on recupere le n° en enlevant le 'c3k' au début de l'id
	//alert (obj.checked);
	if (tbChilds[ipid]!=undefined) { // si cette case a cocher a des enfants
		if (obj.checked) {
			var vis2set=true;
			} 
		else {
			var vis2set=false;
		}
		for(var icid=0;icid<tbChilds[ipid].length;icid++) {
			var cid=tbChilds[ipid][icid];
			document.getElementById('c3k' + cid).checked=vis2set;
			togckb(document.getElementById('c3k' + cid)); // réentre dans cette même fonction
		}
	}
}

function forcedisp (id) {
}

function togdisp(obj) {
	//alert(obj.src);
	var ipid=obj.id.substr(3,20); // on recupere l'id en enlevant le 'i3m' au début de l'id

	if (tbChilds[ipid]!=undefined) { // si ce dossier a des enfants
		if (document.getElementById('n3o' + ipid).value=='true') { // si noeud ouvert
			var vis2set='stDL3Thidd';
			document.getElementById('n3o' + ipid).value='false';
			obj.src=imgfcloseplus;
			} 
		else {
			var vis2set='stDL3Tdisp';
			document.getElementById('n3o' + ipid).value='true';
			obj.src=imgfopen;
		}
		
		for(var icid=0;icid<tbChilds[ipid].length;icid++) {
			var cid=tbChilds[ipid][icid];
			document.getElementById('l3i' + cid).className=vis2set;
			if (vis2set=='stDL3Thidd') {
				if (tbChilds[cid]!=undefined) 	{
					hidenodeandsubs(cid); 
					document.getElementById('n3o' + cid).value='false';
					document.getElementById('i3m' + cid).src=imgfcloseplus;
				} 
				/*else { // on ne change pas les item sans sous-item
					document.getElementById('i3m' + cid).src=imgfclose;
				} */
			}
		}
	}
}

function hidenodeandsubs(i3d) {
	for (ibc=0;ibc < tbChilds[i3d].length; ibc++) {
		var cid=tbChilds[i3d][ibc];
		document.getElementById('l3i' + cid).className='stDL3Thidd';
		if (tbChilds[cid]!=undefined) 	{
			hidenodeandsubs(cid); 
			document.getElementById('n3o' + cid).value='false';
			document.getElementById('i3m' + cid).src=imgfcloseplus;
		} 
		/*else {
			document.getElementById('i3m' + cid).src=imgfclose;
		} */
	}
}

function ahah(url,target) {
   // native XMLHttpRequest object
   document.getElementById(target).innerHTML = 'envoi de la requete au serveur...';
   if (window.XMLHttpRequest) {
       req = new XMLHttpRequest();
       req.onreadystatechange = function() {ahahDone(target);};
       req.open(\"GET\", url, true);
       req.send(null);
   // IE/Windows ActiveX version
   } else if (window.ActiveXObject) {
       req = new ActiveXObject(\"Microsoft.XMLHTTP\");
       if (req) {
           req.onreadystatechange = function() {ahahDone(target);};
           req.open(\"GET\", url, true);
           req.send();
       }
   }
}
function ahahDone(target) {
   // only if req is loaded
   if (req.readyState == 4) {
       // only if OK
       if (req.status == 200 || req.status == 304) {
           results = req.responseText;
           document.getElementById(target).innerHTML = results;
       } else {
           document.getElementById(target).innerHTML=\"ahah error:\" + req.statusText;
       }
   }
}



tbChilds=new Array();
".($dbscr ? "</script>" : ""). "
");
}
// fonction qui echoise les tableaux JAVASCRIPT remplis qui conditionnent le comportement des noeuds
function echDL3TJStbChilds($dbscr=true) {
if (count($this->tbChilds)==0) return("Error: tbChilds est vide !");
//print_r($this->tbChilds);
foreach ($this->tbChilds as $pid=>$tboneChilds) {
	//$str2ret.="tbChilds['".$pid."']=Array(".implode(",",$tboneChilds).");\n"; ne marche pas avec les clé textuelles
	$str2ret.="tbChilds['".$pid."']=Array(";
	foreach ($tboneChilds as $Childs) {
		$str2ret.="'".$Childs."',";
		}
	$str2ret=vdc($str2ret,1).");\n"; // enlève la dernière virgule
	//if (count($tboneChilds)==1) $str2ret.="tbChilds[".$pid."]=Array('".$tboneChilds[0]."');\n"; // le fait d'avoir un tableau avec une seule valeur meme numérique sans '' fout la zone (???)
	}
return (($dbscr ? "<script>" : "")."
	"
	.$str2ret."
	".
	($dbscr ? "</script>" : ""));
}



// méthode qui echoise ou plutot retourne 1 (UNE) ligne de l'arbre.
function echDL3T1line ($id,$pid,$label,$cur_depth,$title="",$href="",$onclickJSAction="",$disp=true,$leafimg="",$dispfoldplus=false,$ckbcheked=false,$nodeopen="false",$cdckbox=true) {
/* ARGUMENTS
$id= id du noeud, 
$pid= id parent du noeud; sert uniquement à remplir la propriété-tableau tbChilds
$label=label affiché dans l'arbre
$cur_depth=profondeur (en unités), modifié l'identation
$title=info-bulle du noeud
$href=lien
$onclickJSAction=sans commantaire
$disp=ligne affichée (vraie par défaut)
$leafimg=image du noeud, si non spécifiée, affiche celle spécifiée par les propriétés $this->imgfcloseplus et $this->imgfclose
$dispfoldplus=unuquement si leafimg non spécifiée
$ckbcheked=case à cocher cochée; NB: l'affichage des cases à cocher est conditionnée par la propriété $this->dispckbox
$nodeopen=indique si noeud ouvert
$cdckbox=permet de ne pas afficher une case a cocher meme si elles sont OK via la propriété $this->dispckbox
*/
	//global $this->tbChilds;
	$this->tbChilds[$pid][]=$id;
	if ($title=="") $title=$this->mesg1; // si title non défini, prend celui par défaut
	if ($href=="") $href="#";
	if ($leafimg=="") $leafimg=($dispfoldplus ? $this->imgfcloseplus : $this->imgfclose);
	if ($onclickJSAction!="") $onclickJSAction=' "onclick='.$onclickJSAction.'" ';
	if ($nodeopen=="false" || $nodeopen=="") {$nodeopen="false";} else $nodeopen="true";
	if ($this->dispckbox && $cdckbox) $strckbox= '<input type="checkbox" onchange="togckb(this)" id="c3k'.$id.'" name="c3k'.$id.'" '.($ckbcheked ? "checked" : "").'/>';
	if ($href=="#" && $onclickJSAction=="") 
		{$a=$label;}
	else $a='<a title="'.$title.'" href="'.$href.'" '.$onclickJSAction.'>'.$label.'</a>';
	return ('
		<div id="l3i'.$id.'" class="'.($disp ? "stDL3Tdisp" : "stDL3Thidd").'" style="margin-left : '.(($cur_depth) * $this->indentpx).'px;" >
		<img src="'.$leafimg.'" id="i3m'.$id.'" onclick="togdisp(this)">
		<input type="hidden" name="n3o'.$id.'" id="n3o'.$id.'" value="'.$nodeopen.'">'.
		$strckbox
		.$a.'</div>
	');
	} // fin méthode
} // fin objet
