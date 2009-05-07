<?php

defined('APPLICATION_PATH')
    or define('APPLICATION_PATH', dirname(__FILE__));

defined('APPLICATION_ENVIRONMENT')
    or define('APPLICATION_ENVIRONMENT', 'development');

$pdo = new PDO_Timer('mysql:dbname=test;host=localhost','test','test');
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$pdo->exec("SET character_set_results='utf8'");

$uri = str_replace(BASEURI,'',$_SERVER['REQUEST_URI']);

if ($uri == "") {
	$uri = 'panel';
}

if ( $uri[strlen($uri)-1] == '/' ) {
	$type = 'cat';
	list($dir,$page) = explode('/',$uri);
		
	if (empty($page)) {
		$page = 'index';
	}
	
	$page = '/' . $dir . '/' . $page ;
} else {
	switch (substr_count($uri, '/')) {
		case 2:
			list($dir,$page,$action) = explode('/',$uri);
			$action = explode('-',$action);
			$page = '/' . $dir . '/' . $page ;
			break;
		case 1:
			list($dir,$page) = explode('/',$uri);
			$page = '/' . $dir . '/' . $page ;
			break;
		default:
			$page = $uri;
			break;
	}
}

if ($page != 'bootstrap' && file_exists(APPLICATION_PATH . '/' . $page . '.php')) {
	require ( APPLICATION_PATH . '/' . $page . '.php' );
} else {
	throw new Exception('This page (' . $page . ') doesn\'t exists.');
}
