<?php
header('Content-Type: text/html; charset=utf-8');
/**
 * SCRIPT: giata.php
 * PURPOSE: Show data from Giata in a dashboard.
 * 
 * This script generates a web-based dashboard to display data from Giata. It fetches data from the database
 * and presents it in various HTML tables. The dashboard includes information on accommodations, chains, cities,
 * destinations, images, room types, texts, variant groups, and variants. The script ensures that the data is
 * presented in a user-friendly manner, allowing for easy navigation and interaction.
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
function get_html_table($data, $columns) {
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
 * @param PDO $dbh The PDO database connection handle.
 * @return string The generated HTML table as a string.
 */
function get_html_definitions_attributes($dbh) {
    $data = dbget_giata_definitions_attributes($dbh);
    $columns = [
        ['field' => 'id', 'label' => 'ID', 'align' => 'right'],
        ['field' => 'label', 'label' => 'Label'],
        ['field' => 'valueType', 'label' => 'Value Type'],
        ['field' => 'units', 'label' => 'Units']
    ];
    return get_html_table($data, $columns);
}

/**
 * Generates an HTML table for context tree definitions.
 *
 * This function retrieves context tree definitions from the database, prepares the columns,
 * and generates an HTML table.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @return string The generated HTML table as a string.
 */
function get_html_definitions_contexttree($dbh) {
    $data = dbget_giata_definitions_contexttree($dbh);
    $columns = [
        ['field' => 'id', 'label' => 'ID', 'align' => 'right'],
        ['field' => 'parent', 'label' => 'Parent'],
        ['field' => 'label', 'label' => 'Label'],
        ['field' => 'facts', 'label' => 'Facts']
    ];
    return get_html_table($data, $columns);
}

/**
 * Generates an HTML table for fact definitions.
 *
 * This function retrieves fact definitions from the database, prepares the columns,
 * and generates an HTML table.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @return string The generated HTML table as a string.
 */
function get_html_definitions_facts($dbh) {
    $data = dbget_giata_definitions_facts($dbh);
    $columns = [
        ['field' => 'id', 'label' => 'ID', 'align' => 'right'],
        ['field' => 'label', 'label' => 'Label']
    ];
    return get_html_table($data, $columns);
}

/**
 * Generates an HTML table for motif type definitions.
 *
 * This function retrieves motif type definitions from the database, prepares the columns,
 * and generates an HTML table.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @return string The generated HTML table as a string.
 */
function get_html_definitions_motif_types($dbh) {
    $data = dbget_giata_definitions_motif_types($dbh);
    $columns = [
        ['field' => 'id', 'label' => 'ID', 'align' => 'right'],
        ['field' => 'label', 'label' => 'Label']
    ];
    return get_html_table($data, $columns);
}

/**
 * Generates an HTML table for unit definitions.
 *
 * This function retrieves unit definitions from the database, prepares the columns,
 * and generates an HTML table.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @return string The generated HTML table as a string.
 */
function get_html_definitions_units($dbh) {
    $data = dbget_giata_definitions_units($dbh);
    $columns = [
        ['field' => 'id', 'label' => 'ID', 'align' => 'right'],
        ['field' => 'label', 'label' => 'Label']
    ];
    return get_html_table($data, $columns);
}

/**
 * Generates an HTML table for accommodations.
 *
 * This function retrieves accommodations data from the database, prepares the columns,
 * and generates an HTML table.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @return string The generated HTML table as a string.
 */
function get_html_accommodations($dbh) {
    $data = dbget_giata_accommodations($dbh);
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
    return get_html_table($data, $columns);
}

/**
 * Generates an HTML table for chains.
 *
 * This function retrieves chains data from the database, prepares the columns,
 * and generates an HTML table.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @return string The generated HTML table as a string.
 */
function get_html_chains($dbh) {
    $data = dbget_giata_chains($dbh);
    $columns = [
        ['field' => 'giataId', 'label' => 'Giata ID', 'align' => 'right'],
        ['field' => 'name', 'label' => 'Name']
    ];
    return get_html_table($data, $columns);
}

/**
 * Generates an HTML table for cities.
 *
 * This function retrieves cities data from the database, prepares the columns,
 * and generates an HTML table.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @return string The generated HTML table as a string.
 */
function get_html_cities($dbh) {
    $data = dbget_giata_cities($dbh);
    $columns = [
        ['field' => 'giataId', 'label' => 'Giata ID', 'align' => 'right'],
        ['field' => 'name', 'label' => 'Name']
    ];
    return get_html_table($data, $columns);
}

/**
 * Generates an HTML table for destinations.
 *
 * This function retrieves destinations data from the database, prepares the columns,
 * and generates an HTML table.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @return string The generated HTML table as a string.
 */
function get_html_destinations($dbh) {
    $data = dbget_giata_destinations($dbh);
    $columns = [
        ['field' => 'giataId', 'label' => 'Giata ID', 'align' => 'right'],
        ['field' => 'name', 'label' => 'Name']
    ];
    return get_html_table($data, $columns);
}

/**
 * Generates an HTML table for room types.
 *
 * This function retrieves room types data from the database, prepares the columns,
 * and generates an HTML table.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @return string The generated HTML table as a string.
 */
function get_html_roomtypes($dbh) {
    $data = dbget_giata_roomtypes($dbh);
    $columns = [
        ['field' => 'variantId', 'label' => 'Variant ID'],
        ['field' => 'variant', 'label' => 'Variant'],
        ['field' => 'category', 'label' => 'Category'],
        ['field' => 'name', 'label' => 'Name'],
        ['field' => 'type', 'label' => 'Type'],
        ['field' => 'view', 'label' => 'View'],
        ['field' => 'image_relations', 'label' => 'Images']
    ];
    return get_html_table($data, $columns);
}

/**
 * Generates an HTML table for texts.
 *
 * This function retrieves texts data from the database, prepares the columns,
 * and generates an HTML table.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @return string The generated HTML table as a string.
 */
function get_html_texts($dbh) {
    $data = dbget_giata_texts($dbh);
    $columns = [
        ['field' => 'giata_id', 'label' => 'Giata ID', 'align' => 'right'],
        ['field' => 'last_update', 'label' => 'Last Update'],
        ['field' => 'sequence', 'label' => 'Sequence'],
        ['field' => 'title', 'label' => 'Title'],
        ['field' => 'paragraph', 'label' => 'Paragraph']
    ];
    return get_html_table($data, $columns);
}

try {
	
	###
	### STANDARD INIT ROUTINE
	###
	
	require 'includes/init.inc.php';
	require 'includes/vendor_giata_sql.inc.php';
	
	###
	### CUSTOM INIT ROUTINE
	###
	
	###
	### DATABASE INIT ROUTINE
	###
	
	$dbh		=	dbopen($dbconfig);

	###
	### PROCESSING ROUTINE
	###
	
	$html_definitions_attributes	=	get_html_definitions_attributes(	$dbh);
	$html_definitions_contexttree	=	get_html_definitions_contexttree(	$dbh);
	$html_definitions_facts			=	get_html_definitions_facts(			$dbh);
	$html_definitions_motif_types	=	get_html_definitions_motif_types(	$dbh);
	$html_definitions_units			=	get_html_definitions_units(			$dbh);
	$html_accommodations			=	get_html_accommodations(			$dbh);
	$html_chains					=	get_html_chains(					$dbh);
	$html_cities					=	get_html_cities(					$dbh);
	$html_destinations				=	get_html_destinations(				$dbh);
	$html_roomtypes					=	get_html_roomtypes(					$dbh);
	$html_texts						=	get_html_texts(						$dbh);

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
    logError('Caught Exception: ' . $e->getMessage());
} finally {

	###
	### STANDARD EXIT ROUTINE
	###

}
?>
<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Giata Dashboard</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.23.5/dist/bootstrap-table.min.css">
	<style>
	body {
		font-size: small;
	}
	img {
		background-color: #fff;
	}
	</style>
	<script src="https://kit.fontawesome.com/da52944850.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="container-fluid">

	<h1>Giata Dashboard</h1>

	<ul class="nav nav-tabs" id="myTab" role="tablist">
		<li class="nav-item" role="presentation">
			<button class="nav-link active" id="attributes-tab"			data-bs-toggle="tab" data-bs-target="#attributes-tab-pane"		type="button" role="tab" aria-controls="attributes-tab-pane"		aria-selected="true">Attribute Definitions</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link"		id="contexttree-tab"		data-bs-toggle="tab" data-bs-target="#contexttree-tab-pane"		type="button" role="tab" aria-controls="contexttree-tab-pane"	aria-selected="false">Context Tree Definitions</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link"		id="facts-tab"				data-bs-toggle="tab" data-bs-target="#facts-tab-pane"			type="button" role="tab" aria-controls="facts-tab-pane"		aria-selected="false">Fact Definitions</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link"		id="motiftypes-tab"			data-bs-toggle="tab" data-bs-target="#motiftypes-tab-pane"		type="button" role="tab" aria-controls="motiftypes-tab-pane"		aria-selected="false">Motif Type Definitions</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link"		id="units-tab"				data-bs-toggle="tab" data-bs-target="#units-tab-pane"			type="button" role="tab" aria-controls="units-tab-pane"		aria-selected="false">Unit Definitions</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link"		id="accommodations-tab"		data-bs-toggle="tab" data-bs-target="#accommodations-tab-pane"	type="button" role="tab" aria-controls="accommodations-tab-pane"		aria-selected="false">Accommodations</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link"		id="texts-tab"				data-bs-toggle="tab" data-bs-target="#texts-tab-pane"			type="button" role="tab" aria-controls="texts-tab-pane"		aria-selected="false">Texts</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link"		id="roomtypes-tab"			data-bs-toggle="tab" data-bs-target="#roomtypes-tab-pane"		type="button" role="tab" aria-controls="roomtypes-tab-pane"		aria-selected="false">Roomtypes</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link"		id="chains-tab"				data-bs-toggle="tab" data-bs-target="#chains-tab-pane"			type="button" role="tab" aria-controls="chains-tab-pane"		aria-selected="false">Chains</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link"		id="cities-tab"				data-bs-toggle="tab" data-bs-target="#cities-tab-pane"			type="button" role="tab" aria-controls="cities-tab-pane"		aria-selected="false">Cities</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link"		id="destinations-tab"		data-bs-toggle="tab" data-bs-target="#destinations-tab-pane"	type="button" role="tab" aria-controls="destinations-tab-pane"		aria-selected="false">Destinations</button>
		</li>
	</ul>
	
	<div class="tab-content" id="myTabContent">
		<div class="tab-pane fade show active"	id="attributes-tab-pane"		role="tabpanel" aria-labelledby="attributes-tab"		tabindex="0">
			<?php	echo $html_definitions_attributes; ?>		
		</div>		
		<div class="tab-pane fade"				id="contexttree-tab-pane"		role="tabpanel" aria-labelledby="contexttree-tab"		tabindex="0">
			<?php	echo $html_definitions_contexttree; ?>		
		</div>		
		<div class="tab-pane fade"				id="facts-tab-pane"				role="tabpanel" aria-labelledby="facts-tab"				tabindex="0">
			<?php	echo $html_definitions_facts; ?>		
		</div>		
		<div class="tab-pane fade"				id="motiftypes-tab-pane"		role="tabpanel" aria-labelledby="motiftypes-tab"		tabindex="0">
			<?php	echo $html_definitions_motif_types; ?>		
		</div>		
		<div class="tab-pane fade"				id="units-tab-pane"				role="tabpanel" aria-labelledby="units-tab"				tabindex="0">
			<?php	echo $html_definitions_units; ?>
		</div>
		<div class="tab-pane fade"				id="accommodations-tab-pane"	role="tabpanel" aria-labelledby="accommodations-tab"	tabindex="0">
			<?php	echo $html_accommodations; ?>
		</div>
		<div class="tab-pane fade"				id="texts-tab-pane"				role="tabpanel" aria-labelledby="texts-tab"				tabindex="0">
			<?php	echo $html_texts; ?>
		</div>
		<div class="tab-pane fade"				id="roomtypes-tab-pane"			role="tabpanel" aria-labelledby="roomtypes-tab"			tabindex="0">
			<?php	echo $html_roomtypes; ?>
		</div>
		<div class="tab-pane fade"				id="chains-tab-pane"			role="tabpanel" aria-labelledby="chains-tab"			tabindex="0">
			<?php	echo $html_chains; ?>
		</div>
		<div class="tab-pane fade"				id="cities-tab-pane"			role="tabpanel" aria-labelledby="cities-tab"			tabindex="0">
			<?php	echo $html_cities; ?>
		</div>
		<div class="tab-pane fade"				id="destinations-tab-pane"		role="tabpanel" aria-labelledby="destinations-tab"		tabindex="0">
			<?php	echo $html_destinations; ?>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.29.0/tableExport.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.23.5/dist/bootstrap-table.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.23.5/dist/extensions/export/bootstrap-table-export.min.js"></script>
	<script>
		function customSort(sortName, sortOrder, data) {
			var order = sortOrder === 'desc' ? -1 : 1
			data.sort(function (a, b) {
			var aa = +((a[sortName] + '').replace(/[^\d]/g, ''))
			var bb = +((b[sortName] + '').replace(/[^\d]/g, ''))
			if (aa < bb) {
				return order * -1
			}
			if (aa > bb) {
				return order
			}
			return 0
			})
		}
	</script>
</div>
</body>
</html>