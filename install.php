<?php
/**
 * Silly Stress Test
 * @author David Overcash <funnylookinhat@gmail.com>
 * Install mysql tables, etc.
 */

define('LOCKED-ACCESS',"blech.");
// Protect access to config.php with this.
// <?php defined('SYSPATH') or die('No direct access allowed.');

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

if( ! $tables_result OR mysql_num_rows($tables_result) )
	die("Install can only be run once.\n");

// Setup Tables
$hashes_table_query = "CREATE TABLE IF NOT EXISTS `hashes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hash` varchar(256) DEFAULT NULL,
  `count` DECIMAL( 10, 3 ) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

if( ! mysql_query($hashes_table_query) )
	die("Failed to create hashes table.\n");

$results_table_query = "CREATE TABLE IF NOT EXISTS `results` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hash_string` varchar(64) DEFAULT NULL,
  `timestamp` bigint(20) unsigned DEFAULT NULL,
  `total` DECIMAL( 20, 3 ) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

if( ! mysql_query($results_table_query) )
	die("Failed to create results table.\n");

// Create test data.
for( $i = 0; $i < 50000; $i++ )
{
	$hash = helper_generatehash();
	$insert_hash_query = 'INSERT INTO hashes (hash,count) VALUES ("'.$hash.'",'.rand(100000,999999).'.'.rand(000,999).');';
	if( ! mysql_query($insert_hash_query) )
		die("Could not insert hash: ".mysql_error()."\n");
}

die("Ready to run index.php\n");