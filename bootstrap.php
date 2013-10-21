<?php

function autoload($class)
{
	$classpath = str_replace('_', DIRECTORY_SEPARATOR, $class);

	$classpath = APPPATH.DIRECTORY_SEPARATOR.'Class'.DIRECTORY_SEPARATOR.$classpath.'.php';

	if (file_exists($classpath))
	{
		require $classpath;
	}
	else
	{
		throw new Exception('Class '.$class.' could not be loaded');
	}
}

spl_autoload_register('autoload');