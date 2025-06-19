<?php
/**
 * Class GiataOpenContentImporter
 * 
 * This class handles the import of open content from XML feeds provided by GIATA. It fetches the XML data,
 * processes it, and inserts it into the appropriate database tables. The class ensures that the database is
 * updated with the latest content from the GIATA feed.
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
class GiataOpenContentImporter {
    private $db;
    private $dbConfigPath;
    private $inputUrls;
    private $locale = 'en';
    private $log;
    private $outputColumns;
    private $outputValues;
    private $outputDataLines = 0;
    private $timeStart;

    /**
     * GiataOpenContentImporter constructor.
     * 
     * @param Database $db The database connection object.
     * @param array $inputUrls The URLs to fetch XML data from.
     */
    public function __construct($dbConfigPath, $inputUrls) {
		$this->dbConfigPath = $dbConfigPath;
        $this->inputUrls = $inputUrls;
        $this->log = new Log();
        $this->initializeOutputColumns();
        $this->initializeOutputValues();
        $this->registerExitHandler();
		$this->connectDatabase();
    }

    /**
     * Initializes the output columns for the database tables.
     */
    private function initializeOutputColumns() {
        $this->outputColumns = [
            'accommodations'                 => ['giata_id', 'name', 'city_giata_id', 'destination_giata_id', 'country_code', 'source', 'rating', 'address_street', 'address_streetnum', 'address_zip', 'address_cityname', 'address_pobox', 'address_federalstate_giata_id', 'phone', 'email', 'url', 'geocode_accuracy', 'geocode_latitude', 'geocode_longitude'],
            'accommodations_chains'          => ['giataId', 'chainId'],
            'accommodations_facts'           => ['giataId', 'factDefId'],
            'accommodations_facts_attributes'=> ['giataId', 'factDefId', 'attributeDefId', 'value', 'unitDefId'],
            'accommodations_facts_variants'  => ['giataId', 'factDefId', 'variantId'],
            'accommodations_roomtypes'       => ['giataId', 'variantId'],
            'chains'                         => ['giataId', 'name'],
            'cities'                         => ['giataId', 'name'],
            'destinations'                   => ['giataId', 'name'],
            'images'                         => ['giata_id', 'motif_type', 'last_update', 'is_hero_image', 'image_id', 'base_name', 'max_width', 'href'],
            'roomtypes'                      => ['variantId', 'category', 'code', 'name', 'type', 'view', 'category_attribute_id', 'category_attribute_name', 'type_attribute_id', 'type_attribute_name', 'view_attribute_id', 'view_attribute_name', 'image_relations'],
            'texts'                          => ['giata_id', 'last_update', 'sequence', 'title', 'paragraph'],
            'variant_groups'                 => ['variantGroupTypeId', 'label'],
            'variants'                       => ['variantId', 'label']
        ];
    }

    /**
     * Initializes the output values for the database tables.
     */
    private function initializeOutputValues() {
        $this->outputValues = [
            'accommodations'                 => [],
            'accommodations_chains'          => [],
            'accommodations_facts'           => [],
            'accommodations_facts_attributes'=> [],
            'accommodations_facts_variants'  => [],
            'accommodations_roomtypes'       => [],
            'chains'                         => [],
            'cities'                         => [],
            'destinations'                   => [],
            'images'                         => [],
            'roomtypes'                      => [],
            'texts'                          => [],
            'variant_groups'                 => [],
            'variants'                       => []
        ];
    }

    /**
     * Register the exit handler.
     *
     * @return void
     */
    private function registerExitHandler() {
        $this->timeStart = microtime(true);
        register_shutdown_function([new ExitHandler($this->timeStart), 'handleExit']);
    }

	/**
	 * Connects to the database using the configuration file.
	 *
	 * This method reads the database configuration from the specified INI file,
	 * parses the configuration, and establishes a connection to the database.
	 * If the configuration file cannot be parsed, an exception is thrown.
	 *
	 * @throws Exception If the configuration file cannot be parsed.
	 * @return void
	 */
	private function connectDatabase() {
		if (($dbConfig = parse_ini_file($this->dbConfigPath, FALSE, INI_SCANNER_TYPED)) === FALSE) {
			throw new Exception("Parsing file " . $this->dbConfigPath	. " FAILED");
		}
		$this->db = new Database($dbConfig);
	}

    /**
     * Imports the data from the XML feeds into the database.
     */
    public function import() {
        $this->truncateTables();
        foreach ($this->inputUrls as $inputUrl) {
            $this->log->info('Reading XML Feed ' . $inputUrl);

            if (($contents = file_get_contents($inputUrl)) !== false) {
                $xml = simplexml_load_string($contents);
                $this->processMainXml($xml);
            }
        }

        $this->log->info('- ' . $this->outputDataLines . ' rows processed');
    }

    /**
     * Truncates the relevant database tables.
     */
    private function truncateTables() {
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
			'vendor_giata_variants',
			'vendor_giata_accommodations_chains'
        ];
        foreach ($tables as $table) {
			$this->db->truncate($table);
        }
    }

    /**
     * Processes the main XML data and fetches additional XML data from URLs.
     * 
     * @param SimpleXMLElement $xml The main XML data.
     */
    private function processMainXml($xml) {
        foreach ($xml->url as $url) {
            $this->log->info('- Reading XML Feed ' . $url->loc);

            if (($contents = file_get_contents($url->loc)) !== false) {
                $xml = simplexml_load_string($contents);
                $this->processContent($xml);
                $this->outputDataLines++;
            }
        }

        $this->insertVariousContent();
    }

    /**
     * Processes the XML content and prepares it for database insertion.
     * 
     * @param SimpleXMLElement $xml The XML content.
     */
    private function processContent($xml) {
        $this->insertAccommodation($xml);
        $this->insertImages($xml);
        $this->insertTexts($xml);
        $this->insertAccommodationFacts($xml);
        $this->insertAccommodationFactsAttributes($xml);
        $this->insertAccommodationFactsVariants($xml);
        $this->insertAccommodationRoomtypes($xml);

        $this->getAccommodationChains($xml);
        $this->getChains($xml);
        $this->getCities($xml);
        $this->getDestinations($xml);
        $this->getRoomtypes($xml);
        $this->getVariantGroups($xml);
        $this->getVariants($xml);
    }

    /**
     * Inserts various types of content into the database.
     */
    private function insertVariousContent() {
        $this->db->insert('vendor_giata_chains', $this->outputColumns['chains'], array_unique($this->outputValues['chains']));
        $this->db->insert('vendor_giata_cities', $this->outputColumns['cities'], array_unique($this->outputValues['cities']));
        $this->db->insert('vendor_giata_destinations', $this->outputColumns['destinations'], array_unique($this->outputValues['destinations']));
        $this->db->insert('vendor_giata_roomtypes', $this->outputColumns['roomtypes'], array_unique($this->outputValues['roomtypes']));
        $this->db->insert('vendor_giata_variant_groups', $this->outputColumns['variant_groups'], array_unique($this->outputValues['variant_groups']));
        $this->db->insert('vendor_giata_variants', $this->outputColumns['variants'], array_unique($this->outputValues['variants']));
        //print_r($this->outputValues['accommodations_chains']);
        $this->db->insert('vendor_giata_accommodations_chains', $this->outputColumns['accommodations_chains'], array_unique($this->outputValues['accommodations_chains']));
    }

    /**
     * Inserts accommodation data into the database.
     * 
     * @param SimpleXMLElement $xml The XML data containing accommodation information.
     */
    private function insertAccommodation($xml) {
        $this->logMultipleEntries($xml, 'addresses', 'address', 'Multiple addresses for ');
        $this->logMultipleEntries($xml, 'emails', 'email', 'Multiple emails for ');
        $this->logMultipleEntries($xml, 'geoCodes', 'geoCode', 'Multiple geoCodes for ');
        $this->logMultipleEntries($xml, 'names', 'name', 'Multiple names for ');
        $this->logMultipleEntries($xml, 'phones', 'phone', 'Multiple phones for ');
        $this->logMultipleEntries($xml, 'ratings', 'rating', 'Multiple ratings for ');
        $this->logMultipleEntries($xml, 'urls', 'url', 'Multiple URLs for ');

        $acco_name = $this->getAccommodationName($xml);
        $acco_rating = $this->getAccommodationRating($xml);
        $acco_phone = $this->getAccommodationPhone($xml);
        $acco_email = $this->getAccommodationEmail($xml);
        $acco_url = $this->getAccommodationUrl($xml);

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
            addslashes($acco_url),
            $xml->geoCodes->geoCode['accuracy'] ?? '',
            $xml->geoCodes->geoCode->latitude ?? '',
            $xml->geoCodes->geoCode->longitude ?? ''
        ];

        $this->db->insert('vendor_giata_accommodations', $this->outputColumns['accommodations'], $output_values);
    }

    /**
     * Inserts accommodation facts data into the database.
     * 
     * @param SimpleXMLElement $xml The XML data containing accommodation facts information.
     */
    private function insertAccommodationFacts($xml) {
        $output_values = [];
        foreach ($xml->facts->fact as $fact) {
            $output_values[] = $this->prepareValues($xml['giataId'], $fact['factDefId']);
        }
        $this->db->insert('vendor_giata_accommodations_facts', $this->outputColumns['accommodations_facts'], array_unique($output_values));
    }

    /**
     * Inserts accommodation facts attributes data into the database.
     * 
     * @param SimpleXMLElement $xml The XML data containing accommodation facts attributes information.
     */
    private function insertAccommodationFactsAttributes($xml) {
        $output_values = [];
        foreach ($xml->facts->fact as $fact) {
            if (isset($fact->factInstance->attributes)) {
                foreach ($fact->factInstance->attributes->attribute as $attribute) {
                    $output_values[] = $this->prepareValues(
                        $xml['giataId'],
                        $fact['factDefId'],
                        $attribute['attributeDefId'],
                        addslashes($attribute['value']),
                        $attribute['unitDefId']
                    );
                }
            }
        }
        $this->db->insert('vendor_giata_accommodations_facts_attributes', $this->outputColumns['accommodations_facts_attributes'], array_unique($output_values));
    }

    /**
     * Inserts accommodation facts variants data into the database.
     * 
     * @param SimpleXMLElement $xml The XML data containing accommodation facts variants information.
     */
    private function insertAccommodationFactsVariants($xml) {
        $output_values = [];
        foreach ($xml->facts->fact as $fact) {
            if (isset($fact->factInstance->appliesTo)) {
                foreach ($fact->factInstance->appliesTo->variant as $variant) {
                    $output_values[] = $this->prepareValues($xml['giataId'], $fact['factDefId'], $variant['variantId']);
                }
            }
        }
        $this->db->insert('vendor_giata_accommodations_facts_variants', $this->outputColumns['accommodations_facts_variants'], array_unique($output_values));
    }

    /**
     * Inserts accommodation room types data into the database.
     * 
     * @param SimpleXMLElement $xml The XML data containing accommodation room types information.
     */
    private function insertAccommodationRoomtypes($xml) {
        if (isset($xml->roomTypes)) {
            $output_values = [];
            foreach ($xml->roomTypes->roomType as $roomtype) {
                $output_values[] = $this->prepareValues($xml['giataId'], $roomtype['variantId']);
            }
            $this->db->insert('vendor_giata_accommodations_roomtypes', $this->outputColumns['accommodations_roomtypes'], array_unique($output_values));
        }
    }

    /**
     * Inserts image data into the database.
     * 
     * @param SimpleXMLElement $xml The XML data containing image information.
     */
    private function insertImages($xml) {
        if (!empty($xml->images)) {
            $output_values = [];
            foreach ($xml->images->image as $image) {
                foreach ($image->sizes->size as $size) {
                    $output_values[] = $this->prepareValues(
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
            $this->db->insert('vendor_giata_images', $this->outputColumns['images'], $output_values);
        }
    }

    /**
     * Inserts text data into the database.
     * 
     * @param SimpleXMLElement $xml The XML data containing text information.
     */
    private function insertTexts($xml) {
        if (isset($xml->texts)) {
            $output_values = [];
            $sequence = 0;
            foreach ($xml->texts->text as $text) {
                if ($text['locale'] == $this->locale) {
                    foreach ($text->sections->section as $section) {
                        $output_values[] = $this->prepareValues(
                            $xml['giataId'],
                            $text['lastUpdate'],
                            ++$sequence,
                            addslashes($section->title),
                            addslashes($section->para)
                        );
                    }
                }
            }
            $this->db->insert('vendor_giata_texts', $this->outputColumns['texts'], $output_values);
        }
    }

    /**
     * Retrieves and processes accommodation chains data from the XML.
     * 
     * @param SimpleXMLElement $xml The XML data containing accommodation chains information.
     */
    private function getAccommodationChains($xml) {
        if (isset($xml->chains)) {
            foreach ($xml->chains->chain as $chain) {
                $this->outputValues['accommodations_chains'][] = $this->prepareValues($xml['giataId'], $chain['giataId']);
            }
        }
    }

    /**
     * Retrieves and processes chain data from the XML.
     * 
     * @param SimpleXMLElement $xml The XML data containing chain information.
     */
    private function getChains($xml) {
        if (isset($xml->chains)) {
            foreach ($xml->chains->chain as $chain) {
                $this->outputValues['chains'][] = $this->prepareValues($chain['giataId'], addslashes(trim($chain->names->name)));
            }
        }
    }

    /**
     * Retrieves and processes city data from the XML.
     * 
     * @param SimpleXMLElement $xml The XML data containing city information.
     */
    private function getCities($xml) {
        if (isset($xml->city)) {
            foreach ($xml->city->names->name as $name) {
                if (!empty($xml->city['giataId']) && !empty($name) && $name['locale'] == $this->locale) {
                    $this->outputValues['cities'][] = $this->prepareValues($xml->city['giataId'], addslashes(trim($name)));
                }
            }
        }
    }

    /**
     * Retrieves and processes destination data from the XML.
     * 
     * @param SimpleXMLElement $xml The XML data containing destination information.
     */
    private function getDestinations($xml) {
        if (isset($xml->destination)) {
            foreach ($xml->destination->names->name as $name) {
                if (!empty($xml->destination['giataId']) && !empty($name) && $name['locale'] == $this->locale) {
                    $this->outputValues['destinations'][] = $this->prepareValues($xml->destination['giataId'], addslashes(trim($name)));
                }
            }
        }
    }

    /**
     * Retrieves and processes room type data from the XML.
     * 
     * @param SimpleXMLElement $xml The XML data containing room type information.
     */
    private function getRoomtypes($xml) {
        if (isset($xml->roomTypes)) {
            foreach ($xml->roomTypes->roomType as $roomtype) {
                $imageIds = [];
                foreach ($roomtype->imageRelations->imageId as $imageId) {
                    $imageIds[] = $imageId;
                }
                $this->outputValues['roomtypes'][] = $this->prepareValues(
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

    private function getVariantGroups($xml) {
        if (isset($xml->variantGroups)) {
            foreach ($xml->variantGroups->variantGroup as $variantGroup) {
                if (!empty($variantGroup['variantGroupTypeId']) && !empty($variantGroup->label)) {
                    $this->outputValues['variant_groups'][] = $this->prepareValues($variantGroup['variantGroupTypeId'], addslashes($variantGroup->label));
                }
            }
        }
    }

    private function getVariants($xml) {
        if (isset($xml->variantGroups) && isset($xml->variantGroups->variantGroup)) {
            foreach ($xml->variantGroups->variantGroup->variants->variant as $variant) {
                if (!empty($variant['variantId']) && !empty($variant->label)) {
                    $this->outputValues['variants'][] = $this->prepareValues($variant['variantId'], addslashes($variant->label));
                }
            }
        }
    }

    private function escapeIdentifier($identifier) {
        return "`" . str_replace("`", "``", $identifier) . "`";
    }

    private function prepareValues(...$values) {
        return "('" . implode("', '", array_map('addslashes', $values)) . "')";
    }

    private function logMultipleEntries($xml, $parent, $child, $message) {
        if (is_countable($xml->$parent->$child)) {
            if (count($xml->$parent->$child) > 1) {
                $this->log->info($message . $xml['giataId']);
            }
        }
    }

    private function getAccommodationEmail($xml) {
        if (is_iterable($xml->emails->email)) {
            $emails = [];
            foreach ($xml->emails->email as $email) {
                $emails[] = trim((string)$email);
            }
            return implode(', ', $emails);
        } else {
            return $xml->emails->email ?? '';
        }
    }

    private function getAccommodationName($xml) {
        foreach ($xml->names->name as $name) {
            if ($name['locale'] == $this->locale || $name['isDefault'] == 'true') {
                return $name;
            }
        }
        return '';
    }

    private function getAccommodationPhone($xml) {
        if (is_iterable($xml->phones->phone)) {
            $phones = [];
            foreach ($xml->phones->phone as $phone) {
                if ($phone['tech'] == 'phone') {
                    $phones[] = trim((string)$phone);
                }
            }
            return implode(', ', $phones);
        } else {
            return $xml->phones->phone ?? '';
        }
    }

    private function getAccommodationRating($xml) {
        if (is_iterable($xml->ratings->rating)) {
            foreach ($xml->ratings->rating as $rating) {
                if ($rating['isDefault'] == 'true') {
                    return str_replace(',', '.', $rating);
                }
            }
        }
        return '';
    }

    private function getAccommodationUrl($xml) {
        if (is_iterable($xml->urls->url)) {
            $urls = [];
            foreach ($xml->urls->url as $url) {
                $urls[] = trim((string)$url);
            }
            return implode(', ', $urls);
        } else {
            return $xml->urls->url ?? '';
        }
    }
}