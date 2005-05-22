<? require("infos.php");
$title="Aide de phpYourAdmin";
include ("header.php"); ?>
<meta name="generator" content="Namo WebEditor v5.0(Trial)">

<span class="titrered20px"><a name="haut"></a>SOMMAIRE DE L'AIDE phpYourAdmin</span><br><br>
<span class="chapitrered12px">� <a href="#intro">G�n�ralit�s...</a><br>
� <a href="#util">Utilisation</a><br>
� <a href="#admin">Administration</a><br>
� <a href="#progobj">Programmation � l'aide des objets</a><br>
� <a href="#clog">Evolution des versions...</a></span><br>
<hr width="70%" size="1">
<a name="intro"></a><span class="chapitrered12px">G�n�ralit�s<br></span>
<blockquote><b>phpYourAdmin</b> est un utilitaire permettant l'�dition param�trable du <u>contenu</u> de bases de donn�es mysql.<br>
Il est compl�mentaire de phpMyAdmin, qui lui est plut�t destin� � l'�dition de la <u>structure</u> des bases (type de champs, valeurs par d�faut, index, etc ...)<br>
Le fonctionnement <b>phpYourAdmin</b> repose sur une table sp�ciale, nomm�e <? echo $TBDname ?>, dont le nom est d�fini dans la variable $TBDname du fichier infos.php 
    ou fonctions.php, cr��e dans chaque base pouvant �tre administr�e. La cr�ation de cette table peut heureusement �tre automatiquement faite par le programme.
Cette table contient un enregistrement par champ et par table de la base administr�e 
    (plus un par table contenant des renseignements propre � la table), contenant 
    les renseignements d'�dition pour  chaque champ de la base dans laquelle elle est cr��e.
    
    <p><b>phpYourAdmin</b> poss�de des �crans sp�cifiques de g�n�ration et 
     d'�dition des enregistrements 
    de cette table.</p>
    <p><a href="aide.php#haut"><img src="haut.gif" width="70" height="11" border="0" alt="Sommaire"></a><br>
    </p>
</blockquote>
<hr width="70%" size="1">
<a name="util"></a><span class="chapitrered12px">Utilisation<br></span>
<blockquote>
<a name="utilpre" class="boldred11px">Pr�ambule</a> 
    <br>Notez les noms des fichiers indiqu�s pour chaque �cran: ils vous permettront 
    d'effectuer un lien � partir d'une de vos pages directement vers certaines 
    pages de PYA, et de g�rer les adresses de retour,&nbsp;� condition toutefois 
    de passer certaines&nbsp;variables par l'URL (sinon l'utilisateur se retrouvera 
    � la page d'accueil &nbsp;...)
    <p><a name="utilacc" class="boldred11px">Ecran d'accueil</a><a class="boldred11px"> 
    (index.php)</a><br>

Sont demand�s: <br>- user et mot de passe pour l'acc�s au serveur de base de 
    donn�es MySql<br>- identifiant de modification d'enrgistrement: cet identifiant 
    sera utilis� pour le renseignement automatique des champs de type user MAJ
    </p>
<p><a name="utilacc" class="boldred11px">Liste des bases du serveur</a><a class="boldred11px"> 
    (LIST_BASES.php)</a><br>

Cet �cran liste les bases accessibles sur le serveur courant. Un clic sur une 
    base am�ne sur la page listant les tables de la base (voir ci-apr�s)<br>En 
    bas de la page, existe un lien (optionel en fonctionde la configuration)&nbsp;vers 
    les pages d'administration r�f�renc� <SPAN class="legendes9x"><i>Cliquez <u><font color="#0033CC">ici</font></u> pour 
appeler l'utilitaire de g�n�ration de table de description (DANGEREUX !)</i></SPAN></p>
    <p><a name="utilLTBL" class="boldred11px">Liste des tables&nbsp;de la base 
    courante</a><a class="boldred11px"> (LIST_TABLES.php)</a><br>

Cet �cran liste les tables de la base courante. En face de chaque table, il 
    existe un bouton  <span class="FdR">+</span> permettant d'ajouter directement un enrgistrement 
    � la table. </p>
    <p> En bas de la page, 
    existe un lien optionel vers les pages d'administration r�f�renc� <SPAN class="legendes9px"><i>Cliquez <font color="#0033CC"><u>ICI 
    </u></font>pour changer 
les propri�t�s d'EDITION des tables .....(R�serv� aux initi�s ...) </i></SPAN></p>
    <p><a name="utilreq" class="boldred11px">Ecran de requ�te optionel</a><a class="boldred11px"> (req_table.php)</a><br>

Cet �cran n'apparait que si au moins un filtre de requ�te ou un affichage de 
    champ optionnel a �t� param�tr� pour au moins un champ de la table (voir 
    les rubriques <a href="#admtypfilt">Type de filtre</a> ou <a href="#admafsel">affichage 
    s�lectionable</a> dans la partie administration.</p>
    <p>Les controles de requ�te requi�rent les pr�cisions suivantes:<br>- dans 
    le cas d'une boite d'entr�e, pour effectuer une recherche sur le d�but d'un 
    champ entrez DEBU%, sur la fin %FIN, ou sur une partie&nbsp;%ART% <br>- 
    toutes les listes d�roulantes sont multiples: s�lection sur plusieurs valeurs 
    possibles (fonction logique OU) en cliquant et&nbsp;en maintenant la touche 
    Ctrl enfonc�e<br>- dans le cas des cases � cocher (peux de valeurrs disponibles), 
    penser � d�cocher la case %<br>- les dates doivent �tre entr�es sous la 
    forme 05/11/2002<br><br>La case � cocher<b> N�gation </b>effectue un non 
    logique de la condition sp�cifi�e&nbsp;au dessus<br>&nbsp;<br>Toutes les 
    conditions entr�es sur des champs diff�rents sont li�es par la fonction 
    logique ET</p>
    <p><a name="utilLENR" class="boldred11px">Liste des enregistrements (list_table.php)</a><br>

Cet �cran liste les enregistrements r�pondant aux crit�res optionnels donn�s 
    ci-avant. A condition que le param�tre $ss_parenv[ro] soit diff�rent de 
    1, il est possible d'�diter, copier ou supprimer les enregistrements. Sinon, 
    il n'est possible que de les visualiser dans une popup.<br>Les enregistrements 
    sont list�s <?=$nbligpp_def?> par <?=$nbligpp_def?>, ce nombre �tant sp�cifi� dans la variable $nbligpp_def dans le fichier fonctions.php<br><br>Il 
    est possible de trier les enregistrements suivant un champ en cliquant sur 
    les fl�ches de classement <span class="FdR"><img src="flasc.gif" width="15" height="15" border="0"> <img src="fldesc.gif" width="15" height="15" border="0"></span> . <br>On peut avoir 3 classements  par ordre 
    de priorit�, indiqu�s par des n� dans les fl�ches; il faut commencer par l'ordre de priorit� le plus bas.<br>Exemple: 
    on veut trier par code postal et en cas d'�galit�de code postal par nom -&gt; on clique 
    d'abord sur la fl�che dans la colonne&nbsp;nom, puis sur celle dans la colonne 
    code postal.<br>R<u>em:</u> pour les champs li�s, le classement s'effectue 
    (malheureusement)&nbsp;non pas suivant la valeur affich�e mais suivant la 
    valeur stock�e, ce qui peut peut donc donner des r�sultats assez bizarres.<br><br><img src="telecharger.gif" width="70" height="11" border="0"> 
    : ceci va permetre de t�l�charger un fichier contenant tous les enregistremnts 
    r�pondant aux crit�res sp�cifi�s (et non seulement les  <?=$nbligpp_def?> affich�s) 
    sous forme d'un fichier texte ou les champs sont s�par�s par des caract�res 
    tabulation (format tsv). Ce type de fichier peut s'ouvrir direcmenet avec 
    Excel.&nbsp;<br></p>
    <p><a name="utilEDNR" class="boldred11px">Edition/copie d'un enregistrements (edit_table.php)</a><br>

Cet �cran permet l'�dition ou copie d'un enregistrement.<br>Le bouton <img src="valider.gif" width="70" height="13" border="0"> 
    valide les changements<br>Le bouton <img src="fermer.gif" width="70" height="11" border="0"> 
    ferme la fen�tre SANS&nbsp;VALIDER LES CHANGEMENTS<br>Le bouton <img src="annuler.gif" width="70" height="13" border="0">&nbsp;ne 
    ferme 
    pas la fen�tre, mais r�initialise les valeurs des champs aux valeurs par 
    d�faut (nouvel enregistrement) ou initiales avant changement, apr�s un message 
    de confirmation.<br>&nbsp;<br>Pour info technique, los de la validation, 
    la page amact_table.php est appel�e par le formulaire.</p>
</blockquote>
<div align="center"><a href="#haut"><img src="haut.gif" width="70" height="11" border="0" alt="Sommaire"></a><br>
</div><hr width="70%" size="1">
<a name="admin" class="chapitrered12px">Administration.<br></a>
<blockquote>
<a name="admgen" class="boldred11px">G�n�ralit�s</a><br>
Les utilitaires de cette partie sont accessibles <br>
- en bas de la liste des bases du serveur, dans le lien r�f�renc�
<SPAN class="legendes9x"><i>Cliquez <u><font color="#0033CC">ici</font></u> pour 
appeler l'utilitaire de g�n�ration de table de description (DANGEREUX !)</i></SPAN> : 
    cet&nbsp;utilitaire va permettre de g�n�rer la table de description d'une 
    base lorsque celle-ci n'a jamais �t� cr��, ou la mettre � jour lorsque la 
    structure de la table a �t� chang�e<br>- 
    en bas de la liste des tables de la base courante, dans le lien r�f�renc�
 <SPAN class="legendes9px"><i>Cliquez <font color="#0033CC"><u>ICI 
    </u></font>pour changer 
les propri�t�s d'EDITION des tables .....(R�serv� aux initi�s ...) : </i></SPAN>&nbsp;cet 
    utilitaire permet l'�dition individuelle des propri�t�s d'�dition 
    de chaque champ ou table<br>Ces liens peuvent-�tre d�sactiv�s en positionnant la variable $lc_parenv[blair] � 1 (pour les blaireaux ;-)<br>
    <p>
    <a name="admdesct" class="boldred11px">(re)g�n�ration de table de description</a><br>Cliquer en bas de la liste des tables de la base courante, dans le lien r�f�renc�
 <SPAN class="legendes9px"><i>Cliquez <font color="#0033CC"><u>ICI 
    </u></font>pour changer 
les propri�t�s d'EDITION des tables .....(R�serv� aux initi�s ...)</i></SPAN><br>Un 
    �cran nomm� Super Administration de phpYourAdmin apparait, comportant 3 
    �tapes:</p>
    <ol type="1">
        <li>S�lection de la base � traiter</li>
        <li>S�lection <br>
        - de ou des tables � traiter (multiple par Ctrl+clic dans la liste);<br>
        - op�ration: consultation de la table de description, MAJ, ou (re)g�n�ration compl�te </li>
        <li>affichage du r�sultat</li>
    </ol>
    A noter<br>
    - ces op�rations sont tr�s risqu�es, notamment sur des bases exsitantes (faire un backup de la table de description en cas de doute)<br>
    - la (re)g�n�ration ne devrait �tre appel�e que lors de la cr�ation<br>
    - la MAJ est � effectuer obligatoirement lorsque des champs ont �t� ajout�s, renomm�s ou supprim�s. Elle est inutile s'il y a eu simplement changement de type.<br>  
    <p><a name="admbas" class="boldred11px">Edition des propri�t�s d'�dition d'une base</a><br>Cliquer en bas de la liste des tables de la base courante, dans le lien r�f�renc�
 <SPAN class="legendes9px"><i>Cliquez <font color="#0033CC"><u>ICI 
    </u></font>pour changer 
les propri�t�s d'EDITION des tables .....(R�serv� aux initi�s ...)</i></SPAN><br>On &quot;retombe&quot; � nouveau sur une liste des tables 
    colori�e en orange pour bien signaler le mode d'�dition, mais cette fois un clic am�ne sur un tableau permettant d'�diter les propri�t�s d'�dition de la table et non ses valeurs<br>
    </p>
<p>Les colonnes sont d�taill�es ci-apr�s</p>
    <p><a name="admlib" class="boldred11px">Nom du champ- Libell� � afficher - Propri�t�s:</a><br>
    Ici l'utilisateur ne peut entrer que le Libell� du champ qui sera ensuite affich�. 
    Lui sont indiqu� le v�ritable nom du champ ainsi que ses caract�ristiques 
    (ne pouvant �tre chang�es qu'avec phpMyAdmin)<br><b><u>Attention</u></b>: 
    il peut exister des applications, qui, si elles sont programm�es � l'aide 
    des objets PYA, se servent de ce libell� (DRH par exemple). Changer le libell�, 
    comme tout autre caract�ristique,&nbsp;peut donc avoir des r�percussions 
    ext�rieures � la seule utilisation de PYA<br>&nbsp;</p>
    <p>

<a name="admafl" class="boldred11px">Ordre aff. Liste/ Type aff. Liste</a><br>
Lorqu'on clique sur une table dans la liste des tables, on a apr�s l'affichage 
    d'une grille de requ�te optionnelle, une liste des enregistrements (affich�es x par x), avec des liens en ent�te de chaque ligne permettant d'effacer, �diter ou copier chaque enregistrement.
Ces deux champs permettent de choisir <br>
� l'ordre des colonnes du tableau (la valeur indiqu�e sera tri�e de fa�on alphanum�rique et non num�rique)<br>
� et le type d'affichage. <br>
Le type ne demande pas de commentaires, sauf pour les valeurs:</p>
<ul>
<li>tronqu�: dans ce cas le champ est tronqu� � <? echo $nbcarmxlist ?>caract�res, ce nombre pouvant �tre chang� en modifiant la variables $nbcarmxlist dans le fichier commun info.php
<li>li�: on affiche dans ce cas les valeurs li�es statiquement ou � une autre table, d�finie dans la <a href="#admval">colonne valeurs 
</a></ul><br><br>

<a name="admtyped" class="boldred11px">Type �dition</a><br>
Ceci concerne le type d'�dition dans l'�cran d'�dition d'un enregistrement.
Les valeurs possibles<ul>
<LI>Cach� : le champ n'apparait pas</li>
<LI>Boite Texte: Sans commentaire, on peut sp�cifier dans la zone valeurs la 
        longueur affich�e de la boite et la longueur max </li>
<LI>Text Area: Sans commentaire, sion, que les dimensions peuvent etre sp�cifi�es dans 
        la   <a href="aide.php#admval">colonne valeurs 
 </a>sous la forme nb_ligne,nb_colonnes</li>
<LI>Auto: adapt� en fonction du type de champ, si c'est un enum ou un set, on a une liste par exemple. Cela prend aussi en compte les NULL autoris�s ou pas</li>
<LI>Liste Deroulante, Liste  Deroul Li�e, Liste Deroulante � choix multiples, Liste  Deroul Li�e � choix multiples: les valeurs statiques 
        affich�es ou les caract. du lien vers une autre table/base sont � rentrer dans la <a href="#admval">colonne valeurs</a> ci-apr�s</li>
<LI>Statique: la valeur du champ apparait mais n'est pas �ditable par l'utilisateur</li>
<LI>Statique Li�e: valeur &quot;p�ch�e&quot; dans une autre table, non �ditable, la table concern�e est � rentrer dans la <a href="#admval">colonne valeur</a> ci-apr�s</li>
<LI>Fichier-Photo: en saisie on touve un bouton parcourir, puis en visu une 
        image si c'est un fichier de type image (en fonction de l'extension),ou 
        un lien vers un fichier si sinon. <br>
<span class="normalred11px">Attention</span> <br>
� la taille des fichiers est limit�e � <? echo $MaxFSize ?> octets, ce nombre pouvant �tre chang� en modifiant la variables $MaxFSize dans le fichier commun info.php<br>
� il est n�c�ssaire de cr�er un r�pertoire, accessible en �criture par le user sous lequel tourne le serveur apache (voir /etc/httpd/conf/httpd.conf), ce r�pertoire est 
        : <br>� par d�faut NOMBASE_NOMTABLE_NOM_CHAMP (attention au minuscules/majuscules).<br> 
         � ou � saisir contenu  dans la <a href="aide.php#admval">colonne valeurs</a> sous 
        forme d'un chemin, termin� par /<br>Il est conseill� de cr�er  un lien symbolique 
        pointant vers un dossier contenu dans le dossier des autres fichiers 
        de l'applications en cours s'il en existe un ....<br>
Si vous respectez ces consignes, la gestion est totalement transparente, et l'effacement des fichiers li�s automatiques
</li>
</ul>
<br>
<a name="admval" class="boldred11px">Valeurs</a><br>
<u>NB:</u>: ce champ a DE LOIN la syntaxe la plus COMPLEXE notamment dans le cas des liaisons inter-champs !. Pri�re de lire en d�tail (et au calme ;-) les explications qui suivent !<br><br>
Les valeurs sont notamment utilis�es dans:<ul>
<li> dans le type d'�dition Normal lorsque celui-ci est sur Normal, on rentrera ici la taille de la boite de saisie, et le nombre max de caract�res du champ INPUT , sinon les valeurs par d�faut du navigateur sont utilis�es
<li> dans le type d'�dition TEXTAREA, on rentre ici les dimensions de la boite, sinon les valeurs $nbrtxa (=<?=$nbrtxa?> et $nbctxa (=<?=$nbctxa?>) sp�cifi�es dans le fichier infos.php 
        ou fonctions.php sont utilis�es pour les dimensions par d�faut
<li> dans les types d'�dition listes
<li> le type d'affichage li�
<li> les filtres de requ�te de type Liste d�roulante valeurs fixes ou valeurs li�es<br>
</ul>
Les valeurs de liste sont de 3 grands types:<br>
� une liste de valeurs fixes, s�par�es par des , : ces valeurs sont affich�es tel quel dans les types d'�dition liste d�roulante et liste d�roulante multiples<br>
� une liste de paires de cl�s-valeurs fixes, (cl�1:valeur1,cl�2:valeur2 ...) : les valeurs sont affich�es, mais ce sont les cl�s qui sont en fait r�ellement stock�es dans le champ<br>
� une liste de valeurs <i>p�ch�es</i> dans une autre table de la base ou m�me d'une autre base. Ceci est le cas lorsque le type d'�dition est positionn� sur <u>liste d�roulante li�e</u>, <u>liste d�roulante � choix multiple li�e</u>, ou sur <u>statique li�e</u>: dans ce cas le syst�me va afficher la ou les valeur(s) d'une autre table (voir ci-dessous) en fonction de la valeur courante du champ qui repr�sentera la cl�. C'est dans ce cas que la syntaxe est la plus complexe....<br><br>
<u>La syntaxe de liaison est :</u><ul>
<li> si les valeurs sont situ�es dans une autre table de la m�me base, on rentrera:<br>
NOM_TABLE,NOM_CHAMP_CLE,NOM_CHAMP_AFF1,NOM_CHAMP_AFF2,...,NOM_CHAMP_AFFn<br><br>
� NOM_CHAMP_CLE est le champ de la table NOM_TABLE qui aura la valeur du champ courant (cl�)<br>
� NOM_CHAMP_AFF1...,NOM_CHAMP_AFFn seront les champs de la table NOM_TABLE affich�s � la suite les uns des autres, s�par�s par d�faut par un <?=$carsepldef?> (contenu de la variable globale $carsepldef)<br><br>
<u>Rem 1</u>: Apparu � la version 0.75: si on fait pr�c�der du caract�re &amp; le nom du champ, la valeur affich�e est d�duite du param�tre VALEURS de ce champ dans son enregistrement de d�finition<br>
<u>Rem 2</u>: il est possible de choisir le caract�re s�parateur s'affichant avant chaque champ NOM_CHAMP_AFFx avec x&gt;=2: pour cela on pourra pr�c�der le nom du champ du caract�re voulu puis ! ex: <i>&quot; -!rfp_mail&quot;</i>. Les caract�res , et ; sont bien entendus proscrits. Si rien n'est sp�cifi�, le caract�re par d�faut <?=carsepldef?>, d�finit dans la variable globale $carsepldef sera utilis�.<br>
<u>Rem 3</u>: il est possible de choisir le champ suivant lequel sera class� la liste: pour cela on mettra un caract @ devant sont nom ex: <i>@rfp_nom</i><br>
<u>Rem 4</u>: On peut depuis la version 0.894 d�finir un champ sp�cifiant la structure hi�rarchique de la table li�e: ce champ doit contenir le pid (parent id) de l'enregistrement parent ex: <i><b>@@</b>ufo_coufosup</i>; dans ce cas la liste d�roulante affiche la hi�rarchie de la table<br>
<u>Rem 5:</u> Ne pas mettre d'espaces avant ou apr�s les , et ; <br><br>

<u>Ex :</u> on ne souhaite afficher dans une liste que les personnes dont le code est situ� dans le champ RPP_CORES d'une table interm�diaire RESPONSABLE; dans la table de d�finition phpYourAdmin de cette table, le champ RPP_CORES poss�de l'enregistrement VALEURS suivant: <em>PERSONNES,per_coper,per_titre,per_prenom,per_nom</em>: <br>
la liste affich�e sera alors &quot;en clair&quot; titre prenom nom. Pour arriver � cet effet, on mettra les valeurs RESPONSABLE,RPP_CORES,&amp;RPP_CORES.
<br><br>
<li> si les valeurs sont situ�es dans une autre table d'une autre base, on rentrera<br>
NOM_BASE(,NOM_HOTE,USER,PASSWD)<b>;</b> NOM_TABLE,NOM_CHAMP_CLE,NOM_CHAMP_AFF1,NOM_CHAMP_AFF2,...,NOM_CHAMP_AFFn<br>
Sans commentaire, sinon que les arguments entre () sont optionnels, que le USER et PASSWD sont ceux du serveur MySql concern�, et bien not� le &quot;<b>;</b>&quot; qui s�pare la partie BASE, SERVEURS, etc. de la partie TABLE, CLE etc.
</ul>
<u>N.B. 2:</u> D'autres champs peuvent utiliser les param�tres de liste ou lien, notamment le <a href="#admafl">type d'affichage dans la liste</a>, et les <a href="#admtypfilt">filtres de requ�te</a>.<br><br> 
</blockquote>

<br><br>
<a name="admtypfilt" class="boldred11px">Type Filtre</a><br>
Ce contr�le, apparu � la version 0.65 permet d'afficher une grille de requ�te optionelle entre la liste des tables et la liste d'enregistrements.
Si au moins un champ poss�de une valeur diff�rente de <i>aucun</i>, la grille de requ�te est affich�e, sinon on passe directement � la liste des enregistrements.<br>
Les valeurs possibles sont :<ul>
<li> <u>Entr�e (like)</u> <small>(INPLIKE)</small> qui affiche une boite de saisie (contenant % par d�faut), accompag�e d'une case � cocher de n�gation de la condition. Le <i>where</i> au sens sql correspondant sera  NOM_CHAMP LIKE' VALEUR_SAISIE' ou NOT(LIKE NOM_CHAMP='VALEUR_SAISIE')
<li> <u>Liste d�r. Valeurs champ  </u><small>(LDC)</small> qui affiche une liste d�roulante � choix multiple, allant chercher toutes les valeurs diff�rentes du champ de la table correspondante, accompag�e d'une case � cocher de n�gation de la condition.
<li> <u>Liste d�r. Valeurs fix�es  </u><small>(LDF)</small> qui affiche une liste d�roulante � choix multiple reprenant les valeurs fix�es dans le champ VALEURS, accompag�e d'une case � cocher de n�gation de la condition. 
<li> <u>Liste d�r. Valeurs li�es  </u><small>(LDL)</small> qui affiche une liste d�roulante � choix multiple, reprenant des valeurs <i>p�ch�es</i> dans une autre table, avec la m�me syntaxe que les listes li�es
<br><br><u>N.B.:</u> pour toutes les listes, le <i>where</i> au sens SQL correspondant sera  NOM_CHAMP LIKE 'VALEUR_SELECTIONNEE_1' OR NOM_CHAMP LIKE 'VALEUR_SELECTIONNEE_2' etc ... ou NOT(NOM_CHAMP LIKE 'VALEUR_SELECTIONNEE_1' OR NOM_CHAMP LIKE 'VALEUR_SELECTIONNEE_2')<br><br> 
<li> <u>Date ...   </u><small>(DANT, DPOST, OU DATAP)</small> est uniquement adapt� aux champs de type date. Affiche une ou plusieurs boites de saisie permettant de rentrer la(les) date(s) limite(s) au format jj/mm/aa(aa), le tout toujours accompag� d'une case � cocher de n�gation de la condition. 
</ul>
<br>
Bien �videmment toutes les conditions rentr�es sur des champs d�iff�rents sont li�s par un <i>AND</i>
<p><br>
<a name="admafsel" class="boldred11px">Affichage s�lectionnable</a><br>
Ce contr�le, apparu � la version 0.70 permet d'afficher sur la grille de requ�te optionelle une case � cocher, permettant � l'utilsateur d'afficher le champ (colonne) dans la liste qui suit ou pas.
Si au moins un champ poss�de une valeur diff�rente de <i>aucun</i>, la grille de requ�te est affich�e, sinon on passe directement � la liste des enregistrements.<br>
Les valeurs possibles sont :<ul>
<li> <u>non  </u>l'affichage du champ n'est pas s�lectionnable par l'utilisateur
<li> <u>Oui, coch� par d�faut</u> <small>(OCD)</small>une case � cocher coch�e par defaut apparait en face du champ, celui-ci sera affich� par d�faut
<li> <u>Oui, non coch� par d�faut</u> <small>(ONCD)</small>une case � cocher non coch�e par defaut apparait en face du champ, mais celui-ci ne sera pas affich� par d�faut
</ul>
<br><br>
<a name="admttavmaj" class="boldred11px">Traitement avant Mise � jour</a><br>
Ceci permet de sp�cifier des MAJ automatiques sur les champs avant leur affichage pour �dition
Les valeurs possibles sont :
<ul>
     <li>Aucun (par d�faut)
     <li>Date du jour <small>(DJ) 
     </small><li>Date Jour si nulle avant <small>(DJSN) 
     </small><li>Date Jour +2 mois si nulle avant <small>(DJP2MSN) 
     </small><li>Code User MAJ <small>(US) 
     </small><li>Code User MAJ si nul avant <small>(USSN) 
     </small><li>Edition permise uniquement en cr�ation/copie (nouvel enregistrement) <small>(EDOOFT) </small>: ensuite, le controle d'�dition passe en statique/statique li�
</ul>
<br>
<br><br>
<a name="admttpdtmaj" class="boldred11px">Traitement pendant Mise � jour</a><br>
Ceci permet de sp�cifier des MAJ automatiques sur les champs pendant leur affichage pour �dition <br>
Elle repose sur l'appel d'une fonction JavaScript, qui devra etre d�finie ailleurs (dans un commentaire par exemple)<br>
Supposons que l'on veuille appeler la fonction <i>Verif();</i> lors d'un �v�nement de type <i>onChange</i> sur ce champ, on entrera alors dans cette case la valeur <i><b>onChange:Verif();</b></i> <br>
Le code Javascript g�n�r� dans l'entr�e de formulaire sera <i>onChange=&quot;Verif();&quot;</i><br>
<u>NB</u>: cette possibilit� ne fonctionne pour l'instant que sur des boites textes manuelles (pas auto)
<br><br>
<a name="admcomment" class="boldred11px">Commentaire</a><br>
Ce champ est un texte libre, qui apparaitra sous chaque lib�ll� de champ en petits caract�res dans la page d'�dition de l'enregistrement. On pourra ici mettre des conseils � l'utilisateur.<br>
On pourra aussi dans ce champ ins�rer les scripts (entre les balises standards HTML) appel�s par les traitements pendant mise � jour.<br><br>
<div align="center"><a href="#haut"><img src="haut.gif" width="70" height="11" border="0" alt="Sommaire"></a><br>
</div><hr width="70%" size="1">
<a name="progobj"></a><span class="chapitrered12px">Param�tres, programmation, fonctions partag�es</span>
<BR><span class="boldred11px">Passage des variables et de param�tres d�environnement</span>
<BR>Certaines variables peuvent ou doivent �tre d�finies pour permettre d�acc�der � une page PYA directement depuis une application.
<BR>
<BR>lc_CO_USMAJ : code du user effectuant la mise � jour (obligatoire, sinon l�appli ram�ne automatiquement en page d�accueil)
<BR>
<BR>Toutes les variables qui suivent, dont le nom commence par lc_ sont ensuite m�moris�e par l�appli en tant que variables de session dont le nom est (presque�) identique et commence par ss_ (par ex lc_parenv[ro] devient m�moris� en ss_parenv[ro])
<BR>
<BR><u>lc_parenv[MySqlUser] , c_parenv[MySqlPasswd]:</u> code user et mot de passe  pour acc�s au serveur MySql (si laiss� vide prend les valeurs par d�faut d�finies dans infos.php)
<BR><u>lc_DBName, lc_NMTABLE :</u> noms de la base et table � traiter
<BR><u>lc_parenv[ro], lc_parenv[blair]:</u> variables d�environnement qui si elles sont positionn�es � 1 permettent respectivement le fonctionnement en consultation seule et le non-affichage des liens d�administration (ainsi que le retour vers la liste des bases)
<BR><u>lc_parenv[noinfos] :</u> si positionn�e � true, permet le non-affichage des infos sur le serveur et des liens vers l�aide
<BR><u>lc_reqcust:</u> valeur de la la requ�te custom utilisateur; <u>lc_parenv[lbreqcust] :</u> Libell� affich� en haut des pages lorsque requ�te sp�cifique
<BR><b><u>Gestion des adresses de retour</u></b>
<BR>lc_adrr[xxx.php] :</u>tableau des adresses de retour : permet de sp�cifier l�adresse point�e par le bouton retour de chaque page de PYA. 
<BR>Si elle est sp�cifi�e � 0, le bouton retour n�est pas affich� (utilisation des pages dans des frames)
<BR>
<BR>Un exemple d�url, pointant vers la page de requ�te req_table.php, base MA_BASE, table MA_TABLE, pas d�affichage des infos, tous les user et mot de passe = hn
<BR>
<BR>href="../../admin/phpYourAdmin/req_table.php?lc_CO_USMAJ=hn&lc_adrr[list_table.php]=mapage.php&lc_adrr[req_table.php]=mapage.php&lc_DBName=MA_BASE&lc_NM_TABLE=MA_TABLE &lc_parenv[noinfos]=true&lc_parenv[MySqlUser]=hn&lc_parenv[MySqlPasswd]=hn"
<BR>
<BR><span class="boldred11px">Fonctions, Programmation avec les objets de phpYourAdmin</span>
<BR>Une grande partie de l�application est bas�e sur l�utilisation d�objets et de fonctions, d�finies le fichier <b>fonctions.php</b>, plac� dans le r�pertoire php_inc � la racine des serveurs.
<BR>Il suffira de consulter ce fichier pour conna�tre l�utilisation des fonctions et objets.
<BR>
<BR>Il suffit d�ins�rer dans une page php le code include("fonctions.php") ; sans sp�cifier de chemin (le serveur est param�tr� pour le trouver automatiquement) pour b�n�ficier de toutes les fonctions et objets.
<BR>
<BR>L�objet PYA se nomme PYAobj
<BR>
<BR>Dans l'exmple qui suit, on suppose que la table de description existe et est correctement initialis�e
<BR>Exemple de code php :
<BR>
<BR>// initialisations
<BR>$CIL=new PYAobj(); // instanciation de l�objet
<BR>$CIL->NmBase="MA_BASE"; // initialisation des propri�t�s de base
<BR>$CIL->NmTable="personne";
<BR>$CIL->NmChamp="per_affectation";
<BR>$CIL->InitPO(); // m�thode d'initialisation des autres propri�t�s � partir du contenu de la table de description
<BR>// utilisation
<BR>// il faut r�cup�rer la valeur du champ
<BR>$CIL->ValChp=$Valeur; 
<BR>echo ($CIL->Libelle);
<BR>$CIL->EchoEditAll(); // m�thode d'affichage en �dition (ici liste d�roulante)
<BR>
<BR>
<div align="center"><a href="#haut"><img src="haut.gif" width="70" height="11" border="0" alt="Sommaire"></a><br>
</div><hr width="70%" size="1">
<a name="clog"></a><span class="chapitrered12px">Evolution des versions...<br></span>
Version courante  <b><? echo $VerNum; ?></b>
<blockquote>
<u>0.894, v 0.9 pre2 (20/05/05):</u><br>
� LD avec hi�rarchie
<u>0.892, v 0.9 pre2 (01/05/05):</u><br>
� Possibilit� d'administrer des donn�es de tables virtuelles: dans la table DESC_TABLE, on rajoute des enregistrements qui ne correspondent � aucune table (ou champ existant): dans ce cas il faut que le nom de la pseudo table contiennent la chaine '_VTB_'
<u>0.891, v 0.9 pre2 (01/05/05):</u><br>
� compatibilit� register_globals=Off
<u>0.890, v 0.9 pre1 (15/10/04):</u><br>
� Multilingue: fichiers de messages
<u>0.865 (23/09/03):</u><br>
� Rajout d'une fonctonnalit� permettant d'envoyer un mail � tous les mails d'une page de liste<br>
<u>0.864 (23/09/04):</u><br>
� Correction bug sur for�age liste en cases � cocher (fichier fonctions.php)<br>
<u>0.863 (23/09/03):</u><br>
� Correction bug sur liste d�roulantes et requetes: remplacementde la fonction array_merge dans ke fichier PYAObjdef.inc (qui reconstruit les indices des tableaux de hachaage) par l'op�rateur +<br>
<u>0.862 (28/02/03):</u><br>
� Nombreuses am�liorations et corrections, surtout dans fonctions.php<br>
� Scindage de fonctions.php<br>
� correction pb upload<br>

<u>0.861 (3/12/02):</u><br>
� Fichier include s�par� pour les infos de connexion MySql<br>
� Possibilit� de d�sactiver les suppressions d'enregistrement<br>
<u>0.860 (29/11/02):</u><br>
� Gestion d'une requete utilisateur, permettant l'affichage d'�tats sp�cifiques t�l�chargeables.<br>
� Ajout de la fonction InitPOReq($req,$Base=""), permettant l'initialisation d'un tableau d'objets PYA fonction d'une requ�t SQL<br>
<u>0.855 (27/11/02):</u><br>
� Modif fonction DispLD, avec argument suppl�mentaire optionnel pour for�age aff en case � cocher boutons radio:<br>
&nbsp;&nbsp;DispLD($tbval,$nmC,$Mult="no",$Fccr="")<br>
<u>NB</U>: Si la propri�t� contient la chaine br, un retour de ligne est ins�r� entre chaque valeur, sinon elles sont s�par�es par des espaces<br>
� Rajout d'une propri�t� Fccr pour l'objet PYA, permettant le for�age d'affichage en case � cocher ou radio. <br>
<u>0.854 (20/11/02):</u><br>
� Creation m�thode pour mise � jour d'un champ, avec gestion des fichiers joints<br>
<u>0.853 (19/11/02):</u><br>
� Am�lioration page de re-g�n�ration de la table de description. Options de listage des caract�ristiques des champs.<br>
� Ajout d'un fichier d'include de scripts JS.<br>
<u>0.852 (18/11/02):</u><br>
� Compatibilt� filtres LD avec set ET pseudo-set<br>
<u>0.851 (13/11/02):</u><br>
� R�vision gestion des types "pseudo-set"<br>
<u>0.850 (7/11/02):</u><br>s
� MAJ du ficghier d'aide<br>
� passage de lc_parenv[blair] en var de session<br>
 <u>0.849 (5/11/02):</u><br>
� Possibilit�s de d�sactiver l'entourage des noms de champs par des ` dans le fichier infos.php en changeant la var $CSpIC (le ` ne fonctionne pas avec de vieilles versions de MySql)
<br>
<u>0.848 (17/10/02):</u><br>
� gestion des user et passwd mysql sur la page d'index, ou par passage des param�tres lc_parenv[MySqlUser] et lc_parenv[MySqlPasswd] <br>
� Am�lioration de la visu sd lc_parenv[noinfos]=true<br>
<u>0.846 (16/10/02):</u><br>
� Gestion des noms de tables prot�g�s par des ` pour des tables ayant des noms r�serv�s (pas les noms de champs)<br>
� Param�tre ss_parenv[noinfos] permettant de ne pas afficher les infos et le pied de page<br>
<u>0.845 (2/10/02):</u><br>
� Traitement pendant Mise � Jour dispo, mais uniquement sur les boites textes manuelles<br>
� possiblit� de passer un 3�me param�tre suppl�mentaire � la fonction msq servant � localiser son appel pour d�bogage<br>
<u>0.844 (18/09/02):</u><br>
� chemin des fichiers joints param�trable (ou automatique)<br>
� d�finition du type de champ en automatique<br>
<u>0.843 (17/09/02):</u><br>
� effacement automatique en super-admin des enregistrements des tables effac�s ds la table de description<br>
� gestion cl�s primaires multiples, sinon prend ts les champs en cl�<br>
<u>0.841 (12/09/02):</u><br>
� divers MAJ cosm�tiques (tableaux color�s) et correctiosns de petits bug<br>

<u>0.840 (3/09/02):</u><br>
� gestion des adresses de retour par tableau de hachage pour appel des pages par programme externe <br>
� applet javascript de confirmation de suppression d'enregistrement dans la liste<br>
� ajout d'un bouton annuler les changements dans la page d'�dition avec confirmation<br>
� gestion du param�tre d'environnement readonly pour consultation uniquement<br>

<br>
<u>0.830 (25/07/02):</u><br>
� nouvelle m�thode de l'initialisation de l'objet PYAObj<br>
� modification des noms de propri�t�s (NmChp/NmChamp qui ne correspondaient 
    pas toujours)<br>
� page d'exemple d'utilisation de l'objet <a href="test_objPYA.php" target="_blank">test_objPYA.php</a><br>
<br>
<u>0.826 (10/07/02):</u><br>
� double click sur une liste effectue un submit<br>
� affichage du where en ent�te de l'�tat<br>
� gestion des singuliers/pluriels<br>
<br>
<u>0.825 (03/07/02):</u><br>
� possibilit� de mettre � jour la table de description lors d'ajout ou de suppression de champs<br>
<br>
<u>0.82 (02/07/02):</u><br>
� un seul objet PYAobj pour tous les contr�les<br>
� Correction bug quand pas de condition choisie dans la requ�te<br><br>
<u>0.81 (28/6/02):</u><br>
� Liste statiques en cl�s:valeurs<br>
� En creation de table de desc, case � cocher permettant de g�n�rer ou pas les mise � jour fonction des noms de champs<br><br>
<u>0.8 (25/6/02):</u><br>
� Correction bug sur nbre de ligne par page qui ne fonctionnait pas<br>
� rajout du traitement avant MAJ EDOOFT, Edit Only On First Time<br>
� Gestion des boutons retour sommaire general et/ou grille de requete<br>
� passage en objets des �tats, et MAJ de l'extraction<br><br>
<u>0.765 (13/6/02):</u><br>
� param�trage nb lignes / nb colonnes textarea dans champs valeurs<br>
� rajout du traitement avant MAJ USSN, code user si nul avant, sinon change rien<br><br>
<u>0.76 (12/6/02):</u><br>
� pr�paration de l'objet d'�dition � la possibilit� d'un champ int�grant des expressions �valu�es (voir propri�t� Val2 dans l'objet EchoEdit)<br>
� possibilit� de lier les caract�res d'�dition des tables dans le champ valeur<br><br>
<u>0.75 (6/6/02):</u><br>
� controles d'�dition en objets<br>
� classement possible sur 1 champ dans les listes li�es (pr�c�d� d'un @), choix du caract�re s�parateur (pr�c�d� d'un !)<br>
<u>0.71 (29/5/02):</u><br>
� options de requ�tes �tendues: <br>
-  choix sur les dates<br> 
-  liste d�roulantes (toutes � choix multiples) sur valeurs effectivement contenues dans le champ, valeurs d'une liste fixe, ou valeur li�e � une autre table<br>
� possibilit� d'affichage des champs dans liste (colonnes) s�lectionnable par l'utilisateur<br>
� le choix des colonnes reste affich� dans le fichier t�l�charg�<br><br>
<u>0.69 (28/5/02):</u><br>
� gestion des type d'affichage liste d�roulante � choix multiples, et liste d�roulante � choix multiples li�e<br>
� javascript (merdique car fait le soir) qui change le type d'affichage dans la liste en fonction du type d'affichage d'�dition<br>
� possibilit� de t�l�chargement au format tsv (Tabulation Separated Values) accessible en bas de la liste, compatible, entre autres, Excel<br><br> 
<u>0.67 (27/5/02):</u><br>
� r�vision du programme de cr�ation de table de description (<a href="CREATE_DESC_TABLES.php">CREATE_DESC_TABLES.php</a>): possiblit� de choisir 1 ou plusieurs tables dans une m�me base pour la visualisation/g�n�ration<br> 
� ajout de la liste d�roulante � choix unique dans les possibilit�s de filtre de requ�te<br><br>

<u>0.65 (23/5/02):</u><br>
� grille de requ�te entre liste des tables et des enregistrements<br>
� extension de ce fichier d'aide ...<br><br>

<u>0.60 (22/5/02):</u><br>
� disponibilit� du pr�sent fichier d'aide ....<br>
� possibilit� d'aller chercher des valeurs de liste dans d'autres bases voie d'autres serveurs<br>
� affichage dans les listes x par x<br>
� tri dans les listes sur 3 champs<br>
<br><u>0.57 (29/4/02):</u><br>
� affichage en clair des libell�s de table<br><br>
<u>0.56 (26/4/02):</u><br>
� gestion des valeurs par d�faut et null/not null stock�es dans les definitions mysql (�dition par phpMyAdmin)<br>
� utilisation avanc�e de la fonction SHOW FIELDS FROM .... dans l'utilitaire d'�dition <br><br>
<u>0.55:</u><br>
� le type de champ m�moris� dans la table desc_table n'est plus utilis� dans l'affichage auto, c'est le &quot;vrai&quot; type du champ qui est utilis�<br> 
� gestion des types set en automatique <br>
� gestion des tailles d'input automatique en fonction des tailles de char et de varchar<br>
</blockquote>
<div align="center"><a href="aide.php#haut"><img src="haut.gif" width="70" height="11" border="0" alt="Sommaire"></a><br>
</div><hr width="70%" size="1">
<p><a href="#" onclick="self.close();"><img src="retour.gif" width="70" height="11" border="0" alt="fermer cette fen�tre"></a>
</p>
</body>
</html>
