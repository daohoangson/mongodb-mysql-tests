<?php

require_once('common.php');

function doMySQL($i) {
	echo 'MySQL #', $i, "\n";
	require('mysql.php');
	
	return array(
		$elapsedTimeInsert,
		$elapsedTimeQuery,
		$elapsedTimeDelete
	);
}

function doMongoDB($i) {
	echo 'MongoDB #', $i, "\n";
	require('mongodb.php');
	
	return array(
		$elapsedTimeInsert,
		$elapsedTimeQuery,
		$elapsedTimeDelete
	);
}

$result = array();
for ($i = 0; $i < NUMBER_OF_TESTS; $i++) {
	$result[$i] = array();
	$result[$i]['mysql'] = doMySQL($i);
	$result[$i]['mongodb'] = doMongoDB($i);
}

$sum = array(
	'mysql' => array(),
	'mongodb' => array()
);
foreach ($result as $oneResult) {
	foreach ($oneResult as $system => $systemResult) {
		foreach ($systemResult as $key => $value) {
			if (!isset($sum[$system][$key])) $sum[$system][$key] = 0;
			
			if (empty($value)) {
				// this is unacceptable!
				die("Empty value for $system, $key!");
			}
			
			$sum[$system][$key] += $value;
		}
	}
}

$avg = array(
	'mysql' => array(),
	'mongodb' => array()
);
foreach ($sum as $system => $systemSum) {
	foreach ($systemSum as $key => $value) {
		$avg[$system][$key] = sprintf("%.5f", $value / count($result));
	}
}

echo "\nRESULT (tests    : ", NUMBER_OF_TESTS ," times each system)\n";
echo 'RECORD_COUNT     : ', RECORD_COUNT, "\n";
echo 'QUERY_TEST_COUNT : ', QUERY_TEST_COUNT, "\n";
echo 'DELETE_TEST_COUNT: ', DELETE_TEST_COUNT, "\n";
echo "\n";
echo 'MySQL            : ', implode(', ', $avg['mysql']), "\n";
echo 'MongoDB          : ', implode(', ', $avg['mongodb']), "\n";
echo "\n";