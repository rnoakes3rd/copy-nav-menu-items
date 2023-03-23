<?php
/*!
 * AJAX functionality.
 *
 * @since 1.0.0
 *
 * @package    Copy Nav Menu Items
 * @subpackage AJAX
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the AJAX functionality.
 *
 * @since 1.0.0
 *
 * @uses Copy_Nav_Menu_Items_Wrapper
 */
final class Copy_Nav_Menu_Items_AJAX extends Copy_Nav_Menu_Items_Wrapper
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
		
		if ($this->base->cache->doing_ajax)
		{
			add_action('wp_ajax_' . Copy_Nav_Menu_Items_Constants::HOOK_COPY_NAV_MENU_ITEM, array($this, 'copy_nav_menu_item'));
			add_action('wp_ajax_' . Copy_Nav_Menu_Items_Constants::HOOK_SAVE_SETTINGS, array($this, 'save_settings'));
		}
		else
		{
			$query_arg = '';
			
			if (isset($_GET[Copy_Nav_Menu_Items_Constants::HOOK_SAVE_SETTINGS]))
			{
				$query_arg = Copy_Nav_Menu_Items_Constants::HOOK_SAVE_SETTINGS;
				
				Copy_Nav_Menu_Items_Noatice::add_success(__('Settings saved successfully.', 'copy-nav-menu-items'));
			}
			
			if (!empty($query_arg))
			{
				$this->base->cache->push('remove_query_args', $query_arg);
			}
		}
	}
	
	/**
	 * Copy a nav menu item.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function copy_nav_menu_item()
	{
		if ($this->_invalid_submission(Copy_Nav_Menu_Items_Constants::HOOK_COPY_NAV_MENU_ITEM, 'edit_theme_options'))
		{
			$this->_send_error(__('You are not authorized to copy nav menu items.', 'copy-nav-menu-items'), 403);
		}
		else if
		(
			!isset($_POST['menu'])
			||
			!isset($_POST['args'])
			||
			!is_array($_POST['args'])
			||
			!isset($_POST['args']['menu-item-title'])
		)
		{
			$this->_send_error(__('Nav menu item could not be copied.', 'copy-nav-menu-items'));
		}
		
		require_once(ABSPATH . 'wp-admin/includes/nav-menu.php');
		
		$args = Copy_Nav_Menu_Items_Sanitization::sanitize(array
		(
			Copy_Nav_Menu_Items_Sanitization::CLASSES => array
			(
				'menu-item-classes' => (isset($_POST['args']['menu-item-classes']))
				? $_POST['args']['menu-item-classes']
				: ''
			),

			Copy_Nav_Menu_Items_Sanitization::NUMBER => array
			(
				'menu-item-object-id' => (isset($_POST['args']['menu-item-object-id']))
				? $_POST['args']['menu-item-object-id']
				: 0,

				'menu-item-parent-id' => (isset($_POST['args']['menu-item-parent-id']))
				? $_POST['args']['menu-item-parent-id']
				: 0,

				'menu-item-position' => (isset($_POST['args']['menu-item-position']))
				? $_POST['args']['menu-item-position']
				: 0
			),

			Copy_Nav_Menu_Items_Sanitization::SLUG => array
			(
				'menu-item-object' => (isset($_POST['args']['menu-item-object']))
				? $_POST['args']['menu-item-object']
				: '',

				'menu-item-type' => (isset($_POST['args']['menu-item-type']))
				? $_POST['args']['menu-item-type']
				: ''
			),

			Copy_Nav_Menu_Items_Sanitization::TEXT => array
			(
				'menu-item-title' => (isset($_POST['args']['menu-item-title']))
				? $_POST['args']['menu-item-title']
				: '',

				'menu-item-attr-title' => (isset($_POST['args']['menu-item-attr-title']))
				? $_POST['args']['menu-item-attr-title']
				: '',

				'menu-item-target' => (isset($_POST['args']['menu-item-target']))
				? $_POST['args']['menu-item-target']
				: '',

				'menu-item-xfn' => (isset($_POST['args']['menu-item-xfn']))
				? $_POST['args']['menu-item-xfn']
				: ''
			),

			Copy_Nav_Menu_Items_Sanitization::TEXTAREA => array
			(
				'menu-item-description' => (isset($_POST['args']['menu-item-description']))
				? $_POST['args']['menu-item-description']
				: ''
			),

			Copy_Nav_Menu_Items_Sanitization::URL => array
			(
				'menu-item-url' => (isset($_POST['args']['menu-item-url']))
				? $_POST['args']['menu-item-url']
				: ''
			)
		));
		
		$copy_failed = $item_ids = false;
		$walker_class = apply_filters('wp_edit_nav_menu_walker', 'Walker_Nav_Menu_Edit', $_POST['menu']);
		
		if (!class_exists($walker_class))
		{
			$copy_failed = true;
		}
		
		if (!$copy_failed)
		{
			$args['menu-item-db-id'] = 0;
			$item_ids = wp_save_nav_menu_items(0, array($args));

			if (is_wp_error($item_ids))
			{
				$copy_failed = true;
			}
		}
		
		if ($copy_failed)
		{
			$this->_send_error(sprintf
			(
				_x('%1$s nav menu item could not be copied.', 'Nav Menu Item Title', 'copy-nav-menu-items'),
				$args['menu-item-title']
			));
		}
		else
		{
			$menu_items = array();
			
			foreach ($item_ids as $item_id)
			{
				$menu_item = get_post($item_id);
				
				if (!empty($menu_item->ID))
				{
					$menu_item = wp_setup_nav_menu_item($menu_item);
					$menu_item->label = $menu_item->title;
					
					$menu_items[] = $menu_item;
				}
			}
			
			wp_send_json_success(array
			(
				'copied' => walk_nav_menu_tree
				(
					$menu_items,
					0,
					
					(object)array
					(
						'after' => '',
						'before' => '',
						'link_after' => '',
						'link_before' => '',
						'walker' => new $walker_class,
					)
				),

				'noatice' => Copy_Nav_Menu_Items_Noatice::generate_success(sprintf
				(
					_x('%1$s nav menu item copied successfully.', 'Nav Menu Item Title', 'copy-nav-menu-items'),
					$args['menu-item-title']
				))
			));
		}
	}
	
	/**
	 * Save the plugin settings.
	 *
	 * @since 1.1.2 Improved query argument.
	 * @since 1.1.0 Added data structure validation.
	 * @since 1.0.1 Improved structure.
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function save_settings()
	{
		if ($this->_invalid_submission(Copy_Nav_Menu_Items_Constants::HOOK_SAVE_SETTINGS))
		{
			$this->_send_error(__('You are not authorized to save settings.', 'copy-nav-menu-items'), 403);
		}
		else if ($this->_invalid_redirect())
		{
			$this->_send_error(__('Settings could not be saved.', 'copy-nav-menu-items'));
		}
		
		$this->base->settings->prepare_meta_boxes();
		
		$option_name = sanitize_key($_POST['option-name']);
		
		update_option($option_name, Copy_Nav_Menu_Items_Sanitization::sanitize
		(
			/**
			 * Validate the data for the settings form.
			 *
			 * @since 1.1.0
			 *
			 * @param array $valid_data Validated data.
			 */
			apply_filters(Copy_Nav_Menu_Items_Constants::HOOK_VALIDATE_DATA, array())
		));
		
		wp_send_json_success(array
		(
			'url' => add_query_arg
			(
				array
				(
					'page' => $option_name,
					Copy_Nav_Menu_Items_Constants::HOOK_SAVE_SETTINGS => 1
				),
				
				admin_url(sanitize_text_field($_POST['admin-page']))
			)
		));
	}
	
	/**
	 * Check for invalid redirect data.
	 *
	 * @since 1.0.1
	 *
	 * @access private
	 * @return boolean True if the required redirect data is missing.
	 */
	private function _invalid_redirect()
	{
		return
		(
			!isset($_POST['admin-page'])
			||
			empty($_POST['admin-page'])
			||
			!isset($_POST['option-name'])
			||
			empty($_POST['option-name'])
		);
	}
	
	/**
	 * Check for an invalid submission.
	 *
	 * @since 1.0.1
	 *
	 * @access private
	 * @param  string $action     AJAX action to verify the nonce for.
	 * @param  string $capability User capability required to complete the submission.
	 * @return boolean            True if the submission is invalid.
	 */
	private function _invalid_submission($action, $capability = 'manage_options')
	{
		return
		(
			!check_ajax_referer($action, false, false)
			||
			(
				!empty($capability)
				&&
				!current_user_can($capability)
			)
		);
	}
	
	/**
	 * Send a general error message.
	 * 
	 * @since 1.0.1 Added status code argument.
	 * @since 1.0.0
	 * 
	 * @access private
	 * @param  string  $message     Message displayed in the error noatice.
	 * @param  integer $status_code HTTP status code to send with the error.
	 * @return void
	 */
	private function _send_error($message, $status_code = null)
	{
		wp_send_json_error
		(
			array
			(
				'noatice' => Copy_Nav_Menu_Items_Noatice::generate_error($message)
			),
			
			$status_code
		);
	}
}
