<?php
/**
 * SCRIPT: exit.inc.php
 * PURPOSE: Standard exit routine for all cronjobs.
 * 
 * This script is included at the end of all cronjob scripts to perform standard
 * cleanup and exit routines. It ensures that any necessary finalization tasks
 * are completed before the script terminates, such as closing database connections,
 * logging, or sending notifications.
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
	### STANDARD EXIT ROUTINE
	###

	$execution_time	= microtime(true) - $time_start;
	
	$execution_text = ($execution_time > 120) 
    	? round($execution_time / 60, 2) . " minutes" 
    	: round($execution_time, 2) . " seconds";

	echo date("[G:i:s] ") . PHP_EOL;
	echo date("[G:i:s] ") . 'Memory usage' . PHP_EOL;
	echo date("[G:i:s] ") . '- current usage: ' . round(memory_get_usage() / (1024 * 1024)) . ' MB' . PHP_EOL;
	echo date("[G:i:s] ") . '- peak usage: ' . round(memory_get_peak_usage() / (1024 * 1024)) . ' MB' . PHP_EOL;
	echo date("[G:i:s] ") . PHP_EOL;
	echo date("[G:i:s] ") . 'Total Execution Time: ' . $execution_text . PHP_EOL;