<?php
/**
 * SCRIPT: downloadGiataOpenContent.php
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
	
	$inputUrls     = ['https://giatadrive.com/europarcs/xml', 'https://myhotel.giatamedia.com/hotel-directory/xml'];
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
	
	$dbh					=	dbopen($dbconfig);

	###
	### PROCESSING ROUTINE
	###

	foreach ($inputUrls as $inputUrl) {
		
		echo date("[G:i:s] ") . 'Reading XML Feed ' . $inputUrl . PHP_EOL;

		# Read XML contents
		if (($contents = file_get_contents($inputUrl)) !== false) {
			
			$xml          = simplexml_load_string($contents);
			$outputValues = initializeVariousContent();
	
			# Find open content for all accommodations
			foreach ($xml->url as $url) {

				echo date("[G:i:s] ") . '- Reading XML Feed ' . $url->loc . PHP_EOL;
					
				# Read XML contents
				if (($contents = file_get_contents($url->loc)) !== false) {
						
					$xml	=	simplexml_load_string($contents);
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
/* */

/**
 * Retrieves the email address of the accommodation from the XML data.
 *
 * @param SimpleXMLElement $xml The XML data containing accommodation information.
 * @return string The email address of the accommodation.
 */
function getAccommodationEmail($xml) {
    return $xml->emails->email ?? '';
}

/**
 * Retrieves the name of the accommodation from the XML data.
 *
 * @param SimpleXMLElement $xml The XML data containing accommodation information.
 * @return string The name of the accommodation.
 */
function getAccommodationName($xml) {
    foreach ($xml->names->name as $name) {
        if ($name['locale'] == 'nl' || $name['isDefault'] == 'true') {
            return $name;
        }
    }
    return '';
}

/**
 * Retrieves the phone number of the accommodation from the XML data.
 *
 * @param SimpleXMLElement $xml The XML data containing accommodation information.
 * @return string The phone number of the accommodation.
 */
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

/**
 * Retrieves the rating of the accommodation from the XML data.
 *
 * @param SimpleXMLElement $xml The XML data containing accommodation information.
 * @return string The rating of the accommodation.
 */
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

/**
 * Processes chain data from the XML and prepares it for database insertion.
 *
 * @param SimpleXMLElement $xml The XML data containing chain information.
 * @param array &$output_values The array to store prepared values for database insertion.
 */
function getChains($xml, &$output_values) {
    if (isset($xml->chains)) {
        $output_values[] = prepareValues($xml->chains->chain['giataId'], addslashes(trim($xml->chains->chain->names->name)));
    }
}

/**
 * Processes city data from the XML and prepares it for database insertion.
 *
 * @param SimpleXMLElement $xml The XML data containing city information.
 * @param array &$output_values The array to store prepared values for database insertion.
 */
function getCities($xml, &$output_values) {
    if (isset($xml->city)) {
        foreach ($xml->city->names->name as $name) {
            if (!empty($xml->city['giataId']) && !empty($name) && $name['locale'] == 'nl') {
                $output_values[] = prepareValues($xml->city['giataId'], addslashes(trim($name)));
            }
        }
    }
}

/**
 * Processes destination data from the XML and prepares it for database insertion.
 *
 * @param SimpleXMLElement $xml The XML data containing destination information.
 * @param array &$output_values The array to store prepared values for database insertion.
 */
function getDestinations($xml, &$output_values) {
    if (isset($xml->destination)) {
        foreach ($xml->destination->names->name as $name) {
            if (!empty($xml->destination['giataId']) && !empty($name) && $name['locale'] == 'nl') {
                $output_values[] = prepareValues($xml->destination['giataId'], addslashes(trim($name)));
            }
        }
    }
}

/**
 * Processes room type data from the XML and prepares it for database insertion.
 *
 * @param SimpleXMLElement $xml The XML data containing room type information.
 * @param array &$output_values The array to store prepared values for database insertion.
 */
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

/**
 * Processes variant group data from the XML and prepares it for database insertion.
 *
 * @param SimpleXMLElement $xml The XML data containing variant group information.
 * @param array &$output_values The array to store prepared values for database insertion.
 */
function getVariantGroups($xml, &$output_values) {
    if (isset($xml->variantGroups)) {
        foreach ($xml->variantGroups->variantGroup as $variantGroup) {
            if (!empty($variantGroup['variantGroupTypeId']) && !empty($variantGroup->label)) {
                $output_values[] = prepareValues($variantGroup['variantGroupTypeId'], addslashes($variantGroup->label));
            }
        }
    }
}

/**
 * Processes variant data from the XML and prepares it for database insertion.
 *
 * @param SimpleXMLElement $xml The XML data containing variant information.
 * @param array &$output_values The array to store prepared values for database insertion.
 */
function getVariants($xml, &$output_values) {
    if (isset($xml->variantGroups) && isset($xml->variantGroups->variantGroup)) {
        foreach ($xml->variantGroups->variantGroup->variants->variant as $variant) {
            if (!empty($variant['variantId']) && !empty($variant->label)) {
                $output_values[] = prepareValues($variant['variantId'], addslashes($variant->label));
            }
        }
    }
}

/**
 * Initializes an array to store various types of content for database insertion.
 *
 * This function returns an associative array with keys for different types of content,
 * each initialized to an empty array. The keys include 'chains', 'cities', 'destinations',
 * 'roomtypes', 'variant_groups', and 'variants'.
 *
 * @return array An associative array with keys for different types of content, each initialized to an empty array.
 */
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

/**
 * Inserts accommodation data from the XML into the database.
 *
 * This function processes the XML data for an accommodation, logs any multiple entries,
 * retrieves various accommodation details, prepares the values for database insertion,
 * and inserts the data into the 'vendor_giata_accommodations' table.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SimpleXMLElement $xml The XML data containing accommodation information.
 * @param array $output_columns The columns to insert values into.
 */
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

/**
 * Inserts accommodation facts data from the XML into the database.
 *
 * This function processes the XML data for accommodation facts, prepares the values for database insertion,
 * and inserts the data into the 'vendor_giata_accommodations_facts' table.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SimpleXMLElement $xml The XML data containing accommodation facts information.
 * @param array $output_columns The columns to insert values into.
 */
function insertAccommodationFacts($dbh, $xml, $output_columns) {
    $output_values = [];
    foreach ($xml->facts->fact as $fact) {
        $output_values[] = prepareValues($xml['giataId'], $fact['factDefId']);
    }
    dbinsert($dbh, 'vendor_giata_accommodations_facts', $output_columns, array_unique($output_values));
}

/**
 * Inserts accommodation facts attributes data from the XML into the database.
 *
 * This function processes the XML data for accommodation facts attributes, prepares the values for database insertion,
 * and inserts the data into the 'vendor_giata_accommodations_facts_attributes' table.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SimpleXMLElement $xml The XML data containing accommodation facts attributes information.
 * @param array $output_columns The columns to insert values into.
 */
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

/**
 * Inserts accommodation facts variants data from the XML into the database.
 *
 * This function processes the XML data for accommodation facts variants, prepares the values for database insertion,
 * and inserts the data into the 'vendor_giata_accommodations_facts_variants' table.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SimpleXMLElement $xml The XML data containing accommodation facts variants information.
 * @param array $output_columns The columns to insert values into.
 */
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

/**
 * Inserts accommodation room types data from the XML into the database.
 *
 * This function processes the XML data for accommodation room types, prepares the values for database insertion,
 * and inserts the data into the 'vendor_giata_accommodations_roomtypes' table.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SimpleXMLElement $xml The XML data containing accommodation room types information.
 * @param array $output_columns The columns to insert values into.
 */
function insertAccommodationRoomtypes($dbh, $xml, $output_columns) {
    if (isset($xml->roomTypes)) {
        $output_values = [];
        foreach ($xml->roomTypes->roomType as $roomtype) {
            $output_values[] = prepareValues($xml['giataId'], $roomtype['variantId']);
        }
        dbinsert($dbh, 'vendor_giata_accommodations_roomtypes', $output_columns, array_unique($output_values));
    }
}

/**
 * Inserts image data from the XML into the database.
 *
 * This function processes the XML data for images, prepares the values for database insertion,
 * and inserts the data into the 'vendor_giata_images' table.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SimpleXMLElement $xml The XML data containing image information.
 * @param array $output_columns The columns to insert values into.
 */
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

/**
 * Inserts text data from the XML into the database.
 *
 * This function processes the XML data for texts, prepares the values for database insertion,
 * and inserts the data into the 'vendor_giata_texts' table.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SimpleXMLElement $xml The XML data containing text information.
 * @param array $output_columns The columns to insert values into.
 */
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

/**
 * Inserts various types of content into the database.
 *
 * This function inserts various types of content such as chains, cities, destinations, room types,
 * variant groups, and variants into their respective database tables.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param array $output_columns The columns to insert values into for each type of content.
 * @param array $output_values The values to be inserted into the database for each type of content.
 */
function insertVariousContent($dbh, $output_columns, $output_values) {
    dbinsert($dbh, 'vendor_giata_chains',         $output_columns['chains'],         array_unique($output_values['chains']));
    dbinsert($dbh, 'vendor_giata_cities',         $output_columns['cities'],         array_unique($output_values['cities']));
    dbinsert($dbh, 'vendor_giata_destinations',   $output_columns['destinations'],   array_unique($output_values['destinations']));
    dbinsert($dbh, 'vendor_giata_roomtypes',      $output_columns['roomtypes'],      array_unique($output_values['roomtypes']));
    dbinsert($dbh, 'vendor_giata_variant_groups', $output_columns['variant_groups'], array_unique($output_values['variant_groups']));
    dbinsert($dbh, 'vendor_giata_variants',       $output_columns['variants'],       array_unique($output_values['variants']));
}

/**
 * Logs an error message.
 *
 * This function logs an error message to the console and optionally to a file.
 *
 * @param string $message The error message to log.
 */
function logError($message) {
    echo date("[G:i:s] ") . $message . PHP_EOL;
    // Optionally log to a file
    // error_log($message, 3, '/path/to/error.log');
}

/**
 * Logs multiple entries for a specific XML element.
 *
 * This function checks if there are multiple entries for a specific XML element and logs a message if there are.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SimpleXMLElement $xml The XML data containing the information.
 * @param string $parent The parent XML element.
 * @param string $child The child XML element.
 * @param string $message The message to log if multiple entries are found.
 */
function logMultipleEntries($dbh, $xml, $parent, $child, $message) {
	if (is_countable($xml->$parent->$child)) {
	    if (count($xml->$parent->$child) > 1) {
  	      dblog($dbh, 'INFO', 'GIATA', $message . $xml['giataId']);
  	      echo $message . $xml['giataId'] . PHP_EOL;
 	   }
	}
}

/**
 * Prepares values for database insertion.
 *
 * This function takes multiple values, escapes them, and formats them as a single string for database insertion.
 *
 * @param mixed ...$values The values to prepare for database insertion.
 * @return string The prepared values formatted as a single string for database insertion.
 */
function prepareValues(...$values) {
    return "('" . implode("', '", array_map('addslashes', $values)) . "')";
}

/**
 * Processes the XML content and inserts various types of data into the database.
 *
 * This function processes the XML content for accommodations, images, texts, accommodation facts,
 * accommodation facts attributes, accommodation facts variants, and accommodation room types.
 * It also retrieves and prepares data for chains, cities, destinations, room types, variant groups, and variants.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param SimpleXMLElement $xml The XML data containing the content information.
 * @param array $outputColumns The columns to insert values into for each type of content.
 * @param array &$outputValues The array to store prepared values for database insertion.
 */
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