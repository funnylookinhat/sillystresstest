<?php
/**
 * Silly Stress Test
 * @author David Overcash <funnylookinhat@gmail.com>
 * This is just used to do some basic load testing on distributed systems,
 * If you think it sucks, then send a pull request, I'd love the help.  :)
 */

define('LOCKED-ACCESS',"blech.");
// Protect access to config.php with this.
// <?php defined('LOCKED-ACCESS') or die('No direct access allowed.');

function jsonerror($error)
{
	die(json_encode((object)array(
		'success' => 0,
		'error' => $error,
		'data' => (object)array(),
	)));
}

if( ! file_exists('config.php') )
	jsonerror("No config file!");

$config = include('config.php');

// Helper functions.
require_once('helpers.php');

$conn = mysql_connect(
	$config['server'],
	$config['username'],
	$config['password']
);

if( ! $conn ) 
	jsonerror("Could not connect to database.");

mysql_select_db($config['database'],$conn);

// Verify tables.
$tables_result = mysql_query('SHOW TABLES;',$conn);

if( ! mysql_num_rows($tables_result) )
	jsonerror("Please run install.php");

$start_time = time();

// Run Test
for( $i = 0; $i < 25; $i++ )
{
	$short_hash = helper_generateshorthash();

	$count_result = mysql_query("SELECT SUM(count) as total FROM hashes WHERE hash LIKE '%".$short_hash."%'");
	if( ! $count_result )
		jsonerror("Could not query for count: ".mysql_error());

	$count = mysql_fetch_array($count_result);

	if( ! mysql_query("INSERT INTO results (hash_string,timestamp,total) VALUES ('".$short_hash."','".time()."','".$count['total']."')") )
		jsonerror("Could not insert test result: ".mysql_error());
}

// Remove Blanks
if( ! mysql_query("DELETE FROM results WHERE total = 0.000") )
	jsonerror("Failed to remove blanks: ".mysql_error());

// PHP Loop Longest Result
$results_result = mysql_query('SELECT * FROM results');

if( ! $results_result )
	jsonerror("Failed to query for results: ".mysql_error()."\n");

$longest_hash = FALSE;
$longest_count = FALSE;

while( $result = mysql_fetch_array($results_result) ) 
{
	if( ! $longest_hash )
	{
		$longest_hash = $result['hash_string'];
		$longest_count = $result['total'];
	}
	else if( strlen($longest_hash) < strlen($result['hash_string']) )
	{
		$longest_hash = $result['hash_string'];
		$longest_count = $result['total'];
	}
}

$end_time = time();

die(json_encode((object)array(
	'success' => 1,
	'error' => "",
	'data' => (object)array(
		'time' => intval( $end_time - $start_time ),
		'hash' => $longest_hash,
		'count' => $longest_count,
	),
)));
