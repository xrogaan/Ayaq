<?php

defined('APPLICATION_PATH')
    or define('APPLICATION_PATH', dirname(__FILE__));

defined('APPLICATION_ENVIRONMENT')
    or define('APPLICATION_ENVIRONMENT', 'development');

defined('TEMPLATE_PATH')
	or define('TEMPLATE_PATH', dirname(__FILE__).'/../templates/');

if (!isset($_SESSION['quizz_step'])) {
  // quizz_step = 0 -> n-1
  $_SESSION['quizz_step'] = 0;
}

mb_internal_encoding("UTF-8");

$pdo = new PDO_Timer('mysql:dbname=test;host=localhost','test','test');
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$pdo->exec("SET character_set_results='utf8'");

$uri = str_replace(BASEURI,'',$_SERVER['REQUEST_URI']);

if ($uri == "") {
	$uri = 'index';
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
			list($_, $dir, $_page) = explode('/',$uri);
			$actions = explode('-',$_page);
			$_page = array_shift($actions);
			$page = '/' . $dir . '/' . $_page ;
			break;
		case 1:
			list($dir,$_page) = explode('/',$uri);
			$actions = explode('-',$_page);
			$_page = array_shift($actions);
			$page = '/' . $dir . '/' . $_page ;
			break;
		default:
			$page = $uri;
			break;
	}
}

foreach ($actions as $action) {
	list($key,$value) = explode(':',$action);
	$_GET[$key] = $value;
}

if ($page != 'bootstrap' && file_exists(APPLICATION_PATH . '/' . $page . '.php')) {
	require ( APPLICATION_PATH . '/' . $page . '.php' );
} else {
	throw new Exception('This page (' . $page . ') doesn\'t exists.');
}
