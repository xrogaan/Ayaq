<?php

$tpl->addFile('results','admin-results.tpl.phtml');

$qid = (empty($_GET['qid'])) ? false : (int) $_GET['qid'];
define('QID', $qid);

$results_name  = $db->fetchPairs("SELECT id, name FROM quizz_results");
ksort($results_name);
$data = $db->fetchAllAsDict2('qid','rid', "SELECT qq.data AS question, qr.id AS rid, qr.qid, qr.data AS reponse
FROM quizz_questions AS qq
	LEFT JOIN quizz_responses AS qr ON (qr.qid = qq.id)");