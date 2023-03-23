<?php
/*!
 * Nav menus functionality.
 *
 * @since 1.0.0
 *
 * @package    Copy Nav Menu Items
 * @subpackage Nav Menus
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the nav menus functionality.
 *
 * @since 1.0.0
 *
 * @uses Copy_Nav_Menu_Items_Wrapper
 */
final class Copy_Nav_Menu_Items_Nav_Menus extends Copy_Nav_Menu_Items_Wrapper
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

		add_action('load-nav-menus.php', array($this, 'load_nav_menus'));
	}

	/**
	 * Load nav menus page functionality.
	 * 
	 * @since 1.1.2 Removed PHP_INT_MAX reference.
	 * @since 1.1.0 Changed hook priority.
	 * @since 1.0.0
	 * 
	 * @access public
	 * @return void
	 */
	public function load_nav_menus()
	{
		add_action('admin_enqueue_scripts', array('Copy_Nav_Menu_Items_Global', 'admin_enqueue_scripts'), 9999999);
		
		Copy_Nav_Menu_Items_Help::output('nav-menus', false);
	}
}
