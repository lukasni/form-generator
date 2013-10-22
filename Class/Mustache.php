<?php

class Mustache {

	public static function factory(array $aSettings = array())
	{
		$defaults = [
			'loader' => new Mustache_Loader_FilesystemLoader(APPPATH.'template'),
			'partials_loader' => new Mustache_Loader_FilesystemLoader(APPPATH.'template/partials'),
		];

		$settings = array_merge($defaults, $aSettings);

		return new Mustache_Engine($settings);
	}

}