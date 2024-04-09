<?php
/*!
 * Plugin constants.
 *
 * @since 1.0.0
 *
 * @package    Copy Nav Menu Items
 * @subpackage Constants
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement plugin constants.
 *
 * @since 1.0.0
 */
final class Copy_Nav_Menu_Items_Constants
{
	/**
	 * Plugin prefixes.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const PREFIX = 'cnmi_';

	/**
	 * Plugin token.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const TOKEN = 'copy_nav_menu_items';

	/**
	 * Plugin versions.
	 *
	 * @since 1.1.1 Added previous version.
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const VERSION = '1.1.4';
	const VERSION_PREVIOUS = '1.1.3';
	
	/**
	 * Plugin hook names.
	 *
	 * @since 1.1.0 Added validate data hook.
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const HOOK_COPY_NAV_MENU_ITEM = self::PREFIX . 'copy_nav_menu_item';
	const HOOK_SAVE_SETTINGS = self::PREFIX . 'save_settings';
	const HOOK_VALIDATE_DATA = self::PREFIX . 'validate_data';

	/**
	 * Plugin option names.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const OPTION_SETTINGS = self::TOKEN . '_settings';
	const OPTION_VERSION = self::TOKEN . '_version';

	/**
	 * Plugin setting names.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const SETTING_DELETE_SETTINGS = 'delete_settings';
	const SETTING_DELETE_USER_META = 'delete_user_meta';
	const SETTING_UNCONFIRMED = '_unconfirmed';

	/**
	 * Plugin URLs.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const URL_BASE = 'https://noakesplugins.com/';
	const URL_DONATE = 'https://www.paypal.com/donate?hosted_button_id=CAWMKCNVS8D8C&source=url';
	const URL_KB = self::URL_BASE . 'kb/copy-nav-menu-items/';
	const URL_SUPPORT = 'https://wordpress.org/support/plugin/copy-nav-menu-items/';
	const URL_REVIEW = self::URL_SUPPORT . 'reviews/?rate=5#new-post';
	const URL_TRANSLATE = 'https://translate.wordpress.org/projects/wp-plugins/copy-nav-menu-items';
}
