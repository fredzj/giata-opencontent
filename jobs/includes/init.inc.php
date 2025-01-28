<?php
/**
 * SCRIPT: init.inc.php
 * PURPOSE: Standard initialization routine for all cronjobs.
 * 
 * This script is included at the beginning of all cronjob scripts to perform standard
 * initialization tasks. It sets up the necessary environment, including loading configuration
 * files, establishing database connections, and initializing any required variables or services.
 * 
 * @package giata-opencontent
 * @version 1.0.0
 * @since 2024
 * @license MIT
 * 
 * COPYRIGHT: 2024 Fred Onis - All rights reserved.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * 
 * @author Fred Onis
 */
	###
	### STANDARD INIT ROUTINE
	###

	date_default_timezone_set(	'Europe/Amsterdam');
	mb_internal_encoding(		'UTF-8');
	setlocale(LC_ALL,			'nl_NL.utf8');

	// Start time for execution time calculation
	$time_start				=	microtime(true);

	// Determine the server domains root
	$server_domains_root	=	substr(__DIR__, 0, mb_strrpos(__DIR__, '/'));
	$server_domains_root	=	substr($server_domains_root, 0, mb_strrpos($server_domains_root, '/'));
	define("WEBROOT", $server_domains_root . '/public_html/');

	// Parse the DB configuration file
	$config_file_name_db	=	$server_domains_root . '/config/db.ini';
	if (($dbconfig			=	parse_ini_file($config_file_name_db,	FALSE, INI_SCANNER_TYPED)) === FALSE) {
		throw new Exception("Parsing file " . $config_file_name_db	. " FAILED");
	}
	
	// Initialize output data lines counter
	$output_data_lines		=	0;
	$outputDataLines		=	0;
