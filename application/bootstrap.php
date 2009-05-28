<?php
/**
 * @category Ayaq
 * @copyright Copyright (c) 2009, Bellière Ludovic
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

$db = Taplod_Db::factory('Pdo_Mysql',$config->db);
$db->exec("SET character_set_results='utf8'");

require_once('Taplod/Url.php');
$url = Taplod_Url::getInstance($config->url);
Taplod_ObjectCache::set('URL',$url);

$tpl = new Taplod_Templates();

if ($category = $url->getCategoryPath()) {
    if (file_exists($category . 'bootstrap.php')) {
        require $category . 'bootstrap.php';
    }
    unset ($category);
}

require ( $url->getPagePath() . '.php' );
