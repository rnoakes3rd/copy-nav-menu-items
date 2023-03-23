<?php
/*!
 * Functionality for plugin help.
 *
 * @since 1.0.0
 *
 * @package    Copy Nav Menu Items
 * @subpackage Help
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement plugin help functionality.
 *
 * @since 1.0.0
 */
final class Copy_Nav_Menu_Items_Help
{
	/**
	 * Output the help tabs.
	 *
	 * @since 1.1.0 Removed 'noreferrer' from links.
	 * @since 1.0.1 Help tab ID change.
	 * @since 1.0.0
	 *
	 * @access public static
	 * @param  string  $kb_path     Path to the knowledge base article associated with this help tab.
	 * @param  boolean $plugin_page True if the help tab is being added to a plugin-specific page.
	 * @return void
	 */
	public static function output($kb_path, $plugin_page = true)
	{
		$cnmi = Copy_Nav_Menu_Items();
		
		if
		(
			!empty($kb_path)
			&&
			!$cnmi->cache->doing_ajax
		)
		{
			$id = 'cnmi-' . $cnmi->cache->option_name;
			
			if ($plugin_page === true)
			{
				$cnmi->cache->screen->set_help_sidebar('<p><strong>' . __('Plugin developed by', 'copy-nav-menu-items') . '</strong><br />'
				. '<a href="https://robertnoakes.com/" rel="noopener" target="_blank">Robert Noakes</a></p>'
				. '<hr />'
				. '<p><a class="button" href="' . Copy_Nav_Menu_Items_Constants::URL_SUPPORT . '" rel="noopener" target="_blank">' . __('Plugin Support', 'copy-nav-menu-items') . '</a></p>'
				. '<p><a class="button" href="' . Copy_Nav_Menu_Items_Constants::URL_REVIEW . '" rel="noopener" target="_blank">' . __('Review Plugin', 'copy-nav-menu-items') . '</a></p>'
				. '<p><a class="button" href="' . Copy_Nav_Menu_Items_Constants::URL_TRANSLATE . '" rel="noopener" target="_blank">' . __('Translate Plugin', 'copy-nav-menu-items') . '</a></p>'
				. '<p><a class="button" href="' . Copy_Nav_Menu_Items_Constants::URL_DONATE . '" rel="noopener" target="_blank">' . __('Plugin Donation', 'copy-nav-menu-items') . '</a></p>');
			}
			else if ($plugin_page !== false)
			{
				$id .= $plugin_page;
			}
			
			$url = Copy_Nav_Menu_Items_Constants::URL_KB . $kb_path . '/';
			
			$cnmi->cache->screen->add_help_tab(array
			(
				'id' => $id,
				'priority' => 20,
				'title' => $cnmi->cache->plugin_data['Name'],
				
				'content' => '<h3>' . __('For more information about this page, view the knowledge base article at:', 'copy-nav-menu-items') . '<br />'
				. '<a href="' . esc_url($url) . '" rel="noopener" target="_blank">' . $url . '</a></h3>'
			));
		}
	}
}
