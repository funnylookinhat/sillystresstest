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

if( ! file_exists('config.php') )
	die("No config file!\n");

$config = include('config.php');

// Helper functions.
require_once('helpers.php');

$conn = mysql_connect(
	$config['server'],
	$config['username'],
	$config['password']
);

if( ! $conn ) 
	die("Could not connect to database.\n");

mysql_select_db($config['database'],$conn);

// Verify tables.
$tables_result = mysql_query('SHOW TABLES;',$conn);

if( ! mysql_num_rows($tables_result) )
	die("Please run install.php\n");

$start_time = time();

// Run Test
for( $i = 0; $i < 10; $i++ )
{
	$short_hash = helper_generateshorthash();
	echo "SEARCH: ".$short_hash."\n";

	$count_result = mysql_query("SELECT SUM(count) as total FROM hashes WHERE hash LIKE '%".$short_hash."%'");
	if( ! $count_result )
		die("Could not query for count: ".mysql_error()."\n");

	$count = mysql_fetch_array($count_result);

	if( ! mysql_query("INSERT INTO results (hash_string,timestamp,total) VALUES ('".$short_hash."','".time()."','".$count['total']."')") )
		die("Could not insert test result: ".mysql_error()."\n");
}

// Remove Blanks
if( ! mysql_query("DELETE FROM results WHERE total = 0.000") )
	die("Failed to remove blanks: ".mysql_error()."\n");

// PHP Loop Longest Result
$results_result = mysql_query('SELECT * FROM results');

if( ! $results_result )
	die("Failed to query for results: ".mysql_error()."\n");

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

echo "Best so far: ".$longest_hash." (".$longest_count.")\n";
die("Complete: ".( $end_time - $start_time )." seconds.\n");