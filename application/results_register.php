<?php
/**
* @category Ayaq
* @copyright Copyright (c) 2009, Bellière Ludovic
* @license http://opensource.org/licenses/mit-license.php MIT license
*/

if (isset($_POST['register'])) {
	$pseudo = $_POST['pseudo'];
	$data = session_encode();
	$results = getQuizzResults('values');
	$result_id1 = array_shift($results);
	$db->insert('quizz_user_results',array('result'=>$result_id1,'data'=>$data));
	$url->redirectError('results','Vos résultats ont bien été enregistré.');
}

$url->redirectError('results','Veuillez utiliser le formulaire approprié pour envoyer vos données.');