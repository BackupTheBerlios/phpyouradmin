<? require("infos.php");
sess_start();
// reset des variables de session de tri
//$_SESSION["where_sup"]=""; 
unregvar ("where_sup");
//$_SESSION["tbchptri"]=array(); 
unregvar ("tbchptri");
//$_SESSION["tbordtri"]=array(); 
unregvar ("tbordtri");
$_SESSION["FirstEnr"]=0;
//$_SESSION["tbAfC"]=array(); 
unregvar ("tbAfC");
//$_SESSION["ss_parenv"]['NoConfSuppr']=""; 
unregvar ("ss_parenv['NoConfSuppr']");
//if ($cfLB=="vrai") $_SESSION["reqcust"]=""; //unregvar("reqcust"); // si on vient de la liste des bases, on anule la req
if ($cfLB=="vrai") unregvar("reqcust"); // si on vient de la liste des bases, on anule la req

// suppression de la var de session au cas ou on ai appel�un ajout directement
if (isset($ss_adrr['edit_table.php'])) {
   $ss_adrr['edit_table.php']="";
   $_SESSION["ss_adrr"]=""; //session_register("ss_adrr");
}

//include_once("reg_glob.inc");
DBconnect();

$admadm = (int)$_REQUEST['admadm'];

$title=($admadm==1? trad(LT_titleadm) : trad(LT_titleedit))." ".$DBName;
include ("header.php");

// gestion creation/effacement des tables virtuelles
// creation
if (isset($_REQUEST['lc_NM_VTB2C'])) {
// lc_NM_TABLE='+table+'&lc_NM_VTB2C=
// $TBDname $NmChDT'
	if (strstr($_REQUEST['lc_NM_VTB2C'],$id_vtb)) { // verifie que le nom entré contient bien l'id de table virtuelle
		if ($_REQUEST['lc_NM_TABLE'] != "newvtable") {
			$rw=db_qr_comprass("SELECT * FROM $TBDname WHERE NM_TABLE='".$_REQUEST['lc_NM_TABLE']."'");
			foreach($rw as $enr) {
				$enr['NM_TABLE'] = $_REQUEST['lc_NM_VTB2C'];
				
				if ($enr['NM_CHAMP']==$NmChDT) {
					$enr['LIBELLE'].="  ! Alias de la table : ".$_REQUEST['lc_NM_TABLE'];
				} else {
					$enr['VALEURS'] .= "\n".'$physTable='.$_REQUEST['lc_NM_TABLE'];
					$enr['VALEURS'] .= "\n".'$locFKeys='.$enr['NM_CHAMP'];
				}
				foreach ($enr as $chp=>$val) $enr[$chp]="'".addslashes($val)."'";
				db_query("INSERT INTO $TBDname ".tbset2insert($enr));
			}
		} else { // nvlle table virtuelle
			db_query("INSERT INTO $TBDname (NM_TABLE,NM_CHAMP,LIBELLE,TYPAFF_L) VALUES ('".$_REQUEST['lc_NM_VTB2C']."','TABLE0COMM','Nouvelle table virtuelle','AUT')");
		}
	} else echo "<H1>".trad("LT_err_cvtb").$id_vtb."</H1>";
}
// effacement
if (isset($_REQUEST['lc_NM_VTB2D'])) {
	db_query("DELETE FROM $TBDname where NM_TABLE='".$_REQUEST['lc_NM_VTB2D']."'");
}
?>

<? if ($admadm=="1") { // affiche les liens en orange pour bien diff�encier
?>
<STYLE>
A {color: <?=$admadm_color?>}
A:visited {color: <?=$admadm_color?>}

</STYLE>
<? } // fin styles pour adm=1

JSprotectlnk(); // colle le code JS d'une fonction qui prot�e un lien par un mot de passe
?>
<SCRIPT language="JavaScript">

function verif(theform)
{
  if (document.theform.lc_NM_TABLE.value=="")
    {
      alert ("<?=trad(LT_notable)?>");
      return false;
    }
  return true;
}
function subm(table)
{ // attention, document. est ncesaire pour Mozilla
  document.theform.lc_NM_TABLE.value=table;
  if (table=='__reqcust' && document.theform.lc_reqcust.value=='') {
     alert('<?=trad(LT_reqv)?>'); }
  else document.theform.submit();
}
// creation table virtuelle
function js_create_vtb(table) { 
	var id_vtb='<?=$id_vtb?>';
	nmvtable=prompt('<?=trad('LT_vtb_name')?>',id_vtb);
	if (nmvtable!=null && nmvtable!=id_vtb) {
	   window.location='LIST_TABLES.php?lc_NM_TABLE='+table+'&lc_NM_VTB2C='+nmvtable+'&admadm=1';
	   }
}	   

// suppression table virtuelle
function js_suppr_vtb(table) { 
if (confirm('<?=trad(LT_confirm_del_vtb)?>')) {
	   window.location='LIST_TABLES.php?lc_NM_VTB2D='+table+'&admadm=1';
	   }
}	   

function submrqc(rqc,rqname)
{ // attention, document. est n��saire pour Mozilla
  document.theform.lc_reqcust.value=rqc;
  document.theform.reqcust_name.value=rqname;
  document.theform.lc_NM_TABLE.value='__reqcust';
  document.theform.submit();
}


function reqsave() {
if (document.theform.lc_reqcust.value=='') {
	alert('<?=trad(LT_reqsavevide)?>');
	document.theform.lc_reqcust.focus;
}
else {
	document.theform.action_req.value='save';
	document.theform.action='LIST_TABLES.php';
	document.theform.submit();
}
}
// boite de confirmation  de suppression d'un enregistrement
function ConfSuppr(url) {
if (confirm('<?=trad(LR_confirm_del_message)?>'))
self.location.href=url;
}

// Pomp�de phpMyAdmin 
function insertValueQuery() {
    var myQuery = document.theform.lc_reqcust;
    var myListBox = document.getElementById('sql_words');

    if(myListBox.options.length > 0) {
        var chaineAj = "";
        var NbSelect = 0;
        for(var i=0; i<myListBox.options.length; i++) {
            if (myListBox.options[i].selected){
                NbSelect++;
                if (NbSelect > 1)
                    chaineAj += ", ";
                chaineAj += myListBox.options[i].value;
            }
        }

        //IE support
        if (document.selection) {
            myQuery.focus();
            sel = document.selection.createRange();
            sel.text = chaineAj;
            document.theform.insert.focus();
        }
        //MOZILLA/NETSCAPE support
        else if (myQuery.selectionStart || myQuery.selectionStart == "0") {
            var startPos = myQuery.selectionStart;
            var endPos = myQuery.selectionEnd;
            var chaineSql = myQuery.value;

            myQuery.value = chaineSql.substring(0, startPos) + chaineAj + chaineSql.substring(endPos, chaineSql.length);
        } else {
            myQuery.value += chaineAj;
        }
    }
}

</SCRIPT>

<? if ($debug && isset ($cktbAfC)) { // le cookie est m�oris�fonctionne
  $tbAfC=explode(";",$cktbAfC);
  echovar ("tbAfC","yes");
  }?>

<form action="<?=($admadm!="1" ? "req_table.php" : "admdesct.php") ?>" method="post" name="theform" onsubmit="return verif(this)" ENCTYPE="multipart/form-data">
<input type="hidden" name="lc_NM_TABLE">
<H1><?=($admadm==1 ? trad('LT_titlehadm') : trad('LT_titlehedit'))." ".$DBName?></H1>
<h2><?=trad('LT_clicktable')?></h2>
<?=($admadm=="1" ? "<h2>".trad(LT_bcadm)."</h2>" :"")?>
<ul>
<?
// affiche liste des tables fonction de ce qu'il y a dans TABLE0COMM
$TYPAFFLHID=($admadm=="1" ? "" :  " AND (TYPAFF_L!='' OR  TYPAFF_L IS NOT NULL) AND NM_TABLE NOT LIKE '$id_vtb%'"); // Chez Oracle - et effectivement contrairement à la norme - la chaîne vide '' est équivalenté à NULL.
$rq = "SELECT NM_TABLE, LIBELLE, ".$GLOBALS["NmChpComment"]." from $TBDname where NM_CHAMP='$NmChDT' AND NM_TABLE != '$TBDname' AND NM_TABLE NOT LIKE '__reqcust' $TYPAFFLHID order by ORDAFF_L, LIBELLE";
$qr = db_query($rq) ; // recupere libelle, ordre affichage et COMMENT, si type affichage ="HID", on affiche pas la table
while ($res=db_fetch_row($qr))
  {
  $tb_name=$res[0];
  $tb_lbl=stripslashes($res[1]);
  $tb_comment=stripslashes($res[2]);
  // type=\"radio\" => maintenant liste �puces
  echo "<LI><a href=\"javascript:subm('$tb_name');\" title=\"".($tb_comment!="" ? $tb_comment : "Acces table")."\">".$tb_lbl."</a>&nbsp;&nbsp;<small>($tb_name)</small>";
  if ($admadm!="1" && $ss_parenv[ro]!=true) { // bton nvle enregsitrement
     echo "&nbsp;&nbsp;<a class=\"fxsmallbutton\" href=\"edit_table.php?lc_NM_TABLE=$tb_name&lc_adrr[edit_table.php]=".$_SERVER["PHP_SELF"]."\" title=\"".trad('LT_addrecord')."\">  <img src=\"new_r.gif\"> </a>";
     }
  elseif ($admadm=="1") { // admin possibilité de crer une table virtuelle a partir d'une reelle ou pas d'ailleurs
  	echo "&nbsp;&nbsp;<a href=\"javascript:js_create_vtb('$tb_name');\" title=\"".trad('LT_creer_vtb')."\"> <IMG SRC=\"vtb_icon.png\" border=\"0\"> </a>";
  	if (strstr($tb_name,$id_vtb)) { // admin possibilité de supprimer une table virtuelle
  		echo "&nbsp;&nbsp;<a href=\"javascript:js_suppr_vtb('$tb_name');\" title=\"".trad('LT_suppr_vtb')."\"> <IMG SRC=\"del.png\" border=\"0\"> </a>";
     }
     }
  
  echo "<br>\n";
// commentaire affich�maintenant en bulle
  } // fin boucle sur les tables
echo "</UL>";
if ($admadm!="1") {
  JSprotectlnk();
  ?><input type="hidden" name="lc_FirstEnr" value="0"><?
  if ($ss_parenv[blair]!="1" && $ss_parenv[ro]!=true) {
    ?>
    <h2><?=trad(LT_reqcust)?></h2>
    
    <? $ss_parenv['reqcust_name']= $_SESSION['reqcust_name'];
    	if ($_REQUEST['action_req']=="-1") {
		msq("delete from $TBDname where NM_TABLE='__reqcust' AND NM_CHAMP='".$_REQUEST['key']."'");
	} elseif ($_REQUEST['action_req']=="load") {
		$resrq = db_qr_rass("select * from $TBDname where NM_TABLE='__reqcust' AND NM_CHAMP='".$_REQUEST['key']."'");
		$ss_parenv['reqcust_name'] = $resrq['LIBELLE'];
		$reqcust = stripslashes($resrq[$GLOBALS["NmChpComment"]]);
		echo '<input type="hidden" name="key" value="'.$resrq['NM_CHAMP'].'"/>';
	} elseif ($_REQUEST['action_req']=="save") {
		$key = md5($_REQUEST['reqcust_name']);
		msq("delete from $TBDname where NM_TABLE='__reqcust' AND NM_CHAMP='".$key."'");
		msq("INSERT INTO $TBDname 
		(NM_TABLE, NM_CHAMP,LIBELLE,".$GLOBALS["NmChpComment"].") 
		VALUES 
		('__reqcust','".$key."','".addslashes($_REQUEST['reqcust_name'])."','".addslashes($_REQUEST['lc_reqcust'])."')");
		$reqcust=$_REQUEST['lc_reqcust'];
		$ss_parenv['reqcust_name'] = $_REQUEST['reqcust_name'];
		echo '<input type="hidden" name="key" value="'.$resrq['NM_CHAMP'].'"/>';
	}

    // GESTION DES REQUETES UTILISATEUR CUSTOM
   	$LT_reqedit=trad("LT_reqedit");
    	$LT_reqdel=trad("LT_reqdel");
	
    	$rqrqc=msq("select * from $TBDname where NM_TABLE='__reqcust'");
    	if (db_num_rows($rqrqc)>0 ) {	
    		echo "<UL>";
		while ($res=db_fetch_array($rqrqc)) {
			$url = addslashes("LIST_TABLES.php?key=".$res['NM_CHAMP']."&action_req=-1");
			echo "<LI> <a href=\"LIST_TABLES.php?key=".$res['NM_CHAMP']."&action_req=load\">".$res['LIBELLE']."</a>&nbsp;\n";
			echo "<A HREF=\"javascript:submrqc('".addslashes($res[$GLOBALS["NmChpComment"]])."','".addslashes($res['LIBELLE'])."');\" TITLE=\"Execute requete\" class=\"fxbutton\"> !</A>&nbsp;";
			echo "<A HREF=\"javascript:ConfSuppr('".$url."');\" TITLE=\"Supprimer la requete\"><IMG SRC=\"del.png\" border=\"0\" height=\"12\"></A>&nbsp;</LI>";
		}
    		echo "</UL>";
    	} // fin si il y a des r�onses
		   
    ?>
    <h3><?=trad('LT_reqcust_cour')?></h3>
    <input type="hidden" name="action_req">
    <b><?=trad('LT_reqcust_name')?> </b><input type="text" name="reqcust_name" value="<?=$ss_parenv['reqcust_name']?>" size="50"  MAXLENGTH="50">&nbsp;&nbsp;<a TITLE="<?=trad("LT_reqsave")?>" href="#" onclick="reqsave();"><img src="filesave.png" border=0></a><br><br/>
    
    <table border="0"><tr>
    <td><b><?=trad('LT_reqcust_code')?> </b><br/>
    <textarea name="lc_reqcust" cols="70" rows="5"><?=$reqcust?></textarea>
    <input type="hidden" name="lc_parenv[lbreqcust]" value="Requete specifique utilisateur"><br>
    <?//<input type="text" name="bite" onclick="alert(getIndex(this))" value="test index objet formulaire"><br>?>
    
    <a class="fxbutton" href="#" onclick="subm('__reqcust');"><?=trad("LT_reqexec")?></a></td>
    <td><a href="#" onclick="insertValueQuery();" class="fxbutton"> << </a></td>
    <td>
    <?
    $tbvalsql=array(" SELECT " =>" SELECT "," * " =>" * "," FROM " =>" FROM "," WHERE " =>" WHERE "," ORDER BY " =>" ORDER BY"," LEFT JOIN " =>" LEFT JOIN ");
    $rqtb=msq("select NM_TABLE,NM_CHAMP,LIBELLE from $TBDname where NM_TABLE NOT LIKE '__reqcust' AND NM_TABLE NOT LIKE '$id_vtb%' AND NM_CHAMP='$NmChDT' ORDER BY ORDAFF_L");
   
    while ($rstb=db_fetch_array($rqtb)) {
    	$tbvalsql[' `'.$rstb['NM_TABLE'].'` ']=$rstb['LIBELLE'];
	$rqchp=msq("select NM_TABLE,NM_CHAMP,LIBELLE from $TBDname where NM_TABLE='".$rstb['NM_TABLE']."' AND NM_CHAMP!='$NmChDT' ORDER BY ORDAFF");
	while ($rschp=db_fetch_array($rqchp)) {
		$tbvalsql[' `'.$rstb['NM_TABLE'].'`.`'.$rschp['NM_CHAMP'].'` ']="-- ".tradLib($rschp['LIBELLE']); // le nom de table fout la merde avec PYA ???
		//$tbvalsql[' `'.$rschp['NM_CHAMP'].'` ']="-- ".$rschp['LIBELLE'];
		
	}
    } // fin boucle sur les tables
    
    DispLD($tbvalsql, "sql_words","yes");
    ?>
    
    <SCRIPT language="JavaScript">
    // modifie l'evenement double clic de la liste
    document.theform.getElementById('sql_words').ondblclick = insertValueQuery;
    </script>
    </td>
    </tr></table>
    <br/>
    <h2><?=trad(LT_param)?></h2>    
    <?=trad(LT_nblig_aff_ppage)?>
    <input type="text" name="lc_nbligpp" size="3" maxlength="3" value="<? echo ($nbligpp>0 ? $nbligpp : $nbligpp_def) ?>"><br>
    <input type="checkbox" name="lc_NoConfSuppr" value="No"><?=trad(LT_noconfirmdelete)?><br><br>
    <small><?=trad(BT_click)?> <a class="fxsmallbutton" href="#" onclick="protectlnk('admadm.php','<?=$jsppwd?>','<?=trad('com_enter_password')?>');"><?=trad('BT_here')?></a> <?=trad(LT_change_table_edit_prop)?></small>
    <br>

    <? } // fin si pas blaireau ni lecture seule
  if ($ss_parenv['blair']!="1") { // si pas blaireau seulement
     ?>
    <br>
      <?=ret_adrr($_SERVER["PHP_SELF"],true,"LT_retLB")?><br><br>
     <?
     } // fin si pas blaireau
    } // fin si admadm<>1
   else
   { // admadm=1
	echo "<p>&nbsp;&nbsp;<a href=\"javascript:js_create_vtb('newvtable');\" title=\"".trad('LT_creer_vtb')."\"> Nouvel table alias (\"virtuelle\") <IMG SRC=\"vtb_icon.png\" border=\"0\"> </a></p>";

   ?>
  <small><br><br>&#149; <?=trad('BT_click')?> <A class="fxsmallbutton" HREF="LIST_TABLES.php?admadm=0"><?=trad('BT_here')?></A> <?=trad(LT_goback_content_table_edit)?>
  <br><br>&#149; <?=trad("BT_click")?> <a class="fxsmallbutton" href="CREATE_DESC_TABLES.php?DBName=<?=$DBName?>"><?=trad("BT_here")?></a> <?=trad("LB_createDT")?></small>
<? } // fin si admadm<>1
?>
</form>
<? include ("footer.php"); ?>
