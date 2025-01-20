<?php
/*

	SCRIPT:		exit.inc.php
	
	PURPOSE:	Standard exit routine for all cronjobs.
	
	Copyright 2024 Fred Onis - All rights reserved.

*/
	###
	### STANDARD EXIT ROUTINE
	###

	$execution_time	= microtime(true) - $time_start;
	
	($execution_time > 120)	?	$text		= round($execution_time / 60, 2) . " minutes"
							:	$text		= round($execution_time     , 2) . " seconds";
	
	echo date("[G:i:s] ") . PHP_EOL;
	echo date("[G:i:s] ") . 'Memory usage' . PHP_EOL;
	echo date("[G:i:s] ") . '- current usage: '	. round(memory_get_usage()		/ (1024 * 1024)) . ' MB' . PHP_EOL;
	echo date("[G:i:s] ") . '- peak usage: '	. round(memory_get_peak_usage() / (1024 * 1024)) . ' MB' . PHP_EOL;
	echo date("[G:i:s] ") . PHP_EOL;
	echo date("[G:i:s] ") . 'Total Execution Time: ' . $text . PHP_EOL;