<?php
/*!
 * Functionality for field sanitization.
 *
 * @since 1.0.0
 *
 * @package    Copy Nav Menu Items
 * @subpackage Sanitization
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement plugin sanitization functionality.
 *
 * @since 1.0.0
 */
final class Copy_Nav_Menu_Items_Sanitization
{
	/**
	 * Sanitization name for CSS class fields.
	 *
	 * @since 1.0.0
	 *
	 * @const string
	 */
	const CLASSES = 'classes';
	
	/**
	 * Sanitization name for confirmation fields.
	 *
	 * @since 1.0.0
	 *
	 * @const string
	 */
	const CONFIRMATION = 'confirmation';
	
	/**
	 * Fields that should not be returned during sanitization.
	 *
	 * @since 1.0.0
	 *
	 * @const string
	 */
	const EXCLUDE = 'exclude';
	
	/**
	 * Sanitization name for general numeric fields.
	 *
	 * @since 1.0.0
	 *
	 * @const string
	 */
	const NUMBER = 'number';
	
	/**
	 * Sanitization name for slug fields.
	 *
	 * @since 1.0.0
	 *
	 * @const string
	 */
	const SLUG = 'slug';
	
	/**
	 * Sanitization name for simple text fields.
	 *
	 * @since 1.0.0
	 *
	 * @const string
	 */
	const TEXT = 'text';
	
	/**
	 * Sanitization name for textarea fields.
	 *
	 * @since 1.0.0
	 *
	 * @const string
	 */
	const TEXTAREA = 'textarea';
	
	/**
	 * Sanitization name for URL fields.
	 *
	 * @since 1.0.0
	 *
	 * @const string
	 */
	const URL = 'url';
	
	/**
	 * Sanitize the provided values.
	 *
	 * @since 1.0.0
	 *
	 * @access public static
	 * @param  array $input Values to sanitize.
	 * @return array        Sanitized values.
	 */
	public static function sanitize($input)
	{
		if
		(
			!is_array($input)
			||
			empty($input)
		)
		{
			return array();
		}
		
		$output = array();
		
		foreach ($input as $type => $fields)
		{
			if
			(
				$type !== self::EXCLUDE
				&&
				is_array($fields)
			)
			{
				foreach ($fields as $name => $value)
				{
					switch ($type)
					{
						case self::CLASSES:
						
							$classes = explode(' ', preg_replace('/\s\s+/', ' ', trim($value)));
							$class_count = count($classes);

							for ($i = 0; $i < $class_count; $i++)
							{
								$classes[$i] = sanitize_html_class($classes[$i]);
							}

							$output[$name] = implode(' ', array_filter($classes));
							
						break;
						
						case self::CONFIRMATION:
						
							$unconfirmed = $name . Copy_Nav_Menu_Items_Constants::SETTING_UNCONFIRMED;

							$output[$name] = $output[$unconfirmed] =
							(
								!isset($input[self::EXCLUDE][$unconfirmed])
								||
								empty($input[self::EXCLUDE][$unconfirmed])
							)
							? ''
							: $value;
							
						break;
						
						case self::NUMBER:
						
							if (is_numeric($value))
							{
								$output[$name] = $value;
							}
							
						break;
						
						case self::SLUG:
						
							$output[$name] = sanitize_key($value);
							
						break;
						
						case self::TEXTAREA:
						
							$output[$name] = sanitize_textarea_field($value);
							
						break;
						
						case self::URL:
						
							$output[$name] = esc_url_raw($value);
							
						break;
						
						default:
						
							$output[$name] = sanitize_text_field($value);
					}
				}
			}
		}
		
		return $output;
	}
}
