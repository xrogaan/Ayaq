<?php
/**
 * @category Ayaq
 * @copyright Copyright (c) 2009, Bellière Ludovic
 * @license http://opensource.org/licenses/mit-license.php MIT license
 */

if (isset($_COOKIE['admin']) && $_COOKIE['admin'] === '1') {
    $url->redirectError(array('index',false,'admin'),"You've been logged in.");
}

$tpl->addFile('login','login.tpl.phtml');

// temporary credential.
$login    = "admin";
$password = "c80bcc3141b691d946f3175937d6696874011be8"; // sha-1

if (isset($_POST['login'])) {
    if ($_POST['login'] == $login && sha1($_POST['password']) == $password) {
        setcookie('admin',true,time()+60*60*24*30);
        $url->redirectError(array('index',false,'admin'),"You've been logged in.");
    } else {
        $url->redirectError('login','Your credential does not matches.');
    }
}

echo $tpl->render('login');