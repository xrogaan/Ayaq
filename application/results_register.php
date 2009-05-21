<?php
/**
* @category Ayaq
* @copyright Copyright (c) 2009, Bellière Ludovic
* @license http://opensource.org/licenses/mit-license.php MIT license
*/

if (isset($_POST['register'])) {
	$encoded_session = session_encode();
	$results         = getQuizzResults('values');
	$result_id       = array_shift($results);
	
	$data = array(
		'result' => $result_id,
		'data'   => $encoded_session
	);
	
	if (!empty($_POST['pseudo'])) {
		$data['pseudo'] = $_POST['pseudo'];
	}
	
	$last_insert_id = $db->insert('quizz_user_results',$data);
	
	$url->redirectError(array('results',array('rid'=>$last_insert_id)),'Vos résultats ont bien été enregistré.');
}

$url->redirectError('results','Veuillez utiliser le formulaire approprié pour envoyer vos données.');