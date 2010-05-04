<?php

/**
 * Classe permattant la gestion d'objets GoogleMaps
 * @author	Vincent MAURY<vmaury@dlcube.com> 
 */

require_once("fonctions.php");

class GoogleMapsTool {

	var $GMKey="";// clé www
	var $MapWidth=650;	
	var $MapHeight=500;
	var $MapControl=true; // outil de zoom
	var $TypeControl=true; // outil type
	var $OVMapControl=true; // outil type
	var $CMapLat=46.920255; // carte de France Affichée par défaut : c'est le centre
	var $CMapLong=2.373047;
	var $CMapZoom=6; // zoom défaut: carte de france
	var $CMapZoomAF=7; // Zoom qd adresse found
	var $IconImageSize=20;
	var $IconImageFile="http://ns305451.ovh.net/GM_icons/pers_stlo.gif";
	//var $IconImageFile="http://www.haras-nationaux.fr/GM_icons/chevalRougeRond.gif";
	//var $HomeImageFile="http://www.haras-nationaux.fr/GM_icons/homeRond.gif";
	var $HomeImageFile="http://ns305451.ovh.net/GM_icons/home_stlo_small.gif";
	var $outJSB=true; // booleen permettant disant si l'on sort le JS
	var $DataScript; // données en script
	var $GeoCodeAddressData; // tableau des données d'une adresse géocodée, comme renvoyées par le sweb en csv
	
	
	function GoogleMapsTool() {
		$this->__construct();
	}

	function __construct() {
	}
	

	function InitMap ($echDIVMap=true) {

	if ($this->GMKey == "") die ('Vous devez specifier la clé Google map $this->$GMKey');

	//echo("Outjsb=".$this->outJSB);
	$val2ret=($this->outJSB ? $this->OutGMJSWK() : "");
	$val2ret.=($echDIVMap ? '      
          <div id="map" style="width: '.$this->MapWidth.'px; height: '.$this->MapHeight.'px"></div>'
          : '');
          
	$val2ret.= ($this->outJSB ? '
          
          <script type="text/javascript">

    //<![CDATA[ ' : '');
    
	$val2ret.= '
	
	var map=null;
	var geocoder=null;
	var icon=null;
	
	function loadmap() {
		
		if (GBrowserIsCompatible()) {
			map = new GMap2(document.getElementById("map"));
 			map.setCenter(new GLatLng('.$this->CMapLat.','.$this->CMapLong.'), '.$this->CMapZoom.');
			'.($this->MapControl ? 'map.addControl(new GLargeMapControl());' : 'map.addControl(new GSmallMapControl());').'
			'.($this->TypeControl ? 'map.addControl(new GMapTypeControl());' : '').'
			'.($this->OVMapControl ? 'map.addControl(new GOverviewMapControl());' : '').'
			geocoder = new GClientGeocoder();
		
			
			// creation icone HN
			icon = new GIcon();
			icon.image = "'.$this->IconImageFile.'";
			icon.iconSize = new GSize('.$this->IconImageSize.', '.$this->IconImageSize.'); /* !!! OBLIGATOIRE AVEC IE !!! */
			icon.iconAnchor = new GPoint('.($this->IconImageSize /2).', '.($this->IconImageSize / 2).'); /* ancrage de l ombre portee. IL FAUT renseigner sinon rien ne s affiche */
			icon.infoWindowAnchor = new GPoint(1, 6); /* position du depart de la bulle par rapport au coin haut gauche de l icone */
			
			'.$this->DataScript.'
			/* on met les points ci-dessus, comme ça tout est dans le script ppal place dans le header par typo sinon sous IE, ca deconne */
			
		} else alert ("changez votre navigateur antediluvien pour afficher les cartes Google");
		
	} // fin fonction loadmap()
	
	function createMarker(point, title, url, bulleTxt, icon) {
		var gmarkeroptions = new Object();
		gmarkeroptions.icon = icon;
		gmarkeroptions.clickable = true;
		gmarkeroptions.title = title;
		
		var marker = new GMarker(point,gmarkeroptions);
		//var url = \'http://www.dlcube.com\';
		//var bulleTxt = \'<h6>Ouaouh</h6> ca depote <br/> <a href="http://www.dlcube.com">test</a>\';
		
		if (url !="") {
			GEvent.addListener(marker, "click", function() {
				window.location.href=url;
			});
		} else if (bulleTxt !="") {
			GEvent.addListener(marker, "click", function() {
				marker.openInfoWindowHtml(bulleTxt);
				//marker.openInfoWindow(bulleTxt); 
			});
		}
		
		return marker;
	}
	';
	
	$val2ret.= ($this->outJSB ? '
    //]]>
    </script>

<noscript>Javascript doit etre active.</noscript>
		' : '');
	return($val2ret);

	}
	
	function OutGMJSWK() {
	return('<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$this->GMKey.'"
      type="text/javascript"></script>');
      	}
      	
     function OutMapIdS() {
      	return ('<script type="text/javascript">
			/*<![CDATA[*/
			if (GBrowserIsCompatible()) {
			document.write(\'<div id="map" style="width: '.$this->MapWidth.'px; height: '.$this->MapHeight.'px"></div>\');
			setTimeout(\'loadmap()\', 500);
			}
			else {
			alert(\'Javascript doit etre actif sur votre navigateur pour pouvoir afficher les cartes Google !! \');
			}
			/*]]>*/
		</script>');

      	}

// on passe un code région, cela va rechercher ses coordonnées sur la carte, et la recentre
	function RegionReCenter($region,$factech=0) {
		if ($region == "ListPolesFR") {
			$this->CMapZoom = 5;
			return($this->centerMap());
		} else {
			$rep_rg=db_qr_comprass("select geo_pos,geo_lat,geo_long from tx_dlcubehn03geomatic_points WHERE region='".$region."' AND type='CENT_REGIO' AND deleted=0 AND hidden=0");
			if ($rep_rg && $rep_rg[0]['geo_lat']>0) {
				$this->CMapLat=$rep_rg[0]['geo_lat'];
				$this->CMapLong=$rep_rg[0]['geo_long'];
				if ($rep_rg[0]['geo_pos']>0) $this->CMapZoom = $rep_rg[0]['geo_pos'] + $factech;
				
				return($this->centerMap());	
			}
		} 
		return(false);
	}
	
	function centerMap($lat=1000,$long=1000,$zoom=0) {
	return(($this->outJSB ? '
          <script type="text/javascript">

    //<![CDATA[ ' : '').'
		map.setCenter(new GLatLng('.($lat!=1000 ? $lat: $this->CMapLat).','.($long!=1000 ? $long :$this->CMapLong).'), '.($zoom>0 ? $zoom : $this->CMapZoom).');'
		.($this->outJSB ? '
    		//]]>
    	</script>' : ''));
	}
	
	
	function getPoints($param,$recenter=false,$limit=250) {
		$where=$this->retWhere($param);
		if ($this->base2look == "typo3") {
			$rep = db_qr_comprass("select * from tx_dlcubehn03geomatic_points WHERE 1 $where AND deleted=0 AND hidden=0 order by cdpst limit $limit");
		} else { // gdp2
			$rep = $this->db_qr_comprass_ingdp2("select * from zgrh_UNITE_FONCTION,zgrh_LIEU_ACTIVITE WHERE UFO_NULIEUACTIVITE=LAC_NULIEUACT $where AND UFO_COACTIVE='O' order by ".$this->odblufo." limit $limit");
		}
		if ($rep) {
			foreach ($rep as $lrep) {
				$ce = $this->cvlrep2res($lrep);
				//$this->PointWindowInfo = "Centre technique de ".$rep['nom'];
				// verrue pour afficher "haras national de " à la place de "pole de";
				$ce['nom'] = str_replace("Pôle d","Haras national d",$ce['nom']);
				$ce['nom'] = str_replace("Pole d","Haras national d",$ce['nom']);
				$ce['nom'] = str_replace("PÔLE D","HARAS NATIONAL D",$ce['nom']);
				$ce['nom'] = str_replace("POLE D","HARAS NATIONAL D",$ce['nom']);
				$TSite = $this->GetSiteType($lrep);
				$ce['TSite'] = $TSite;
				$url = str_replace("NUUNITEAPOINTER",$lrep["UFO_NUUNITE"],$this->tbLinks2SitePages[$TSite]);
				$ce['url'] = $url;
				$img = $this->GetSiteImg($TSite);
				$ce['img'] = $img;
				
				$this->tbresult[] = $ce;
				$ret.=$this->displayPoint(array("lat"=>$ce['lat'],"long"=>$ce['long'],"title"=>$ce['nom'],"url"=>$url,"img"=>$img));
			}
		} else $this->tbresult=false;
		return($ret);
	}
	// retourne le type de site : CT, Pol , ?? a venir ??
	function GetSiteType($ligne) {
		// on chrche le type de site
		foreach ($this->type2typostruct as $Ksite=>$lnTSites) {
			$tbTSites = explode(",",$lnTSites);
			if (in_array($ligne['UFO_NUTYPOSTRUCT'],$tbTSites)) {
				$TSite = $Ksite;
				break;
			}
		}
		return ($TSite);
	}
	
	function GetSiteImg($TSite) {
		switch ($TSite) {
			case "Pol":
			  return ($this->PoleImageFile);
			  break;
			
			case "CT":
			default:
			  return ($this->IconImageFile);
			  break;
		}
	}
	
	function retWhere($param) {
		foreach ($param as $paire) {
			//print_r($paire);
			$key=$paire->key;
			$value=$paire->value;
			switch ($key) {
				case "type":
					/// seul le type CT est utilisé pour l'instant
					/// a affiner par la suite
					if ($this->base2look == "typo3") {
						$where.=" AND type ='$value' ";	
					} else {
						if ($value == "tsTypesConnus") {
							$where .=" AND UFO_NUTYPOSTRUCT IN (".implode(",",$this->type2typostruct).") ";
						} else {
							$where .=" AND UFO_NUTYPOSTRUCT IN (".$this->type2typostruct[$value].") ";
						}
					}
				break;
				
				case "typeCentre":
					$value=substr($value,2); // vire "ct" au début
					if ($this->base2look == "typo3") {
						$where.=" AND type_det LIKE '%_".$value."_%' ";
					} else {
						$where.=" AND UFO_LMTYPCT LIKE '%,".$value.",%' ";
					}
					
				break;
				
				case "centre":
					if ($this->base2look == "typo3") {
						$where.=" AND name LIKE '%$value%' ";	
					} else {
						$where.=" AND UFO_LLUNITE LIKE '%$value%' ";	
					}
					
				
				break;
				
				case "codeRegion":
					//$where.=" AND region ='$value' ";	
					switch ($value) {
						case "ALS" :
						$ldpt="(67,68)";
						break;
						case "AQU":
						$ldpt=  "(24,33,40,47,64)";
						break;    
						case "AUV" : 
						$ldpt=  "(3,15,43,63)";
						break;
						case "BND": 
						$ldpt= "(14,50,61)";
						break;
						case "BRG": 
						$ldpt= "(21,58,71,89)";
						break;
						case "BRT": 
						$ldpt= "(22,29,35,56)";
						break;
						case "CTR" :
						$ldpt= "(18,28,36,37,41,45)";
						break;
						case "CHP": 
						$ldpt= "(08,10,51,52)";
						break;
						case "COR": 
						$ldpt= "(20)";
						break;
						case "FRC": 
						$ldpt="(25,39,70,90)";
						break;
						case "HND": 
						$ldpt= "(27,76)";
						break;
						case "IDF": 
						$ldpt= "(75,77,78,91,92,93,94,95)";
						break;
						case "LGR": 
						$ldpt= "(11,30,34,48,66)";
						break;
						case "LIM": 
						$ldpt= "(19,23,87)";
						break;
						case "LOR": 
						$ldpt= "(54,55,57,88)";
						break;
						case "MID": 
						$ldpt="(09,12,31,32,46,65,81,82)";
						break;
						case "NPC": 
						$ldpt= "(59,62)";
						break;
						case "PDL" :
						$ldpt= "(44,49,53,72,85)";
						break;
						case "PIC": 
						$ldpt= "(2,60,80)";
						break;
						case "PCH": 
						$ldpt= "(16,17,79,86)";
						break;
						case "PAC":
						$ldpt="(4,5,6,13,83,84)";
						break;
						case "RHA" :
						$ldpt= "(1,7,26,38,42,69,73,74)";
						break;
						case "O.M":
						$ldpt= "(971,972,973,974)";
						break;
						case "ListPolesFR":
						$ldpt= "ListPolesFR";
						break;
						
					}
					//echo $ldpt;
					if ($this->base2look == "typo3") {
						if ($ldpt!="") $where.=" AND FLOOR(CAST(cdpst as UNSIGNED)/1000) IN $ldpt ";
					} else {
						if ($ldpt == "ListPolesFR") {
							$where.=" AND FLOOR(CAST(UFO_COPOSTAL as UNSIGNED)/1000) < 100 ";
							//$this->odblufo = "UFO_LLUNITE";
						} elseif ($ldpt!="") {
							$where.=" AND FLOOR(CAST(UFO_COPOSTAL as UNSIGNED)/1000) IN $ldpt ";
						}
					}
				break;
				
				case "number":
					if ($this->base2look == "typo3") {
						$where.=" AND number =$value ";	
					} else {
						$where.=" AND UFO_NUPERSOSIRE =$value ";	

					}
				break;
			}
		}
		return($where);
	}
	
	function displayPointbyNumber($number,$recenter=false) {
		if ($this->base2look == "typo3") {
			$rep=db_qr_comprass("select geo_pos,geo_lat,geo_long,region,name from tx_dlcubehn03geomatic_points WHERE number='$number' AND deleted=0 AND hidden=0");
			if ($this->PointInfo=="") $this->PointInfo=$rep[0]['name'];
			if ($rep && $rep[0]['geo_lat']>0) {
				if ($recenter) $ret=$this->RegionReCenter($rep[0]['region']);
				if (!$ret) $ret="";
				$ret.=$this->displayPoint (array("lat"=>$rep[0]['geo_lat'],"long"=>$rep[0]['geo_long']));
			}
		} else {
			$rep=$this->db_qr_comprass_ingdp2("select LAC_LMGEOINFO,LAC_FLLAT,LAC_FLLONG,PTR_COREGION,UFO_LLUNITE,UFO_NUTYPOSTRUCT,UFO_NUUNITE from zgrh_UNITE_FONCTION,zgrh_LIEU_ACTIVITE,zgrh_PLATEF_REG WHERE UFO_NULIEUACTIVITE=LAC_NULIEUACT AND UFO_NUPTR=PTR_NUPTR AND UFO_NUPERSOSIRE='$number' AND UFO_COACTIVE='O'");
			if ($rep && $rep[0]['LAC_FLLAT']>0) {
				if ($recenter) $ret=$this->RegionReCenter($rep[0]['PTR_COREGION']);
				if (!$ret) $ret="";
				$TSite = $this->GetSiteType($lrep[0]);
				$url = str_replace("NUUNITEAPOINTER",$lrep[0]["UFO_NUUNITE"],$this->tbLinks2SitePages[$TSite]);
				$img = $this->GetSiteImg($TSite);
				$ret.=$this->displayPoint (array("lat"=>$rep[0]['LAC_FLLAT'],"long"=>$rep[0]['LAC_FLLONG'],"title"=>$rep[0]['UFO_LLUNITE'],"url"=>$url,"img"=>$img));
			}
		}
		return($ret);
	}
	
	function look4closest($lat,$long,$limit=5,$type="%",$addwhere="",$pointinfo="") {
		$this->tbresult=array();
		
		// 111.64=2pi/360*6400, rayon de la terre, arrondi à 120 car dist à vol d'oiseau
		if ($this->base2look == "typo3") {
			$rep=db_qr_comprass("select *,sqrt((geo_lat - $lat)*(geo_lat- $lat ) + (geo_long - $long)*(geo_long - $long))*120 as dist from tx_dlcubehn03geomatic_points WHERE type LIKE '$type' $addwhere  AND deleted=0 AND hidden=0 ORDER BY dist asc LIMIT $limit");
		} else {
			if ($type != "" && $type != "%") $whtype =" AND UFO_NUTYPOSTRUCT IN (".$this->type2typostruct[$type].") ";
			$rep = $this->db_qr_comprass_ingdp2("select *,sqrt((LAC_FLLAT - $lat)*(LAC_FLLAT- $lat ) + (LAC_FLLONG - $long)*(LAC_FLLONG - $long))*120 as dist  from zgrh_UNITE_FONCTION,zgrh_LIEU_ACTIVITE WHERE UFO_NULIEUACTIVITE=LAC_NULIEUACT 
			 AND UFO_COACTIVE='O' AND LAC_FLLAT>0 AND LAC_FLLONG!=0 $whtype $addwhere ORDER BY dist asc limit $limit");
		}
		if ($rep) {
			foreach ($rep as $lrep) {
				$ce = $this->cvlrep2res($lrep);
				$this->tbresult[] = $ce;
				$TSite = $this->GetSiteType($lrep);
				$url = str_replace("NUUNITEAPOINTER",$lrep["UFO_NUUNITE"],$this->tbLinks2SitePages[$TSite]);
				$img = $this->GetSiteImg($TSite);
				$ret.=$this->displayPoint(array("lat"=>$ce['lat'],"long"=>$ce['long'],"title"=>$ce['nom'],"url"=>$url,"img"=>$img));
			}
		} else $this->tbresult=false;
		// affiche le point demandé avec une pitite maison
		$this->PointInfo=$pointinfo;
		$ret.=$this->centerMap($lat,$long,8);
		$ret.=$this->displayPoint(array("lat"=>$lat,"long"=>$long,"title"=>"Mon adresse","img"=>$this->HomeImageFile));

		return($ret);
	}

	function displayPoint ($tbPI) {
	// $tbPI : tableau associatifs de caract des points
	//$lat,$long,$title, $url, $bulleTxt, $img
	if ($tbPI['lat'] > 0 && $tbPI['long'] !=0) 
	return(($this->outJSB ? '
          <script type="text/javascript">

    //<![CDATA[ ' : '').'
		var point = new GLatLng('.$tbPI['lat'].",".$tbPI['long'].');
		var title =\''.addslashes($tbPI['title']).'\';
		var url =\''.addslashes($tbPI['url']).'\';
		var bulleTxt =\''.addslashes($tbPI['bulleTxt']).'\';
		icon.image = "'.($tbPI['img'] !="" ? $tbPI['img'] : $this->IconImageFile).'";
		map.addOverlay(createMarker(point, title, url, bulleTxt, icon));'
		.($this->outJSB ? '
    		//]]>
    		</script>' : ''));
	}

/*
La doc du geocoder : http://www.google.com/apis/maps/documentation/#Geocoding_Structured
*/
	
	function map_geocoder($address) {
		$address = urlencode($address);
		$file = fopen('http://maps.google.com/maps/geo?q='.$address.'&key='.$this->GMKey.'&output=csv', "r");
       		while(!feof($file)) {
          	 	$data = $data . fgets($file, 4096);
       		}
       		fclose ($file);
       		$tbdata=explode(",",$data);
       		if ($tbdata[0]==200) {
       			$this->GeoCodeAddressData=$tbdata;
       			return(true);
       		}
		else return (false);

	}		
}
/*if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_01/class.GeoHelper.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dlcube_hn_01/class.GeoHelper.php"]);
}*/
?>
