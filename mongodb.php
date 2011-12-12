<?php

require_once('common.php');

$mongo = new Mongo();

// setup the collection
$students = $mongo->selectDB('test')->selectCollection('students');
$students->drop(); // make sure the collection is empty

// test insert
	$startTime = getTime();
	for ($i = 0; $i < RECORD_COUNT; $i++) {
		$students->insert(array(
			'student_id' => $i,
			'student_name' => sprintf('Student #%d', $i)
		), array('timeout' => 0));
	}

	$elapsedTimeInsert = getTime() - $startTime;
	$count = $students->find(array(), array('timeout' => 0));
	$count->timeout(0);
	$count = $count->count();
	$checked = $count == RECORD_COUNT;
	if ($checked) {
		echo 'Inserted ', RECORD_COUNT, ' records in ', $elapsedTimeInsert, "\n";
	} else {
		echo 'Failed inserting records! Current count: ', $count, "\n";
		$elapsedTimeInsert = 0;
	}

// test query
	$startTime = getTime();
	for ($i = 0; $i < QUERY_TEST_COUNT; $i++) {
		$studentId = getRandomStudentId();
		$found = $students->findOne(array('student_id' => $studentId));
		
		if (empty($found) OR $found['student_id'] != $studentId) {
			var_dump($studentId, $found);
			die("Incorrect result!\n");
		}
	}
	$elapsedTimeQuery = getTime() - $startTime;
	echo 'Queried ', QUERY_TEST_COUNT, ' times in ', $elapsedTimeQuery, "\n";

// test delete
	$startTime = getTime();
	for ($i = 1; $i <= DELETE_TEST_COUNT; $i++) {
		$students->remove(array('student_id' => $i));
	}

	$elapsedTimeDelete = getTime() - $startTime;
	$count = $students->find(array(), array('timeout' => 0));
	$count->timeout(0);
	$count = $count->count();
	$checked = $count == (RECORD_COUNT - DELETE_TEST_COUNT); 
	if ($checked) {
		echo 'Deleted ', DELETE_TEST_COUNT, ' records in ', $elapsedTimeDelete, "\n";
	} else {
		echo 'Failed deleting records! Current count: ', $count, "\n";
		$elapsedTimeDelete = 0;
	}

// clean up
$students->drop();
$mongo->close();