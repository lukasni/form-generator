<?php

/**
 * Wrapper class for the Mustache templating engine. Sets basic config options.
 *
 * @author Lukas Niederberger <lukas.niederberger@gibmit.ch>
 */
class Mustache {

	public static function factory($template, array $aSettings = array())
	{
		$defaults = Config::get('mustache', 'default');

		$settings = array_merge($defaults, $aSettings);

		$m = new Mustache_Engine($settings);

		return $m->loadTemplate($template);
	}

}