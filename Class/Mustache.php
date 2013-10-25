<?php

class Mustache {

	public static function factory(array $aSettings = array())
	{
		$defaults = Config::get('mustache', 'default');

		$settings = array_merge($defaults, $aSettings);

		return new Mustache_Engine($settings);
	}

}