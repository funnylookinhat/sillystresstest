<?php defined('LOCKED-ACCESS') or die('No direct access allowed.');

function helper_generatehash()
{
	// Source for hash.
	$hash = md5(time().rand(1000,9999).microtime().rand(1000,9999).time()).
			md5(time().rand(1000,9999).microtime().rand(1000,9999).time()).
			md5(time().rand(1000,9999).microtime().rand(1000,9999).time()).
			md5(time().rand(1000,9999).microtime().rand(1000,9999).time()).
			md5(time().rand(1000,9999).microtime().rand(1000,9999).time()).
			md5(time().rand(1000,9999).microtime().rand(1000,9999).time()).
			md5(time().rand(1000,9999).microtime().rand(1000,9999).time()).
			md5(time().rand(1000,9999).microtime().rand(1000,9999).time());

	for( $a = 0; $a < rand(0,1000); $a++ ) {
		$hash = md5(substr($hash,rand(0,223),rand(0,32))).
				md5(substr($hash,rand(0,223),rand(0,32))).
				md5(substr($hash,rand(0,223),rand(0,32))).
				md5(substr($hash,rand(0,223),rand(0,32))).
				md5(substr($hash,rand(0,223),rand(0,32))).
				md5(substr($hash,rand(0,223),rand(0,32))).
				md5(substr($hash,rand(0,223),rand(0,32))).
				md5(substr($hash,rand(0,223),rand(0,32)));
	}

	return $hash;
}

function helper_generateshorthash()
{
	$hash = md5(time().rand(1000,9999).microtime().rand(1000,9999).time()).
			md5(time().rand(1000,9999).microtime().rand(1000,9999).time()).
			md5(time().rand(1000,9999).microtime().rand(1000,9999).time()).
			md5(time().rand(1000,9999).microtime().rand(1000,9999).time()).
			md5(time().rand(1000,9999).microtime().rand(1000,9999).time()).
			md5(time().rand(1000,9999).microtime().rand(1000,9999).time());

	return substr($hash,rand(0,96),rand(1,64));
}