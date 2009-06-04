<?php
/**
 * @category Ayaq
 * @copyright Copyright (c) 2009, Bellière Ludovic
 * @license http://opensource.org/licenses/mit-license.php MIT license
 */

$results = $db->fetchAllAsDict2('qid','rid', "SELECT qr.qid, qr.id AS rid, qq.data AS question, qr.data AS response
FROM quizz_questions AS qq
    LEFT JOIN quizz_responses AS qr ON (qr.qid = qq.id)");
$response_points = $db->fetchAllGroupBy('response_id',"SELECT qrp.response_id, qrp.result_id, qrp.points
FROM quizz_responses_points AS qrp");

$sxe = new SimpleXmlElement('<?xml version="1.0" encoding="UTF-8"?><questions></questions>');

$nextQuestion = true;
foreach ($results as $qid => $question_data) {
    foreach ($question_data as $rid => $response_data) {
        if ($nextQuestion) {
            $question = $sxe->addChild('question');
            $question->addChild('id',$qid);
            $question->addChild('data',$response_data['question']);
            $nextQuestion = false;
            $responses = $question->addChild('responses');
        }
        $response = $responses->addChild('response');
        $response->addChild('id',$rid);
        $response->addChild('qid',$qid);
        $response->addChild('data',$response_data['response']);
        $pointsData = $response->addChild('pointsData');
        foreach ($response_points[$rid] as $point_data) {
            $point = $pointsData->addChild('point');
            $point->addChild('response_id', $rid);
            $point->addChild('result_id', $point_data['result_id']);
            $point->addChild('points', $point_data['points']);
        }
    }
    $nextQuestion = true;
}

header('Content-Type: text/xml');
$doc = new DOMDocument('1.0');
$doc->preserveWhiteSpace = false;
$doc->loadXML( $sxe->asXML());
$doc->formatOutput = true;
echo $doc->saveXML();