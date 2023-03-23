<?php
/*!
 * Functionality for plugin uninstallation.
 * 
 * @since 1.0.1 Minor MySQL query cleanup.
 * @since 1.0.0
 * 
 * @package Copy Nav Menu Items
 */

if
(
	!defined('WP_UNINSTALL_PLUGIN')
	&&
	!defined('NDT_FAUX_UNINSTALL_PLUGIN')
)
{
	exit;
}

global $wpdb;

require_once(dirname(__FILE__) . '/includes/static/class-constants.php');

$settings = wp_unslash(get_option(Copy_Nav_Menu_Items_Constants::OPTION_SETTINGS));
$deleted = 0;

if
(
	isset($settings[Copy_Nav_Menu_Items_Constants::SETTING_DELETE_SETTINGS])
	&&
	$settings[Copy_Nav_Menu_Items_Constants::SETTING_DELETE_SETTINGS]
)
{
	delete_option(Copy_Nav_Menu_Items_Constants::OPTION_SETTINGS);
	
	$deleted++;
}

if
(
	isset($settings[Copy_Nav_Menu_Items_Constants::SETTING_DELETE_USER_META])
	&&
	$settings[Copy_Nav_Menu_Items_Constants::SETTING_DELETE_USER_META]
)
{
	$wpdb->query($wpdb->prepare
	(
		"DELETE FROM 
			$wpdb->usermeta 
		WHERE 
			meta_key LIKE %s;\n",
			
		'%' . $wpdb->esc_like(Copy_Nav_Menu_Items_Constants::TOKEN) . '%'
	));
	
	$deleted++;
}

if ($deleted === 2)
{
	delete_option(Copy_Nav_Menu_Items_Constants::OPTION_VERSION);
}
