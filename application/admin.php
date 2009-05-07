<?php

$tpl = new templates();

$tpl->addFile('_begin','_header.tpl.phtml')
	->addFile('_end','_footer.tpl.phtml')
	->addFile('dashboard','admin-dashboard.tpl.phtml')
	->addFile('admin-show', 'admin-show.tpl.phtml')
	->addFile('admin-results', 'admin-results.tpl.phtml')
	->addFile('admin-edit', 'admin-edit.tpl.phtml');

$tpl->action = $action = empty($_GET['action']) ? 'index' : $_GET['action'];
$qid = (empty($_GET['qid'])) ? false : (int) $_GET['qid'];
define('QID', $qid);

$results_name  = $pdo->fetchPairs("SELECT id, name FROM quizz_results");
ksort($results_name);
$data = $pdo->fetchAllAsDict2('qid','rid', "SELECT qq.data AS question, qr.id AS rid, qr.qid, qr.data AS reponse
FROM quizz_questions AS qq
	LEFT JOIN quizz_responses AS qr ON (qr.qid = qq.id)");

foreach ($data as $tmp_qid => $value) {
	foreach($value as $vdata) {
		$questions[$tmp_qid] = $vdata['question'];
	}
}
unset($value,$vdata,$tmp_qid);

switch ($action) {
	case 'edit':
		
		if (!$qid) {
			redirect('/admin');
		}
		
		$response_data = $pdo->fetchAllAsDict2('response_id', 'result_id',
			"SELECT qr.qid AS question_id, qr.id as response_id, qrp.result_id, qre.name AS result_name, qrp.points, qr.data AS response_data\n"
			. "FROM quizz_responses AS qr\n"
			. "	LEFT JOIN (quizz_responses_points AS qrp, quizz_results AS qre) ON (qr.id = qrp.response_id AND qre.id=qrp.result_id)\n"
			. "WHERE qr.qid=%s;", $qid);
		
		if (isset($_POST['points'])) {
			$pre_insert = $pdo->prepare('INSERT INTO quizz_responses_points (response_id,result_id,points) VALUES (:response_id,:result_id,:points)');
			$pre_update = $pdo->prepare('UPDATE quizz_responses_points SET points=:points WHERE response_id=:response_id AND result_id=:result_id');
			
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
			redirect('admin.php?action=show#qid_'.QID);
			
		}
		
		$tpl->results_name  = $results_name;
		$tpl->response_data = $response_data;
		$tpl->questions     = $questions;
		$tpl->data          = $data;
		$tpl->qid = $qid;
		
		$tpl->render('admin-edit');
		break;
	case 'show-results':
		$results = $pdo->query('SELECT * FROM quizz_results');
		$tpl->results = $results->fetchAll(PDO::FETCH_ASSOC);
		$tpl->render('admin-results');
		break;
	case 'show':
		$responses_id = array();
		foreach ($data as $_qid => $tmp) {
			if ($_qid > QID) {
				break;
			}
			foreach ($tmp as $response_id => $tmp_data) {
				$responses_id[] = $response_id;
			}
		}
		$where = ($qid) ? 'WHERE qrp.response_id IN ('. implode(',',$responses_id) .')' : '';
		
		$response_points = $pdo->fetchAllGroupBy('response_id',"SELECT qrp.response_id, qrp.result_id, qrp.points, qr.name as result_name, qr.description as result_description
		FROM quizz_responses_points AS qrp
			LEFT JOIN quizz_results AS qr ON (qr.id = qrp.result_id)
		$where");
		
		$tpl->data            = $data;
		$tpl->questions       = $questions;
		$tpl->response_points = $response_points;
		$tpl->results_name    = $results_name;
		
		$tpl->render('admin-show');
		
		break;
	case 'index':
	default:
		$tpl->data      = $data;
		$tpl->questions = $questions;

		$tpl->render('dashboard');
}
