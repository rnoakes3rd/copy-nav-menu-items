<?php
/*!
 * Global plugin hooks.
 *
 * @since 1.0.0
 *
 * @package    Copy Nav Menu Items
 * @subpackage Global
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement global hooks.
 *
 * @since 1.0.0
 */
final class Copy_Nav_Menu_Items_Global
{
	/**
	 * Enqueue plugin assets.
	 *
	 * @since 1.0.1 Added AJAX script options.
	 * @since 1.0.0
	 *
	 * @access public static
	 * @param  string $hook_suffix The current admin page.
	 * @return void
	 */
	public static function admin_enqueue_scripts($hook_suffix)
	{
		$cnmi = Copy_Nav_Menu_Items();
		
		wp_enqueue_style('noatice', $cnmi->cache->asset_path('styles', 'noatice.css'), array(), Copy_Nav_Menu_Items_Constants::VERSION);
		wp_enqueue_style('cnmi-style', $cnmi->cache->asset_path('styles', 'style.css'), array('noatice'), Copy_Nav_Menu_Items_Constants::VERSION);
		
		wp_enqueue_script('noatice', $cnmi->cache->asset_path('scripts', 'noatice.js'), array(), Copy_Nav_Menu_Items_Constants::VERSION, true);
		wp_enqueue_script('cnmi-script', $cnmi->cache->asset_path('scripts', 'script.js'), array('noatice', 'postbox'), Copy_Nav_Menu_Items_Constants::VERSION, true);
		
		$options = array
		(
			'admin_page' => $cnmi->cache->admin_page,
			'noatices' => Copy_Nav_Menu_Items_Noatice::output(),
			'option_name' => $cnmi->cache->option_name,
			'token' => Copy_Nav_Menu_Items_Constants::TOKEN,

			'strings' => array
			(
				'copy' => __('Copy Nav Menu Item', 'copy-nav-menu-items'),
				'save_alert' => __('The changes you made will be lost if you navigate away from this page.', 'copy-nav-menu-items')
			),

			'urls' => array
			(
				'ajax' => admin_url('admin-ajax.php'),
				'current' => remove_query_arg($cnmi->cache->get_remove_query_args())
			)
		);
		
		if ($hook_suffix === 'nav-menus.php')
		{
			$options['copy'] = array
			(
				'action' => Copy_Nav_Menu_Items_Constants::HOOK_COPY_NAV_MENU_ITEM,
				'nonce' => wp_create_nonce(Copy_Nav_Menu_Items_Constants::HOOK_COPY_NAV_MENU_ITEM)
			);
		}
		
		wp_localize_script('cnmi-script', 'cnmi_script_options', $options);
	}
}
