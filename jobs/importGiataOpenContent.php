<?php
/**
 * SCRIPT: importGiataOpenContent.php
 * PURPOSE: Download Open Content in XML feeds from GIATA and insert the data into the database.
 * 
 * This script fetches XML data from specified GIATA URLs, processes the data, and inserts it into
 * the appropriate database tables. It handles various types of content such as accommodations, 
 * chains, cities, destinations, images, room types, texts, variant groups, and variants. The script 
 * ensures that the database is updated with the latest content from the GIATA feed.
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
require 'classes/GiataOpenContentImporter.php';
require 'classes/ExitHandler.php';

// Set defaults
date_default_timezone_set(	'Europe/Amsterdam');
mb_internal_encoding(		'UTF-8');
setlocale(LC_ALL,			'nl_NL.utf8');

// Parse the DB configuration file
$path		=	substr(__DIR__, 0, mb_strrpos(__DIR__, '/'));
$filename	=	$path . '/config/db.ini';
if (($dbConfig = parse_ini_file($filename, FALSE, INI_SCANNER_TYPED)) === FALSE) {
	throw new Exception("Parsing file " . $filename	. " FAILED");
}

// Register the exit handler
$timeStart			=	microtime(true);
register_shutdown_function([new ExitHandler($timeStart), 'handleExit']);

// URLs to fetch XML data from
$inputUrls = ['https://giatadrive.com/europarcs/xml', 'https://myhotel.giatamedia.com/hotel-directory/xml'];

// Create an instance of the importer and run the import
try {
	$db       = new Database($dbConfig);
    $importer = new GiataOpenContentImporter($db, $inputUrls);
    $importer->import();
} catch (PDOException $e) {
    echo 'Caught PDOException: ' . $e->getMessage();
} catch (Exception $e) {
    echo 'Caught Exception: ' . $e->getMessage();
} finally {
    // The exit handler will be called automatically at the end of the script
}