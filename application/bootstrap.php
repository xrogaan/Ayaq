<?php
/**
* @category Ayaq
* @copyright Copyright (c) 2008, Bellière Ludovic
* @license http://opensource.org/licenses/mit-license.php MIT license
*/

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

$db = Taplod_Db::factory('Pdo_Mysql',array(
	'dbname'=>'test',
	'host'=>'localhost',
	'username'=>'test',
	'password'=>'test'
));
$db->exec("SET character_set_results='utf8'");

require_once('Taplod/Url.php');
$url = Taplod_Url::getInstance(array(
	'application_path' => APPLICATION_PATH,
	'baseUrl'          => SITEURL,
	'baseUri'          => BASEURI
));
Taplod_ObjectCache::set('URL',$url);

$tpl = new Taplod_Templates();

require ( $url->getPagePath() . '.php' );
