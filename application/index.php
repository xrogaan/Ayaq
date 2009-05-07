<?php

$tpl = new templates();
$tpl->addFile('_begin','_header.tpl.phtml')
	->addFile('_end','_footer.tpl.phtml')
	->addFile('index','index.tpl.phtml');

// Response id = 1 -> n
$key = $_SESSION['quizz_step']+1;

if (isset($_POST['submit']) && isset($_POST['question_'.$key])) {
	$result = $_POST['question_'.$key];
	
	$r = $pdo->fetchAllAsDict('result_id','SELECT result_id, points FROM quizz_responses_points WHERE response_id=%s',$result);
	
	foreach ($r as $result_id => $result_data) {
		if (!isset($_SESSION['quizz_responces'][$key][$result_id])) {
			$_SESSION['quizz_responces'][$key][$result_id] = 0;
		}
		$_SESSION['quizz_responces'][$key][$result_id] += $result_data['points'];
	}
	$_SESSION['quizz_step']++;
	
	$key++;
}
if (!isset($_SESSION['last_question']) || !$_SESSION['last_question']) {
	$r = $pdo->query('SELECT count(*) as num_question FROM quizz_questions');
	$data = $r->fetchColumn();
	if ((int) $data == $_SESSION['quizz_step']) {
		$_SESSION['last_question'] = true;
	} else {
		$_SESSION['last_question'] = false;
	}
}

if (isset($_SESSION['last_question']) && $_SESSION['last_question']===true) {
	redirect('results.php');
} else {
	$results = $pdo->query('SELECT qq.data AS question, qr.id AS rid, qr.qid, qr.data AS reponse
	FROM quizz_questions AS qq
		LEFT JOIN quizz_responses AS qr ON (qr.qid = qq.id)
	WHERE qq.id='.$key);

	$data = $results->fetchAll(PDO::FETCH_ASSOC);

	$tpl->data      = $data;
	$tpl->questions = $data[0]['question'];
	$tpl->render('index');
}

// Fichier de stockage des informations d'inclusion
/*
$fp = fopen('/tmp/wp.json', 'w');
if ($fp) {
    $clue = inclued_get_data();
    if ($clue) {
        fwrite($fp, serialize($clue));
    }
    fclose($fp);
}
*/