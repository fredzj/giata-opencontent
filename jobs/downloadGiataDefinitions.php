<?php
/**
 * SCRIPT: downloadGiataDefinitions.php
 * PURPOSE: Download the definitions in a JSON feed from GIATA and insert the data into the database.
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

try {
	
	###
	### STANDARD INIT ROUTINE
	###
	
	require 'includes/init.inc.php';
	require 'includes/vendor_giata_func.inc.php';
	require 'includes/vendor_giata_sql.inc.php';
	
	###
	### CUSTOM INIT ROUTINE
	###
	
	$inputUrl      = 'https://myhotel.giatamedia.com/i18n/facts/nl';
    $outputColumns = [
        'attributes'				=> ['id', 'label', 'valueType', 'units'],
        'contexttree'				=> ['id', 'label', 'parentContextTreeId'],
        'contexttree_facts'			=> ['contextTreeId', 'factId'],
        'facts'						=> ['id', 'label'],
        'facts_attributes'			=> ['factId', 'attributeId'],
        'facts_variantgrouptypes'	=> ['factId', 'variantGroupTypeId'],
        'motif_types'				=> ['id', 'label'],
        'units'						=> ['id', 'label']
    ];
	
	###
	### DATABASE INIT ROUTINE
	###
	
	$dbh    = dbopen($dbconfig);
    $tables = [
        'vendor_giata_definitions_attributes',
        'vendor_giata_definitions_contexttree',
        'vendor_giata_definitions_contexttree_facts',
        'vendor_giata_definitions_facts',
        'vendor_giata_definitions_facts_attributes',
        'vendor_giata_definitions_facts_variantgrouptypes',
        'vendor_giata_definitions_motif_types',
        'vendor_giata_definitions_units'
    ];
    foreach ($tables as $table) {
        dbtruncate($dbh, $table);
    }


	###
	### PROCESSING ROUTINE
	###

	echo date("[G:i:s] ") . 'Reading JSON Feed ' . $inputUrl . PHP_EOL;

	if (($json = file_get_contents($inputUrl)) !== false) {
		
		$decodedJson  =	json_decode($json, true);
        $outputValues = [
            'attributes'				=> [],
            'contexttree'				=> [],
            'contexttree_facts'			=> [],
            'facts'						=> [],
            'facts_attributes'			=> [],
            'facts_variantgrouptypes'	=> [],
            'motif_types'				=> [],
            'units'						=> []
        ];

		foreach ($decodedJson as $language => $subjects) {
			
			foreach ($subjects as $subject => $array) {
			
				foreach ($array as $key => $values) {
				
					switch ($subject) {
						
						case 'contextTree':
							$outputValues['contexttree'][]	=	"('" . $key . "', '" . addslashes($values['label']) . "', '" . "" . "')";
							$outputDataLines++;
							foreach ($values['facts'] as $factId) {
								$outputValues['contexttree_facts'][]	=	"('" . $key . "', '" . $factId . "')";
								$outputDataLines++;
							}
							if (array_key_exists('sub', $values)) {
								foreach ($values['sub'] as $key2 => $values2) {
									$outputValues['contexttree'][]	=	"('" . $key2 . "', '" . addslashes($values2['label']) . "', '" . $key . "')";
									foreach ($values2['facts'] as $factId) {
										$outputValues['contexttree_facts'][]	=	"('" . $key2 . "', '" . $factId . "')";
										$outputDataLines++;
									}
								}
							}
							break;
						case 'facts':
							$outputValues['facts'][]	=	"('" . $key . "', '" . addslashes($values['label']) . "')";
							$outputDataLines++;
							foreach ($values['attributes'] as $fact_attribute) {
								$outputValues['facts_attributes'][]	=	"('" . $key . "', '" . $fact_attribute . "')";
								$outputDataLines++;
							}
							if (array_key_exists('variantGroupTypes', $values)) {
								foreach ($values['variantGroupTypes'] as $fact_variantGroupType) {
									$outputValues['facts_variantgrouptypes'][]	=	"('" . $key . "', '" . $fact_variantGroupType . "')";
									$outputDataLines++;
								}
							}
							break;
						case 'attributes':
							if (array_key_exists('valueType', $values)) {
								$valueType	=	$values['valueType'];
							} else {
								$valueType	=	'';
							}
							if (array_key_exists('units', $values)) {
								$units	=	implode('|', $values['units']);
							} else {
								$units	=	'';
							}
							$outputValues['attributes'][]	=	"('" . $key . "', '" . addslashes($values['label']) . "', '" . $valueType . "', '" . $units . "')";
							$outputDataLines++;
							break;
						case 'units':
							$outputValues['units'][]	    =	"('" . $key . "', '" . addslashes($values['label']) . "')";
							$outputDataLines++;
							break;
						case 'motifTypes':
							$outputValues['motif_types'][]	=	"('" . $key . "', '" . addslashes($values['label']) . "')";
							$outputDataLines++;
							break;
						default:
					}
				}
			}
		}
		dbinsert($dbh, 'vendor_giata_definitions_attributes',				$outputColumns['attributes'],	            array_unique($outputValues['attributes']));
		dbinsert($dbh, 'vendor_giata_definitions_contexttree',				$outputColumns['contexttree'],	            array_unique($outputValues['contexttree']));
		dbinsert($dbh, 'vendor_giata_definitions_contexttree_facts',		$outputColumns['contexttree_facts'],	    array_unique($outputValues['contexttree_facts']));
		dbinsert($dbh, 'vendor_giata_definitions_facts',					$outputColumns['facts'],	                array_unique($outputValues['facts']));
		dbinsert($dbh, 'vendor_giata_definitions_facts_attributes',			$outputColumns['facts_attributes'],	        array_unique($outputValues['facts_attributes']));
		dbinsert($dbh, 'vendor_giata_definitions_facts_variantgrouptypes',	$outputColumns['facts_variantgrouptypes'],	array_unique($outputValues['facts_variantgrouptypes']));
		dbinsert($dbh, 'vendor_giata_definitions_motif_types',				$outputColumns['motif_types'],	            array_unique($outputValues['motif_types']));
		dbinsert($dbh, 'vendor_giata_definitions_units',					$outputColumns['units'],	                array_unique($outputValues['units']));
	}
	
	echo date("[G:i:s] ") . '- ' . $outputDataLines . ' rows processed' . PHP_EOL;

	###
	### DATABASE EXIT ROUTINE
	###
		
	$dbh = null;

	###
	### STANDARD EXCEPTION ROUTINE
	###

} catch (PDOException $e) {
	logError('Caught PDOException: ' . $e->getMessage());
} catch (Exception $e) {
	logError('Caught Exception: '    . $e->getMessage());
} finally {

	###
	### STANDARD EXIT ROUTINE
	###

	require 'includes/exit.inc.php';
}