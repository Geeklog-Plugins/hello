<?php

###############################################################################
# french_france_utf-8.php
#
# This is the French France language file for the Geeklog hello plugin
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#
###############################################################################


// Language for plugin users
$LANG_HELLO01 = array(
    'plugin_name'             => 'Hello',
    'overview'                => 'Le module Hello vous permet de rédiger et d\'expédier des emails au format html au groupe de votre choix. Vous pouvez aussi expédier manuellement un avis de publication de nouveaux articles ou modifier les paramètres d\'expédition des avis (articles et module forum si disponible) pour un membre donné, ce qui peut être utile lorsque l\'adresse email de ce membre est invalide et que les emails vous reviennent.',
	'email_save'              => 'Email sauvegardé',
	'email_schedule'          => 'email(s) programmé(s).',
    'email_sent'              => 'email(s) envoyé(s)',
    'email_fail'              => 'email(s) abandonné(s)',
	'menu_label'              => 'Menu',
	'homepage_label'          => 'Hello',
	'send_email_group'        => 'Envoyer un email',
    'manual'                  => 'Emailing Manuel',
	'configuration'           => 'Configuration',
	'read_email'              => 'Archives',
	'see_email'               => 'Voir',
	'id'                      => 'ID',
	'subjet'                  => 'Suject',
    'creation'                => 'Date',
    'group'                   => 'Groupe/Source',
    'quantity'                => 'Quantité',
	'email_display'           => '',
	'email'                   => 'Email',
    'max'                     => 'max (voir la configuration pour changer cela ou demander à votre administrateur)',
    'send_next'               => 'Envoyer les emails maintenant',
	'import_message'          => 'ou choisir un fichier csv sur votre disque dur pour importer vos contacts et sélectionner le séparateur pour les données. Assurez-vous que l\'email du destinataire est le premier champ de votre fichier. Les emails seront sauvegardés temporairement dans la base de données.',
	'select_file'             => 'Choisir un fichier .csv',
	'csv_file'                => 'fichier csv',
	'separator'               => 'Sélectionner le délimiteur',
	'hello_sent'              => 'Hello envoyé à',
	'contacts'                => 'contacts',
	'mdigest'                 => 'Notifications nouveaux articles',
	'ddigest'                 => 'Résumé quotidien',
    'access_denied'           => 'Accès interdit',
    'access_denied_msg'       => 'Vous n\'avez pas accès à cette interface administrative.',
    'installation_failed'     => 'L\'installation a échoué.',
    'installation_failed_msg' => 'L\'installation du plugin Manual Digest a échoué. Merci de consulter le ficheir error.log pour plus d\'informations.',
    'uninstall_failed'        => 'La désinstallation a échoué.',
    'uninstall_failed_msg'    => 'La désinstallation du plugin Manual Digest a échoué. Merci de consulter le ficheir error.log pour plus d\'informations.',

    'digest_sent'             => 'La notification a bien été expédiée. <a href="' . $_CONF['site_admin_url'] . '/plugins/hello/index.php">Retour à l\'interface d\'administration</a>.',
    'digest_intro'            => 'Cette fonction vous permet d\'informer les membres de votre site de la publication d\'un nouvel article. Elle est indépendante de la fonction automatisée par cronjob et vous permet d\'expédier une notification supplémentaire lorsque vous le souhaitez. Cliquez sur le bouton "Envoyer" ci-dessous (disponible uniquement si vous avez de nouveaux articles à signaler) pour expédier l\'avis de parution d\'un nouvel article aux membres qui le souhaitent.',
    'digest_last_sent'        => 'Dernière notification expédiée :',
    'never'                   => '(Jamais)',
    'no_stories'              => '<b>Aucun nouvel article trouvé.</b>',
    'num_stories'             => '<b>%d</b> articles seront expédiés.',
    'num_stories_digest'      => 'Nombre d\'articles expédiés : %d',
    'send_button'             => 'Envoyer!',
    'not_enabled1'            => '<strong>Attention:</strong> Le résumé quotidien n\'est pas activé. Assurez-vous que',
    'not_enabled2'            => 'dans l\'interface de configuration de l\'administrateur.',

    'search_text'             => 'Rechercher un nom de membre, une adresse email ou un identifiant d\'utilisateur.',
    'search_button'           => 'Rechercher',
    'new_search'              => 'Nouvelle recherche',
    'inspect_text'            => "Cliquez sur le nom de l'utilisateur pour inspecter ses paramètres de notification.",
    'uid_not_found'           => 'Il n\'y a pas de compte pour l\'identifiant %d.',
    'not_found'               => 'Aucun résultat pour <b>%s</b>.',
    'try_again'               => 'Merci d\'essayer à nouveau.',
    'user'                    => 'Membre',
    'topics'                  => 'Catégories',
    'all_topics'              => 'Toutes les catégories',
    'no_topics'               => 'Aucun',
    'reset_button'            => 'Reset',
    'success'                 => 'La notification pour l\'utilisateur <b>%s</b> a été supprimée.',
    'block_headline'          => 'Notification de publication',
    'digest_reset'            => 'La notification a été réinitialisée. <a href="' . $_CONF['site_admin_url'] . '/plugins/hello/index.php">Retour</a>.',
    'explain_reset'           => 'Utilisation avancée : Si vous ne souhaitez pas que système expédie de notification pour les derniers articles publiés, cliquez sur le bouton "Reset". Ceci aura pour effet d\'annuler d\'éventuelles notification par cronjob.',

    'forums'                  => 'Forums',
    'no_forums'               => 'Aucun',
    'forum_topics'            => 'sujets dans le forum'
);

// Localization of the Admin Configuration UI
$LANG_configsections['hello'] = array(
    'label' => 'Hello',
    'title' => 'Hello Configuration'
);

$LANG_confignames['hello'] = array(
    'max_email' => 'Number of emails to send per run?',
);

$LANG_configsubgroups['hello'] = array(
    'sg_0' => 'Main Settings',
);

$LANG_fs['hello'] = array(
    'fs_01' => 'Hello plugin'
);

// Note: entries 0, 1, and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['hello'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => TRUE, 'False' => FALSE)
);
?>
