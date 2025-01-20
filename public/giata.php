<?php
/*

	SCRIPT:		index.php
	
	PURPOSE:	Show data from Giata in a dashboard.
	
	Copyright 2024 Fred Onis - All rights reserved.
	
	get_html_definitions_attributes
	get_html_definitions_contexttree
	get_html_definitions_facts
	get_html_definitions_motif_types
	get_html_definitions_units
	
	get_html_accommodations
	get_html_chains
	get_html_cities
	get_html_destinations
	get_html_roomtypes
	get_html_texts

*/
function get_html_definitions_attributes($dbh) {
	
	$html		=	'';
	
	foreach (dbget_giata_definitions_attributes($dbh) as $row) {
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $row['id']			.	'</td>';
		$html	.=	'<td>' . $row['label']		.	'</td>';
		$html	.=	'<td>' . $row['valueType']	.	'</td>';
		$html	.=	'<td>' . $row['units']		.	'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort=""
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" data-field="id"		data-sortable="true" data-align="right"	>ID</th>'
			.	'<th scope="col" data-field="label"		data-sortable="true"					>Label</th>'
			.	'<th scope="col" data-field="valuetype"	data-sortable="true"					>Value Type</th>'
			.	'<th scope="col" data-field="units"		data-sortable="true"					>Units</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';
	
	return $html;
}

function get_html_definitions_contexttree($dbh) {

	$html		=	'';
	
	foreach (dbget_giata_definitions_contexttree($dbh) as $row) {
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $row['id']		.	'</td>';
		$html	.=	'<td>' . $row['parent']	.	'</td>';
		$html	.=	'<td>' . $row['label']	.	'</td>';
		$html	.=	'<td>' . $row['facts']	.	'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort=""
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" data-field="id"		data-sortable="true" data-align="right"	>ID</th>'
			.	'<th scope="col" data-field="parent"	data-sortable="true"					>Parent</th>'
			.	'<th scope="col" data-field="label"		data-sortable="true"					>Label</th>'
			.	'<th scope="col" data-field="facts"		data-sortable="true"					>Facts</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';

	return $html;
}

function get_html_definitions_facts($dbh) {
	
	$html		=	'';
	
	foreach (dbget_giata_definitions_facts($dbh) as $row) {
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $row['id']		.	'</td>';
		$html	.=	'<td>' . $row['label']	.	'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort=""
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" data-field="id"	data-sortable="true" data-align="right"	>ID</th>'
			.	'<th scope="col" data-field="label"	data-sortable="true"					>Label</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';
	
	return $html;
}

function get_html_definitions_motif_types($dbh) {

	$html		=	'';

	foreach (dbget_giata_definitions_motif_types($dbh) as $row) {
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $row['id']		.	'</td>';
		$html	.=	'<td>' . $row['label']	.	'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort=""
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" data-field="id"	data-sortable="true">ID</th>'
			.	'<th scope="col" data-field="label"	data-sortable="true">Label</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';

	return $html;
}

function get_html_definitions_units($dbh) {

	$html		=	'';
	
	foreach (dbget_giata_definitions_units($dbh) as $row) {
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $row['id']		.	'</td>';
		$html	.=	'<td>' . $row['label']	.	'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort=""
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" data-field="id"	data-sortable="true" data-align="right"	>ID</th>'
			.	'<th scope="col" data-field="label"	data-sortable="true"					>Label</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';
	
	return $html;
}

function get_html_accommodations($dbh) {

	$html		=	'';
	
	foreach (dbget_giata_accommodations($dbh) as $row) {
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $row['giata_id']			.	'</td>';
		$html	.=	'<td>' . $row['name']				.	'</td>';
		$html	.=	'<td>' . $row['city']				.	'</td>';
		$html	.=	'<td>' . $row['destination']		.	'</td>';
		$html	.=	'<td>' . $row['country_code']		.	'</td>';
		$html	.=	'<td>' . $row['rating']				.	'</td>';
		$html	.=	'<td>' . $row['address_street'] . ' ' . $row['address_streetnum'] . '<br>' . $row['address_zip'] . ' ' . $row['address_cityname'] . '<br>' . $row['address_pobox']		.	'</td>';
		$html	.=	'<td>' . $row['phone'] . '<br>' . $row['email'] . '<br>' . $row['url']				.	'</td>';
		$html	.=	'<td>' . $row['geocode_accuracy'] . '<br>' . $row['geocode_latitude'] . '<br>' . $row['geocode_longitude']	.	'</td>';
		$html	.=	'<td class="text-nowrap">' . $row['roomtypes']			.	'</td>';
		$html	.=	'<td>' . $row['facts']				.	'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort=""
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" data-field="giata_id"			data-sortable="true" data-align="right"	>Giata ID</th>'
			.	'<th scope="col" data-field="name"				data-sortable="true"					>Name</th>'
			.	'<th scope="col" data-field="city"				data-sortable="true"					>City</th>'
			.	'<th scope="col" data-field="destination"		data-sortable="true"					>Destination</th>'
			.	'<th scope="col" data-field="country_code"		data-sortable="true"					>Country</th>'
			.	'<th scope="col" data-field="rating"			data-sortable="true" data-align="right"	>Rating</th>'
			.	'<th scope="col" data-field="address"			data-sortable="true"					>Address</th>'
			.	'<th scope="col" data-field="contact"			data-sortable="true"					>Contact</th>'
			.	'<th scope="col" data-field="geocode"			data-sortable="true" data-align="right"	>Geocodes</th>'
			.	'<th scope="col" data-field="roomtypes"			data-sortable="true"					>Roomtypes</th>'
			.	'<th scope="col" data-field="facts"				data-sortable="true"					>Facts</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';
	
	return $html;
}

function get_html_chains($dbh) {

	$html		=	'';
	
	foreach (dbget_giata_chains($dbh) as $row) {
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $row['giataId']			.	'</td>';
		$html	.=	'<td>' . $row['name']				.	'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort=""
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" data-field="giataId"			data-sortable="true" data-align="right"	>Giata ID</th>'
			.	'<th scope="col" data-field="name"				data-sortable="true"					>Name</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';
	
	return $html;
}

function get_html_cities($dbh) {

	$html		=	'';
	
	foreach (dbget_giata_cities($dbh) as $row) {
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $row['giataId']			.	'</td>';
		$html	.=	'<td>' . $row['name']				.	'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort=""
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" data-field="giataId"			data-sortable="true" data-align="right"	>Giata ID</th>'
			.	'<th scope="col" data-field="name"				data-sortable="true"					>Name</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';
	
	return $html;
}

function get_html_destinations($dbh) {

	$html		=	'';
	
	foreach (dbget_giata_destinations($dbh) as $row) {
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $row['giataId']			.	'</td>';
		$html	.=	'<td>' . $row['name']				.	'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort=""
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" data-field="giataId"			data-sortable="true" data-align="right"	>Giata ID</th>'
			.	'<th scope="col" data-field="name"				data-sortable="true"					>Name</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';
	
	return $html;
}

function get_html_roomtypes($dbh) {

	$html		=	'';
	
	foreach (dbget_giata_roomtypes($dbh) as $row) {
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $row['variantId']			.	'</td>';
		$html	.=	'<td>' . $row['variant']			.	'</td>';
		$html	.=	'<td>' . $row['category']			.	'</td>';
		$html	.=	'<td>' . $row['name']				.	'</td>';
		$html	.=	'<td>' . $row['type']				.	'</td>';
		$html	.=	'<td>' . $row['view']				.	'</td>';
		$html	.=	'<td>' . $row['image_relations']	.	'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort=""
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" data-field="variantId"			data-sortable="true"					>Variant ID</th>'
			.	'<th scope="col" data-field="variant"			data-sortable="true"					>Variant</th>'
			.	'<th scope="col" data-field="category"			data-sortable="true"					>Category</th>'
			.	'<th scope="col" data-field="name"				data-sortable="true"					>Name</th>'
			.	'<th scope="col" data-field="type"				data-sortable="true"					>Type</th>'
			.	'<th scope="col" data-field="view"				data-sortable="true"					>View</th>'
			.	'<th scope="col" data-field="image_relations"	data-sortable="true"					>Images</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';
	
	return $html;
}

function get_html_texts($dbh) {

	$html		=	'';
	
	foreach (dbget_giata_texts($dbh) as $row) {
		
		$html	.=	'<tr>';
		$html	.=	'<td>' . $row['giata_id']			.	'</td>';
		$html	.=	'<td>' . $row['last_update']		.	'</td>';
		$html	.=	'<td>' . $row['sequence']			.	'</td>';
		$html	.=	'<td>' . $row['title']				.	'</td>';
		$html	.=	'<td>' . $row['paragraph']			.	'</td>';
		$html	.=	'</tr>';
	}

	$html	=	'<table class="table table-sm"	data-custom-sort=""
												data-toggle="table"
												data-pagination="true"
												data-search="true"
												data-show-export="true">'
			.	'<thead><tr>'
			.	'<th scope="col" data-field="giata_id"			data-sortable="true" data-align="right"	>Giata ID</th>'
			.	'<th scope="col" data-field="last_update"		data-sortable="true"					>Last Update</th>'
			.	'<th scope="col" data-field="sequence"			data-sortable="true"					>Sequence</th>'
			.	'<th scope="col" data-field="title"				data-sortable="true"					>Title</th>'
			.	'<th scope="col" data-field="paragraph"			data-sortable="true"					>Paragraph</th>'
			.	'</tr></thead>'
			.	'<tbody>'
			.	$html
			.	'</tbody>'
			.	'</table>';
	
	return $html;
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
	
	echo date("[G:i:s] ") . 'Caught PDOException: ' . $e->getMessage() . '<br/>';
	
} catch (Exception $e) {
	
	echo date("[G:i:s] ") . 'Caught Exception: ' . $e->getMessage() . '<br/>';
	
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