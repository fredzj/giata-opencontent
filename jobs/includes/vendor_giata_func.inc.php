<?php
/*
	SCRIPT:		vendor_giata_func.inc.php
	PURPOSE:	Functions for Giata cronjobs.
	COPYRIGHT:  2024 Fred Onis - All rights reserved.

	getAccommodationEmail
	getAccommodationName
	getAccommodationPhone
	getAccommodationRating
	getChains
	getCities
	getDestinations
	getRoomtypes
	getVariantGroups
	getVariants

	initializeVariousContent

	insertAccommodation
	insertAccommodationFacts
	insertAccommodationFactsAttributes
	insertAccommodationFactsVariants
	insertAccommodationRoomtypes
	insertImages
	insertTexts
	insertVariousContent

	logError
	logMultipleEntries

	prepareValues

	processContent
*/

function getAccommodationEmail($xml) {
    return $xml->emails->email ?? '';
}

function getAccommodationName($xml) {
    foreach ($xml->names->name as $name) {
        if ($name['locale'] == 'nl' || $name['isDefault'] == 'true') {
            return $name;
        }
    }
    return '';
}

function getAccommodationPhone($xml) {
	if (is_iterable($xml->phones->phone)) {
        foreach ($xml->phones->phone as $phone) {
            if ($phone['tech'] == 'phone') {
                return $phone;
            }
        }
    }
    return '';
}

function getAccommodationRating($xml) {
	if (is_iterable($xml->ratings->rating)) {
 	   foreach ($xml->ratings->rating as $rating) {
    	    if ($rating['isDefault'] == 'true') {
        	    return str_replace(',', '.', $rating);
        	}
    	}
	}
    return '';
}

function getChains($xml, &$output_values) {
    if (isset($xml->chains)) {
        $output_values[] = prepareValues($xml->chains->chain['giataId'], addslashes(trim($xml->chains->chain->names->name)));
    }
}

function getCities($xml, &$output_values) {
    if (isset($xml->city)) {
        foreach ($xml->city->names->name as $name) {
            if (!empty($xml->city['giataId']) && !empty($name) && $name['locale'] == 'nl') {
                $output_values[] = prepareValues($xml->city['giataId'], addslashes(trim($name)));
            }
        }
    }
}

function getDestinations($xml, &$output_values) {
    if (isset($xml->destination)) {
        foreach ($xml->destination->names->name as $name) {
            if (!empty($xml->destination['giataId']) && !empty($name) && $name['locale'] == 'nl') {
                $output_values[] = prepareValues($xml->destination['giataId'], addslashes(trim($name)));
            }
        }
    }
}

function getRoomtypes($xml, &$output_values) {
    if (isset($xml->roomTypes)) {
        foreach ($xml->roomTypes->roomType as $roomtype) {
            $imageIds = [];
            foreach ($roomtype->imageRelations->imageId as $imageId) {
                $imageIds[] = $imageId;
            }
            $output_values[] = prepareValues(
                $roomtype['variantId'],
                addslashes($roomtype->category),
                addslashes($roomtype->code),
                addslashes($roomtype->name),
                addslashes($roomtype->type),
                addslashes($roomtype->view),
                $roomtype->categoryInformation->attributeDefId,
                addslashes($roomtype->categoryInformation->name),
                $roomtype->typeInformation->attributeDefId,
                addslashes($roomtype->typeInformation->name),
                $roomtype->viewInformation->attributeDefId,
                addslashes($roomtype->viewInformation->name),
                implode('|', $imageIds)
            );
        }
    }
}

function getVariantGroups($xml, &$output_values) {
    if (isset($xml->variantGroups)) {
        foreach ($xml->variantGroups->variantGroup as $variantGroup) {
            if (!empty($variantGroup['variantGroupTypeId']) && !empty($variantGroup->label)) {
                $output_values[] = prepareValues($variantGroup['variantGroupTypeId'], addslashes($variantGroup->label));
            }
        }
    }
}

function getVariants($xml, &$output_values) {
    if (isset($xml->variantGroups) && isset($xml->variantGroups->variantGroup)) {
        foreach ($xml->variantGroups->variantGroup->variants->variant as $variant) {
            if (!empty($variant['variantId']) && !empty($variant->label)) {
                $output_values[] = prepareValues($variant['variantId'], addslashes($variant->label));
            }
        }
    }
}

function initializeVariousContent() {
    return [
        'chains'         => [],
        'cities'         => [],
        'destinations'   => [],
        'roomtypes'      => [],
        'variant_groups' => [],
        'variants'       => []
    ];
}

function insertAccommodation($dbh, $xml, $output_columns) {
	
    logMultipleEntries($dbh, $xml, 'addresses', 'address', 'Multiple addresses for ');
    logMultipleEntries($dbh, $xml, 'emails',    'email',   'Multiple emails for ');
    logMultipleEntries($dbh, $xml, 'geoCodes',  'geoCode', 'Multiple geoCodes for ');
    logMultipleEntries($dbh, $xml, 'names',     'name',    'Multiple names for ');
    logMultipleEntries($dbh, $xml, 'phones',    'phone',   'Multiple phones for ');
    logMultipleEntries($dbh, $xml, 'ratings',   'rating',  'Multiple ratings for ');
    logMultipleEntries($dbh, $xml, 'urls',      'url',     'Multiple URLs for ');
	
    $acco_name   = getAccommodationName($xml);
    $acco_rating = getAccommodationRating($xml);
    $acco_phone  = getAccommodationPhone($xml);
    $acco_email  = getAccommodationEmail($xml);

    $output_values = [
        $xml['giataId'],
        addslashes($acco_name),
        $xml->city['giataId'],
        $xml->destination['giataId'],
        $xml->country->code,
        $xml->source,
        $acco_rating,
        addslashes($xml->addresses->address->street),
        $xml->addresses->address->streetNum,
        $xml->addresses->address->zip,
        addslashes($xml->addresses->address->cityName),
        $xml->addresses->address->poBox,
        $xml->federalState['giataId'],
        $acco_phone,
        addslashes($acco_email),
        addslashes($xml->urls->url),
        $xml->geoCodes->geoCode['accuracy'] ?? '',
        $xml->geoCodes->geoCode->latitude ?? '',
        $xml->geoCodes->geoCode->longitude ?? ''
    ];
	
	dbinsert($dbh, 'vendor_giata_accommodations', $output_columns,	$output_values);

	return;
}

function insertAccommodationFacts($dbh, $xml, $output_columns) {
    $output_values = [];
    foreach ($xml->facts->fact as $fact) {
        $output_values[] = prepareValues($xml['giataId'], $fact['factDefId']);
    }
    dbinsert($dbh, 'vendor_giata_accommodations_facts', $output_columns, array_unique($output_values));
}

function insertAccommodationFactsAttributes($dbh, $xml, $output_columns) {
    $output_values = [];
    foreach ($xml->facts->fact as $fact) {
        if (isset($fact->factInstance->attributes)) {
            foreach ($fact->factInstance->attributes->attribute as $attribute) {
                $output_values[] = prepareValues(
                    $xml['giataId'],
                    $fact['factDefId'],
                    $attribute['attributeDefId'],
                    addslashes($attribute['value']),
                    $attribute['unitDefId']
                );
            }
        }
    }
    dbinsert($dbh, 'vendor_giata_accommodations_facts_attributes', $output_columns, array_unique($output_values));
}

function insertAccommodationFactsVariants($dbh, $xml, $output_columns) {
    $output_values = [];
    foreach ($xml->facts->fact as $fact) {
        if (isset($fact->factInstance->appliesTo)) {
            foreach ($fact->factInstance->appliesTo->variant as $variant) {
                $output_values[] = prepareValues($xml['giataId'], $fact['factDefId'], $variant['variantId']);
            }
        }
    }
    dbinsert($dbh, 'vendor_giata_accommodations_facts_variants', $output_columns, array_unique($output_values));
}

function insertAccommodationRoomtypes($dbh, $xml, $output_columns) {
    if (isset($xml->roomTypes)) {
        $output_values = [];
        foreach ($xml->roomTypes->roomType as $roomtype) {
            $output_values[] = prepareValues($xml['giataId'], $roomtype['variantId']);
        }
        dbinsert($dbh, 'vendor_giata_accommodations_roomtypes', $output_columns, array_unique($output_values));
    }
}

function insertImages($dbh, $xml, $output_columns) {
    if (!empty($xml->images)) {
        $output_values = [];
        foreach ($xml->images->image as $image) {
            foreach ($image->sizes->size as $size) {
                $output_values[] = prepareValues(
                    $xml['giataId'],
                    $image['motifType'],
                    $image['lastUpdate'],
                    ($image['heroImage'] == 'true') ? 1 : 0,
                    $image->id,
                    $image->baseName,
                    $size['maxWidth'],
                    $size['href']
                );
            }
        }
        dbinsert($dbh, 'vendor_giata_images', $output_columns, $output_values);
    }
}

function insertTexts($dbh, $xml, $output_columns) {
    if (isset($xml->texts)) {
        $output_values = [];
        $sequence = 0;
        foreach ($xml->texts->text as $text) {
            if ($text['locale'] == 'nl') {
                foreach ($text->sections->section as $section) {
                    $output_values[] = prepareValues(
                        $xml['giataId'],
                        $text['lastUpdate'],
                        ++$sequence,
                        addslashes($section->title),
                        addslashes($section->para)
                    );
                }
            }
        }
        dbinsert($dbh, 'vendor_giata_texts', $output_columns, $output_values);
    }
}

function insertVariousContent($dbh, $output_columns, $output_values) {
    dbinsert($dbh, 'vendor_giata_chains',         $output_columns['chains'],         array_unique($output_values['chains']));
    dbinsert($dbh, 'vendor_giata_cities',         $output_columns['cities'],         array_unique($output_values['cities']));
    dbinsert($dbh, 'vendor_giata_destinations',   $output_columns['destinations'],   array_unique($output_values['destinations']));
    dbinsert($dbh, 'vendor_giata_roomtypes',      $output_columns['roomtypes'],      array_unique($output_values['roomtypes']));
    dbinsert($dbh, 'vendor_giata_variant_groups', $output_columns['variant_groups'], array_unique($output_values['variant_groups']));
    dbinsert($dbh, 'vendor_giata_variants',       $output_columns['variants'],       array_unique($output_values['variants']));
}

function logError($message) {
    echo date("[G:i:s] ") . $message . PHP_EOL;
    // Optionally log to a file
    // error_log($message, 3, '/path/to/error.log');
}

function logMultipleEntries($dbh, $xml, $parent, $child, $message) {
	if (is_countable($xml->$parent->$child)) {
	    if (count($xml->$parent->$child) > 1) {
  	      dblog($dbh, 'INFO', 'GIATA', $message . $xml['giataId']);
  	      echo $message . $xml['giataId'] . PHP_EOL;
 	   }
	}
}

function prepareValues(...$values) {
    return "('" . implode("', '", array_map('addslashes', $values)) . "')";
}

function processContent($dbh, $xml, $outputColumns, &$outputValues) {
    insertAccommodation(               $dbh, $xml, $outputColumns['accommodations']);
    insertImages(                      $dbh, $xml, $outputColumns['images']);
    insertTexts(                       $dbh, $xml, $outputColumns['texts']);
    insertAccommodationFacts(          $dbh, $xml, $outputColumns['accommodations_facts']);
    insertAccommodationFactsAttributes($dbh, $xml, $outputColumns['accommodations_facts_attributes']);
    insertAccommodationFactsVariants(  $dbh, $xml, $outputColumns['accommodations_facts_variants']);
    insertAccommodationRoomtypes(      $dbh, $xml, $outputColumns['accommodations_roomtypes']);

    getChains(       $xml, $outputValues['chains']);
    getCities(       $xml, $outputValues['cities']);
    getDestinations( $xml, $outputValues['destinations']);
    getRoomtypes(    $xml, $outputValues['roomtypes']);
    getVariantGroups($xml, $outputValues['variant_groups']);
    getVariants(     $xml, $outputValues['variants']);
}