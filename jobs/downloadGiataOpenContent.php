<?php
/*

	SCRIPT:		downloadGiataOpenContent.php
	
	PURPOSE:	Download Open Content in XML feeds from GIATA and insert the data into the database.
	
	Copyright 2024 Fred Onis - All rights reserved.

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
	
//	$i						=	0;
	
	$input_urls				=	['https://myhotel.giatamedia.com/hotel-directory/xml'];

	$output_columns_acco	=	['giata_id', 'name', 'city_giata_id', 'destination_giata_id', 'country_code', 'source', 'rating', 'address_street', 'address_streetnum', 'address_zip', 'address_cityname', 'address_pobox', 'address_federalstate_giata_id', 'phone', 'email', 'url', 'geocode_accuracy', 'geocode_latitude', 'geocode_longitude'];
	$output_columns_acat	=	['giataId', 'factDefId', 'attributeDefId', 'value', 'unitDefId'];
	$output_columns_acfa	=	['giataId', 'factDefId'];
	$output_columns_acro	=	['giataId', 'variantId'];
	$output_columns_acva	=	['giataId', 'factDefId', 'variantId'];
	$output_columns_chai	=	['giataId', 'name'];
	$output_columns_citi	=	['giataId', 'name'];
	$output_columns_dest	=	['giataId', 'name'];
	$output_columns_fact	=	['giata_id', 'fact_def_id', 'attribute_def_id', 'value'];
	$output_columns_fede	=	['giataId', 'name', 'code'];
	$output_columns_imag	=	['giata_id', 'motif_type', 'last_update', 'is_hero_image', 'image_id', 'base_name', 'max_width', 'href'];
	$output_columns_room	=	['variantId', 'category', 'code', 'name', 'type', 'view', 'category_attribute_id', 'category_attribute_name', 'type_attribute_id', 'type_attribute_name', 'view_attribute_id', 'view_attribute_name', 'image_relations'];
	$output_columns_text	=	['giata_id', 'last_update', 'sequence', 'title', 'paragraph'];
	$output_columns_vagr	=	['variantGroupTypeId', 'label'];
	$output_columns_vari	=	['variantId', 'label'];
	
	###
	### DATABASE INIT ROUTINE
	###
	
	$dbh					=	dbopen($dbconfig);
	
	$fetched_rows			=	dbget_giata_ids($dbh);
	$giata_ids				=	[];
	foreach ($fetched_rows as $fetched_row) {
		$giata_id				=	$fetched_row['giata_id'];
		$giata_ids[$giata_id]	=	$fetched_row['timestamp'];
	}

	###
	### PROCESSING ROUTINE
	###

	foreach ($input_urls as $input_url) {
		
		echo date("[G:i:s] ") . 'Reading XML Feed ' . $input_url . PHP_EOL;

		# Read XML contents
		if (($contents = file_get_contents($input_url)) !== false) {
			
			$xml				=	simplexml_load_string($contents);
			$output_values_chai	=	[];
			$output_values_citi	=	[];
			$output_values_dest	=	[];
			$output_values_room	=	[];
			$output_values_vagr	=	[];
			$output_values_vari	=	[];
	
			# Find open content for all accommodations
			foreach ($xml->url as $url) {
		
				# Extract Giata ID from URL
				$giata_id		=	str_replace('https://myhotel.giatamedia.com/', '', $url->loc);
				$giata_id		=	str_replace('/xml', '', $giata_id);
				
				# Process new Giata IDs only
				if (!array_key_exists($giata_id, $giata_ids))	{
					
					# Process not more than one thousand rows in a batch
//					if (++$i > 10000) {
//						break;
//					}

					echo date("[G:i:s] ") . '- Reading XML Feed ' . $url->loc . PHP_EOL;
					
					# Read XML contents
					if (($contents = file_get_contents($url->loc)) !== false) {
						
						$xml				=	simplexml_load_string($contents);
						$output_values_fact	=	[];
						$output_values_fede	=	[];
						
						# Collect all content
						giata_accommodation(					$dbh, $xml, $output_columns_acco);
						giata_images(							$dbh, $xml, $output_columns_imag);
						giata_texts(							$dbh, $xml, $output_columns_text);
						giata_accommodation_facts(				$dbh, $xml, $output_columns_acfa);
						giata_accommodation_facts_attributes(	$dbh, $xml, $output_columns_acat);
						giata_accommodation_facts_variants(		$dbh, $xml, $output_columns_acva);
						giata_accommodation_roomtypes(			$dbh, $xml, $output_columns_acro);
	
						giata_chains(			$xml, $output_values_chai);
						giata_cities(			$xml, $output_values_citi);
						giata_destinations(		$xml, $output_values_dest);
						giata_roomtypes(		$xml, $output_values_room);
						giata_variant_groups(	$xml, $output_values_vagr);
						giata_variants(			$xml, $output_values_vari);
						
						$output_data_lines++;
					}
				}
			}
			
			dbinsert($dbh, 'vendor_giata_chains',					$output_columns_chai,	array_unique($output_values_chai));
			dbinsert($dbh, 'vendor_giata_cities',					$output_columns_citi,	array_unique($output_values_citi));
			dbinsert($dbh, 'vendor_giata_destinations',				$output_columns_dest,	array_unique($output_values_dest));
			dbinsert($dbh, 'vendor_giata_roomtypes',				$output_columns_room,	array_unique($output_values_room));
			dbinsert($dbh, 'vendor_giata_variant_groups',			$output_columns_vagr,	array_unique($output_values_vagr));
			dbinsert($dbh, 'vendor_giata_variants',					$output_columns_vari,	array_unique($output_values_vari));
		}
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