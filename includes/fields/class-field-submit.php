<?php
/*!
 * Submit field functionality.
 *
 * @since 1.0.0
 *
 * @package    Copy Nav Menu Items
 * @subpackage Submit Field
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the submit field object.
 *
 * @since 1.0.0
 *
 * @uses Copy_Nav_Menu_Items_Field
 */
final class Copy_Nav_Menu_Items_Field_Submit extends Copy_Nav_Menu_Items_Field
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
			 * Additional actions added next to the submit button.
			 *
			 * @since 1.0.1
			 *
			 * @var string
			 */
			case 'additional_actions':
			
				return '';
				
			/**
			 * Label for the submit button.
			 *
			 * @since 1.0.0
			 *
			 * @var string
			 */
			case 'button_label':
			
				return __('Submit', 'copy-nav-menu-items');
		}

		return parent::_default($name);
	}
	
	/**
	 * Generate the output for the submit field.
	 *
	 * @since 1.0.1 Added additional actions functionality.
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  boolean $echo True if the submit field should be echoed.
	 * @return string        Generated submit field if $echo is false.
	 */
	public function output($echo = false)
	{
		return parent::_output
		(
			'<div class="cnmi-field-actions">'
				. '<button class="button button-large button-primary cnmi-button' . $this->_field_classes(false) . '"' . $this->input_attributes . ' disabled="disabled" type="submit"><span>' . $this->button_label . '</span></button>'
				. $this->additional_actions
			. '</div>',
			
			'submit',
			$echo
		);
	}
}
