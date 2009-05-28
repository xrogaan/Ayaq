<?php
/**
 * @category Ayaq
 * @copyright Copyright (c) 2009, Bellière Ludovic
 * @license http://opensource.org/licenses/mit-license.php MIT license
 */

if (!isset($_COOKIE['admin']) || $_COOKIE['admin'] !== '1') {
    $url->redirectError('login',"You're not logged in.");
}