<?
// Fonction de connection anonyme ave un user en lecture seule
 function DBLS_conn() {
     $DBLSHost="localhost";
     $DBLSAnoUser="blair19";
     $DBLSAnoPasswd="VG69";
     $lnkdb=mysql_connect($DBLSHost,$DBLSAnoUser,$DBLSAnoPasswd) or die ("ERREUR : Connection au serveur $DBLSHost par l'utilisateur $DBLSAnoUser impossible");
     return($lnkdb);
} ?>
