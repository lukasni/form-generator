<?php

function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}

set_error_handler("exception_error_handler");

function autoload($class)
{
	$tmp_classpath = str_replace('_', DIRECTORY_SEPARATOR, $class);

	$classdir  = APPPATH.DIRECTORY_SEPARATOR.'Class'.DIRECTORY_SEPARATOR;

	$classpath = $classdir.$tmp_classpath.'.php';

	if (file_exists($classpath))
	{
		require $classpath;
	}
}

spl_autoload_register('autoload');

// Register Mustache autoloader
require APPPATH.'lib/mustache/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

// Require htmLawed functions
require_once APPPATH.'lib/htmLawed/htmLawed.php';