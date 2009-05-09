<?php

$tpl->addFile('_begin','_header.tpl.phtml')
	->addFile('_end','_footer.tpl.phtml')
	->addFile('results','admin-results.tpl.phtml')

$qid = (empty($_GET['qid'])) ? false : (int) $_GET['qid'];
define('QID', $qid);

$results_name  = $pdo->fetchPairs("SELECT id, name FROM quizz_results");
ksort($results_name);
$data = $pdo->fetchAllAsDict2('qid','rid', "SELECT qq.data AS question, qr.id AS rid, qr.qid, qr.data AS reponse
FROM quizz_questions AS qq
	LEFT JOIN quizz_responses AS qr ON (qr.qid = qq.id)");