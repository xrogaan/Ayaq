<?php
/**
 * @category Ayaq
 * @copyright Copyright (c) 2009, Bellière Ludovic
 * @license http://opensource.org/licenses/mit-license.php MIT license
 */

if (isset($_COOKIE['loggedin']) && $_COOKIE['loggedin'] === '1') {
    $url->redirectError(array('index',false,'admin'),"You've been logged in.");
}

$tpl->addFile('login','login.tpl.phtml');

if (isset($_POST['login']) && !empty($_POST['login'])) {
    $userExists = $db->fetchOne('SELECT count(*) as userExists FROM quizz_users WHERE email = %s AND password = SHA1(%s)',trim($_POST['login']),trim($_POST['password']));
    if ($userExists == 1) {
        setcookie('loggedin',true,time()+60*60*24*30);
        $url->redirectError(array('index','false','admin'),'You have been logged in.');
    } else {
        $url->redirectError('login','You have failed, try again.');
    }
}

echo $tpl->render('login');