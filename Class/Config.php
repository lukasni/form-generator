<?php

/**
 * Basic config reader class.
 *
 * @author Lukas Niederberger <lukas.niederberger@gibmit.ch>
 */
class Config {

	/**
	 * @var string Config directory relative to application path
	 */
	protected static $config_dir = 'config';
	
	/**
	 * Get a config value using dot notation. 
	 * Returns the whole config array if no key is passed.
	 * 
	 * @param  string $file config file the value is in.
	 * @param  string $key  Key of the required config value
	 * @return mixed        Required config value.
	 */
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