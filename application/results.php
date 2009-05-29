<?php
/**
* @category Ayaq
* @copyright Copyright (c) 2009, Bellière Ludovic
* @license http://opensource.org/licenses/mit-license.php MIT license
*/

if (isset($_GET['rid'])) {
	$rid = intval($_GET['rid']);
	$query_result = $db->fetch('SELECT * FROM quizz_user_results WHERE id=%s',$rid);
	if ($query_result) {
		$_SESSION['quizz_responces'] = array();
		$tpl->pseudo  = $query_result['pseudo'];
		$tpl->rid     = $rid;
		session_decode($query_result['data']);
	} else {
		$tpl->addFile('_begin','_header.tpl.phtml')
			->addFile('_end','_footer.tpl.phtml')
			->addFile('results','results.tpl.phtml');
		$tpl->message = "Sorry, but we can't found entry $rid.";
		echo $tpl->render('results');
		die;
	}
}

if (isset($_SESSION['quizz_responces'])) {
	$r = $db->query('SELECT count(*) as num_question FROM quizz_questions');
	$data = $r->fetchColumn();
	if ($data['num_question'] < count($_SESSION['quizz_step'])) {die('huho');
		$url->redirect('index');
	}
} else {
	$url->redirect('index');
}

$tpl->addFile('_begin','_header.tpl.phtml')
	->addFile('_end','_footer.tpl.phtml')
	->addFile('results','results.tpl.phtml');


$results = getQuizzResults('values');
$result_id1 = array_shift($results);
$result_id2 = array_shift($results);

$statement = $db->prepare('SELECT * FROM quizz_results WHERE id=?');
$statement->execute(array($result_id1));
$tpl->result  = $statement->fetch(PDO::FETCH_ASSOC);
$statement->execute(array($result_id2));
$result2 = $statement->fetch(PDO::FETCH_ASSOC);

$result2['name'] = ($result2['name'][1] == "'") ? 'de '.strtolower($result2['name']) : substr($result2['name'],2);

$tpl->result2 = $result2;
$tpl->stored_data = (isset($rid)) ? true : false;
echo $tpl->render('results');
