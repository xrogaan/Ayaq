<?php

error_reporting(E_ALL);

require_once 'inc/functions.php';
require_once 'inc/templates.php';
require_once 'inc/PDO_Timer.php';

define('SITEURL', 'http://bouli.homeip.net/');
define('BASEURI', '~xrogaan/quizz-zombie/');
define('APPLICATION_ENVIRONMENT', 'developpment');

session_name('zombie_quizz');
session_start();

if (isset($_GET['logout'])) {
	session_destroy();
	redirect('index.php');
}

if (!isset($_SESSION['quizz_step'])) {
	// quizz_step = 0 -> n-1
	$_SESSION['quizz_step'] = 0;
}

try {
	$pdo = new PDO_Timer('mysql:dbname=test;host=localhost','test','test');
	$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	$pdo->exec("SET character_set_results='utf8'");
} catch (PDOException $e) {
    echo 'Connexion échouée : ' . $e->getMessage();
	die;
}