<?php

/**
 * Helper class that offers static Array utility methods
 *
 * @author Lukas Niederberger <lukas.niederberger@gibmit.ch>
 */
class Arr {

	/**
	 * Get a value from a multidimensional associative array using dot notation
	 * @param  array  $array   source array
	 * @param  string $key     key of the desired value using dot notation
	 * @param  mixed $default  Default value to be used if the key cannot be found.
	 * @return mixed           Value of the desired key.
	 */
	public static function get(array $array, $key, $default = null)
	{
		$pieces = explode('.', $key);

		foreach ($pieces as $piece) 
		{
			if ( !is_array($array) || !array_key_exists($piece, $array) ) 
			{
				return $default;
			}

			$array = $array[$piece];
		}

		return $array;
	}

}