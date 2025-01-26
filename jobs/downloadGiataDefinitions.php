<?php
/*
	SCRIPT:		downloadGiataDefinitions.php
	PURPOSE:	Download the definitions in a JSON feed from GIATA and insert the data into the database.
	COPYRIGHT:  2024 Fred Onis - All rights reserved.
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
                            processContextTree(	$key, $values, $outputValues, $outputDataLines);
							break;
						case 'facts':
                            processFacts(		$key, $values, $outputValues, $outputDataLines);
							break;
						case 'attributes':
                            processAttributes(	$key, $values, $outputValues, $outputDataLines);
							break;
						case 'units':
                            $outputValues['units'][]		= prepareValues($key, $values['label']);
							$outputDataLines++;
							break;
						case 'motifTypes':
                            $outputValues['motif_types'][]	= prepareValues($key, $values['label']);
							$outputDataLines++;
							break;
						default:
					}
				}
			}
		}
		foreach ($outputValues as $table => $values) {
            dbinsert($dbh, 'vendor_giata_definitions_' . $table, $outputColumns[$table], array_unique($values));
        }
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

function processContextTree($key, $values, &$outputValues, &$outputDataLines) {
    $outputValues['contexttree'][] = prepareValues($key, $values['label'], '');
    $outputDataLines++;
    foreach ($values['facts'] as $factId) {
        $outputValues['contexttree_facts'][] = prepareValues($key, $factId);
        $outputDataLines++;
    }
    if (array_key_exists('sub', $values)) {
        foreach ($values['sub'] as $key2 => $values2) {
            $outputValues['contexttree'][] = prepareValues($key2, $values2['label'], $key);
            foreach ($values2['facts'] as $factId) {
                $outputValues['contexttree_facts'][] = prepareValues($key2, $factId);
                $outputDataLines++;
            }
        }
    }
}

function processFacts($key, $values, &$outputValues, &$outputDataLines) {
    $outputValues['facts'][] = prepareValues($key, $values['label']);
    $outputDataLines++;
    foreach ($values['attributes'] as $factAttribute) {
        $outputValues['facts_attributes'][] = prepareValues($key, $factAttribute);
        $outputDataLines++;
    }
    if (array_key_exists('variantGroupTypes', $values)) {
        foreach ($values['variantGroupTypes'] as $factVariantGroupType) {
            $outputValues['facts_variantgrouptypes'][] = prepareValues($key, $factVariantGroupType);
            $outputDataLines++;
        }
    }
}

function processAttributes($key, $values, &$outputValues, &$outputDataLines) {
    $valueType = $values['valueType'] ?? '';
    $units = array_key_exists('units', $values) ? implode('|', $values['units']) : '';
    $outputValues['attributes'][] = prepareValues($key, $values['label'], $valueType, $units);
    $outputDataLines++;
}