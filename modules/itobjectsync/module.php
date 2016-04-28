<?php

$Module = array( 'name' => 'itobjectsync' );

$ViewList = array();

$ViewList['test'] = array(
    'script'					=>	'test.php',
    'params'					=> 	array(),
    'unordered_params'			=> 	array(),
    'single_post_actions'		=> 	array(),
    'post_action_parameters'	=> 	array()
);

$ViewList['testFiltri'] = array(
    'script'					=>	'testFiltri.php',
    'params'					=> 	array(),
    'unordered_params'			=> 	array(),
    'single_post_actions'		=> 	array(),
    'post_action_parameters'	=> 	array()
);

$ViewList['testTags'] = array(
    'script'					=>	'testTags.php',
    'params'					=> 	array(),
    'unordered_params'			=> 	array(),
    'single_post_actions'		=> 	array(),
    'post_action_parameters'	=> 	array()
);

$ViewList['client'] = array(
    'script'			=>	'client.php',
    'params'			=> 	array('repository'),
    'unordered_params'		=> 	array(),
    'single_post_actions'	=> 	array(),
    'post_action_parameters'	=> 	array()
);

$ViewList['tematiche'] = array(
    'script'			=>	'tematiche.php',
    'params'			=> 	array(),
    'unordered_params'		=> 	array(),
    'single_post_actions'	=> 	array(),
    'post_action_parameters'	=> 	array()
);

$ViewList['tematichequery'] = array(
    'script'			=>	'tematichequery.php',
    'params'			=> 	array('class_id', 'tematiche', 'days'),
    'unordered_params'		=> 	array(),
    'single_post_actions'	=> 	array(),
    'post_action_parameters'	=> 	array()
);

$FunctionList = array();
$FunctionList['client'] = array();
$FunctionList['tematiche'] = array();
$FunctionList['tematichequery'] = array();

?>