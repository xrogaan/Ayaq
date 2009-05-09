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


function addMessageInSession($message) {
	$_SESSION['session_messages'][] = $message;
}

function redirect($page) {
	header('Location: '.SITEURL.BASEURI.$page);
	die;
}

function redirectError($page, $message) {
	addMessageInSession($message);
	redirect($page.'#redirect_message_box');
}


function showSessionMessages() {
	if (!empty($_SESSION['session_messages'])) {
		$messages = '<div onclick="this.parentNode.removeChild(this);" id="redirect_message_box" name="redirect_message_box" class="redirect_message_box">'
				  . '<div class=redirect_message id=redirect_message name=redirect_message>'
				  . implode("<br/>", array_map('htmlentities', $_SESSION['session_messages']))
				  . '</div></div>';
		$_SESSION['session_messages'] = null;
		return $messages;
	} else {
		return false;
	}
}