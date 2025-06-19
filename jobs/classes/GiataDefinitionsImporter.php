<?php
/**
 * Class GiataDefinitionsImporter
 * 
 * This class handles the import of definitions from a JSON feed provided by GIATA. It fetches the JSON data,
 * processes it, and inserts it into the appropriate database tables. The class ensures that the database is
 * updated with the latest definitions from the GIATA feed.
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
class GiataDefinitionsImporter {
    private $db;
    private $dbConfigPath;
    private $inputUrl;
    private $log;
    private $outputColumns;
    private $outputValues;
    private $outputDataLines = 0;
    private $timeStart;

    /**
     * GiataDefinitionsImporter constructor.
     * 
     * @param Database $db The database connection object.
     * @param string $url The URL to fetch JSON data from.
     */
    public function __construct($dbConfigPath, $inputUrl) {
		$this->dbConfigPath  = $dbConfigPath;
        $this->inputUrl = $inputUrl;
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
            'attributes'                => ['id', 'label', 'valueType', 'units'],
            'contexttree'               => ['id', 'label', 'parentContextTreeId'],
            'contexttree_facts'         => ['contextTreeId', 'factId'],
            'facts'                     => ['id', 'label'],
            'facts_attributes'          => ['factId', 'attributeId'],
            'facts_variantgrouptypes'   => ['factId', 'variantGroupTypeId'],
            'motif_types'               => ['id', 'label'],
            'units'                     => ['id', 'label']
        ];
    }

     /**
     * Initializes the output values for the database tables.
     */
    private function initializeOutputValues() {
        $this->outputValues = [
            'attributes'                => [],
            'contexttree'               => [],
            'contexttree_facts'         => [],
            'facts'                     => [],
            'facts_attributes'          => [],
            'facts_variantgrouptypes'   => [],
            'motif_types'               => [],
            'units'                     => []
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
     * Imports the data from the JSON feed into the database.
     */
    public function import() {
        $this->truncateTables();
        $jsonData = $this->fetchData();
        $data = json_decode($jsonData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            //die("Failed to decode JSON: " . json_last_error_msg());
            $this->log->error('- Failed to decode JSON: ' . json_last_error_msg());
        }

        $this->processData($data);
        $this->insertData();
        $this->log->info('- ' . $this->outputDataLines . ' rows processed');
    }

    /**
     * Truncates the relevant database tables.
     */
    private function truncateTables() {
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
			$this->db->truncate($table);
        }
    }

    /**
     * Fetches the JSON data from the specified URL.
     * 
     * @return string The fetched JSON data.
     */
    private function fetchData() {
        $this->log->info('Reading JSON Feed ' . $this->inputUrl . '...');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->inputUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            die("Failed to fetch data from URL");
        }

        return $response;
    }
    
    /**
     * Processes the JSON data and prepares it for database insertion.
     * 
     * @param array $data The JSON data as an associative array.
     */
    private function processData($data) {
        foreach ($data as $language => $subjects) {
            foreach ($subjects as $subject => $array) {
                foreach ($array as $key => $values) {
                    switch ($subject) {
                        case 'contextTree':
                            $this->processContextTree($key, $values);
                            break;
                        case 'facts':
                            $this->processFacts($key, $values);
                            break;
                        case 'attributes':
                            $this->processAttributes($key, $values);
                            break;
                        case 'units':
                            $this->processUnits($key, $values);
                            break;
                        case 'motifTypes':
                            $this->processMotifTypes($key, $values);
                            break;
                        default:
                            break;
                    }
                }
            }
        }
    }

    /**
     * Processes context tree data and prepares it for database insertion.
     * 
     * @param string $key The key of the context tree.
     * @param array $values The values of the context tree.
     */
    private function processContextTree($key, $values) {
        $this->outputValues['contexttree'][] = "('" . $key . "', '" . addslashes($values['label']) . "', '')";
        $this->outputDataLines++;
        foreach ($values['facts'] as $factId) {
            $this->outputValues['contexttree_facts'][] = "('" . $key . "', '" . $factId . "')";
            $this->outputDataLines++;
        }
        if (array_key_exists('sub', $values)) {
            foreach ($values['sub'] as $key2 => $values2) {
                $this->outputValues['contexttree'][] = "('" . $key2 . "', '" . addslashes($values2['label']) . "', '" . $key . "')";
                foreach ($values2['facts'] as $factId) {
                    $this->outputValues['contexttree_facts'][] = "('" . $key2 . "', '" . $factId . "')";
                    $this->outputDataLines++;
                }
            }
        }
    }

    /**
     * Processes facts data and prepares it for database insertion.
     * 
     * @param string $key The key of the fact.
     * @param array $values The values of the fact.
     */
    private function processFacts($key, $values) {
        $this->outputValues['facts'][] = "('" . $key . "', '" . addslashes($values['label']) . "')";
        $this->outputDataLines++;
        foreach ($values['attributes'] as $fact_attribute) {
            $this->outputValues['facts_attributes'][] = "('" . $key . "', '" . $fact_attribute . "')";
            $this->outputDataLines++;
        }
        if (array_key_exists('variantGroupTypes', $values)) {
            foreach ($values['variantGroupTypes'] as $fact_variantGroupType) {
                $this->outputValues['facts_variantgrouptypes'][] = "('" . $key . "', '" . $fact_variantGroupType . "')";
                $this->outputDataLines++;
            }
        }
    }

    /**
     * Processes attributes data and prepares it for database insertion.
     * 
     * @param string $key The key of the attribute.
     * @param array $values The values of the attribute.
     */
    private function processAttributes($key, $values) {
        $valueType = $values['valueType'] ?? '';
        $units = array_key_exists('units', $values) ? implode('|', $values['units']) : '';
        $this->outputValues['attributes'][] = "('" . $key . "', '" . addslashes($values['label']) . "', '" . $valueType . "', '" . $units . "')";
        $this->outputDataLines++;
    }

    /**
     * Processes units data and prepares it for database insertion.
     * 
     * @param string $key The key of the unit.
     * @param array $values The values of the unit.
     */
    private function processUnits($key, $values) {
        $this->outputValues['units'][] = "('" . $key . "', '" . addslashes($values['label']) . "')";
        $this->outputDataLines++;
    }

    /**
     * Processes motif types data and prepares it for database insertion.
     * 
     * @param string $key The key of the motif type.
     * @param array $values The values of the motif type.
     */
    private function processMotifTypes($key, $values) {
        $this->outputValues['motif_types'][] = "('" . $key . "', '" . addslashes($values['label']) . "')";
        $this->outputDataLines++;
    }

    /**
     * Inserts the processed data into the database.
     */
    private function insertData() {
        $this->db->insert('vendor_giata_definitions_attributes', $this->outputColumns['attributes'], array_unique($this->outputValues['attributes']));
        $this->db->insert('vendor_giata_definitions_contexttree', $this->outputColumns['contexttree'], array_unique($this->outputValues['contexttree']));
        $this->db->insert('vendor_giata_definitions_contexttree_facts', $this->outputColumns['contexttree_facts'], array_unique($this->outputValues['contexttree_facts']));
        $this->db->insert('vendor_giata_definitions_facts', $this->outputColumns['facts'], array_unique($this->outputValues['facts']));
        $this->db->insert('vendor_giata_definitions_facts_attributes', $this->outputColumns['facts_attributes'], array_unique($this->outputValues['facts_attributes']));
        $this->db->insert('vendor_giata_definitions_facts_variantgrouptypes', $this->outputColumns['facts_variantgrouptypes'], array_unique($this->outputValues['facts_variantgrouptypes']));
        $this->db->insert('vendor_giata_definitions_motif_types', $this->outputColumns['motif_types'], array_unique($this->outputValues['motif_types']));
        $this->db->insert('vendor_giata_definitions_units', $this->outputColumns['units'], array_unique($this->outputValues['units']));
    }

    /**
     * Escapes an identifier for use in SQL queries.
     * 
     * @param string $identifier The identifier to escape.
     * @return string The escaped identifier.
     */
    private function escapeIdentifier($identifier) {
        return "`" . str_replace("`", "``", $identifier) . "`";
    }
}