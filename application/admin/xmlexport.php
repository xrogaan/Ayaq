<?php
/**
 * @category Ayaq
 * @copyright Copyright (c) 2009, Bellière Ludovic
 * @license http://opensource.org/licenses/mit-license.php MIT license
 */


$results = $db->fetchAllAsDict('id', 'SELECT id, name, description FROM quizz_results');
$questions = $db->fetchAllAsDict('id', 'SELECT id, data FROM quizz_questions');
$responses = $db->fetchAllAsDict('id', 'SELECT id, qid, data FROM quizz_responses');
$points = $db->fetchAllAsDict2('response_id','result_id','SELECT response_id, result_id, points FROM quizz_responses_points');

$export = array(
    'results' => $results,
    'questions' => $questions,
    'responses' => $responses,
    'points' => $points
);

require 'spyc-0.4.2/spyc.php';

$x = Spyc::YAMLDump($export);
header('Content-Type: text/yaml');
header('Content-Disposition: attachment; filename="quizz-data.yml"');
echo $x;

