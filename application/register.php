<?php
/**
 * @category Ayaq
 * @copyright Copyright (c) 2009, Bellière Ludovic
 * @license http://opensource.org/licenses/mit-license.php MIT license
 */

if (isset($_COOKIE['loggedin']) && $_COOKIE['loggedin'] === '1') {
    $url->redirectError(array('index',false,'admin'),"Don't you have an account ?");
}

$tpl->addFile('register','register.tpl.phtml');

if (isset($_POST['login']) && !empty($_POST['login'])) {
    $userExists = $db->fetchOne('SELECT count(*) as userExists FROM quizz_users WHERE email = %s',trim($_POST['login']));
    if ($userExists == 1) {
        $url->redirectError('register','This e-mail already exists in the database, please try another one or try to log in with it.');
    } else {
        $email = trim($_POST['login']);
        $displayName = strstr($email,'@');
        $db->insert('quizz_users', array('email'=>$email,'password'=>sha1(trim($_POST['password'])),'display_name'=>$displayName));
        $url->redirectError('login','Your account has been created. Please, log in !');
    }
}

echo $tpl->render('register');
