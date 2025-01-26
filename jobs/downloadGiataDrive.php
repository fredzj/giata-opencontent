<?php
/*
	SCRIPT:		downloadGiataDrive.php
	PURPOSE:	Download Drive Content in XML feeds from GIATA and insert the data into the database.
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
	
	$inputUrls     = ['https://giatadrive.com/europarcs/xml'];
	$outputColumns = [
        'accommodations'					=> ['giata_id', 'name', 'city_giata_id', 'destination_giata_id', 'country_code', 'source', 'rating', 'address_street', 'address_streetnum', 'address_zip', 'address_cityname', 'address_pobox', 'address_federalstate_giata_id', 'phone', 'email', 'url', 'geocode_accuracy', 'geocode_latitude', 'geocode_longitude'],
        'accommodations_facts'				=> ['giataId', 'factDefId'],
        'accommodations_facts_attributes'	=> ['giataId', 'factDefId', 'attributeDefId', 'value', 'unitDefId'],
        'accommodations_facts_variants' 	=> ['giataId', 'factDefId', 'variantId'],
        'accommodations_roomtypes'			=> ['giataId', 'variantId'],
        'chains'							=> ['giataId', 'name'],
        'cities'							=> ['giataId', 'name'],
        'destinations'						=> ['giataId', 'name'],
        'images'							=> ['giata_id', 'motif_type', 'last_update', 'is_hero_image', 'image_id', 'base_name', 'max_width', 'href'],
        'roomtypes'							=> ['variantId', 'category', 'code', 'name', 'type', 'view', 'category_attribute_id', 'category_attribute_name', 'type_attribute_id', 'type_attribute_name', 'view_attribute_id', 'view_attribute_name', 'image_relations'],
        'texts'								=> ['giata_id', 'last_update', 'sequence', 'title', 'paragraph'],
        'variant_groups'					=> ['variantGroupTypeId', 'label'],
        'variants'							=> ['variantId', 'label']
    ];
	
	###
	### DATABASE INIT ROUTINE
	###
	
	$dbh    = dbopen($dbconfig);
    $tables = [
        'vendor_giata_accommodations',
        'vendor_giata_accommodations_facts',
        'vendor_giata_accommodations_facts_attributes',
        'vendor_giata_accommodations_facts_variants',
        'vendor_giata_accommodations_roomtypes',
        'vendor_giata_chains',
        'vendor_giata_cities',
        'vendor_giata_destinations',
        'vendor_giata_images',
        'vendor_giata_roomtypes',
        'vendor_giata_texts',
        'vendor_giata_variant_groups',
        'vendor_giata_variants'
    ];
    foreach ($tables as $table) {
        dbtruncate($dbh, $table);
    }

	###
	### PROCESSING ROUTINE
	###

	foreach ($inputUrls as $inputUrl) {
		
		echo date("[G:i:s] ") . 'Reading XML Feed ' . $inputUrl . PHP_EOL;

		if (($contents = file_get_contents($inputUrl)) !== false) {

			$xml          = simplexml_load_string($contents);
            $outputValues = initializeVariousContent();
	
			foreach ($xml->url as $url) {
				
				echo date("[G:i:s] ") . '- Reading XML Feed ' . $url->loc . PHP_EOL;
				
				if (($contents = file_get_contents($url->loc)) !== false) {
					
					$xml = simplexml_load_string($contents);
                    processContent($dbh, $xml, $outputColumns, $outputValues);
                    $outputDataLines++;
				}
			}
			
            insertVariousContent($dbh, $outputColumns, $outputValues);
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