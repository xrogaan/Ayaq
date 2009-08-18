<?php
/**
 * @category Ayaq
 * @copyright Copyright (c) 2009, Bellière Ludovic
 * @license http://opensource.org/licenses/mit-license.php MIT license
 */

$sessionData = isSessionLoaded();
if (is_array($sessionData)) {
    define('SESSION_SID',$sessionData['sid']);
    $db->update('quizz_session', array('last_changes'=>mktime()), $db->where(array('sid'=>SESSION_SID)));
} else {
    $url->redirectError('login',"You're not logged in.");
}


$headbar = array(
    'dashboard' => array(
        'url'         => array('category' => 'admin'),
        'displayName' => 'Dashboard'
    ),
    'show' => array(
        'url' => array('category' => 'admin'),
        'displayName' => 'Q&A'
    ),
);

$tpl->headContent = $tpl->partialContent('header', 'admin', array('headbar'=>$headbar));