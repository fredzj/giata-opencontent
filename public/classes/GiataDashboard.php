<?php
class GiataDashboard {
    private $db;
    private $dbConfigPath;

    public function __construct($dbConfigPath) {
        $this->db;
        $this->dbConfigPath = $dbConfigPath;
		$this->connectDatabase();
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
		unset($dbConfig);
	}
	
	/**
	* Generates an HTML table with the given data and columns.
	*
	* @param array $data An array of associative arrays representing the table rows.
	* @param array $columns An array of associative arrays representing the table columns. Each column should have:
	*                       - 'field': The key in the data array to be displayed in this column.
	*                       - 'label': The label to be displayed in the table header.
	*                       - 'align' (optional): The alignment of the column ('left', 'center', 'right').
	*
	* @return string The generated HTML table as a string.
	*/
	private function getHtmlTable($data, $columns) {
		$html = '<table class="table table-sm" data-custom-sort="" data-toggle="table" data-pagination="true" data-search="true" data-show-export="true">';
		$html .= '<thead><tr>';
		foreach ($columns as $column) {
			$html .= '<th scope="col" data-field="' . $column['field'] . '" data-sortable="true" ' . (isset($column['align']) ? 'data-align="' . $column['align'] . '"' : '') . '>' . $column['label'] . '</th>';
		}
		$html .= '</tr></thead><tbody>';
		foreach ($data as $row) {
			$html .= '<tr>';
			foreach ($columns as $column) {
				$html .= '<td>' . htmlspecialchars($row[$column['field']]) . '</td>';
			}
			$html .= '</tr>';
		}
		$html .= '</tbody></table>';
		return $html;
	}
	
	/**
	* Generates an HTML table for attribute definitions.
	*
	* This function retrieves attribute definitions from the database, prepares the columns,
	* and generates an HTML table.
	*
	* @return string The generated HTML table as a string.
	*/
	public function getHtmlDefinitionsAttributes() {
		$data = $this->getDefinitionsAttributes();
		$columns = [
			['field' => 'id', 'label' => 'ID', 'align' => 'right'],
			['field' => 'label', 'label' => 'Label'],
			['field' => 'valueType', 'label' => 'Value Type'],
			['field' => 'units', 'label' => 'Units']
		];
		return $this->getHtmlTable($data, $columns);
	}

	/**
	* Generates an HTML table for context tree definitions.
	*
	* This function retrieves context tree definitions from the database, prepares the columns,
	* and generates an HTML table.
	*
	* @return string The generated HTML table as a string.
	*/
	public function getHtmlDefinitionsContexttree() {
		$data = $this->getDefinitionsContexttree();
		$columns = [
			['field' => 'id', 'label' => 'ID', 'align' => 'right'],
			['field' => 'parent', 'label' => 'Parent'],
			['field' => 'label', 'label' => 'Label'],
			['field' => 'facts', 'label' => 'Facts']
		];
		return $this->getHtmlTable($data, $columns);
	}
	
	/**
	* Generates an HTML table for fact definitions.
	*
	* This function retrieves fact definitions from the database, prepares the columns,
	* and generates an HTML table.
	*
	* @return string The generated HTML table as a string.
	*/
	public function getHtmlDefinitionsFacts() {
		$data = $this->getDefinitionsFacts();
		$columns = [
			['field' => 'id', 'label' => 'ID', 'align' => 'right'],
			['field' => 'label', 'label' => 'Label']
		];
		return $this->getHtmlTable($data, $columns);
	}
	
	/**
	* Generates an HTML table for motif type definitions.
	*
	* This function retrieves motif type definitions from the database, prepares the columns,
	* and generates an HTML table.
	*
	* @return string The generated HTML table as a string.
	*/
	public function getHtmlDefinitionsMotifTypes() {
		$data = $this->getDefinitionsMotifTypes();
		$columns = [
			['field' => 'id', 'label' => 'ID', 'align' => 'right'],
			['field' => 'label', 'label' => 'Label']
		];
		return $this->getHtmlTable($data, $columns);
	}

	/**
	* Generates an HTML table for unit definitions.
	*
	* This function retrieves unit definitions from the database, prepares the columns,
	* and generates an HTML table.
	*
	* @return string The generated HTML table as a string.
	*/
	public function getHtmlDefinitionsUnits() {
		$data = $this->getDefinitionsUnits();
		$columns = [
			['field' => 'id', 'label' => 'ID', 'align' => 'right'],
			['field' => 'label', 'label' => 'Label']
		];
		return $this->getHtmlTable($data, $columns);
	}
	
	/**
	* Generates an HTML table for accommodations.
	*
	* This function retrieves accommodations data from the database, prepares the columns,
	* and generates an HTML table.
	*
	* @return string The generated HTML table as a string.
	*/
	public function getHtmlAccommodations() {
		$data = $this->getAccommodations();
		$columns = [
			['field' => 'giata_id', 'label' => 'Giata ID', 'align' => 'right'],
			['field' => 'name', 'label' => 'Name'],
			['field' => 'city', 'label' => 'City'],
			['field' => 'destination', 'label' => 'Destination'],
			['field' => 'country_code', 'label' => 'Country'],
			['field' => 'rating', 'label' => 'Rating', 'align' => 'right'],
			['field' => 'address', 'label' => 'Address'],
			['field' => 'contact', 'label' => 'Contact'],
			['field' => 'geocode', 'label' => 'Geocodes', 'align' => 'right'],
			['field' => 'roomtypes', 'label' => 'Roomtypes'],
			['field' => 'facts', 'label' => 'Facts']
		];
		return $this->getHtmlTable($data, $columns);
	}

	/**
	* Generates an HTML table for chains.
	*
	* This function retrieves chains data from the database, prepares the columns,
	* and generates an HTML table.
	*
	* @return string The generated HTML table as a string.
	*/
	public function getHtmlChains() {
		$data = $this->getChains();
		$columns = [
			['field' => 'giataId', 'label' => 'Giata ID', 'align' => 'right'],
			['field' => 'name', 'label' => 'Name']
		];
		return $this->getHtmlTable($data, $columns);
	}
	
	/**
	* Generates an HTML table for cities.
	*
	* This function retrieves cities data from the database, prepares the columns,
	* and generates an HTML table.
	*
	* @return string The generated HTML table as a string.
	*/
	public function getHtmlCities() {
		$data = $this->getCities();
		$columns = [
			['field' => 'giataId', 'label' => 'Giata ID', 'align' => 'right'],
			['field' => 'name', 'label' => 'Name']
		];
		return $this->getHtmlTable($data, $columns);
	}
	
	/**
	* Generates an HTML table for destinations.
	*
	* This function retrieves destinations data from the database, prepares the columns,
	* and generates an HTML table.
	*
	* @return string The generated HTML table as a string.
	*/
	public function getHtmlDestinations() {
		$data = $this->getDestinations();
		$columns = [
			['field' => 'giataId', 'label' => 'Giata ID', 'align' => 'right'],
			['field' => 'name', 'label' => 'Name']
		];
		return $this->getHtmlTable($data, $columns);
	}

	/**
	* Generates an HTML table for room types.
	*
	* This function retrieves room types data from the database, prepares the columns,
	* and generates an HTML table.
	*
	* @return string The generated HTML table as a string.
	*/
	public function getHtmlRoomtypes() {
		$data = $this->getRoomtypes();
		$columns = [
			['field' => 'variantId', 'label' => 'Variant ID'],
			['field' => 'variant', 'label' => 'Variant'],
			['field' => 'category', 'label' => 'Category'],
			['field' => 'name', 'label' => 'Name'],
			['field' => 'type', 'label' => 'Type'],
			['field' => 'view', 'label' => 'View'],
			['field' => 'image_relations', 'label' => 'Images']
		];
		return $this->getHtmlTable($data, $columns);
	}

	/**
	* Generates an HTML table for texts.
	*
	* This function retrieves texts data from the database, prepares the columns,
	* and generates an HTML table.
	*
	* @return string The generated HTML table as a string.
	*/
	public function getHtmlTexts() {
		$data = $this->getTexts();
		$columns = [
			['field' => 'giata_id', 'label' => 'Giata ID', 'align' => 'right'],
			['field' => 'last_update', 'label' => 'Last Update'],
			['field' => 'sequence', 'label' => 'Sequence'],
			['field' => 'title', 'label' => 'Title'],
			['field' => 'paragraph', 'label' => 'Paragraph']
		];
		return $this->getHtmlTable($data, $columns);
	}

	private function getAccommodations() {

		$sql			=	"
		SELECT			a.giata_id,
						a.name,
						c.name										AS	city,
						d.name										AS	destination,
						a.country_code,
						a.source,
						a.rating,
						a.address_street,
						a.address_streetnum,
						a.address_zip,
						a.address_cityname,
						a.address_pobox,
						a.phone,
						a.email,
						a.url,
						a.geocode_accuracy,
						a.geocode_latitude,
						a.geocode_longitude,
						GROUP_CONCAT(DISTINCT v.label ORDER BY 1 SEPARATOR ', ')
																	AS	roomtypes,
						GROUP_CONCAT(DISTINCT df.label ORDER BY 1 SEPARATOR ', ')
																	AS	facts
		FROM			vendor_giata_accommodations a
		LEFT JOIN		vendor_giata_cities c						ON	c.giataId	=	a.city_giata_id
		LEFT JOIN		vendor_giata_destinations d					ON	d.giataId	=	a.destination_giata_id
		LEFT JOIN		vendor_giata_accommodations_roomtypes t		ON	t.giataId	=	a.giata_id
		LEFT JOIN		vendor_giata_variants v						ON	v.variantID	=	t.variantId
		LEFT JOIN		vendor_giata_accommodations_facts f			ON	f.giataId	=	a.giata_id
		LEFT JOIN		vendor_giata_definitions_facts df			ON	df.id		=	f.factDefId
		GROUP BY		1";
		
		return	$this->db->query($sql);
	}
	
	private function getChains() {
		$sql = "
		SELECT giataId, name
		FROM vendor_giata_chains
		ORDER BY 1";
		return $this->db->query($sql);
	}
	
	private function getCities() {
		$sql = "
		SELECT giataId, name
		FROM vendor_giata_cities
		ORDER BY 1";
		return $this->db->query($sql);
	}
	
	private function getDefinitionsAttributes() {
		$sql = "
		SELECT a.id, a.label, a.valueType,
			   CASE WHEN u1.label = u2.label THEN u1.label ELSE CONCAT(u1.label, ', ', u2.label) END AS units
		FROM vendor_giata_definitions_attributes a
		LEFT JOIN vendor_giata_definitions_units u1 ON u1.id = SUBSTRING_INDEX(a.units, '|', 1)
		LEFT JOIN vendor_giata_definitions_units u2 ON u2.id = SUBSTRING_INDEX(a.units, '|', -1)
		ORDER BY 1";
		return $this->db->query($sql);
	}
	
	private function getDefinitionsContexttree() {
		$sql = "
		SELECT t1.id, t1.label, t2.label AS parent,
			   GROUP_CONCAT(DISTINCT df.label ORDER BY 1 SEPARATOR ', ') AS facts
		FROM vendor_giata_definitions_contexttree t1
		LEFT JOIN vendor_giata_definitions_contexttree t2 ON t2.id = t1.parentContextTreeId
		LEFT JOIN vendor_giata_definitions_contexttree_facts f ON f.contextTreeId = t1.id
		JOIN vendor_giata_definitions_facts df ON df.id = f.factId
		GROUP BY 1";
		return $this->db->query($sql);
	}
	
	private function getDefinitionsFacts() {
		$sql = "
		SELECT id, label
		FROM vendor_giata_definitions_facts
		ORDER BY 1";
		return $this->db->query($sql);
	}
	
	private function getDefinitionsMotifTypes() {
		$sql = "
		SELECT id, label
		FROM vendor_giata_definitions_motif_types
		ORDER BY 1";
		return $this->db->query($sql);
	}
	
	private function getDefinitionsUnits() {
		$sql = "
		SELECT id, label
		FROM vendor_giata_definitions_units
		ORDER BY 1";
		return $this->db->query($sql);
	}
	
	private function getDestinations() {
		$sql = "
		SELECT giataId, name
		FROM vendor_giata_destinations
		ORDER BY 1";
		return $this->db->query($sql);
	}
	
	private function getRoomtypes() {
		$sql = "
		SELECT r.variantId, v.label AS variant, r.category, r.name, r.type, r.view, r.image_relations
		FROM vendor_giata_roomtypes r
		LEFT JOIN vendor_giata_variants v ON v.variantId = r.variantId
		ORDER BY 1";
		return $this->db->query($sql);
	}
	
	private function getTexts() {
		$sql = "
		SELECT giata_id, last_update, sequence, title, paragraph
		FROM vendor_giata_texts
		ORDER BY 1, 3";
		return $this->db->query($sql);
	}
}