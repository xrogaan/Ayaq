<?php

$tpl->addFile('dashboard','admin-dashboard.tpl.phtml');

$stats = array (
    'question_count' => array_pop($db->fetch('SELECT count(*) as question_count FROM quizz_questions')),
    'response_count' => array_pop($db->fetch('SELECT count(*) as response_count FROM quizz_responses')),
    'results_count' => array_pop($db->fetch('SELECT count(*) as results_count FROM quizz_results qrr'))
);


$tpl->assign($stats);

echo $tpl->render('dashboard');