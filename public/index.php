<?php
/**
* @category Ayaq
* @copyright Copyright (c) 2009, Bellière Ludovic
* @license http://opensource.org/licenses/mit-license.php MIT license
*/

error_reporting(E_ALL);

session_name('zombie_quizz');
session_start();

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application/'));

set_include_path(
    realpath(APPLICATION_PATH . '/../inc') 
    . PATH_SEPARATOR . get_include_path()
);

require_once 'functions.php';
require_once 'Taplod/Loader.php';
require_once '../config.php';

Taplod_Loader::registerAutoload();

try {
    require APPLICATION_PATH . '/bootstrap.php';
} catch (Exception $exception) {
    echo '<html><body><center>'
       . 'An exception occured while bootstrapping the application.';
    if (defined('APPLICATION_ENVIRONMENT')
        && APPLICATION_ENVIRONMENT != 'production'
    ) {
        echo '<br /><br />' . $exception->getMessage() . '<br />'
           . '<div align="left">Stack Trace:' 
           . '<pre>' . $exception->getTraceAsString() . '</pre></div>';
        if ($exception instanceof Taplod_Db_Adapter_Exception) {
			$trace = $exception->getTrace();
            echo '<div align="left">Query Trace:'
               . '<pre>' . $trace[0]['args'][0] . '</pre></div>';
		}
    }
    echo '</center></body></html>';
    exit(1);
}