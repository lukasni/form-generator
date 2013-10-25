<?php

return [

	'default' => [
		'loader' => new Mustache_Loader_FilesystemLoader(APPPATH.'template'),
		'partials_loader' => new Mustache_Loader_FilesystemLoader(APPPATH.'template/partials'),
	],
	
];