<?php

define('RECORD_COUNT', 19000);
define('QUERY_TEST_COUNT', RECORD_COUNT / 10);
define('DELETE_TEST_COUNT', RECORD_COUNT / 10);
define('NUMBER_OF_TESTS', 10);

function getTime() {
	$microtime = microtime();
	$parts = explode(' ', $microtime);
	return floatval($parts[0]) + (intval($parts[1]));
}

function getRandomStudentId() {
	return rand(1, RECORD_COUNT - 1);
}