<?php
/**
 * SCRIPT: importGiataDefinitions.php
 * PURPOSE: Download definitions in a JSON feed from GIATA and insert the data into the database.
 * 
 * This script fetches JSON data from a specified GIATA URL, processes the data, and inserts it into
 * the appropriate database tables. It handles various types of definitions such as context trees, facts,
 * attributes, units, and motif types. The script ensures that the database is updated with the latest
 * definitions from the GIATA feed.
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

require __DIR__ . '/classes/Database.php';
require __DIR__ . '/classes/ExitHandler.php';
require __DIR__ . '/classes/GiataDefinitionsImporter.php';
require __DIR__ . '/classes/Log.php';

// Set defaults
date_default_timezone_set(	'Europe/Amsterdam');
mb_internal_encoding(		'UTF-8');
setlocale(LC_ALL,			'nl_NL.utf8');

$dbConfigPath = substr(__DIR__, 0, mb_strrpos(__DIR__, '/')) . '/config/db.ini';
$inputUrl = 'https://giatadrive.com/i18n/facts/en';
$log = new Log();

// Create an instance of the importer and run the import
try {
    $importer = new GiataDefinitionsImporter($dbConfigPath, $inputUrl);
    $importer->import();
} catch (PDOException $e) {
    $log->error('Caught PDOException: ' . $e->getMessage());
} catch (Exception $e) {
    $log->error('Caught Exception: ' . $e->getMessage());
} finally {
	// The exit handler will be called automatically at the end of the script
}