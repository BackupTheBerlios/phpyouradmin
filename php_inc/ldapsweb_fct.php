<?
// Adresse URL des services web ldap disponibles
define("servldap_url_prod",'http://xinf-ldaplinux/ldapsweb/');
define("servldap_url_test",'http://xinf-testlinux1/ldapsweb/');

require_once('nusoap_okphp5.php');

// fonction d'authentification
function auth ($parametres,$mprod=true) {
$servldap_url = ($mprod ? servldap_url_prod : servldap_url_test);
//echo $servldap_url;
$client = new soapclient_nusoap($servldap_url.'auth.php');
/* exempples de parametres
$parametres[uid]="bsimonnet";
$parametres[passwd]="toto2003";
$parametres[encrypt]=false; // facultatif
$parametres[code_appli]="sdm2g"; */
/* la m�thode appel�e renvoie en fait un objet, mais en PHP, pas la peine de passer par un objet, on peut le traiter directement comme un tableau associatif.... il y des propri�t�s qui ne servent � rien, that's all
*/
return($client->call('auth', $parametres));
}


// fonction de renvoi d'information sur une ou pluesieurs personnes
function reqper ($parametres,$mprod=true) {
$servldap_url = ($mprod ? servldap_url_prod : servldap_url_test);
$client = new soapclient_nusoap($servldap_url.'req_annu_pers.php');
/*
Exemples de parametres
Exemple 1 :
// on sp�cifie qu'on passe directement une requete de type rech LDAP
$parametres[attrib_filt]="direct";
// puis la requete
$parametres[filter]="(|(uid=fbr*)(uid=fg*)(uid=evi*))";

Exemple 2 :
// on ne passe pas d'attribut de filtre: la recherche se fait par d�faut sur l'attribut uid
// on ne recherche qu'un utilisateur
$parametres[filter]="fbr*";

Exemple 3 :
// on passe l'attribut de filtre, ici le prenom
// on recherche plusieurs utilisateurs : on passe un tableau en arguments
$parametres[filter]=Array(0=>"Claude",1=>"Pascal");
$parametres[attrib_filt]="Prenom";
*/

/* la m�thode renvoie un objet pour ces abrutis de Javatistes (Guillaume je plaisante)
Mais en PHP, pas la peine de passer par un objet, on peut le traiter directement comme un
tableau associatif.... il y des propri�t�s qui ne servent � rien, that's all
*/
return( $client->call('req_annu_pers', $parametres));
}

