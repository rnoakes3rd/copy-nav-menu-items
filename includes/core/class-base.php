<?php
/*!
 * Base plugin functionality.
 *
 * @since 1.0.0
 *
 * @package    Copy Nav Menu Items
 * @subpackage Base
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the base plugin functionality.
 *
 * @since 1.0.0
 *
 * @uses Copy_Nav_Menu_Items_Wrapper
 */
final class Copy_Nav_Menu_Items extends Copy_Nav_Menu_Items_Wrapper
{
	/**
	 * Main instance of Copy_Nav_Menu_Items.
	 *
	 * @since 1.0.0
	 *
	 * @access private static
	 * @var    Copy_Nav_Menu_Items
	 */
	private static $_instance = null;

	/**
	 * Returns the main instance of Copy_Nav_Menu_Items.
	 *
	 * @since 1.0.0
	 *
	 * @access public static
	 * @param  string          $file Main plugin file.
	 * @return Copy_Nav_Menu_Items       Main Copy_Nav_Menu_Items instance. 
	 */
	public static function _get_instance($file)
	{
		if (is_null(self::$_instance))
		{
			self::$_instance = new self($file);
		}

		return self::$_instance;
	}

	/**
	 * Base name for the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var    string
	 */
	public $plugin;

	/**
	 * Global cache object.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var    Copy_Nav_Menu_Items_Cache
	 */
	public $cache;

	/**
	 * Global settings object.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var    Copy_Nav_Menu_Items_Settings
	 */
	public $settings;

	/**
	 * Global nav menus object.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var    Copy_Nav_Menu_Items_Nav_Menus
	 */
	public $nav_menus;

	/**
	 * Global AJAX object.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @var    Copy_Nav_Menu_Items_AJAX
	 */
	public $ajax;

	/**
	 * Constructor function.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  string $file Main plugin file.
	 * @return void
	 */
	public function __construct($file)
	{
		if
		(
			!empty($file)
			&&
			file_exists($file)
		)
		{
			$this->plugin = $file;

			add_action('plugins_loaded', array($this, 'plugins_loaded'));
		}
	}

	/**
	 * Load the plugin functionality.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function plugins_loaded()
	{
		$this->cache = new Copy_Nav_Menu_Items_Cache();
		$this->settings = new Copy_Nav_Menu_Items_Settings();
		$this->nav_menus = new Copy_Nav_Menu_Items_Nav_Menus();
		$this->ajax = new Copy_Nav_Menu_Items_AJAX();

		add_action('admin_init', array('Copy_Nav_Menu_Items_Setup', 'check_version'), 0);
		add_action('init', array($this, 'init'));
		
		add_filter('plugin_row_meta', array($this, 'plugin_row_meta'), 10, 2);
	}

	/**
	 * Initialize the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function init()
	{
		load_plugin_textdomain('copy-nav-menu-items', false, dirname(plugin_basename($this->plugin)) . '/languages/');
	}

	/**
	 * Add links to the plugin page.
	 *
	 * @since 1.1.0 Removed 'noreferrer' from links and added non-breaking space before dashicons.
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  array  $links Default links for the plugin.
	 * @param  string $file  Main plugin file name.
	 * @return array         Modified links for the plugin.
	 */
	public function plugin_row_meta($links, $file)
	{
		return ($file === plugin_basename($this->plugin))
		? array_merge
		(
			$links,

			array
			(
				'<a class="dashicons-before dashicons-sos" href="' . Copy_Nav_Menu_Items_Constants::URL_SUPPORT . '" rel="noopener" target="_blank">&nbsp;' . __('Support', 'copy-nav-menu-items') . '</a>',
				'<a class="dashicons-before dashicons-star-filled" href="' . Copy_Nav_Menu_Items_Constants::URL_REVIEW . '" rel="noopener" target="_blank">&nbsp;' . __('Review', 'copy-nav-menu-items') . '</a>',
				'<a class="dashicons-before dashicons-translation" href="' . Copy_Nav_Menu_Items_Constants::URL_TRANSLATE . '" rel="noopener" target="_blank">&nbsp;' . __('Translate', 'copy-nav-menu-items') . '</a>',
				'<a class="dashicons-before dashicons-coffee" href="' . Copy_Nav_Menu_Items_Constants::URL_DONATE . '" rel="noopener" target="_blank">&nbsp;' . __('Donate', 'copy-nav-menu-items') . '</a>'
			)
		)
		: $links;
	}
}
