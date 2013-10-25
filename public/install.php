<?php

$htaccess = __DIR__.DIRECTORY_SEPARATOR.'.htaccess';
$config = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'global.php';
$message = '';

if ( is_writable($htaccess) && is_writable($config) )
{
	$message .= '.htaccess and /config/global are is writable, automatic install possible!<br>';
}
else
{
	$message .= 'Not all required files are writable. Please perform manual install according to manual<br>';
}

$baseurl = dirname($_SERVER['REQUEST_URI']).'/';

if ( array_key_exists('baseurl', $_POST) )
{
	$installdir = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR;
	$htaccess_content = file_get_contents($installdir.'original.htaccess');
	$config_content = file_get_contents($installdir.'original.global.php');

	$htaccess_content = str_replace('{{BASEURL}}', $_POST['baseurl'], $htaccess_content);
	$config_content = str_replace('{{BASEURL}}', $_POST['baseurl'], $config_content);

	file_put_contents($htaccess, $htaccess_content);
	file_put_contents($config, $config_content);

	header('Location: '.$baseurl);
}

?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Form Generator Installation</title>
</head>
<body>
	<h1>Installation</h1>

	<p class="message"><?=$message?></p>

	<p>Please check if the base url below is correct, edit if necessary. Press "Install" to install. You will be redirected to the index page.</p>
	<form action="" method="post">
		<fieldset>
			<legend>Base URL</legend>
			<input type="text" name="baseurl" value="<?=$baseurl?>">
			<button>Install</button>
		</fieldset>
	</form>

</body>
</html>