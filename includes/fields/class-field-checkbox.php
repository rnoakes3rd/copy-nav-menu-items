<?php
/*!
 * Checkbox field functionality.
 *
 * @since 1.0.0
 *
 * @package    Copy Nav Menu Items
 * @subpackage Checkbox Field
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the checkbox field object.
 *
 * @since 1.0.0
 *
 * @uses Copy_Nav_Menu_Items_Field
 */
final class Copy_Nav_Menu_Items_Field_Checkbox extends Copy_Nav_Menu_Items_Field
{
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
			 * Custom value for a checkbox field.
			 *
			 * @since 1.0.0
			 *
			 * @var string
			 */
			case 'checkbox_value':
			
			/**
			 * Label displayed next to the checkbox.
			 *
			 * @since 1.0.0
			 *
			 * @var string
			 */
			case 'field_label':
			
				return '';
		}

		return parent::_default($name);
	}
	
	/**
	 * Generate the output for the checkbox field.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  boolean $echo True if the checkbox field should be echoed.
	 * @return string        Generated checkbox field if $echo is false.
	 */
	public function output($echo = false)
	{
		$output = '';
		
		if (!empty($this->id))
		{
			if ($this->checkbox_value === '')
			{
				$this->checkbox_value = '1';
			}

			if (!empty($this->value))
			{
				$this->value = $this->checkbox_value;
			}
			
			$output = '<label class="cnmi-description">'
				. '<input ' . checked($this->checkbox_value, $this->value, false) . ' ' . $this->_field_classes() . $this->input_attributes . ' type="checkbox" value="' . esc_attr($this->checkbox_value) . '" />'
				. $this->field_label
			. '</label>';
		}
		
		return parent::_output($output, 'checkbox', $echo);
	}
	
	/**
	 * Add confirmation fields to a meta box.
	 *
	 * @since 1.0.0
	 *
	 * @access public static
	 * @param  Copy_Nav_Menu_Items_Meta_Box $meta_box    Meta box object to add the confirmation fields to.
	 * @param  string                       $field_label Label displayed next to the confirmation checkboxes.
	 * @param  string                       $label       Primary label for the checkbox fields.
	 * @param  string                       $name        Name of the main confirmation field.
	 * @param  string                       $value       Value for the main confirmation field.
	 * @return Copy_Nav_Menu_Items_Meta_Box              Meta box with the added confirmation fields.
	 */
	public static function add_confirmation($meta_box, $field_label, $label, $name, $value)
	{
		$confirmed = (!empty($value));

		$unconfirmed = new Copy_Nav_Menu_Items_Field_Checkbox(array
		(
			'field_label' => $field_label,
			'label' => $label,
			'name' => $name . Copy_Nav_Menu_Items_Constants::SETTING_UNCONFIRMED,
			'sanitization' => Copy_Nav_Menu_Items_Sanitization::EXCLUDE,

			'wrapper_classes' => ($confirmed)
			? 'cnmi-hidden'
			: ''
		));
		
		$meta_box->add_fields(array
		(
			$unconfirmed,
			
			new Copy_Nav_Menu_Items_Field_Checkbox(array
			(
				'field_label' => $field_label,
				'name' => $name,
				'sanitization' => Copy_Nav_Menu_Items_Sanitization::CONFIRMATION,

				'conditions' => ($confirmed)
				? array()
				: array
				(
					array
					(
						'field' => $unconfirmed,
						'value' => '1'
					)
				),

				'label' => ($confirmed)
				? $label
				: sprintf
				(
					_x('Confirm %1$s', 'Label', 'copy-nav-menu-items'),
					$label
				),

				'wrapper_classes' => ($confirmed)
				? ''
				: 'cnmi-confirmation cnmi-hidden'
			))
		));
		
		return $meta_box;
	}
}
