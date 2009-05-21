<?php
/**
* @category Ayaq
* @copyright Copyright (c) 2009, Bellière Ludovic
* @license http://opensource.org/licenses/mit-license.php MIT license
*/

if (isset($_SESSION['quizz_responces'])) {
	$r = $db->query('SELECT count(*) as num_question FROM quizz_questions');
	$data = $r->fetchColumn();
	if ($data['num_question'] < count($_SESSION['quizz_step'])) {
		$url->redirect('index');
	}
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
echo $tpl->render('results');