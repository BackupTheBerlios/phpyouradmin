<?
function trad($mess) {
$loc_message=Array(


"IND_title"=>"Bienvenue dans l'utilitaire d'édition phpYourAdmin",
"IND_txtacc"=>"Vous devez vous identifiez pour accéder aux tables<BR>
Entrez ci-dessous : ",
"IND_bdduser"=>"Le nom d'utilisateur du serveur de base de données",
"IND_bddpasswd"=>"Le mot de passe du serveur de base de données",
"IND_pyauser"=>"L'identifiant servant à tracer les mises à jour",
"IND_choilang"=>"Langue utilisée",
"IND_bddtype"=>"Type de serveur de base de donn�es",

"LB_title"=>"Edition des bases du serveur ",
"LB_txtacc"=>"L'&eacute;dition du contenu des tables n'est accessible que si la base a &eacute;t&eacute; param&eacute;tr&eacute;e &agrave; cet effet.<BR>
Contacter  le <a href=\"mailto:webtech@haras-nationaux.fr?subject=Administration des Bdd Mysql avec phpYourAdmin\">webmaster</a> pour connaitre la proc&eacute;dure exacte<br>
Merci<br><br>",
"LB_baselist"=>"Liste des bases param&eacute;tr&eacute;es et &eacute;ditables :",
"LB_createDT"=>" &nbsp;pour accéder à l'utilitaire de description (réservé aux initiés)",


"LT_reqv"=>"Impossible d\'exécuter une requête vide !",
"LT_notable"=>"Vous devez sélectionner une table !",
"LT_titleedit"=>"Tables de la base ",
"LT_titleadm"=>"AdmAdm des tables de la base ",
"LT_titlehedit"=>"Edition du contenu des tables de la base ",
"LT_titlehadm"=>"Propriétés d'édition des tables de la base ",
"LT_clicktable"=>"Cliquez sur la table à éditer ou afficher",
"LT_bcadm"=>"Attention, vous êtes en mode <em>administration</em>",
"LT_addrecord"=>" Ajouter un enregistrement",
"LT_reqcust"=>"Exécuter une requête spéciale :",
"LT_noconfirmdelete"=>"Ne pas confirmer les suppressions d'enregistrement",
"LT_nblig_aff_ppage"=>"Nombre de lignes affichées par page",
"LT_change_table_edit_prop"=>" pour changer les propriétés d'édition des tables de cette base",
"LT_goback_content_table_edit"=>" pour revenir en édition du <b>contenu</b> des tables de cette base",
"LT_retLB"=>"Retour à la liste des bases",

"REQ_query_on_table"=>"Requête sur la table ",
"REQ_crit_select"=>"CRITERES DE SELECTION/AFFICHAGE DE LA TABLE ",
"REQ_select_text"=>"Sélectionnez critère(s), et option(s) d'affichage pour le(s) champ(s) ci-dessous... &nbsp;&nbsp;&nbsp;&nbsp;, ou",
"REQ_retLT"=>"Retour à la liste des tables",

"LR_title"=>"Enregistrements de la table ",
"LR_no_record"=>"Aucun enregistrement à afficher ",
"LR_display_record"=>"Affichage enregistrement",
"LR_confirm_del_message"=>"Etes vous certain de vouloir supprimer cet enregistrement ?",
"LR_to_list"=>" répondant aux critères dans cette table",
"LR_orderby"=>" Classement",
"LR_order_asc"=>"Classer par ordre croissant",
"LR_order_desc"=>"Classer par ordre décroissant",
"LR_del_mod_cop"=>"Suppr./Mod.<br>Copie",
"LR_record_edit"=>"Modifier l'enregistrement",
"LR_record_delete"=>"Effacer l'enregistrement",
"LR_record_copy"=>"Copier l'enregistrement",
"LR_record_show"=>"Afficher les détails de l'enregistrement",
"LR_display_the"=>"Afficher les ",
"LR_follow_recs"=>" enregistrements suivants ",
"LR_preced_recs"=>" enregistrements précédents ",
"LR_query_back"=>"Retour requete",
"LR_query_back_bulle"=>"Retour à la grille de requete",
"LR_download"=>"Télécharger",
"LR_download_bulle"=>"Télécharger tous les enregistrements au format tsv (compatible tableur)",
"LR_mail_to_all_bulle"=>"Envoyer un mail à toutes les adresses de cette page",
"LR_mail_to_all"=>"mail à tous",

"ER_record_of_table"=>" d'un enregistrement de la table ",
"ER_record_details"=>"Details d'un enregistrement ",
"ER_record_car"=>"Caract�ristiques de l'enregistrement ",
"ER_raz_confirm"=>"Etes-vous certain de vouloir remettre � leur valeur d\'origine tous les champs de cette page ? ",


"BT_retour"=>" << Retour ",
"BT_valider"=>"Valider",
"BT_reset"=>"R.A.Z.",
"BT_click"=>"Cliquez",
"BT_here"=>" ICI ",


"foo_user_connected"=>"Utilisateur connecté",
"com_database"=>" base de donnees ",
"com_consultation"=>"Consultation ",
"com_edition"=>"Edition ",
"com_de_la_table"=>" de la table ",
"com_record"=>" Enregistrement",
"com_copy"=>"Copie ",
"com_add"=>"Ajout ",
"com_to"=>"à",
"com_details"=>"Détails",
"com_vers_enbas"=>" bas",
"com_vers_enbas_bulle"=>"vers le bas de cette page",
"com_vers_enhaut"=>" haut",
"com_vers_enhaut_bulle"=>"vers le haut de cette page",
"com_enter_password"=>"Entrez un mot de passe pour accéder à la partie admin",
);
return ($loc_message[$mess]!="" ?  utf8_decode($loc_message[$mess]) : $mess);
}
?>
