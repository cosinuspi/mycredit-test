<?php
// Check Version
if (version_compare(phpversion(), '7.0.0', '<') == true) {
	exit('PHP7.0+ Required');
}

function autoloader($class) {
	include '../' . str_replace('\\', '/', $class) . '.php';
}

spl_autoload_register('autoloader');

$config = require_once '../config.php';

$app = core\App::getInstance($config);

$app->run();
