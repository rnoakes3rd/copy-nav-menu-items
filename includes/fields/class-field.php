<?php
/*!
 * Base field functionality.
 *
 * @since 1.0.0
 *
 * @package    Copy Nav Menu Items
 * @subpackage Field
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Abstract class used to implement the base field object.
 *
 * @since 1.0.0
 *
 * @uses Copy_Nav_Menu_Items_Wrapper
 */
abstract class Copy_Nav_Menu_Items_Field extends Copy_Nav_Menu_Items_Wrapper
{
	/**
	 * Object names that must contain array values.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 * @var    array
	 */
	protected $_array_only = array('attributes', 'conditions', 'fields', 'value_collection', 'wrapper_attributes');
	
	/**
	 * Object names that may contain array or string values.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 * @var    array
	 */
	protected $_array_or_string = array('classes', 'wrapper_classes');
	
	/**
	 * Constructor function.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  array $properties Properties for the field.
	 * @return void
	 */
	public function __construct($properties = array())
	{
		parent::__construct($properties);
		
		foreach ($this->_array_only as $name)
		{
			if (!is_array($this->{$name}))
			{
				$this->{$name} = array();
			}
		}
		
		foreach ($this->_array_or_string as $name)
		{
			$this->{$name} = Copy_Nav_Menu_Items_Utilities::check_array($this->{$name});
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
			 * Additional attributes to add to the field.
			 *
			 * @since 1.0.0
			 *
			 * @var array
			 */
			case 'attributes':
			
			/**
			 * CSS classes added to the field input element.
			 *
			 * @since 1.0.0
			 *
			 * @var array
			 */
			case 'classes':
			
			/**
			 * Conditions for a field to be visible.
			 *
			 * @since 1.0.0
			 *
			 * @var array
			 */
			case 'conditions':
			
			/**
			 * Child fields for this field.
			 *
			 * @since 1.0.0
			 *
			 * @var array
			 */
			case 'fields':
			
			/**
			 * Value collection containing the values for all meta box fields.
			 *
			 * @since 1.0.0
			 *
			 * @var mixed
			 */
			case 'value_collection':
			
			/**
			 * Attributes for the field wrapper.
			 *
			 * @since 1.0.0
			 *
			 * @var array
			 */
			case 'wrapper_attributes':
			
			/**
			 * CSS classes added to the field wrapper.
			 *
			 * @since 1.0.0
			 *
			 * @var array
			 */
			case 'wrapper_classes':
			
				return array();
			
			/**
			 * Conditions output generated by other fields.
			 *
			 * @since 1.0.0
			 *
			 * @var string
			 */
			case 'conditions_output':
			
			/**
			 * Short description display with the field.
			 *
			 * @since 1.0.0
			 *
			 * @var string
			 */
			case 'description':
			
			/**
			 * Output label displayed with the field.
			 *
			 * @since 1.0.0
			 *
			 * @var string
			 */
			case 'label':
			
			/**
			 * Base name for the field.
			 *
			 * @since 1.0.0
			 *
			 * @var string
			 */
			case 'name':
			
			/**
			 * Field option name.
			 *
			 * @since 1.0.0
			 *
			 * @var string
			 */
			case 'option_name':
			
				return '';

			/**
			 * True if the labels should be hidden from the field output.
			 *
			 * @since 1.0.0
			 *
			 * @var boolean
			 */
			case 'hide_labels':
			
			/**
			 * True if a clear DIV should be added after this field.
			 *
			 * @since 1.0.0
			 *
			 * @var boolean
			 */
			case 'include_clear':
			
			/**
			 * True if the field is tall and the description should be displayed below the label.
			 *
			 * @since 1.0.0
			 *
			 * @var boolean
			 */
			case 'is_tall':
			
			/**
			 * True if the current field is a template.
			 *
			 * @since 1.0.0
			 *
			 * @var boolean
			 */
			case 'is_template':
			
			/**
			 * True if the field has been outputted.
			 *
			 * @since 1.0.0
			 *
			 * @var boolean
			 */
			case 'outputted':
			
				return false;
				
			/**
			 * Generated DOM ID.
			 *
			 * @since 1.0.0
			 *
			 * @var string
			 */
			case 'id':

				$name = $this->name;

				if (!empty($name))
				{
					$has_sanitization = (!empty($this->sanitization));

					if (empty($this->option_name))
					{
						$name = ($has_sanitization)
						? $this->sanitization . '[' . $name . ']'
						: $name;
					}
					else
					{
						$name = '[' . $name . ']';

						$name = ($has_sanitization)
						? $this->option_name . '[' . $this->sanitization . ']' . $name
						: $this->option_name . $name;
					}
				}

				return str_replace('[]]', '][]', $name);

			/**
			 * Generated field identifier attributes.
			 *
			 * @since 1.0.0
			 *
			 * @var string
			 */
			case 'input_attributes':

				$attributes = '';
				
				if (!empty($this->id))
				{
					$attributes = (strpos($this->id, '__i__') === false)
					? ' id="cnmi-' . $this->id . '" name="' . $this->id . '"'
					: ' data-cnmi-identifier="' . $this->id . '"';
				}
				
				foreach ($this->attributes as $name => $value)
				{
					if ($value !== '')
					{
						$attributes .= ' ' . sanitize_key($name) . '="' . esc_attr($value) . '"';
					}
				}

				return $attributes;

			/**
			 * Generated label attributes.
			 *
			 * @since 1.0.0
			 *
			 * @var string
			 */
			case 'label_attribute':

				if (empty($this->id))
				{
					return '';
				}
				
				return (strpos($this->id, '__i__') === false)
				? ' for="cnmi-' . $this->id . '"'
				: ' data-cnmi-identifier="cnmi-' . $this->id . '"';
				
			/**
			 * Sanitization name to use for the field.
			 *
			 * @since 1.0.0
			 *
			 * @var string
			 */
			case 'sanitization':
			
				return Copy_Nav_Menu_Items_Sanitization::TEXT;
			
			/**
			 * Current value for the field.
			 *
			 * @since 1.0.0
			 *
			 * @var mixed
			 */
			case 'value':
			
				if
				(
					empty($this->name)
					||
					empty($this->value_collection)
				)
				{
					return '';
				}
				
				$value_collection = $this->value_collection;
				
				if (is_object($value_collection))
				{
					return (isset($value_collection->{$this->name}))
					? $value_collection->{$this->name}
					: '';
				}
				
				return (isset($value_collection[$this->name]))
				? $value_collection[$this->name]
				: '';
		}

		return parent::_default($name);
	}

	/**
	 * Assemble the field classes.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 * @param  boolean $add_attr True if the class attribute should be added.
	 * @return string            Assembled field class(es).
	 */
	protected function _field_classes($add_attr = true)
	{
		if (!empty($this->classes))
		{
			$classes = esc_attr(implode(' ', $this->classes));

			return ($add_attr)
			? ' class="' . $classes . '"'
			: ' ' . $classes;
		}

		return '';
	}
	
	/**
	 * Generate the output for the field.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 * @param  string  $field Generated field output.
	 * @param  string  $type  Field type slug.
	 * @param  boolean $echo  True if the field should be echoed.
	 * @return string         Generated field if $echo is false.
	 */
	protected function _output($field, $type, $echo)
	{
		$this->outputted = true;
		
		$output = '';
		
		if (!empty($field))
		{
			$wrapper_attributes = $description = $label_description = $label = '';
			$condition_attr = ' data-cnmi-field="';
			$condition_replacement = $condition_attr . '__f__"';
			
			$this->push('wrapper_classes', 'cnmi-field');
			$this->push('wrapper_classes', 'cnmi-field-' . sanitize_key($type));
			
			if ($this->is_template)
			{
				$this->push('wrapper_classes', 'cnmi-field-template');
			}
			
			foreach ($this->wrapper_attributes as $name => $value)
			{
				$wrapper_attributes .= ' ' . sanitize_key($name) . '="' . esc_attr($value) . '"';
			}
			
			if (!$this->hide_labels)
			{
				if (!empty($this->description))
				{
					$description = '<div class="cnmi-description">'
						. '<label' . $this->label_attribute . '>' . $this->description . '</label>'
					. '</div>';

					if ($this->is_tall)
					{
						$label_description = $description;
						$description = '';
					}
				}
				
				if (!empty($this->label))
				{
					$label = '<div class="cnmi-field-label">'
						. '<label' . $this->label_attribute . '><strong>' . $this->label . '</strong></label>'
						. $label_description
					. '</div>';
				}
			}

			$output = '<div class="' . esc_attr(implode(' ', $this->wrapper_classes)) . '"' . $wrapper_attributes . '>'
				. $label
				. '<div class="cnmi-field-input">'
					. $field;
			
			if (!empty($this->conditions))
			{
				foreach ($this->conditions as $condition)
				{
					if
					(
						is_array($condition)
						&&
						isset($condition['field'])
						&&
						isset($condition['value'])
						&&
						is_object($condition['field'])
						&&
						Copy_Nav_Menu_Items_Utilities::starts_with('Copy_Nav_Menu_Items_Field_', get_class($condition['field']))
						&&
						!empty($condition['field']->name)
					)
					{
						if (!isset($condition['compare']))
						{
							$condition['compare'] = '=';
						}

						$condition_output = '<div class="cnmi-hidden cnmi-condition" data-cnmi-compare="' . esc_attr($condition['compare']) . '" data-cnmi-conditional="' . $this->id . '"' . $condition_replacement . ' data-cnmi-value="' . esc_attr($condition['value']) . '"></div>';
						
						if ($condition['field']->outputted)
						{
							$output .= str_replace($condition_replacement, $condition_attr . $condition['field']->id . '"', $condition_output);
						}
						else
						{
							$condition['field']->conditions_output .= str_replace($condition_replacement, $condition_attr . $condition['field']->name . '"', $condition_output);
						}
					}
				}
			}
			
			if (!empty($this->conditions_output))
			{
				$output .= str_replace($condition_attr . $this->name . '"', $condition_attr . $this->id . '"', $this->conditions_output);
			}
			
			$output .= $description
				. '</div>'
			. '</div>';
			
			if ($this->include_clear)
			{
				$output .= '<div class="cnmi-clear"></div>';
			}
		}
		
		if (!$echo)
		{
			return $output;
		}
		
		echo $output;
	}
	
	/**
	 * Add one or more child field to the field.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  mixed $fields Child field object or an array of child field objects to add to the field.
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
	 * Validate the data associated with this field.
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 * @param  array $raw_data Raw data to be validated.
	 * @return array           Validated data.
	 */
	public function validate_data($raw_data)
	{
		$valid_data = array();
		
		if (is_array($raw_data))
		{
			$has_child_data = method_exists($this, 'validate_child_data');
			$has_sanitization = (!empty($this->sanitization));
			
			if (!empty($this->name))
			{
				if
				(
					$has_sanitization
					&&
					isset($raw_data[$this->sanitization])
					&&
					isset($raw_data[$this->sanitization][$this->name])
				)
				{
					$valid_data = array
					(
						$this->sanitization => array
						(
							$this->name => ($has_child_data)
							? $this->validate_child_data($raw_data[$this->sanitization][$this->name])
							: $raw_data[$this->sanitization][$this->name]
						)
					);
				}
				else if
				(
					!$has_sanitization
					&&
					isset($raw_data[$this->name])
				)
				{
					$valid_data = array
					(
						$this->name => ($has_child_data)
						? $this->validate_child_data($raw_data[$this->name])
						: $raw_data[$this->name]
					);
				}
				else if ($has_child_data)
				{
					$valid_data = array_merge_recursive($valid_data, $this->validate_child_data($raw_data));
				}
			}
			else if ($has_child_data)
			{
				$valid_data = array_merge_recursive($valid_data, $this->validate_child_data($raw_data));
			}
		}
		
		return $valid_data;
	}
}
