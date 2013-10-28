<?php

class Mustache {

	public static function factory($template, array $aSettings = array())
	{
		$defaults = Config::get('mustache', 'default');

		$settings = array_merge($defaults, $aSettings);

		$m = new Mustache_Engine($settings);

		return $m->loadTemplate($template);
	}

}