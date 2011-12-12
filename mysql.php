<?php

require_once('common.php');

$mysqli = new mysqli('127.0.0.1', 'mysql', '123456', 'test');

// setup the table
$table = $mysqli->query("SHOW TABLES LIKE 'students'");
if ($table->num_rows > 0) {
	$mysqli->query("DROP TABLE students");
}
$mysqli->query("CREATE TABLE students (student_id INT(10) UNSIGNED, student_name VARCHAR(255))");

// test insert
	$startTime = getTime();
	for ($i = 0; $i < RECORD_COUNT; $i++) {
		$mysqli->query(sprintf("INSERT INTO students VALUES(%d, 'Student %d')", $i, $i));
	}

	$elapsedTimeInsert = getTime() - $startTime;
	$result = $mysqli->query("SELECT COUNT(*) AS total FROM students")->fetch_object();
	$checked = $result->total == RECORD_COUNT;
	if ($checked) {
		echo 'Inserted ', RECORD_COUNT, ' records in ', $elapsedTimeInsert, "\n";
	} else {
		echo 'Failed inserting records! Current count: ', $result->total, "\n";
		$elapsedTimeInsert = 0;
	}
	
// test query
	$startTime = getTime();
	for ($i = 0; $i < QUERY_TEST_COUNT; $i++) {
		$studentId = getRandomStudentId();
		$found = $mysqli->query(sprintf("SELECT * FROM students WHERE student_id = %d", $studentId))->fetch_object();
		
		if (empty($found) OR $found->student_id != $studentId) {
			var_dump($studentId, $found);
			die("Incorrect result!\n");
		}
	}
	$elapsedTimeQuery = getTime() - $startTime;
	echo 'Queried ', QUERY_TEST_COUNT, ' times in ', $elapsedTimeQuery, "\n";
	

// test delete
	$startTime = getTime();
	for ($i = 1; $i <= DELETE_TEST_COUNT; $i++) {
		$mysqli->query(sprintf("DELETE FROM students WHERE student_id = %d", $i));
	}
	
	$elapsedTimeDelete = getTime() - $startTime;
	$result = $mysqli->query("SELECT COUNT(*) AS total FROM students")->fetch_object();
	$checked = $result->total == (RECORD_COUNT - DELETE_TEST_COUNT);
	if ($checked) {
		echo 'Deleted ', DELETE_TEST_COUNT, ' records in ', $elapsedTimeDelete, "\n";
	} else {
		echo 'Failed deleting records! Current count: ', $result->total, "\n";
		$elapsedTimeDelete = 0;
	}

// clean up
$mysqli->query("DROP TABLE students");
$mysqli->close();