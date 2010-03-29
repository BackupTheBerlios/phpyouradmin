<?
// fonction qui echoise les FONCTIONS JAVASCRIPT a initialiser
// pour l'utilisation d'Ajax
function echAjaxJSFunctions($dbscr=true) {

$theJS = "
/* JS Functions for Ajax/XMLHttpRequest dynamic content (made by DLCube ;) */

function ahah(url,target) {
   // native XMLHttpRequest object
   document.getElementById(target).innerHTML = 'envoi de la requete au serveur...';
   if (window.XMLHttpRequest) {
       var req = new XMLHttpRequest();
       req.onreadystatechange = function() {ahahDone(target,req);};
       req.open(\"GET\", url, true);
       req.send(null);
   // IE/Windows ActiveX version
   } else if (window.ActiveXObject) {
       var req = new ActiveXObject(\"Microsoft.XMLHTTP\");
       if (req) {
           req.onreadystatechange = function() {ahahDone(target,req);};
           req.open(\"GET\", url, true);
           req.send();
       }
   } 
}
function ahahDone(target,req) {
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
/* End of JS Functions for Ajax/XMLHttpRequest dynamic content (made by DLCube ;) */

";
return ($dbscr ? outJS($theJS) : $theJS);
}
?>