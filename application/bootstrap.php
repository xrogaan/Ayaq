<?php

defined('APPLICATION_PATH')
    or define('APPLICATION_PATH', dirname(__FILE__));

defined('APPLICATION_ENVIRONMENT')
    or define('APPLICATION_ENVIRONMENT', 'development');

defined('TEMPLATE_PATH')
	or define('TEMPLATE_PATH', realpath(dirname(__FILE__).'/../templates').'/');

if (!isset($_SESSION['quizz_step'])) {
  // quizz_step = 0 -> n-1
  $_SESSION['quizz_step'] = 0;
}

mb_internal_encoding("UTF-8");

$pdo = new PDO_Timer('mysql:dbname=test;host=localhost','test','test');
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$pdo->exec("SET character_set_results='utf8'");

require_once('Url.php');
$url = new Url(SITEURL,BASEURI);


require ( $url->getPagePath() . '.php' );
