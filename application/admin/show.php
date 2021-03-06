<?php
// list q&a

$tpl->addFile('show','admin/show.tpl.phtml');

$qid = (empty($_GET['qid'])) ? false : (int) $_GET['qid'];
define('QID', $qid);

$results_name  = $db->fetchPairs("SELECT id, name FROM quizz_results");
ksort($results_name);

if (QID) {
    $where = ' WHERE qr.qid = ' . QID;
} else {
    $where = '';
}
$questionsAndAnswers = $db->fetchAllAsDict2('qid','rid', "SELECT qq.data AS question, qr.id AS rid, qr.qid, qr.data AS reponse
FROM quizz_questions AS qq
	LEFT JOIN quizz_responses AS qr ON (qr.qid = qq.id) $where");
unset($where);

// build question list
foreach ($questionsAndAnswers as $tmp_qid => $value) {
	foreach($value as $vdata) {
		$questions[$tmp_qid] = $vdata['question'];
	}
}
unset($value,$vdata,$tmp_qid);

// build response id list
$responses_id = array();
foreach ($questionsAndAnswers as $_qid => $tmp) {
	if ($_qid > QID) {
		break;
	}
	foreach ($tmp as $response_id => $tmp_data) {
		$responses_id[] = $response_id;
	}
}

if (QID) {
    $where = 'WHERE qrp.response_id IN ('. implode(',',$responses_id) .')';
} else {
    $where = '';
}
$response_points = $db->fetchAllGroupBy('response_id',"SELECT qrp.response_id, qrp.result_id, qrp.points, qr.name as result_name, qr.description as result_description
FROM quizz_responses_points AS qrp
	LEFT JOIN quizz_results AS qr ON (qr.id = qrp.result_id)
$where");
unset($where);

$tpl->data            = $questionsAndAnswers;
$tpl->questions       = $questions;
$tpl->response_points = $response_points;
$tpl->results_name    = $results_name;

echo $tpl->render('show');