<?php
/**
 * @category Ayaq
 * @copyright Copyright (c) 2009, Bellière Ludovic
 * @license http://opensource.org/licenses/mit-license.php MIT license
 */
$sessionData = isSessionLoaded();
if ($sessionData !== false) {
    $url->redirectError(array('index',false,'admin'),"You're already logged in.");
}

$tpl->addFile('login','login.tpl.phtml');

if (isset($_POST['login']) && !empty($_POST['login'])) {
    $userData = $db->fetch('SELECT count(*) as userExists, id FROM quizz_users WHERE email = %s AND password = SHA1(%s)',trim($_POST['login']),trim($_POST['password']));
    $userExists = $userData['userExists'];
    $uid = $userData['id'];
    if ($userExists == 1) {
        $sid = uniqid(rand() . rand(),true);
        $db->insert('quizz_session',array('sid'=>sha1($sid), 'created'=>mktime(), 'last_changes'=>mktime(), 'uid'=>(int) $uid, 'session_data'=>null));
        setcookie('loggedin',sha1($uid+$config->secret_key),time()+$config->get('session_lifetime',60*60*24*30),'/');
        setcookie('qsid',$sid,time()+$config->get('session_lifetime',60*60*24*30));
        $url->redirectError(array('index',false,'admin'),'You have been logged in.');
    } else {
        $url->redirectError('login','You have failed, try again.');
    }
}

echo $tpl->render('login');