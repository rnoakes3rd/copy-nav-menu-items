<?php
/*!
 * Plugin setup functionality.
 *
 * @since 1.0.0
 *
 * @package    Copy Nav Menu Items
 * @subpackage Setup
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the setup functionality.
 *
 * @since 1.0.0
 */
final class Copy_Nav_Menu_Items_Setup
{
	/**
	 * Check and update the plugin version.
	 *
	 * @since 1.1.1 Added previous version constant.
	 * @since 1.0.0
	 *
	 * @access public static
	 * @return void
	 */
	public static function check_version()
	{
		$current_version =
		(
			!defined('NDT_FORCE_PREVIOUS_VERSION')
			||
			!NDT_FORCE_PREVIOUS_VERSION
		)
		? wp_unslash(get_option(Copy_Nav_Menu_Items_Constants::OPTION_VERSION))
		: Copy_Nav_Menu_Items_Constants::VERSION_PREVIOUS;

		if (empty($current_version))
		{
			add_option(Copy_Nav_Menu_Items_Constants::OPTION_VERSION, sanitize_text_field(Copy_Nav_Menu_Items_Constants::VERSION));
		}
		else if ($current_version !== Copy_Nav_Menu_Items_Constants::VERSION)
		{
			update_option(Copy_Nav_Menu_Items_Constants::OPTION_VERSION, sanitize_text_field(Copy_Nav_Menu_Items_Constants::VERSION));
		}
	}
}
