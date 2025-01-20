<?php
/*

	SCRIPT:		downloadGiataDefinitions.php
	
	PURPOSE:	Download the definitions in a JSON feed from GIATA and insert the data into the database.
	
	Copyright 2024 Fred Onis - All rights reserved.

*/

try {
	
	###
	### STANDARD INIT ROUTINE
	###
	
	require 'includes/init.inc.php';
	require 'includes/vendor_giata_sql.inc.php';
	
	###
	### CUSTOM INIT ROUTINE
	###
	
	$input_url				=	'https://myhotel.giatamedia.com/i18n/facts/nl';
	
	$output_columns_tree	=	['id', 'label', 'parentContextTreeId'];
	$output_columns_trfa	=	['contextTreeId', 'factId'];
	$output_columns_faat	=	['factId', 'attributeId'];
	$output_columns_fact	=	['id', 'label'];
	$output_columns_fava	=	['factId', 'variantGroupTypeId'];
	$output_columns_attr	=	['id', 'label', 'valueType', 'units'];
	$output_columns_unit	=	['id', 'label'];
	$output_columns_moti	=	['id', 'label'];
	
	###
	### DATABASE INIT ROUTINE
	###
	
	$dbh					=	dbopen($dbconfig);
	dbtruncate($dbh, 'vendor_giata_definitions_attributes');
	dbtruncate($dbh, 'vendor_giata_definitions_contexttree');
	dbtruncate($dbh, 'vendor_giata_definitions_contexttree_facts');
	dbtruncate($dbh, 'vendor_giata_definitions_facts');
	dbtruncate($dbh, 'vendor_giata_definitions_facts_attributes');
	dbtruncate($dbh, 'vendor_giata_definitions_facts_variantgrouptypes');
	dbtruncate($dbh, 'vendor_giata_definitions_motif_types');
	dbtruncate($dbh, 'vendor_giata_definitions_units');

	###
	### PROCESSING ROUTINE
	###

	echo date("[G:i:s] ") . 'Reading JSON Feed ' . $input_url . PHP_EOL;

	# Read JSON contents
	if (($json = file_get_contents($input_url)) !== false) {
		
		$decoded_json		=	json_decode($json, true);

		$output_values_attr	=	[];
		$output_values_faat	=	[];
		$output_values_fact	=	[];
		$output_values_unit	=	[];
		$output_values_moti	=	[];
		$output_values_tree	=	[];
		$output_values_trfa	=	[];

		foreach ($decoded_json as $language => $subjects) {
			
			foreach ($subjects as $subject => $array) {
			
				foreach ($array as $key => $values) {
				
					switch ($subject) {
						
						case 'contextTree':
							$output_values_tree[]	=	"('" . $key . "', '" . addslashes($values['label']) . "', '" . "" . "')";
							$output_data_lines++;
							foreach ($values['facts'] as $factId) {
								$output_values_trfa[]	=	"('" . $key . "', '" . $factId . "')";
								$output_data_lines++;
							}
							if (array_key_exists('sub', $values)) {
								foreach ($values['sub'] as $key2 => $values2) {
									$output_values_tree[]	=	"('" . $key2 . "', '" . addslashes($values2['label']) . "', '" . $key . "')";
									foreach ($values2['facts'] as $factId) {
										$output_values_trfa[]	=	"('" . $key2 . "', '" . $factId . "')";
										$output_data_lines++;
									}
								}
							}
							break;
						case 'facts':
							$output_values_fact[]	=	"('" . $key . "', '" . addslashes($values['label']) . "')";
							$output_data_lines++;
							foreach ($values['attributes'] as $fact_attribute) {
								$output_values_faat[]	=	"('" . $key . "', '" . $fact_attribute . "')";
								$output_data_lines++;
							}
							if (array_key_exists('variantGroupTypes', $values)) {
								foreach ($values['variantGroupTypes'] as $fact_variantGroupType) {
									$output_values_fava[]	=	"('" . $key . "', '" . $fact_variantGroupType . "')";
									$output_data_lines++;
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
							$output_values_attr[]	=	"('" . $key . "', '" . addslashes($values['label']) . "', '" . $valueType . "', '" . $units . "')";
							$output_data_lines++;
							break;
						case 'units':
							$output_values_unit[]	=	"('" . $key . "', '" . addslashes($values['label']) . "')";
							$output_data_lines++;
							break;
						case 'motifTypes':
							$output_values_moti[]	=	"('" . $key . "', '" . addslashes($values['label']) . "')";
							$output_data_lines++;
							break;
						default:
					}
				}
			}
		}
		
		dbinsert($dbh, 'vendor_giata_definitions_attributes',				$output_columns_attr,	array_unique($output_values_attr));
		dbinsert($dbh, 'vendor_giata_definitions_contexttree',				$output_columns_tree,	array_unique($output_values_tree));
		dbinsert($dbh, 'vendor_giata_definitions_contexttree_facts',		$output_columns_trfa,	array_unique($output_values_trfa));
		dbinsert($dbh, 'vendor_giata_definitions_facts',					$output_columns_fact,	array_unique($output_values_fact));
		dbinsert($dbh, 'vendor_giata_definitions_facts_attributes',			$output_columns_faat,	array_unique($output_values_faat));
		dbinsert($dbh, 'vendor_giata_definitions_facts_variantgrouptypes',	$output_columns_fava,	array_unique($output_values_fava));
		dbinsert($dbh, 'vendor_giata_definitions_motif_types',				$output_columns_moti,	array_unique($output_values_moti));
		dbinsert($dbh, 'vendor_giata_definitions_units',					$output_columns_unit,	array_unique($output_values_unit));
	}
	
	echo date("[G:i:s] ") . '- ' . $output_data_lines . ' rows processed' . PHP_EOL;

	###
	### DATABASE EXIT ROUTINE
	###
		
	$dbh = null;

	###
	### STANDARD EXCEPTION ROUTINE
	###

} catch (PDOException $e) {
	
	echo date("[G:i:s] ") . 'Caught PDOException: ' . $e->getMessage() . PHP_EOL;
	
} catch (Exception $e) {
	
	echo date("[G:i:s] ") . 'Caught Exception: '    . $e->getMessage() . PHP_EOL;
	
} finally {

	###
	### STANDARD EXIT ROUTINE
	###

	require 'includes/exit.inc.php';
}