<?php

if (isset($_SESSION['quizz_responces'])) {
	$r = $pdo->query('SELECT count(*) as num_question FROM quizz_questions');
	$data = $r->fetchColumn();
	if ($data['num_question'] < count($_SESSION['quizz_step'])) {
		redirect('index');
	}
}

$tpl = new templates();
$tpl->addFile('_begin','_header.tpl.phtml')
	->addFile('_end','_footer.tpl.phtml')
	->addFile('results','results.tpl.phtml');

$results = array();

foreach ($_SESSION['quizz_responces'] as $qid => $data) {
	foreach ($data as $result_id => $points) {
		if (!isset($results[$result_id])) {
			$results[$result_id] = 0;
		}
		$results[$result_id] += $points;
	}
}

$results = array_flip($results);
krsort($results);
$result_id1 = array_shift($results);
$result_id2 = array_shift($results);

$statement = $pdo->prepare('SELECT * FROM quizz_results WHERE id=?');
$statement->execute(array($result_id1));
$tpl->result  = $statement->fetch(PDO::FETCH_ASSOC);
$statement->execute(array($result_id2));
$result2 = $statement->fetch(PDO::FETCH_ASSOC);

$result2['name'] = ($result2['name'][1] == "'") ? 'de '.strtolower($result2['name']) : substr($result2['name'],2);

$tpl->result2 = $result2;
$tpl->render('results');