<?php
/**
 * @category Ayaq
 * @copyright Copyright (c) 2009, Bellière Ludovic
 * @license http://opensource.org/licenses/mit-license.php MIT license
 */

$sessionData = isSessionLoaded();
if (is_array($sessionData)) {
    $db->delete('quizz_session', $db->where(array('sid'=>$sessionData['sid'])));
    setcookie('loggedin',null);
    setcookie('qsid',null);
    session_destroy();
}

$url->redirect('index');