<?php
/*!
 * Settings functionality.
 *
 * @since 1.0.0
 *
 * @package    Copy Nav Menu Items
 * @subpackage Settings
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the settings functionality.
 *
 * @since 1.0.0
 *
 * @uses Copy_Nav_Menu_Items_Wrapper
 */
final class Copy_Nav_Menu_Items_Settings extends Copy_Nav_Menu_Items_Wrapper
{
	/**
	 * Constructor function.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load_option();
		
		add_action('admin_menu', array($this, 'admin_menu'));
		
		add_filter('plugin_action_links_' . plugin_basename($this->base->plugin), array($this, 'plugin_action_links'));
	}
	
	/**
	 * Get a default value based on the provided name.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 * @param  string $name Name of the value to return.
	 * @return mixed        Default value if it exists, otherwise an empty string.
	 */
	protected function _default($name)
	{
		switch ($name)
		{
			/**
			 * Settings page title.
			 *
			 * @since 1.0.0
			 *
			 * @var string
			 */
			case 'page_title':
			
				return __('Settings', 'copy-nav-menu-items');
			
			/**
			 * True if plugin settings should be deleted when the plugin is uninstalled.
			 *
			 * @since 1.0.0
			 *
			 * @var boolean
			 */
			case Copy_Nav_Menu_Items_Constants::SETTING_DELETE_SETTINGS:
			
			/**
			 * True if plugin settings should be deleted when the plugin is uninstalled.
			 *
			 * @since 1.0.0
			 *
			 * @var boolean
			 */
			case Copy_Nav_Menu_Items_Constants::SETTING_DELETE_SETTINGS . Copy_Nav_Menu_Items_Constants::SETTING_UNCONFIRMED:
			
			/**
			 * True if plugin user meta should be deleted when the plugin is uninstalled.
			 *
			 * @since 1.0.0
			 *
			 * @var boolean
			 */
			case Copy_Nav_Menu_Items_Constants::SETTING_DELETE_USER_META:
			
			/**
			 * True if plugin user meta should be deleted when the plugin is uninstalled.
			 *
			 * @since 1.0.0
			 *
			 * @var boolean
			 */
			case Copy_Nav_Menu_Items_Constants::SETTING_DELETE_USER_META . Copy_Nav_Menu_Items_Constants::SETTING_UNCONFIRMED:
			
				return false;
		}

		return parent::_default($name);
	}

	/**
	 * Load the settings option.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  array $settings Settings array to load, or null of the settings should be loaded from the database.
	 * @return void
	 */
	public function load_option($settings = null)
	{
		if (empty($settings))
		{
			$settings = wp_unslash(get_option(Copy_Nav_Menu_Items_Constants::OPTION_SETTINGS));
		}
		
		if (empty($settings))
		{
			$this->_value_collection = $this;
		}
		else
		{
			$this->_set_properties($settings);
		}
	}

	/**
	 * Add the settings menu item.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function admin_menu()
	{
		$settings_page = add_options_page(Copy_Nav_Menu_Items_Output::page_title($this->page_title), $this->base->cache->plugin_data['Name'], 'manage_options', Copy_Nav_Menu_Items_Constants::OPTION_SETTINGS, array($this, 'settings_page'));

		if ($settings_page)
		{
			Copy_Nav_Menu_Items_Output::add_tab('options-general.php', Copy_Nav_Menu_Items_Constants::OPTION_SETTINGS, $this->page_title);
			Copy_Nav_Menu_Items_Output::add_tab('nav-menus.php', '', __('Nav Menus', 'copy-nav-menu-items'));
			
			add_action('load-' . $settings_page, array($this, 'load_settings_page'));
		}
	}

	/**
	 * Output the settings page.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function settings_page()
	{
		Copy_Nav_Menu_Items_Output::admin_form_page($this->page_title, Copy_Nav_Menu_Items_Constants::HOOK_SAVE_SETTINGS, Copy_Nav_Menu_Items_Constants::OPTION_SETTINGS);
	}

	/**
	 * Load settings page functionality.
	 * 
	 * @since 1.1.2 Removed PHP_INT_MAX reference.
	 * @since 1.1.0 Changed hook priority and added data structure validation.
	 * @since 1.0.1 Changed uninstall setting labels.
	 * @since 1.0.0
	 * 
	 * @access public
	 * @return void
	 */
	public function load_settings_page()
	{
		/**
		 * Setup the screen for Noakes Development Tools.
		 *
		 * @since 1.0.0
		 *
		 * @param  array $suffix Page suffix to use when resetting the screen.
		 * @return void
		 */
		do_action('ndt_screen_setup');
		
		add_action('admin_enqueue_scripts', array('Copy_Nav_Menu_Items_Global', 'admin_enqueue_scripts'), 9999999);
		
		add_screen_option
		(
			'layout_columns',

			array
			(
				'default' => 2,
				'max' => 2
			)
		);

		Copy_Nav_Menu_Items_Help::output('settings');
		
		$this->prepare_meta_boxes();

		Copy_Nav_Menu_Items_Meta_Box::side_meta_boxes();
		Copy_Nav_Menu_Items_Meta_Box::finalize_meta_boxes();
	}
	
	/**
	 * Prepare the settings form meta boxes.
	 * 
	 * @since 1.1.0
	 * 
	 * @access public
	 * @return void
	 */
	public function prepare_meta_boxes()
	{
		$plugin_name = $this->base->cache->plugin_data['Name'];
		
		$uninstall_settings_box = Copy_Nav_Menu_Items_Field_Checkbox::add_confirmation
		(
			new Copy_Nav_Menu_Items_Meta_Box(array
			(
				'context' => 'normal',
				'id' => 'uninstall_settings',
				'option_name' => Copy_Nav_Menu_Items_Constants::OPTION_SETTINGS,
				'title' => __('Uninstall Settings', 'copy-nav-menu-items'),
				'value_collection' => $this->_get_value_collection()
			)),
			
			sprintf
			(
				_x('Delete settings for %1$s when the plugin is uninstalled.', 'Plugin Name', 'copy-nav-menu-items'),
				$plugin_name
			),
			
			__('Delete Settings', 'copy-nav-menu-items'),
			Copy_Nav_Menu_Items_Constants::SETTING_DELETE_SETTINGS,
			$this->{Copy_Nav_Menu_Items_Constants::SETTING_DELETE_SETTINGS}
		);
		
		$uninstall_settings_box = Copy_Nav_Menu_Items_Field_Checkbox::add_confirmation
		(
			$uninstall_settings_box,
			
			sprintf
			(
				_x('Delete user meta for %1$s when the plugin is uninstalled.', 'Plugin Name', 'copy-nav-menu-items'),
				$plugin_name
			),
			
			__('Delete User Meta', 'copy-nav-menu-items'),
			Copy_Nav_Menu_Items_Constants::SETTING_DELETE_USER_META,
			$this->{Copy_Nav_Menu_Items_Constants::SETTING_DELETE_USER_META}
		);
		
		$uninstall_settings_box->add_fields(new Copy_Nav_Menu_Items_Field_Submit(array
		(
			'button_label' => __('Save Settings', 'copy-nav-menu-items')
		)));
	}

	/**
	 * Add settings to the plugin action links.
	 *
	 * @since 1.1.0 Added non-breaking space before dashicon.
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  array $links Existing action links.
	 * @return array        Modified action links.
	 */
	public function plugin_action_links($links)
	{
		array_unshift($links, '<a class="dashicons-before dashicons-admin-tools" href="' . get_admin_url(null, 'options-general.php?page=' . Copy_Nav_Menu_Items_Constants::OPTION_SETTINGS) . '">&nbsp;' . $this->page_title . '</a>');

		return $links;
	}
}
