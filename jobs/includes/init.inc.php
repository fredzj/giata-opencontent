<?php
/*

	SCRIPT:		init.inc.php
	
	PURPOSE:	Standard initialization for all cronjobs.
	
	Copyright 2024 Fred Onis - All rights reserved.

*/
	###
	### STANDARD INIT ROUTINE
	###

	date_default_timezone_set(	'Europe/Amsterdam');
	mb_internal_encoding(		'UTF-8');
	setlocale(LC_ALL,			'nl_NL.utf8');
	$time_start				=	microtime(true);
	$server_domains_root	=	substr(__DIR__, 0, mb_strrpos(__DIR__, '/'));
	$server_domains_root	=	substr($server_domains_root, 0, mb_strrpos($server_domains_root, '/'));
	define("WEBROOT", $server_domains_root . '/public_html/');

	# Parse the DB configuration file
	$config_file_name_db	=	$server_domains_root . '/config/db.ini';
	if (($dbconfig			=	parse_ini_file($config_file_name_db,	FALSE, INI_SCANNER_TYPED)) === FALSE) {
		throw new Exception("Parsing file " . $config_file_name_db	. " FAILED");
	}
	
	$output_data_lines		=	0;
