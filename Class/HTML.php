<?php

/**
 * HTML helper class. Contains static functions for generating HTML tags
 *
 * @author  Lukas Niederberger <lukas.niederberger@gibmit.ch>
 */
class HTML {

	/**
	 * Generate generic HTML Tag.
	 * @param  string  $tag        Name of the html-tag
	 * @param  array   $attributes key/value paired array of attributes.
	 * @param  boolean $empty_tag  create xml-compliant empty tag? < />
	 * @return string              Full HTML-Tag as string.
	 */
	public static function tag($tag, array $attributes = array(), $empty_tag = false)
	{
		$result = '<'.$tag;
		$result .= count($attributes) > 0 ? ' '.self::attributes($attributes) : '';
		$result .= $empty_tag ? ' />' : '>';

		return $result;
	}

	public static function addAttribute($key, $value, array &$target)
	{
		if ( ! array_key_exists($key, $target))
		{
			$target[$key] = $value;
		}
	}

	/**
	 * Generate a string of HTML- or XML Attributes from an array of key/value pairs.
	 * @param  array  $attributes key/value paired array of attributes.
	 * @return string             string of all attributes in xml format.
	 */
	public static function attributes(array $attributes)
	{
		$result = '';
		foreach ($attributes as $key => $value)
		{
			$result .= $key.'='.'"'.$value.'" ';
		}
		return trim($result);
	}

}