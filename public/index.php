<?php

$application = '..';

$base_url = "/form-generator/";

define('DOCROOT', realpath(__DIR__).DIRECTORY_SEPARATOR);
define('APPPATH', realpath(DOCROOT.$application).DIRECTORY_SEPARATOR);
define('BASEURL', $base_url);

require_once(APPPATH.'bootstrap.php');

try 
{
	$request = Request::instance()->execute();
}
catch (Exception $e)
{
	echo "ERROR: ".$e->getMessage();
}