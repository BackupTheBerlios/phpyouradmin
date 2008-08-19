<?
function trad($mess) {
$loc_message=Array(

"IND_title"=>"Welcome in phpYourAdmin tool",
"IND_txtacc"=>"You have to fill the folowing form to access the databases<BR>
Please fill : ",
"IND_bdduser"=>"Database user name",
"IND_bddpasswd"=>"Database password",
"IND_pyauser"=>"User for logs",
"IND_choilang"=>"Language",

"LB_title"=>"Databases editig of server ",
"LB_txtacc"=>"Editing is only possible if the database has been specialiy parametered.<br/>Contact your webmaster<br/><br/>",
"LB_baselist"=>"List of the databases you can edit:",
"LB_createDT"=>" to access the tool to parameter the databases",


"LT_reqv"=>"Impossible to execute an empry query !",
"LT_notable"=>"You have to select a table !",



"BT_retour"=>"Back",
"BT_valider"=>"Submit",
"BT_click"=>"Click",
"BT_here"=>" here ",

);
return ($loc_message[$mess]!="" ?  utf8_decode($loc_message[$mess]) : $mess);
}
?>
