<?php

class Config {

	protected static $config_dir = 'config';
	
	public static function get($file = 'global', $key = null)
	{
		$file = str_replace('/', DIRECTORY_SEPARATOR, $file);
		$file = APPPATH.self::$config_dir.DIRECTORY_SEPARATOR.$file.'.php';

		$config = include($file);

		if ( is_null($key) )
		{
			return $config;
		}
		else
		{
			return Arr::get($config, $key);
		}
	}

}