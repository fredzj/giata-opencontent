<?php
/*

	SCRIPT:		_func_giata.inc.php
	
	PURPOSE:	Functions for Giata cronjobs.
	
	Copyright 2024 Fred Onis - All rights reserved.

	giata_accommodation
	giata_accommodation_facts
	giata_accommodation_facts_attributes
	giata_accommodation_facts_variants
	giata_accommodation_roomtypes
	giata_chains
	giata_cities
	giata_destinations
	giata_images
	giata_roomtypes
	giata_texts
	giata_variant_groups
	giata_variants

*/

function giata_accommodation($dbh, $xml, $output_columns) {
	
	if (count($xml->addresses->address) > 1) {
		dblog($dbh, 'INFO', 'GIATA', 'Multiple addresses for '	. $xml['giataId']);
		echo 'Multiple addresses for '	. $xml['giataId'] . PHP_EOL;
	}
	if (count($xml->emails) > 1) {
		dblog($dbh, 'INFO', 'GIATA', 'Multiple emails for '		. $xml['giataId']);
		echo 'Multiple emails for '	. $xml['giataId'] . PHP_EOL;
	}
	if (!empty($xml->geoCodes)) {
		if (count($xml->geoCodes->geoCode) > 1) {
			dblog($dbh, 'INFO', 'GIATA', 'Multiple geoCodes for '	. $xml['giataId']);
			echo 'Multiple geoCodes for '	. $xml['giataId'] . PHP_EOL;
		}
	}
	if (count($xml->names) > 1) {
		dblog($dbh, 'INFO', 'GIATA', 'Multiple names for '		. $xml['giataId']);
		echo 'Multiple names for '	. $xml['giataId'] . PHP_EOL;
	}
	if (count($xml->phones) > 1) {
		dblog($dbh, 'INFO', 'GIATA', 'Multiple phones for '		. $xml['giataId']);
		echo 'Multiple phones for '	. $xml['giataId'] . PHP_EOL;
	}
	if (count($xml->ratings) > 1) {
		dblog($dbh, 'INFO', 'GIATA', 'Multiple ratings for '	. $xml['giataId']);
		echo 'Multiple ratings for '	. $xml['giataId'] . PHP_EOL;
	}
	if (count($xml->urls) > 1) {
		dblog($dbh, 'INFO', 'GIATA', 'Multiple URLs for '		. $xml['giataId']);
		echo 'Multiple URLs for '	. $xml['giataId'] . PHP_EOL;
	}
	
	# Set accommodation name
	$acco_name			=	'';
	foreach ($xml->names->name as $name) {
		
		if ($name['locale'] == 'nl') {
			$acco_name	=	$name;
			break;
		}
		if ($name['isDefault'] == 'true') {
			$acco_name	=	$name;
		}
	}
	
	# Set accommodation star classification
	$acco_rating		=	'';
	if (!empty($xml->ratings)) {
		foreach ($xml->ratings->rating as $rating) {
			if ($rating['isDefault'] == 'true') {
				$acco_rating	=	str_replace(',', '.', $xml->ratings->rating);
			}
		}
	}
	
	# Set accommodation telephone number
	$acco_phone			=	'';
	if (!empty($xml->phones)) {
		foreach ($xml->phones->phone as $phone) {
			if ($phone['tech'] == 'phone') {
				$acco_phone		=	$phone;
			}
		}
	}
	
	# Set accommodation email address
	$acco_email			=	'';
	if (!empty($xml->emails)) {
		$acco_email		=	$xml->emails->email;
	}

	# Set the values
	$output_values		=	[];
	$output_values[]	=	$xml['giataId'];								// The GIATA-ID of the accommodation.
	$output_values[]	=	addslashes($acco_name);							// Names of the accommodation.
	$output_values[]	=	$xml->city['giataId'];							// The city where the accommodation is located.
	$output_values[]	=	$xml->destination['giataId'];					// The destination where the accommodation is located.
	$output_values[]	=	$xml->country->code;							// The country where the accommodation is located.
	$output_values[]	=	$xml->source;									// The source of the content for the accommodation (1 = GIATA Drive, 2 = GIATA Multilingual Hotel Guide).
	$output_values[]	=	$acco_rating;									// Star Ratings for the accommodation.
	$output_values[]	=	addslashes($xml->addresses->address->street);	// The name of the street.
	$output_values[]	=	$xml->addresses->address->streetNum;			// The street number.
	$output_values[]	=	$xml->addresses->address->zip;					// The zip/postal code.
	$output_values[]	=	addslashes($xml->addresses->address->cityName);	// The name of the city.
	$output_values[]	=	$xml->addresses->address->poBox;				// The P.O. box number.
	$output_values[]	=	$xml->federalState['giataId'];					// The GIATA-ID of the federal state.
	$output_values[]	=	$acco_phone;									// Phone number of the accommodation.
	$output_values[]	=	addslashes($acco_email);						// Email of the accommodation.
	$output_values[]	=	addslashes($xml->urls->url);					// URL of the accommodation.
	if (!empty($xml->geoCodes)) {
		$output_values[]	=	$xml->geoCodes->geoCode['accuracy'];		// Indicates the level of accuracy of the geo code (address, street, locality or city).
		$output_values[]	=	$xml->geoCodes->geoCode->latitude;			// The latitude of the accommodation.
		$output_values[]	=	$xml->geoCodes->geoCode->longitude;			// The longitude of the accommodation.
	} else {
		$output_values[]	=	'';
		$output_values[]	=	'';
		$output_values[]	=	'';
	}
	
	# Insert a new row
	dbinsert($dbh, 'vendor_giata_accommodations', $output_columns,	$output_values);

	return;
}

function giata_accommodation_facts($dbh, $xml, $output_columns) {
	
	$output_values		=	[];
	
	foreach ($xml->facts->fact as $fact) {

		$array				=	[];
		$array[]			=	$xml['giataId'];						// The GIATA-ID of the accommodation.
		$array[]			=	$fact['factDefId'];						// The fact definition id of the fact instance.
		$output_values[]	=	"('" . implode("', '", $array) . "')";
	}
	
	# Insert a new row
	dbinsert($dbh, 'vendor_giata_accommodations_facts',		$output_columns,	array_unique($output_values));
	
	return;
}

function giata_accommodation_facts_attributes($dbh, $xml, $output_columns) {
	
	$output_values		=	[];
	
	foreach ($xml->facts->fact as $fact) {
		
		if (isset($fact->factInstance->attributes)) {
		
			foreach ($fact->factInstance->attributes->attribute as $attribute) {
	
				$array				=	[];
				$array[]			=	$xml['giataId'];						// The GIATA-ID of the accommodation.
				$array[]			=	$fact['factDefId'];						// The fact definition id of the fact instance.
				$array[]			=	$attribute['attributeDefId'];			// The attribute definition id of the attribute.
				$array[]			=	addslashes($attribute['value']);		// The value of the attribute of a given fact.
				$array[]			=	$attribute['unitDefId'];				// The ID of the unit, specifying the value.
				$output_values[]	=	"('" . implode("', '", $array) . "')";
			}
		}
	}
	
	# Insert a new row
	dbinsert($dbh, 'vendor_giata_accommodations_facts_attributes',	$output_columns,	array_unique($output_values));
	
	return;
}

function giata_accommodation_facts_variants($dbh, $xml, $output_columns) {
	
	$output_values		=	[];
	
	foreach ($xml->facts->fact as $fact) {
		
		if (isset($fact->factInstance->appliesTo)) {
		
			foreach ($fact->factInstance->appliesTo->variant as $variant) {
	
				$array				=	[];
				$array[]			=	$xml['giataId'];						// The GIATA-ID of the accommodation.
				$array[]			=	$fact['factDefId'];						// The fact definition id of the fact instance.
				$array[]			=	$variant['variantId'];					// Optional attribute containing the variant id.
				$output_values[]	=	"('" . implode("', '", $array) . "')";
			}
		}
	}
	
	# Insert a new row
	dbinsert($dbh, 'vendor_giata_accommodations_facts_variants',	$output_columns,	array_unique($output_values));
	
	return;
}

function giata_accommodation_roomtypes($dbh, $xml, $output_columns) {
	
	if (isset($xml->roomTypes)) {
		
		$output_values			=	[];
		
		foreach ($xml->roomTypes->roomType as $roomtype) {
	
			$array				=	[];
			$array[]			=	$xml['giataId'];						// The GIATA-ID of the accommodation.
			$array[]			=	$roomtype['variantId'];					// The variant id of the room type.
			$output_values[]	=	"('" . implode("', '", $array) . "')";
		}
		
		# Insert a new row
		dbinsert($dbh, 'vendor_giata_accommodations_roomtypes',	$output_columns, array_unique($output_values));
	}
	
	return;
}

function giata_chains($xml, &$output_values) {
	
	if (isset($xml->chains)) {
		
		$output_values[]	=	"('" . $xml->chains->chain['giataId'] . "', '" . addslashes(trim($xml->chains->chain->names->name)) . "')";
	}

	return;
}

function giata_cities($xml, &$output_values) {

	if (isset($xml->city)) {
		
		foreach ($xml->city->names->name as $name) {
			
			if (!empty($xml->city['giataId']) && !empty($name)) {
				
				if ($name['locale'] == 'nl') {
					
					$output_values[]	=	"('" . $xml->city['giataId'] . "', '" . addslashes(trim($name)) . "')";
				}
			}
		}
	}
	
	return;
}

function giata_destinations($xml, &$output_values) {

	if (isset($xml->destination)) {
	
		foreach ($xml->destination->names->name as $name) {
			
			if (!empty($xml->destination['giataId']) && !empty($name)) {
				
				if ($name['locale'] == 'nl') {
					
					$output_values[]	=	"('" . $xml->destination['giataId'] . "', '" . addslashes(trim($name)) . "')";
				}
			}
		}
	}
	
	return;
}

function giata_images($dbh, $xml, $output_columns) {
	
	if (!empty($xml->images)) {
		
		$output_values		=	[];
		
		foreach ($xml->images->image as $image) {
			
			foreach ($image->sizes->size as $size) {
				
				$array		=	[];
				$array[]	=	$xml['giataId'];
				$array[]	=	$image['motifType'];
				$array[]	=	$image['lastUpdate'];
				$array[]	=	($image['heroImage'] == 'true') ? 1 : 0;
				$array[]	=	$image->id;
				$array[]	=	$image->baseName;
				$array[]	=	$size['maxWidth'];
				$array[]	=	$size['href'];
				$output_values[]	=	"('" . implode("', '", $array) . "')";
			}
		}
	
		# Insert a new row
		dbinsert($dbh, 'vendor_giata_images', $output_columns,	$output_values);
	}
	
	return;
}

function giata_roomtypes($xml, &$output_values) {
	
	if (isset($xml->roomTypes)) {
		
		foreach ($xml->roomTypes->roomType as $roomtype) {
			
			$imageIds	=	[];
			foreach ($roomtype->imageRelations->imageId as $imageId) {
				$imageIds[]	=	$imageId;
			}
			
			$array		=	[];
			$array[]	=	$roomtype['variantId'];
			$array[]	=	addslashes($roomtype->category);
			$array[]	=	addslashes($roomtype->code);
			$array[]	=	addslashes($roomtype->name);
			$array[]	=	addslashes($roomtype->type);
			$array[]	=	addslashes($roomtype->view);
			$array[]	=	$roomtype->categoryInformation->attributeDefId;
			$array[]	=	addslashes($roomtype->categoryInformation->name);
			$array[]	=	$roomtype->typeInformation->attributeDefId;
			$array[]	=	addslashes($roomtype->typeInformation->name);
			$array[]	=	$roomtype->viewInformation->attributeDefId;
			$array[]	=	addslashes($roomtype->viewInformation->name);
			$array[]	=	implode('|', $imageIds);
			$output_values[]	=	"('" . implode("', '", $array) . "')";
		}
	}
	
	return;
}

function giata_texts($dbh, $xml, $output_columns) {
	
	if (isset($xml->texts)) {
	
		$output_values			=	[];
		$sequence				=	0;
		
		foreach ($xml->texts->text as $text) {
			
			if ($text['locale'] == 'nl') {
				
				foreach ($text->sections->section as $section) {
					
					$array		=	[];
					$array[]	=	$xml['giataId'];
					$array[]	=	$text['lastUpdate'];
					$array[]	=	++$sequence;
					$array[]	=	addslashes($section->title);
					$array[]	=	addslashes($section->para);
					$output_values[]	=	"('" . implode("', '", $array) . "')";
				}
			}
		}
	
		# Insert a new row
		dbinsert($dbh, 'vendor_giata_texts', $output_columns, $output_values);
	}
	
	return;
}

function giata_variant_groups($xml, &$output_values) {
	
	if (isset($xml->variantGroups)) {
		
		foreach ($xml->variantGroups->variantGroup as $variantGroup) {
			
			if (!empty($variantGroup['variantGroupTypeId']) && !empty($variantGroup->label)) {
				
				$output_values[]	=	"('" . $variantGroup['variantGroupTypeId'] . "', '" . addslashes($variantGroup->label) . "')";
			}
		}
	}
	
	return;
}

function giata_variants($xml, &$output_values) {
	
	if (isset($xml->variantGroups) && isset($xml->variantGroups->variantGroup)) {

		foreach ($xml->variantGroups->variantGroup->variants->variant as $variant) {
			
			if (!empty($variant['variantId']) && !empty($variant->label)) {
			
				$output_values[]	=	"('" . $variant['variantId'] . "', '" . addslashes($variant->label) . "')";
			}
		}
	}
	
	return;
}