<?php

class Arr {

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