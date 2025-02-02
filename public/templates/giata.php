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