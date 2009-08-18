<?php
/**
* @category Ayaq
* @package Ayaq_Functions
* @copyright Copyright (c) 2008, Bellière Ludovic
* @license http://opensource.org/licenses/mit-license.php MIT license
*/

/**
 * Provide nice formatting for print_r
 */
function xdump( $v ) {
    echo '<pre>';
    print_r( $v );
    echo '</pre>';
}

/**
 * Provide nice formatting for var_dump
 */
function xvdump( ) {
    $args = func_get_args();
    echo '<pre>';
    foreach($args as $arg) { var_dump($arg); }
    echo '</pre>';
}


function isSessionLoaded() {
    global $db, $config;

    if (isset($_COOKIE['qsid']) && isset($_COOKIE['loggedin'])) {
        $sessionData = $db->fetch('SELECT * FROM quizz_session WHERE sid=%s',sha1($_COOKIE['qsid']+$config->secret_key));
        if ( sha1($sessionData['uid'] + $config->secret_key) == $_COOKIE['loggedin'] ) {
            return $sessionData;
        }
    }
    return false;
}

function redirect($page) {
	trigger_error('This function is deprecated. Use url->redirect instead.',E_USER_ERROR);
	header('Location: '.SITEURL.BASEURI.$page);
	die;
}

function redirectError($page, $message) {
	addMessageInSession($message);
	redirect($page.'#redirect_message_box');
}

function mapToHtmlentities($value) {
	return htmlentities($value, ENT_COMPAT, 'utf-8');
}

function showSessionMessages() {
	if (!empty($_SESSION['session_messages'])) {
		$messages = '<div onclick="this.parentNode.removeChild(this);" id="redirect_message_box" name="redirect_message_box" class="redirect_message_box">'
				  . '<div class=redirect_message id=redirect_message name=redirect_message>'
				  . implode("<br/>", array_map('mapToHtmlentities', $_SESSION['session_messages']))
				  . '</div></div>';
		$_SESSION['session_messages'] = null;
		return $messages;
	} else {
		return false;
	}
}

function getQuizzResults($sort='values') {
	if (isset($_SESSION['quizz_responces'])) {
		$results = array();
		foreach ($_SESSION['quizz_responces'] as $qid => $data) {
			foreach ($data as $result_id => $points) {
				if (!isset($results[$result_id])) {
					$results[$result_id] = 0;
				}
				$results[$result_id] += $points;
			}
		}
		
		if ($sort) {
			switch ($sort) {
				case 'values':
					$results = array_flip($results);
					krsort($results);
					break;
				default:
					break;
			}
		}
		
		return $results;
	} else {
		require_once 'Taplod/Exception.php';
		throw new Taplod_Exception('there is no result to process');
	}
}