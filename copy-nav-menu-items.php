<?php
/*!
 * Plugin Name: Copy Nav Menu Items
 * Plugin URI:  https://wordpress.org/plugins/copy-nav-menu-items/
 * Description: Simple plugin that allows for nav menu items to be copied with a single click.
 * Version:     1.1.4
 * Author:      Robert Noakes
 * Author URI:  https://robertnoakes.com/
 * Text Domain: copy-nav-menu-items
 * Domain Path: /languages/
 * Copyright:   (c) 2020-2024 Robert Noakes (mr@robertnoakes.com)
 * License:     GNU General Public License v3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */
 
/**
 * Main plugin file.
 * 
 * @since 1.1.2 Removed PHP_INT_MAX fallback.
 * @since 1.1.0 Added fallback for PHP_INT_MAX.
 * @since 1.0.0
 * 
 * @package Copy Nav Menu Items
 */
 
if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Setup autoloading for plugin classes.
 *
 * @since 1.0.0
 */
spl_autoload_register(function ($class)
{
	$base_class = 'Copy_Nav_Menu_Items';

	if (strpos($class, $base_class) === 0)
	{
		$includes_path = dirname(__FILE__) . '/includes/';
		$core_path = $includes_path . 'core/class-';
		$static_path = $includes_path . 'static/class-';
		$standalone_path = $includes_path . 'standalone/class-';
		$fields_path = $includes_path . 'fields/class-';
		$plugins_path = $includes_path . 'plugins/class-';

		$file_name = ($class === $base_class)
		? 'base'
		: strtolower(str_replace(array($base_class . '_', '_'), array('', '-'), $class));

		$file_name .= '.php';

		if (file_exists($core_path . $file_name))
		{
			require_once($core_path . $file_name);
		}
		else if (file_exists($static_path . $file_name))
		{
			require_once($static_path . $file_name);
		}
		else if (file_exists($standalone_path . $file_name))
		{
			require_once($standalone_path . $file_name);
		}
		else if (file_exists($fields_path . $file_name))
		{
			require_once($fields_path . $file_name);
		}
		else if (file_exists($plugins_path . $file_name))
		{
			require_once($plugins_path . $file_name);
		}
	}
	else if ($class === 'WP_Screen')
	{
		require_once(ABSPATH . 'wp-admin/includes/class-wp-screen.php');
	}
});

/**
 * Returns the main instance of Copy_Nav_Menu_Items.
 *
 * @since 1.0.0
 *
 * @param  string          $file Optional main plugin file name.
 * @return Copy_Nav_Menu_Items       Main Copy_Nav_Menu_Items instance.
 */
function Copy_Nav_Menu_Items($file = '')
{
	return Copy_Nav_Menu_Items::_get_instance($file);
}

Copy_Nav_Menu_Items(__FILE__);
