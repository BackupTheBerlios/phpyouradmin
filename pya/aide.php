<? require("infos.php");
$title="Aide de phpYourAdmin";
include ("header.php"); ?>
<meta name="generator" content="Namo WebEditor v5.0(Trial)">

<span class="titrered20px"><a name="haut"></a>SOMMAIRE DE L'AIDE phpYourAdmin</span><br><br>
<span class="chapitrered12px">&#149; <a href="#intro">Généralités...</a><br>
&#149; <a href="#util">Utilisation</a><br>
&#149; <a href="#admin">Administration</a><br>
&#149; <a href="#progobj">Programmation à l'aide des objets</a><br>
&#149; <a href="#clog">Evolution des versions...</a></span><br>
<hr width="70%" size="1">
<a name="intro"></a><span class="chapitrered12px">Généralités<br></span>
<blockquote><b>phpYourAdmin</b> est un utilitaire permettant l'édition paramétrable du <u>contenu</u> de bases de données mysql.<br>
Il est complémentaire de phpMyAdmin, qui lui est plutôt destiné à l'édition de la <u>structure</u> des bases (type de champs, valeurs par défaut, index, etc ...)<br>
Le fonctionnement <b>phpYourAdmin</b> repose sur une table spéciale, nommée <? echo $TBDname ?>, dont le nom est défini dans la variable $TBDname du fichier infos.php 
    ou fonctions.php, créée dans chaque base pouvant être administrée. La création de cette table peut heureusement être automatiquement faite par le programme.
Cette table contient un enregistrement par champ et par table de la base administrée 
    (plus un par table contenant des renseignements propre à la table), contenant 
    les renseignements d'édition pour  chaque champ de la base dans laquelle elle est créée.
    
    <p><b>phpYourAdmin</b> possède des écrans spécifiques de génération et 
     d'édition des enregistrements 
    de cette table.</p>
    <p><a href="aide.php#haut" class="fxbutton" title="to top"> ^ ^ </a><br>
    </p>
</blockquote>
<hr width="70%" size="1">
<a name="util"></a><span class="chapitrered12px">Utilisation<br></span>
<blockquote>
<a name="utilpre" class="boldred11px">Préambule</a> 
    <br>Notez les noms des fichiers indiqués pour chaque écran: ils vous permettront 
    d'effectuer un lien à partir d'une de vos pages directement vers certaines 
    pages de PYA, et de gérer les adresses de retour, à condition toutefois 
    de passer certaines,variables par l'URL (sinon l'utilisateur se retrouvera 
    à la page d'accueil...)
    <p><a name="utilacc" class="boldred11px">Ecran d'accueil</a><a class="boldred11px"> 
    (index.php)</a><br>

Sont demandés: <br>
 - user et mot de passe pour l'accès au serveur de base de données MySql<br>
 - identifiant de modification d'enrgistrement: cet identifiant 
    sera utilisé pour le renseignement automatique des champs de type user MAJ
    </p>
<p><a name="utilacc" class="boldred11px">Liste des bases du serveur</a><a class="boldred11px"> 
    (LIST_BASES.php)</a><br>

Cet écran liste les bases accessibles sur le serveur courant. Un clic sur une 
    base amène sur la page listant les tables de la base (voir ci-après)<br>En 
    bas de la page, existe un lien (optionel en fonctionde la configuration)&nbsp;vers 
    les pages d'administration référencé <SPAN class="legendes9x"><i>Cliquez <u><font color="#0033CC">ici</font></u> pour 
appeler l'utilitaire de génération de table de description (DANGEREUX !)</i></SPAN></p>
    <p><a name="utilLTBL" class="boldred11px">Liste des tables&nbsp;de la base 
    courante</a><a class="boldred11px"> (LIST_TABLES.php)</a><br>

Cet écran liste les tables de la base courante. En face de chaque table, il 
    existe un bouton  <span class="FdR">+</span> permettant d'ajouter directement un enrgistrement 
    à la table. </p>
    <p> En bas de la page, 
    existe un lien optionel vers les pages d'administration référencé <SPAN class="legendes9px"><i>Cliquez <font color="#0033CC"><u>ICI 
    </u></font>pour changer 
les propriétés d'EDITION des tables .....(Réservé aux initiés ...) </i></SPAN></p>
    <p><a name="utilreq" class="boldred11px">Ecran de requête optionel</a><a class="boldred11px"> (req_table.php)</a><br>

Cet écran n'apparait que si au moins un filtre de requête ou un affichage de 
    champ optionnel a été paramétré pour au moins un champ de la table (voir 
    les rubriques <a href="#admtypfilt">Type de filtre</a> ou <a href="#admafsel">affichage 
    sélectionable</a> dans la partie administration.</p>
    <p>Les controles de requête requièrent les précisions suivantes:<br>- dans 
    le cas d'une boite d'entrée, pour effectuer une recherche sur le début d'un 
    champ entrez DEBU%, sur la fin %FIN, ou sur une partie&nbsp;%ART% <br>- 
    toutes les listes déroulantes sont multiples: sélection sur plusieurs valeurs 
    possibles (fonction logique OU) en cliquant et&nbsp;en maintenant la touche 
    Ctrl enfoncée<br>- dans le cas des cases à cocher (peux de valeurrs disponibles), 
    penser à décocher la case %<br>- les dates doivent être entrées sous la 
    forme 05/11/2002<br><br>La case à cocher<b> Négation </b>effectue un non 
    logique de la condition spécifiée&nbsp;au dessus<br>&nbsp;<br>Toutes les 
    conditions entrées sur des champs différents sont liées par la fonction 
    logique ET</p>
    <p><a name="utilLENR" class="boldred11px">Liste des enregistrements (list_table.php)</a><br>

Cet écran liste les enregistrements répondant aux critères optionnels donnés 
    ci-avant. A condition que le paramètre $ss_parenv[ro] soit différent de 
    1, il est possible d'éditer, copier ou supprimer les enregistrements. Sinon, 
    il n'est possible que de les visualiser dans une popup.<br>Les enregistrements 
    sont listés <?=$nbligpp_def?> par <?=$nbligpp_def?>, ce nombre étant spécifié dans la variable $nbligpp_def dans le fichier fonctions.php<br><br>Il 
    est possible de trier les enregistrements suivant un champ en cliquant sur 
    les flèches de classement <span class="FdR"><img src="flasc.gif" width="15" height="15" border="0"> <img src="fldesc.gif" width="15" height="15" border="0"></span> . <br>On peut avoir 3 classements  par ordre 
    de priorité, indiqués par des n° dans les flèches; il faut commencer par l'ordre de priorité le plus bas.<br>Exemple: 
    on veut trier par code postal et en cas d'égalitéde code postal par nom -&gt; on clique 
    d'abord sur la flèche dans la colonne&nbsp;nom, puis sur celle dans la colonne 
    code postal.<br>R<u>em:</u> pour les champs liés, le classement s'effectue 
    (malheureusement)&nbsp;non pas suivant la valeur affichée mais suivant la 
    valeur stockée, ce qui peut peut donc donner des résultats assez bizarres.<br><br><img src="filesave.png"> 
    : ceci va permetre de télécharger un fichier contenant tous les enregistremnts 
    répondant aux critères spécifiés (et non seulement les  <?=$nbligpp_def?> affichés) 
    sous forme d'un fichier texte ou les champs sont séparés par des caractères 
    tabulation (format tsv). Ce type de fichier peut s'ouvrir direcmenet avec 
    Excel.&nbsp;<br></p>
    <p><a name="utilEDNR" class="boldred11px">Edition/copie d'un enregistrements (edit_table.php)</a><br>

Cet écran permet l'édition ou copie d'un enregistrement.<br>Le bouton <input type="button" class="fxbutton" value="valider">
    valide les changements<br>Le bouton <img src="fermer.gif" width="70" height="11" border="0"> 
    ferme la fenêtre SANS&nbsp;VALIDER LES CHANGEMENTS<br>Le bouton <img src="annuler.gif" width="70" height="13" border="0">&nbsp;ne 
    ferme 
    pas la fenêtre, mais réinitialise les valeurs des champs aux valeurs par 
    défaut (nouvel enregistrement) ou initiales avant changement, après un message 
    de confirmation.<br>&nbsp;<br>Pour info technique, los de la validation, 
    la page amact_table.php est appelée par le formulaire.</p>
</blockquote>
<div align="center"><a href="#haut" class="fxbutton" title="to top"> ^ ^ </a><br>
</div><hr width="70%" size="1">
<a name="admin" class="chapitrered12px">Administration.<br></a>
<blockquote>
<a name="admgen" class="boldred11px">Généralités</a><br>
Les utilitaires de cette partie sont accessibles <br>
- en bas de la liste des bases du serveur, dans le lien référencé
<SPAN class="legendes9x"><i>Cliquez <u><font color="#0033CC">ici</font></u> pour 
appeler l'utilitaire de génération de table de description (DANGEREUX !)</i></SPAN> : 
    cet&nbsp;utilitaire va permettre de générer la table de description d'une 
    base lorsque celle-ci n'a jamais été créé, ou la mettre à jour lorsque la 
    structure de la table a été changée<br>- 
    en bas de la liste des tables de la base courante, dans le lien référencé
 <SPAN class="legendes9px"><i>Cliquez <font color="#0033CC"><u>ICI 
    </u></font>pour changer 
les propriétés d'EDITION des tables .....(Réservé aux initiés ...) : </i></SPAN>&nbsp;cet 
    utilitaire permet l'édition individuelle des propriétés d'édition 
    de chaque champ ou table<br>Ces liens peuvent-être désactivés en positionnant la variable $lc_parenv[blair] à 1 (pour les blaireaux ;-)<br>
    <p>
    <a name="admdesct" class="boldred11px">(re)génération de table de description</a><br>Cliquer en bas de la liste des tables de la base courante, dans le lien référencé
 <SPAN class="legendes9px"><i>Cliquez <font color="#0033CC"><u>ICI 
    </u></font>pour changer 
les propriétés d'EDITION des tables .....(Réservé aux initiés ...)</i></SPAN><br>Un 
    écran nommé Super Administration de phpYourAdmin apparait, comportant 3 
    étapes:</p>
    <ol type="1">
        <li>Sélection de la base à traiter</li>
        <li>Sélection <br>
        - de ou des tables à traiter (multiple par Ctrl+clic dans la liste);<br>
        - opération: consultation de la table de description, MAJ, ou (re)génération complète </li>
        <li>affichage du résultat</li>
    </ol>
    A noter<br>
    - ces opérations sont très risquées, notamment sur des bases exsitantes (faire un backup de la table de description en cas de doute)<br>
    - la (re)génération ne devrait être appelée que lors de la création<br>
    - la MAJ est à effectuer obligatoirement lorsque des champs ont été ajoutés, renommés ou supprimés. Elle est inutile s'il y a eu simplement changement de type.<br>  
    <p><a name="admbas" class="boldred11px">Edition des propriétés d'édition d'une base</a><br>Cliquer en bas de la liste des tables de la base courante, dans le lien référencé
 <SPAN class="legendes9px"><i>Cliquez <font color="#0033CC"><u>ICI 
    </u></font>pour changer 
les propriétés d'EDITION des tables .....(Réservé aux initiés ...)</i></SPAN><br>On &quot;retombe&quot; à nouveau sur une liste des tables 
    coloriée en orange pour bien signaler le mode d'édition, mais cette fois un clic amène sur un tableau permettant d'éditer les propriétés d'édition de la table et non ses valeurs<br>
    </p>
<p>Les colonnes sont détaillées ci-après</p>
    <p><a name="admlib" class="boldred11px">Nom du champ- Libellé à afficher - Propriétés:</a><br>
    Ici l'utilisateur ne peut entrer que le Libellé du champ qui sera ensuite affiché. 
    Lui sont indiqué le véritable nom du champ ainsi que ses caractéristiques 
    (ne pouvant être changées qu'avec phpMyAdmin)<br><b><u>Attention</u></b>: 
    il peut exister des applications, qui, si elles sont programmées à l'aide 
    des objets PYA, se servent de ce libellé (DRH par exemple). Changer le libellé, 
    comme tout autre caractéristique,&nbsp;peut donc avoir des répercussions 
    extérieures à la seule utilisation de PYA<br>&nbsp;</p>
    <p>

<a name="admafl" class="boldred11px">Ordre aff. Liste/ Type aff. Liste</a><br>
Lorqu'on clique sur une table dans la liste des tables, on a après l'affichage 
    d'une grille de requête optionnelle, une liste des enregistrements (affichées x par x), avec des liens en entête de chaque ligne permettant d'effacer, éditer ou copier chaque enregistrement.
Ces deux champs permettent de choisir <br>
&#149; l'ordre des colonnes du tableau (la valeur indiquée sera triée de façon alphanumérique et non numérique)<br>
&#149; et le type d'affichage. <br>
Le type ne demande pas de commentaires, sauf pour les valeurs:</p>
<ul>
<li>tronqué: dans ce cas le champ est tronqué à <? echo $nbcarmxlist ?>caractères, ce nombre pouvant être changé en modifiant la variables $nbcarmxlist dans le fichier commun info.php
<li>lié: on affiche dans ce cas les valeurs liées statiquement ou à une autre table, définie dans la <a href="#admval">colonne valeurs 
</a></ul><br><br>

<a name="admtyped" class="boldred11px">Type édition</a><br>
Ceci concerne le type d'édition dans l'écran d'édition d'un enregistrement.
Les valeurs possibles<ul>
<LI>Caché : le champ n'apparait pas</li>
<LI>Boite Texte: Sans commentaire, on peut spécifier dans la zone valeurs la 
        longueur affichée de la boite et la longueur max </li>
<LI>Text Area: Sans commentaire, sinon que les dimensions peuvent etre spécifiées dans 
        la   <a href="aide.php#admval">colonne valeurs  </a>sous la forme nb_ligne,nb_colonnes</li>
<LI>Text Area avec RTE, cad avec éditeur de texte enrichi; les dimensions peuvent etre spécifiées dans 
        la   <a href="aide.php#admval">colonne valeurs  </a>sous la forme hauteur en px,largeur en px</li>
<LI>Auto: adapté en fonction du type de champ, si c'est un enum ou un set, on a une liste par exemple. Cela prend aussi en compte les NULL autorisés ou pas</li>
<LI>Liste Deroulante, Liste  Deroul Liée, Liste Deroulante à choix multiples, Liste  Deroul Liée à choix multiples: les valeurs statiques 
        affichées ou les caract. du lien vers une autre table/base sont à rentrer dans la <a href="#admval">colonne valeurs</a> ci-après</li>
<LI>Statique: la valeur du champ apparait mais n'est pas éditable par l'utilisateur</li>
<LI>Statique Liée: valeur &quot;pêchée&quot; dans une autre table, non éditable, la table concernée est à rentrer dans la <a href="#admval">colonne valeur</a> ci-après</li>
<LI>Fichier-Photo: en saisie on touve un bouton parcourir, puis en visu une 
        image si c'est un fichier de type image (en fonction de l'extension),ou 
        un lien vers un fichier si sinon. <br>
<span class="normalred11px">Attention</span> <br>
&#149; la taille des fichiers est limitée à <? echo $MaxFSize ?> octets, ce nombre pouvant être changé en modifiant la variables $MaxFSize dans le fichier commun info.php<br>
&#149; il est nécéssaire de créer un répertoire, accessible en écriture par le user sous lequel tourne le serveur apache (voir /etc/httpd/conf/httpd.conf), ce répertoire est 
        : <br>&#149; par défaut NOMBASE_NOMTABLE_NOM_CHAMP (attention au minuscules/majuscules).<br> 
         &#149; ou à saisir contenu  dans la <a href="aide.php#admval">colonne valeurs</a> sous 
        forme d'un chemin, terminé par /<br>Il est conseillé de créer  un lien symbolique 
        pointant vers un dossier contenu dans le dossier des autres fichiers 
        de l'applications en cours s'il en existe un ....<br>
Si vous respectez ces consignes, la gestion est totalement transparente, et l'effacement des fichiers liés automatiques
</li>
</ul>
<br>
<a name="admval" class="boldred11px">Valeurs</a><br>
<u>NB:</u>: ce champ a DE LOIN la syntaxe la plus COMPLEXE notamment dans le cas des liaisons inter-champs !. Prière de lire en détail (et au calme ;-) les explications qui suivent !<br><br>
Les valeurs sont notamment utilisées dans:<ul>
<li> dans le type d'édition Normal lorsque celui-ci est sur Normal, on rentrera ici la taille de la boite de saisie, et le nombre max de caractères du champ INPUT , sinon les valeurs par défaut du navigateur sont utilisées
<li> dans le type d'édition TEXTAREA, on rentre ici les dimensions de la boite, sinon les valeurs $nbrtxa (=<?=$nbrtxa?> et $nbctxa (=<?=$nbctxa?>) spécifiées dans le fichier infos.php 
        ou fonctions.php sont utilisées pour les dimensions par défaut
<li> dans les types d'édition listes
<li> le type d'affichage lié
<li> les filtres de requête de type Liste déroulante valeurs fixes ou valeurs liées<br>
</ul>
Les valeurs de liste sont de 3 grands types:<br>
&#149; une liste de valeurs fixes, séparées par des , : ces valeurs sont affichées tel quel dans les types d'édition liste déroulante et liste déroulante multiples<br>
&#149; une liste de paires de clés-valeurs fixes, (clé1:valeur1,clé2:valeur2 ...) : les valeurs sont affichées, mais ce sont les clés qui sont en fait réellement stockées dans le champ<br>
&#149; une liste de valeurs <i>pêchées</i> dans une autre table de la base ou même d'une autre base. Ceci est le cas lorsque le type d'édition est positionné sur <u>liste déroulante liée</u>, <u>liste déroulante à choix multiple liée</u>, ou sur <u>statique liée</u>: dans ce cas le système va afficher la ou les valeur(s) d'une autre table (voir ci-dessous) en fonction de la valeur courante du champ qui représentera la clé. C'est dans ce cas que la syntaxe est la plus complexe....<br><br>
<u>La syntaxe de liaison est :</u><ul>
<li> si les valeurs sont situées dans une autre table de la même base, on rentrera:<br>
NOM_TABLE,NOM_CHAMP_CLE,NOM_CHAMP_AFF1,NOM_CHAMP_AFF2,...,NOM_CHAMP_AFFn<br><br>
&#149; NOM_CHAMP_CLE est le champ de la table NOM_TABLE qui aura la valeur du champ courant (clé)<br>
&#149; NOM_CHAMP_AFF1...,NOM_CHAMP_AFFn seront les champs de la table NOM_TABLE affichés à la suite les uns des autres, séparés par défaut par un <?=$carsepldef?> (contenu de la variable globale $carsepldef)<br><br>
<u>Rem 1</u>: Apparu à la version 0.75: si on fait précéder du caractère &amp; le nom du champ, la valeur affichée est déduite du paramètre VALEURS de ce champ dans son enregistrement de définition<br>
<u>Rem 2</u>: il est possible de choisir le caractère séparateur s'affichant avant chaque champ NOM_CHAMP_AFFx avec x&gt;=2: pour cela on pourra précéder le nom du champ du caractère voulu puis ! ex: <i>&quot; -!rfp_mail&quot;</i>. Les caractères , et ; sont bien entendus proscrits. Si rien n'est spécifié, le caractère par défaut <?=carsepldef?>, définit dans la variable globale $carsepldef sera utilisé.<br>
<u>Rem 3</u>: il est possible de choisir le champ suivant lequel sera classé la liste: pour cela on mettra un caract @ devant sont nom ex: <i>@rfp_nom</i><br>
 si l'on fait <strong>précéder le nom du champ de ~@</strong>, on classera par ordre <u>inverse</u><br/>
<u>Rem 4</u>: On peut depuis la version 0.894 définir un champ spécifiant la structure hiérarchique de la table liée: ce champ doit contenir le pid (parent id) de l'enregistrement parent ex: <i><b>@@</b>ufo_coufosup</i>; dans ce cas la liste déroulante affiche la hiérarchie de la table<br>
<u>Rem 5:</u> Ne pas mettre d'espaces avant ou après les , et ; <br><br>

<u>Ex :</u> on ne souhaite afficher dans une liste que les personnes dont le code est situé dans le champ RPP_CORES d'une table intermédiaire RESPONSABLE; dans la table de définition phpYourAdmin de cette table, le champ RPP_CORES possède l'enregistrement VALEURS suivant: <em>PERSONNES,per_coper,per_titre,per_prenom,per_nom</em>: <br>
la liste affichée sera alors &quot;en clair&quot; titre prenom nom. Pour arriver à cet effet, on mettra les valeurs RESPONSABLE,RPP_CORES,&amp;RPP_CORES.
<br><br>
<li> si les valeurs sont situées dans une autre table d'une autre base, on rentrera<br>
NOM_BASE(,NOM_HOTE,USER,PASSWD)<b>;</b> NOM_TABLE,NOM_CHAMP_CLE,NOM_CHAMP_AFF1,NOM_CHAMP_AFF2,...,NOM_CHAMP_AFFn<br>
Sans commentaire, sinon que les arguments entre () sont optionnels, que le USER et PASSWD sont ceux du serveur MySql concerné, et bien noté le &quot;<b>;</b>&quot; qui sépare la partie BASE, SERVEURS, etc. de la partie TABLE, CLE etc.
</ul>
<u>N.B. 2:</u> D'autres champs peuvent utiliser les paramètres de liste ou lien, notamment le <a href="#admafl">type d'affichage dans la liste</a>, et les <a href="#admtypfilt">filtres de requête</a>.<br><br> 
</blockquote>

<br><br>
<a name="admtypfilt" class="boldred11px">Type Filtre</a><br>
Ce contrôle, apparu à la version 0.65 permet d'afficher une grille de requête optionelle entre la liste des tables et la liste d'enregistrements.
Si au moins un champ possède une valeur différente de <i>aucun</i>, la grille de requête est affichée, sinon on passe directement à la liste des enregistrements.<br>
Les valeurs possibles sont :<ul>
<li> <u>Entrée (like)</u> <small>(INPLIKE)</small> qui affiche une boite de saisie (contenant % par défaut), accompagée d'une case à cocher de négation de la condition. Le <i>where</i> au sens sql correspondant sera  NOM_CHAMP LIKE' VALEUR_SAISIE' ou NOT(LIKE NOM_CHAMP='VALEUR_SAISIE')
<li> <u>Liste dér. Valeurs champ  </u><small>(LDC)</small> qui affiche une liste déroulante à choix multiple, allant chercher toutes les valeurs différentes du champ de la table correspondante, accompagée d'une case à cocher de négation de la condition.
<li> <u>Liste dér. Valeurs fixées  </u><small>(LDF)</small> qui affiche une liste déroulante à choix multiple reprenant les valeurs fixées dans le champ VALEURS, accompagée d'une case à cocher de négation de la condition. 
<li> <u>Liste dér. Valeurs liées  </u><small>(LDL)</small> qui affiche une liste déroulante à choix multiple, reprenant des valeurs <i>pêchées</i> dans une autre table, avec la même syntaxe que les listes liées
<br><br><u>N.B.:</u> pour toutes les listes, le <i>where</i> au sens SQL correspondant sera  NOM_CHAMP LIKE 'VALEUR_SELECTIONNEE_1' OR NOM_CHAMP LIKE 'VALEUR_SELECTIONNEE_2' etc ... ou NOT(NOM_CHAMP LIKE 'VALEUR_SELECTIONNEE_1' OR NOM_CHAMP LIKE 'VALEUR_SELECTIONNEE_2')<br><br> 
<li> <u>Date ...   </u><small>(DANT, DPOST, OU DATAP)</small> est uniquement adapté aux champs de type date. Affiche une ou plusieurs boites de saisie permettant de rentrer la(les) date(s) limite(s) au format jj/mm/aa(aa), le tout toujours accompagé d'une case à cocher de négation de la condition. 
</ul>
<br>
Bien évidemment toutes les conditions rentrées sur des champs déifférents sont liés par un <i>AND</i>
<p><br>
<a name="admafsel" class="boldred11px">Affichage sélectionnable</a><br>
Ce contrôle, apparu à la version 0.70 permet d'afficher sur la grille de requête optionelle une case à cocher, permettant à l'utilsateur d'afficher le champ (colonne) dans la liste qui suit ou pas.
Si au moins un champ possède une valeur différente de <i>aucun</i>, la grille de requête est affichée, sinon on passe directement à la liste des enregistrements.<br>
Les valeurs possibles sont :<ul>
<li> <u>non  </u>l'affichage du champ n'est pas sélectionnable par l'utilisateur
<li> <u>Oui, coché par défaut</u> <small>(OCD)</small>une case à cocher cochée par defaut apparait en face du champ, celui-ci sera affiché par défaut
<li> <u>Oui, non coché par défaut</u> <small>(ONCD)</small>une case à cocher non cochée par defaut apparait en face du champ, mais celui-ci ne sera pas affiché par défaut
</ul>
<br><br>
<a name="admttavmaj" class="boldred11px">Traitement avant Mise à jour</a><br>
Ceci permet de spécifier des MAJ automatiques sur les champs avant leur affichage pour édition
Les valeurs possibles sont :
<ul>
     <li>Aucun (par défaut)
     <li>Date du jour <small>(DJ) 
     </small><li>Date Jour si nulle avant <small>(DJSN) 
     </small><li>Date Jour +2 mois si nulle avant <small>(DJP2MSN) 
     </small><li>Code User MAJ <small>(US) 
     </small><li>Code User MAJ si nul avant <small>(USSN) 
     </small><li>Edition permise uniquement en création/copie (nouvel enregistrement) <small>(EDOOFT) </small>: ensuite, le controle d'édition passe en statique/statique lié
</ul>
<br>
<br><br>
<a name="admttpdtmaj" class="boldred11px">Traitement pendant Mise à jour</a><br>
Si l'on choisit une des vérifs prédéfinies, le formulaire sera checké lors du submit.<br/>
Pour pouvoir utiliser cette option dans les applis (ainsi que les calendriers automatiques), il faut :<br/>
<strong> - inclure les fichiers <em>css4sharedjs_inc.css.php</em> et <em>shared_inc.js.php</em></strong><br>
Exemple : <pre>
&lt;link href="css4sharedjs_inc.css.php" rel="stylesheet" type="text/css"&gt;
&lt;script type="text/javascript" src="shared_inc.js.php"&gt;&lt;/script&gt;
</pre>
<strong> - rajouter dans la balise &lt;form&gt; <em>onsubmit="return testJSValChp(this);"</em></strong><br><br>
Si l'on choisit <i>autre</i> et qu'on rentre une valeur, ceci permet de spécifier des MAJ automatiques sur les champs pendant leur affichage pour édition <br>
Elle repose sur l'appel d'une fonction JavaScript, qui devra etre définie ailleurs (dans un commentaire par exemple)<br>
Supposons que l'on veuille appeler la fonction <i>Verif();</i> lors d'un évènement de type <i>onChange</i> sur ce champ, on entrera alors dans cette case la valeur <i><b>onChange:Verif();</b></i> <br>
Le code Javascript généré dans l'entrée de formulaire sera <i>onChange=&quot;Verif();&quot;</i><br>
<u>NB</u>: cette possibilité ne fonctionne pour l'instant que sur des boites textes manuelles (pas auto)
<br><br>
<a name="admcomment" class="boldred11px">Commentaire</a><br>
Ce champ est un texte libre, qui apparaitra sous chaque libéllé de champ en petits caractères dans la page d'édition de l'enregistrement. On pourra ici mettre des conseils à l'utilisateur.<br>
On pourra aussi dans ce champ insérer les scripts (entre les balises standards HTML) appelés par les traitements pendant mise à jour.<br><br>
<div align="center"><a href="#haut" class="fxbutton" title="to top"> ^ ^ </a><br>
</div><hr width="70%" size="1">
<a name="progobj"></a><span class="chapitrered12px">Paramètres, programmation, fonctions partagées</span>
<BR><span class="boldred11px">Passage des variables et de paramètres denvironnement</span>
<BR>Certaines variables peuvent ou doivent être définies pour permettre daccéder à une page PYA directement depuis une application.
<BR>
<BR>lc_CO_USMAJ : code du user effectuant la mise à jour (obligatoire, sinon lappli ramène automatiquement en page daccueil)
<BR>
<BR>Toutes les variables qui suivent, dont le nom commence par lc_ sont ensuite mémorisée par lappli en tant que variables de session dont le nom est (presque) identique et commence par ss_ (par ex lc_parenv[ro] devient mémorisé en ss_parenv[ro])
<BR>
<BR><u>lc_parenv[MySqlUser] , c_parenv[MySqlPasswd]:</u> code user et mot de passe  pour accès au serveur MySql (si laissé vide prend les valeurs par défaut définies dans infos.php)
<BR><u>lc_DBName, lc_NMTABLE :</u> noms de la base et table à traiter
<BR><u>lc_parenv[ro], lc_parenv[blair]:</u> variables denvironnement qui si elles sont positionnées à 1 permettent respectivement le fonctionnement en consultation seule et le non-affichage des liens dadministration (ainsi que le retour vers la liste des bases)
<BR><u>lc_parenv[noinfos] :</u> si positionnée à true, permet le non-affichage des infos sur le serveur et des liens vers laide
<BR><u>lc_reqcust:</u> valeur de la la requête custom utilisateur; <u>lc_parenv[lbreqcust] :</u> Libellé affiché en haut des pages lorsque requête spécifique
<BR><b><u>Gestion des adresses de retour</u></b>
<BR>lc_adrr[xxx.php] :</u>tableau des adresses de retour : permet de spécifier ladresse pointée par le bouton retour de chaque page de PYA. 
<BR>Si elle est spécifiée à 0, le bouton retour nest pas affiché (utilisation des pages dans des frames)
<BR>
<BR>Un exemple durl, pointant vers la page de requête req_table.php, base MA_BASE, table MA_TABLE, pas daffichage des infos, tous les user et mot de passe = hn
<BR>
<BR>href="../../admin/phpYourAdmin/req_table.php?lc_CO_USMAJ=hn&lc_adrr[list_table.php]=mapage.php&lc_adrr[req_table.php]=mapage.php&lc_DBName=MA_BASE&lc_NM_TABLE=MA_TABLE &lc_parenv[noinfos]=true&lc_parenv[MySqlUser]=hn&lc_parenv[MySqlPasswd]=hn"
<BR>
<BR><span class="boldred11px">Fonctions, Programmation avec les objets de phpYourAdmin</span>
<BR>Une grande partie de lapplication est basée sur lutilisation dobjets et de fonctions, définies le fichier <b>fonctions.php</b>, placé dans le répertoire php_inc à la racine des serveurs.
<BR>Il suffira de consulter ce fichier pour connaître lutilisation des fonctions et objets.
<BR>
<BR>Il suffit dinsérer dans une page php le code include("fonctions.php") ; sans spécifier de chemin (le serveur est paramétré pour le trouver automatiquement) pour bénéficier de toutes les fonctions et objets.
<BR>
<BR>Lobjet PYA se nomme PYAobj
<BR>
<BR>Dans l'exmple qui suit, on suppose que la table de description existe et est correctement initialisée
<BR>Exemple de code php :
<BR>
<BR>// initialisations
<BR>$CIL=new PYAobj(); // instanciation de lobjet
<BR>$CIL->NmBase="MA_BASE"; // initialisation des propriétés de base
<BR>$CIL->NmTable="personne";
<BR>$CIL->NmChamp="per_affectation";
<BR>$CIL->InitPO(); // méthode d'initialisation des autres propriétés à partir du contenu de la table de description
<BR>// utilisation
<BR>// il faut récupérer la valeur du champ
<BR>$CIL->ValChp=$Valeur; 
<BR>echo ($CIL->Libelle);
<BR>$CIL->EchoEditAll(); // méthode d'affichage en édition (ici liste déroulante)
<BR>
<BR>
<U>N.B.</U>: depuis la version 0.895, il existe une propriété complémentaire : DirEcho.
Par défaut elle est initialisée à true, c'est à dire que quand on appelle la méthode EchoEditAll par ex, le contrôle est "échoisé" directement. <br/>
Pour pouvoir utiliser les objets avec un système de templates par exemple on procèdera comme suit:<BR>
<PRE>
$CIL->DirEcho=false;
$val2disp=$CIL->EchoEditAll(); // fait que le code html est renvoyé ds le return
$tpl->set_var('val2disp', $val2disp );
</PRE>
<div align="center"><a href="#haut" class="fxbutton" title="to top"> ^ ^ </a><br>
</div><hr width="70%" size="1">
<a name="clog"></a><span class="chapitrered12px">Evolution des versions...<br></span>
Version courante  <b><? echo $VerNum; ?></b>
<blockquote>
<u>0.896, v 0.9 pre2 (05/08/06):</u><br>
&#149; possibilité de genrerer une tablle virtuelle <img src="vtb_icon.png"/>a partir d'une autre table dans l'ecran d'admin<br>
<u>0.895, v 0.9 pre2 (28/05/05):</u><br>
&#149; - correction bug uploads de fichiers<br>
&#149; - possibilité de ne pas "échoiser" directement dans les méthode de l'objet PYA: utilisation de la propriéré DirEcho à false<br>
<u>0.894, v 0.9 pre2 (20/05/05):</u><br>
&#149; LD avec hiérarchie; code @@ devant le nom du champ contenant le pid<br>
<u>0.892, v 0.9 pre2 (01/05/05):</u><br>
&#149; Possibilité d'administrer des données de tables virtuelles: dans la table DESC_TABLE, on rajoute des enregistrements qui ne correspondent à aucune table (ou champ existant): dans ce cas il faut que le nom de la pseudo table commence par la chaine '_vtb_' (suffixe referencé par la var $id_vtb)
<u>0.891, v 0.9 pre2 (01/05/05):</u><br>
&#149; compatibilité register_globals=Off<br>
<u>0.890, v 0.9 pre1 (15/10/04):</u><br>
&#149; Multilingue: fichiers de messages<br>
<u>0.865 (23/09/03):</u><br>
&#149; Rajout d'une fonctonnalité permettant d'envoyer un mail à tous les mails d'une page de liste<br>
<u>0.864 (23/09/04):</u><br>
&#149; Correction bug sur forçage liste en cases à cocher (fichier fonctions.php)<br>
<u>0.863 (23/09/03):</u><br>
&#149; Correction bug sur liste déroulantes et requetes: remplacementde la fonction array_merge dans ke fichier PYAObjdef.inc (qui reconstruit les indices des tableaux de hachaage) par l'opérateur +<br>
<u>0.862 (28/02/03):</u><br>
&#149; Nombreuses améliorations et corrections, surtout dans fonctions.php<br>
&#149; Scindage de fonctions.php<br>
&#149; correction pb upload<br>

<u>0.861 (3/12/02):</u><br>
&#149; Fichier include séparé pour les infos de connexion MySql<br>
&#149; Possibilité de désactiver les suppressions d'enregistrement<br>
<u>0.860 (29/11/02):</u><br>
&#149; Gestion d'une requete utilisateur, permettant l'affichage d'états spécifiques téléchargeables.<br>
&#149; Ajout de la fonction InitPOReq($req,$Base=""), permettant l'initialisation d'un tableau d'objets PYA fonction d'une requêt SQL<br>
<u>0.855 (27/11/02):</u><br>
&#149; Modif fonction DispLD, avec argument supplémentaire optionnel pour forçage aff en case à cocher boutons radio:<br>
&nbsp;&nbsp;DispLD($tbval,$nmC,$Mult="no",$Fccr="")<br>
<u>NB</U>: Si la propriété contient la chaine br, un retour de ligne est inséré entre chaque valeur, sinon elles sont séparées par des espaces<br>
&#149; Rajout d'une propriété Fccr pour l'objet PYA, permettant le forçage d'affichage en case à cocher ou radio. <br>
<u>0.854 (20/11/02):</u><br>
&#149; Creation méthode pour mise à jour d'un champ, avec gestion des fichiers joints<br>
<u>0.853 (19/11/02):</u><br>
&#149; Amélioration page de re-génération de la table de description. Options de listage des caractéristiques des champs.<br>
&#149; Ajout d'un fichier d'include de scripts JS.<br>
<u>0.852 (18/11/02):</u><br>
&#149; Compatibilté filtres LD avec set ET pseudo-set<br>
<u>0.851 (13/11/02):</u><br>
&#149; Révision gestion des types "pseudo-set"<br>
<u>0.850 (7/11/02):</u><br>s
&#149; MAJ du ficghier d'aide<br>
&#149; passage de lc_parenv[blair] en var de session<br>
 <u>0.849 (5/11/02):</u><br>
&#149; Possibilités de désactiver l'entourage des noms de champs par des ` dans le fichier infos.php en changeant la var $CSpIC (le ` ne fonctionne pas avec de vieilles versions de MySql)
<br>
<u>0.848 (17/10/02):</u><br>
&#149; gestion des user et passwd mysql sur la page d'index, ou par passage des paramètres lc_parenv[MySqlUser] et lc_parenv[MySqlPasswd] <br>
&#149; Amélioration de la visu sd lc_parenv[noinfos]=true<br>
<u>0.846 (16/10/02):</u><br>
&#149; Gestion des noms de tables protégés par des ` pour des tables ayant des noms réservés (pas les noms de champs)<br>
&#149; Paramètre ss_parenv[noinfos] permettant de ne pas afficher les infos et le pied de page<br>
<u>0.845 (2/10/02):</u><br>
&#149; Traitement pendant Mise à Jour dispo, mais uniquement sur les boites textes manuelles<br>
&#149; possiblité de passer un 3ème paramètre supplémentaire à la fonction msq servant à localiser son appel pour débogage<br>
<u>0.844 (18/09/02):</u><br>
&#149; chemin des fichiers joints paramétrable (ou automatique)<br>
&#149; définition du type de champ en automatique<br>
<u>0.843 (17/09/02):</u><br>
&#149; effacement automatique en super-admin des enregistrements des tables effacés ds la table de description<br>
&#149; gestion clés primaires multiples, sinon prend ts les champs en clé<br>
<u>0.841 (12/09/02):</u><br>
&#149; divers MAJ cosmétiques (tableaux colorés) et correctiosns de petits bug<br>

<u>0.840 (3/09/02):</u><br>
&#149; gestion des adresses de retour par tableau de hachage pour appel des pages par programme externe <br>
&#149; applet javascript de confirmation de suppression d'enregistrement dans la liste<br>
&#149; ajout d'un bouton annuler les changements dans la page d'édition avec confirmation<br>
&#149; gestion du paramètre d'environnement readonly pour consultation uniquement<br>

<br>
<u>0.830 (25/07/02):</u><br>
&#149; nouvelle méthode de l'initialisation de l'objet PYAObj<br>
&#149; modification des noms de propriétés (NmChp/NmChamp qui ne correspondaient 
    pas toujours)<br>
&#149; page d'exemple d'utilisation de l'objet <a href="test_objPYA.php" target="_blank">test_objPYA.php</a><br>
<br>
<u>0.826 (10/07/02):</u><br>
&#149; double click sur une liste effectue un submit<br>
&#149; affichage du where en entête de l'état<br>
&#149; gestion des singuliers/pluriels<br>
<br>
<u>0.825 (03/07/02):</u><br>
&#149; possibilité de mettre à jour la table de description lors d'ajout ou de suppression de champs<br>
<br>
<u>0.82 (02/07/02):</u><br>
&#149; un seul objet PYAobj pour tous les contrôles<br>
&#149; Correction bug quand pas de condition choisie dans la requête<br><br>
<u>0.81 (28/6/02):</u><br>
&#149; Liste statiques en clés:valeurs<br>
&#149; En creation de table de desc, case à cocher permettant de générer ou pas les mise à jour fonction des noms de champs<br><br>
<u>0.8 (25/6/02):</u><br>
&#149; Correction bug sur nbre de ligne par page qui ne fonctionnait pas<br>
&#149; rajout du traitement avant MAJ EDOOFT, Edit Only On First Time<br>
&#149; Gestion des boutons retour sommaire general et/ou grille de requete<br>
&#149; passage en objets des états, et MAJ de l'extraction<br><br>
<u>0.765 (13/6/02):</u><br>
&#149; paramétrage nb lignes / nb colonnes textarea dans champs valeurs<br>
&#149; rajout du traitement avant MAJ USSN, code user si nul avant, sinon change rien<br><br>
<u>0.76 (12/6/02):</u><br>
&#149; préparation de l'objet d'édition à la possibilité d'un champ intégrant des expressions évaluées (voir propriété Val2 dans l'objet EchoEdit)<br>
&#149; possibilité de lier les caractères d'édition des tables dans le champ valeur<br><br>
<u>0.75 (6/6/02):</u><br>
&#149; controles d'édition en objets<br>
&#149; classement possible sur 1 champ dans les listes liées (précédé d'un @), choix du caractère séparateur (précédé d'un !)<br>
<u>0.71 (29/5/02):</u><br>
&#149; options de requêtes étendues: <br>
-  choix sur les dates<br> 
-  liste déroulantes (toutes à choix multiples) sur valeurs effectivement contenues dans le champ, valeurs d'une liste fixe, ou valeur liée à une autre table<br>
&#149; possibilité d'affichage des champs dans liste (colonnes) sélectionnable par l'utilisateur<br>
&#149; le choix des colonnes reste affiché dans le fichier téléchargé<br><br>
<u>0.69 (28/5/02):</u><br>
&#149; gestion des type d'affichage liste déroulante à choix multiples, et liste déroulante à choix multiples liée<br>
&#149; javascript (merdique car fait le soir) qui change le type d'affichage dans la liste en fonction du type d'affichage d'édition<br>
&#149; possibilité de téléchargement au format tsv (Tabulation Separated Values) accessible en bas de la liste, compatible, entre autres, Excel<br><br> 
<u>0.67 (27/5/02):</u><br>
&#149; révision du programme de création de table de description (<a href="CREATE_DESC_TABLES.php">CREATE_DESC_TABLES.php</a>): possiblité de choisir 1 ou plusieurs tables dans une même base pour la visualisation/génération<br> 
&#149; ajout de la liste déroulante à choix unique dans les possibilités de filtre de requête<br><br>

<u>0.65 (23/5/02):</u><br>
&#149; grille de requête entre liste des tables et des enregistrements<br>
&#149; extension de ce fichier d'aide ...<br><br>

<u>0.60 (22/5/02):</u><br>
&#149; disponibilité du présent fichier d'aide ....<br>
&#149; possibilité d'aller chercher des valeurs de liste dans d'autres bases voie d'autres serveurs<br>
&#149; affichage dans les listes x par x<br>
&#149; tri dans les listes sur 3 champs<br>
<br><u>0.57 (29/4/02):</u><br>
&#149; affichage en clair des libellés de table<br><br>
<u>0.56 (26/4/02):</u><br>
&#149; gestion des valeurs par défaut et null/not null stockées dans les definitions mysql (édition par phpMyAdmin)<br>
&#149; utilisation avancée de la fonction SHOW FIELDS FROM .... dans l'utilitaire d'édition <br><br>
<u>0.55:</u><br>
&#149; le type de champ mémorisé dans la table desc_table n'est plus utilisé dans l'affichage auto, c'est le &quot;vrai&quot; type du champ qui est utilisé<br> 
&#149; gestion des types set en automatique <br>
&#149; gestion des tailles d'input automatique en fonction des tailles de char et de varchar<br>
</blockquote>
<div align="center"><a href="aide.php#haut" class="fxbutton" title="to top"> ^ ^ </a><br>
</div><hr width="70%" size="1">
<p><a href="#" onclick="self.close();" title="close"  class="fxbutton">Fermer</a>
</p>
</body>
</html>
