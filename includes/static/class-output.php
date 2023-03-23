<?php
/*!
 * Plugin output functionality.
 *
 * @since 1.0.0
 *
 * @package    Copy Nav Menu Items
 * @subpackage Output
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the output functionality.
 *
 * @since 1.0.0
 */
final class Copy_Nav_Menu_Items_Output
{
	/**
	 * Admin page tabs.
	 *
	 * @since 1.0.0
	 *
	 * @access private static
	 * @var    array
	 */
	private static $_tabs = array();

	/**
	 * Add an admin page tab.
	 *
	 * @since 1.0.0
	 *
	 * @access public static
	 * @param  string $menu_parent Parent page for the admin page.
	 * @param  string $menu_slug   Menu slug for the admin page.
	 * @param  string $page_title  Title for the admin page tab.
	 * @return void
	 */
	public static function add_tab($menu_parent, $menu_slug, $title)
	{
		if (!empty($menu_parent))
		{
			$url = admin_url($menu_parent);

			if (!empty($menu_slug))
			{
				$url = add_query_arg('page', $menu_slug, $url);
			}

			self::$_tabs[] = array
			(
				'title' => $title,
				'url' => $url,

				'active_class' => ($menu_slug === Copy_Nav_Menu_Items()->cache->option_name)
				? ' cnmi-tab-active'
				: ''
			);
		}
	}

	/**
	 * Output an admin form page.
	 *
	 * @since 1.0.1 Changed admin page output.
	 * @since 1.0.0
	 *
	 * @access public static
	 * @param  string $heading     Heading displayed at the top of the admin form page.
	 * @param  string $action      AJAX action to request on form submission.
	 * @param  string $option_name Option name to generate the admin form page for.
	 * @return void
	 */
	public static function admin_form_page($heading, $action = '', $option_name = '')
	{
		$cnmi = Copy_Nav_Menu_Items();
		
		echo '<div class="wrap">';

		self::admin_nav_bar($heading);

		$screen = $cnmi->cache->screen;
		$columns = $screen->get_columns();
		
		if (empty($columns))
		{
			$columns = 2;
		}

		echo '<form method="post" id="cnmi-form">'
			. '<input name="admin-page" type="hidden" value="' . esc_attr($cnmi->cache->admin_page) . '" />';
		
		if (!empty($action))
		{
			$action = sanitize_key($action);
			
			echo '<input name="action" type="hidden" value="' . $action . '" />';
			
			wp_nonce_field($action);
		}

		if (!empty($option_name))
		{
			echo '<input name="option-name" type="hidden" value="' . sanitize_key($option_name) . '" />';
		}

		wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
		wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);

		echo '<div id="poststuff">'
			. '<div id="post-body" class="metabox-holder columns-' . $columns . '">'
			. '<div id="postbox-container-1" class="postbox-container">';

		do_meta_boxes($screen->id, 'side', '');

		echo '</div>'
		. '<div id="cnmi-primary-wrapper">'
		. '<div id="postbox-container-2" class="postbox-container">';

		do_meta_boxes($screen->id, 'advanced', '');
		do_meta_boxes($screen->id, 'normal', '');

		echo '</div>'
						. '</div>'
						. '<div class="cnmi-clear"></div>'
					. '</div>'
				. '</div>'
			. '</form>'
		. '</div>';
	}

	/**
	 * Output the admin page nav bar.
	 *
	 * @since 1.0.0
	 *
	 * @access public static
	 * @param  string $heading Heading displayed in the nav bar.
	 * @return void
	 */
	public static function admin_nav_bar($heading)
	{
		$cnmi = Copy_Nav_Menu_Items();
		$buttons = '';
		
		echo '<div class="cnmi-nav">'
			. '<div class="cnmi-nav-title">'
				. '<h1>'
					. '<strong>' . $cnmi->cache->plugin_data['Name'] . '</strong> | ' . $heading
				. '</h1>'
				. '<div class="cnmi-clear"></div>'
			. '</div>';
		
		if (count(self::$_tabs) > 1)
		{
			echo '<div class="cnmi-tab-wrapper">';

			foreach (self::$_tabs as $tab)
			{
				echo '<a class="cnmi-tab' . $tab['active_class'] . '" href="' . $tab['url'] . '">' . $tab['title'] . '</a>';
			}

			echo '</div>';
		}

		echo '</div>'
		. '<hr class="wp-header-end" />';
	}
	
	/**
	 * Add the plugin name to a page title.
	 *
	 * @since 1.0.0
	 *
	 * @access public static
	 * @param  string $page_title Current page title.
	 * @return string             Modified page title.
	 */
	public static function page_title($page_title)
	{
		return $page_title . ' &#0139; ' . Copy_Nav_Menu_Items()->cache->plugin_data['Name'];
	}
}
