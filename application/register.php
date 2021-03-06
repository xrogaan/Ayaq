<?php
/**
 * @category Ayaq
 * @copyright Copyright (c) 2009, Bellière Ludovic
 * @license http://opensource.org/licenses/mit-license.php MIT license
 */
$sessionData = isSessionLoaded();
if ($sessionData !== false) {
    $url->redirectError(array('index',false,'admin'),"Don't you have an account ?");
}

$tpl->addFile('register','register.tpl.phtml');

if (isset($_POST['login']) && !empty($_POST['login'])) {
    if (isset($_POST['password']) && strlen($_POST['password']) < 5) {
        $url->redirectError('register','Your password must be at least 5 characters.');
    }

    $userExists = $db->fetchOne('SELECT count(*) as userExists FROM quizz_users WHERE email = %s',trim($_POST['login']));
    if ($userExists == 1) {
        $url->redirectError('register','This e-mail already exists in the database, please try another one or try to log in with it.');
    } else {
        $email = trim($_POST['login']);

        if (strlen($_POST['password']) < 6) {
            $url->addMessageInSession('Your password must be highter than 6 characters.');
        }

        if (!validateEmail($_POST['login'])) {
            $url->addMessageInSession('Your e-mail is not a valid e-mail.');
        }

        if ($url->isMessageInSession()) {
            $url->redirect('login','redirect_message_box');
        }

        $displayName = strstr($email,'@');
        $db->insert('quizz_users', array('email'=>$email,'password'=>sha1(trim($_POST['password'])),'username'=>$displayName));
        $message = "here is your credentials :\nlogin: $email\npassword: ".$_POST['password']."\n\nPlease login to ".$config->url->baseurl.$config->url->baseuri."login\n\n--\nBe aware : your password is fully encrypted and cannot be recovered.\nThe team.";
        $headers = 'From: '.$config->sender_email."\r\n".
            'Reply-To: '.$config->bounce_email."\r\n".
            'X-Mailer: PHP/'.phpversion();
        if (!mail($email,'Ayaq : your account',$message,$headers)) {
            $url->addMessageInSession('WARNING: the email wasn\'t send.');
        }
        $url->redirectError('login','Your account has been created. Please, log in !');
    }
}

echo $tpl->render('register');
