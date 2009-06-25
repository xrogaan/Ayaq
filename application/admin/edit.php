<?php
/**
 * @category Ayaq
 * @copyright Copyright (c) 2009, Bellière Ludovic
 * @license http://opensource.org/licenses/mit-license.php MIT license
 */

$tpl->addFile('edit','admin-edit.tpl.phtml');

$qid = (empty($_GET['qid'])) ? false : (int) $_GET['qid'];
define('QID', $qid);

$results_name  = $db->fetchPairs("SELECT id, name FROM quizz_results");
ksort($results_name);
$data = $db->fetchAllAsDict2('qid','rid', "SELECT qq.data AS question, qr.id AS rid, qr.qid, qr.data AS reponse
FROM quizz_questions AS qq
	LEFT JOIN quizz_responses AS qr ON (qr.qid = qq.id)");

foreach ($data as $tmp_qid => $value) {
	foreach($value as $vdata) {
		$questions[$tmp_qid] = $vdata['question'];
	}
}

if (!$qid) {
	$url->redirect('/admin');
}

$response_data = $db->fetchAllAsDict2('response_id', 'result_id',
	"SELECT qr.qid AS question_id, qr.id as response_id, qrp.result_id, qre.name AS result_name, qrp.points, qr.data AS response_data\n"
	. "FROM quizz_responses AS qr\n"
	. "	LEFT JOIN (quizz_responses_points AS qrp, quizz_results AS qre) ON (qr.id = qrp.response_id AND qre.id=qrp.result_id)\n"
	. "WHERE qr.qid=%s;", $qid);

if (isset($_POST['points'])) {
	$pre_insert = $db->prepare('INSERT INTO quizz_responses_points (response_id,result_id,points) VALUES (:response_id,:result_id,:points)');
	$pre_update = $db->prepare('UPDATE quizz_responses_points SET points=:points WHERE response_id=:response_id AND result_id=:result_id');
	
	$points = $_POST['points'];
	foreach ($points as $_rid => $tmp) {
		if (isset($response_data[$_rid])) {
			foreach ($tmp as $_result_id => $_data) {
				if (empty($tmp)) {
					redirectError('admin.php?action=edit&qid='.QID,"Vous n'avez pas remplis tout les champs.");
				}
				$sql_data = array(
					':response_id' => $_rid,
					':result_id'   => $_result_id,
					':points'      => $_data
				);
				if (isset($response_data[$_rid][$_result_id])) {
					$pre_update->execute($sql_data);
				} else {
					$pre_insert->execute($sql_data);
				}
			}
		}
	}
	$url->redirect('admin/show','#qid_'.QID);
	
}

$tpl->results_name  = $results_name;
$tpl->response_data = $response_data;
$tpl->questions     = $questions;
$tpl->data          = $data;
$tpl->qid = $qid;

echo $tpl->render('edit');