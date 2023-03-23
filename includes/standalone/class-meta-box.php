<?php
/*!
 * Meta box functionality.
 *
 * @since 1.0.0
 *
 * @package    Copy Nav Menu Items
 * @subpackage Meta Box
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the meta box object.
 *
 * @since 1.0.0
 *
 * @uses Copy_Nav_Menu_Items_Wrapper
 */
final class Copy_Nav_Menu_Items_Meta_Box extends Copy_Nav_Menu_Items_Wrapper
{
	/**
	 * Constructor function.
	 *
	 * @since 1.1.0 Added data structure validation.
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  array $properties Properties for the meta box.
	 * @return void
	 */
	public function __construct($properties)
	{
		parent::__construct($properties);

		if
		(
			is_callable($this->callback)
			&&
			!empty($this->id)
			&&
			$this->title !== ''
		)
		{
			if ($this->base->cache->doing_ajax)
			{
				add_filter(Copy_Nav_Menu_Items_Constants::HOOK_VALIDATE_DATA, array($this, 'validate_data'));
			}
			else
			{
				$this->id = Copy_Nav_Menu_Items_Constants::TOKEN . '_meta_box_' . $this->id;

				add_action('add_meta_boxes', array($this, 'add_meta_box'));
				add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 0);
			}
		}
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
			 * Function used to populate the meta box.
			 *
			 * @since 1.0.0
			 *
			 * @var function
			 */
			case 'callback':
			
				return array($this, 'callback');

			/**
			 * Data that should be set as the $args property of the box array.
			 *
			 * @since 1.0.0
			 *
			 * @var array
			 */
			case 'callback_args':
			
				return null;

			/**
			 * CSS classes added to the meta box.
			 *
			 * @since 1.0.0
			 *
			 * @var array
			 */
			case 'classes':
			
			/**
			 * Fields displayed in the meta box.
			 *
			 * @since 1.0.0
			 *
			 * @var array
			 */
			 case 'fields':
			
			/**
			 * Value collection for the fields displayed in the meta box.
			 *
			 * @since 1.0.0
			 *
			 * @var mixed
			 */
			case 'value_collection':
			
				return array();

			/**
			 * Context within the screen where the boxes should display.
			 *
			 * @since 1.0.0
			 *
			 * @var string
			 */
			case 'context':
			
				return 'advanced';

			/**
			 * Base ID for the meta box.
			 *
			 * @since 1.0.0
			 *
			 * @var string
			 */
			case 'id':
			
			/**
			 * Title displayed in the meta box.
			 *
			 * @since 1.0.0
			 *
			 * @var string
			 */
			case 'title':
			
				return '';
			
			/**
			 * Option name for the fields in the meta box.
			 *
			 * @since 1.0.0
			 *
			 * @var string
			 */
			case 'option_name':
			
				return Copy_Nav_Menu_Items_Constants::TOKEN;

			/**
			 * Priority within the context where the boxes should show.
			 *
			 * @since 1.0.0
			 *
			 * @var string
			 */
			case 'priority':
			
				return 'default';
		}
		
		return parent::_default($name);
	}
	
	/**
	 * Validate data associated with this meta box.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 * @param  array $valid_data Existing validated data.
	 * @return array             Modified validated data.
	 */
	public function validate_data($valid_data)
	{
		if
		(
			!empty($this->option_name)
			&&
			isset($_POST[$this->option_name])
			&&
			is_array($_POST[$this->option_name])
		)
		{
			foreach ($this->fields as $field)
			{
				$valid_data = array_merge_recursive($valid_data, $field->validate_data($_POST[$this->option_name]));
			}
		}
		
		return $valid_data;
	}

	/**
	 * Add the meta box to the page.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function add_meta_box()
	{
		$title = esc_html($this->title);
		
		add_meta_box($this->id, $title, $this->callback, $this->base->cache->screen, $this->context, $this->priority, $this->callback_args);

		add_filter('postbox_classes_' . esc_attr($this->base->cache->screen->id) . '_' . esc_attr($this->id), array($this, 'postbox_classes'));
	}

	/**
	 * The default callback that is fired for the meta box when one isn't provided.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function callback()
	{
		echo '<div class="cnmi-field-wrapper">';
		
		foreach ($this->fields as $field)
		{
			$field->output(true);
		}
		
		echo '</div>';

		wp_nonce_field($this->id, $this->id . '_nonce', false);
	}

	/**
	 * Add additional classes to a meta box.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  array $classes Current meta box classes.
	 * @return array          Modified meta box classes.
	 */
	public function postbox_classes($classes)
	{
		$add_classes = Copy_Nav_Menu_Items_Utilities::check_array($this->classes);
		
		array_unshift($add_classes, 'cnmi-meta-box');

		return array_merge($classes, $add_classes);
	}

	/**
	 * Verify and setup the meta box fields.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function admin_enqueue_scripts()
	{
		$fields = (is_array($this->fields))
		? $this->fields
		: array();
		
		$verified_fields = array();
		
		foreach ($fields as $field)
		{
			if (Copy_Nav_Menu_Items_Utilities::is_field($field))
			{
				$field->option_name = $this->option_name;
				$field->value_collection = $this->value_collection;
				
				$verified_fields[] = $field;
			}
		}
		
		$this->fields = $verified_fields;
	}
	
	/**
	 * Add one or more field to the meta box.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  mixed $fields Field object or an array of field objects to add to the meta box.
	 * @return void
	 */
	public function add_fields($fields)
	{
		$fields = Copy_Nav_Menu_Items_Utilities::check_array($fields);
		
		foreach ($fields as $field)
		{
			$this->push('fields', $field);
		}
	}

	/**
	 * Generate the side meta boxes.
	 *
	 * @since 1.1.0 Removed 'noreferrer' from links.
	 * @since 1.0.0
	 *
	 * @access public static
	 * @return void
	 */
	public static function side_meta_boxes()
	{
		$cnmi = Copy_Nav_Menu_Items();
		
		new self(array
		(
			'classes' => array('cnmi-meta-box-locked'),
			'context' => 'side',
			'id' => 'support',
			'title' => __('Support', 'copy-nav-menu-items'),
			
			'fields' => array
			(
				new Copy_Nav_Menu_Items_Field_HTML(array
				(
					'content' => '<strong>' . __('Plugin developed by', 'copy-nav-menu-items') . '</strong><br />'
					. '<a href="https://robertnoakes.com/" rel="noopener" target="_blank"><img alt="Robert Noakes" height="62" src="' . $cnmi->cache->asset_path('images', 'robert-noakes.png') . '" width="514" /></a>'
				)),
				
				new Copy_Nav_Menu_Items_Field_HTML(array
				(
					'content' => '<strong>' . __('Knowledge base available on', 'copy-nav-menu-items') . '</strong><br />'
					. '<a href="' . Copy_Nav_Menu_Items_Constants::URL_KB . '" rel="noopener" target="_blank"><img alt="Noakes Plugins" height="75" src="' . $cnmi->cache->asset_path('images', 'noakes-plugins.png') . '" width="514" /></a>'
				)),
				
				new Copy_Nav_Menu_Items_Field_HTML(array
				(
					'content' => __('Running into issues with the plugin?', 'copy-nav-menu-items') . '<br />'
					. '<a href="' . Copy_Nav_Menu_Items_Constants::URL_SUPPORT . '" rel="noopener" target="_blank"><strong>' . __('Submit a ticket.', 'copy-nav-menu-items') . '</strong></a>'
				)),
				
				new Copy_Nav_Menu_Items_Field_HTML(array
				(
					'content' => __('Have some feedback you\'d like to share?', 'copy-nav-menu-items') . '<br />'
					. '<a href="' . Copy_Nav_Menu_Items_Constants::URL_REVIEW . '" rel="noopener" target="_blank"><strong>' . __('Provide a review.', 'copy-nav-menu-items') . '</strong></a>'
				)),
				
				new Copy_Nav_Menu_Items_Field_HTML(array
				(
					'content' => __('Want to see the plugin in your language?', 'copy-nav-menu-items') . '<br />'
					. '<a href="' . Copy_Nav_Menu_Items_Constants::URL_TRANSLATE . '" rel="noopener" target="_blank"><strong>' . __('Assist with translation.', 'copy-nav-menu-items') . '</strong></a>'
				)),
				
				new Copy_Nav_Menu_Items_Field_HTML(array
				(
					'content' => __('Would you like to support development?', 'copy-nav-menu-items') . '<br />'
					. '<strong>'
						. sprintf
						(
							_x('Sign up for WPEngine using the banner in the \'Better Hosting with WPEngine\' meta box or %1$s.', 'Donate Link', 'copy-nav-menu-items'),
							'<a href="' . Copy_Nav_Menu_Items_Constants::URL_DONATE . '" rel="noopener" target="_blank">' . __('make a donation', 'copy-nav-menu-items') . '</a>'
						)
					. '</strong>'
				))
			)
		));

		new self(array
		(
			'classes' => array('cnmi-meta-box-locked'),
			'context' => 'normal',
			'id' => 'advertising',
			'title' => __('Better Hosting with WPEngine', 'copy-nav-menu-items'),
			
			'fields' => array
			(
				new Copy_Nav_Menu_Items_Field_HTML(array
				(
					'content' => '<a href="https://shareasale.com/r.cfm?b=1144535&amp;u=1815763&amp;m=41388&amp;urllink=&amp;afftrack=" rel="noopener" target="_blank">'
						. '<img alt="WPEngine - Your WordPress Digital Experience Platform. Get 3 months free with annual plan purchases. - LEARN MORE" border="0" class="cnmi-banner-tall" src="' . $cnmi->cache->asset_path('images', 'YourWordPressDXP300x600.png') . '" />'
						. '<img alt="WPEngine - High performance WordPress hosting that just works. Get 3 months free with annual plan purchases - LEARN MORE" border="0" class="cnmi-banner-wide" src="' . $cnmi->cache->asset_path('images', 'YourWordPressDXP728x90.png') . '" />'
					. '</a>'
				))
			)
		));
	}

	/**
	 * Finalize the meta boxes.
	 *
	 * @since 1.1.2 Removed PHP_INT_MAX reference.
	 * @since 1.0.0
	 *
	 * @access public static
	 * @return void
	 */
	public static function finalize_meta_boxes()
	{
		add_action('add_meta_boxes', array(__CLASS__, 'remove_meta_boxes'), 9999999);
		do_action('add_meta_boxes', Copy_Nav_Menu_Items()->cache->screen->id, null);
	}

	/**
	 * Remove unnecessary meta boxes.
	 *
	 * @since 1.0.0
	 *
	 * @access public static
	 * @return void
	 */
	public static function remove_meta_boxes()
	{
		$cnmi = Copy_Nav_Menu_Items();

		remove_meta_box('eg-meta-box', $cnmi->cache->screen->id, 'normal');
		remove_meta_box('mymetabox_revslider_0', $cnmi->cache->screen->id, 'normal');
	}
}
