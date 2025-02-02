<?php
header('Content-Type: text/html; charset=utf-8');
/**
 * SCRIPT: giata.php
 * PURPOSE: Show data from Giata in a dashboard.
 * 
 * This script generates a web-based dashboard to display data from Giata. It fetches data from the database
 * and presents it in various HTML tables. The dashboard includes information on accommodations, chains, cities,
 * destinations, images, room types, texts, variant groups, and variants. The script ensures that the data is
 * presented in a user-friendly manner, allowing for easy navigation and interaction.
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

require 'classes/Database.php';
require 'classes/GiataDashboard.php';
	
// Set defaults
date_default_timezone_set('Europe/Amsterdam');
mb_internal_encoding('UTF-8');
setlocale(LC_ALL, 'nl_NL.utf8');
	
$dbConfigPath = mb_substr(__DIR__, 0, mb_strrpos(__DIR__, '/'));
$dbConfigPath = mb_substr($dbConfigPath, 0, mb_strrpos($dbConfigPath, '/')) . '/config/db.ini';
		
// Create an instance of the dashboard and get the data
try {
		
		$dashboard = new GiataDashboard($dbConfigPath);
	
		$html_definitions_attributes	=	$dashboard->getHtmlDefinitionsAttributes();
		$html_definitions_contexttree	=	$dashboard->getHtmlDefinitionsContexttree();
		$html_definitions_facts			=	$dashboard->getHtmlDefinitionsFacts();
		$html_definitions_motif_types	=	$dashboard->getHtmlDefinitionsMotifTypes();
		$html_definitions_units			=	$dashboard->getHtmlDefinitionsUnits();
		$html_accommodations			=	$dashboard->getHtmlAccommodations();
		$html_chains					=	$dashboard->getHtmlChains();
		$html_cities					=	$dashboard->getHtmlCities();
		$html_destinations				=	$dashboard->getHtmlDestinations();
		$html_roomtypes					=	$dashboard->getHtmlRoomtypes();
		$html_texts						=	$dashboard->getHtmlTexts();
		
		require 'templates/giata.php';
	
} catch (PDOException $e) {
	echo date("[G:i:s] ") . 'Caught PDOException: ' . $e->getMessage() . PHP_EOL;
} catch (Exception $e) {
	echo date("[G:i:s] ") . 'Caught Exception: ' . $e->getMessage() . PHP_EOL;
} finally {
}