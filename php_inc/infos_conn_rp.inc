<? function DBDRH_conn_sel() {
     $DBDRHost="localhost";
     $DBDRHAnoUser="blair19";
     $DBDRHAnoPasswd="VG69";
     $DBDRH="INTRADRH";
     $lnkdb=mysql_connect($DBDRHost,$DBDRHAnoUser,$DBDRHAnoPasswd) or die ("ERREUR : Connection au serveur $DBDRHost par l'utilisateur $DBDRHAnoUser impossible");
     mysql_select_db($DBDRH) or die("ERREUR : base $DBDRH sur serveur $DBDRHost inaccessible !");
     return($lnkdb);
} ?>
