<?php
/*

	SCRIPT:		vendor_giata_sql.inc.php
	
	PURPOSE:	Database routines for TradeTracker dashboard.
	
	Copyright 2024 Fred Onis - All rights reserved.

	dbget
	dbget_giata_ids
	dbinsert
	dbopen
	dbtruncate
*/

function dbget($dbh, $sql) {
	
	$stmt			=	$dbh->prepare($sql);
	
	$stmt->execute();
	$fetched_rows	=	$stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	$stmt			=	null;

	return	$fetched_rows;
}

function dbget_giata_ids($dbh) {

	$sql			=	"
	SELECT			giata_id,
					timestamp
	FROM			vendor_giata_accommodations";
	
	return	dbget($dbh, $sql);
}

function dbinsert($dbh, $table, $columns, $values) {

	if (count($values) > 0) {
		
		$columns	=	implode(", ", $columns);
		
		if (mb_substr($values[0], 0, 1) == '(') {
			$values		=	implode(", ", $values);
		} else {
			$values		=	"('" . implode("', '", $values) . "')";
		}
		
		try {
			$sql		=	"INSERT IGNORE INTO $table ($columns) VALUES $values";
			$stmt 		=	$dbh->prepare($sql);
			$stmt->execute();
			$stmt->closeCursor();
			$stmt		=	null;
		} catch (PDOException $e) {
			
			echo date("[G:i:s] ") . 'Caught PDOException: ' . $e->getMessage() . ' SQL:' . $sql . PHP_EOL;
			
		}
	}
	return;
}

function dbopen($dbconfig) {

	$dbh	=	new PDO($dbconfig['db_pdo_driver_name']	. ':host=' . $dbconfig['db_hostname']  . ';dbname='	. $dbconfig['db_database'] . ';charset=utf8mb4',
						$dbconfig['db_username'],
						$dbconfig['db_password'],
						array(
							PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
							PDO::ATTR_PERSISTENT => false
						)
	);
	
	return $dbh;
}

function dbtruncate($dbh, $table_name) {
	
	$sql			= 'TRUNCATE ' . $table_name;
	
	$stmt			= $dbh->prepare($sql);
	$stmt->execute();
	echo date("[G:i:s] ") . '- truncated table ' . $table_name . PHP_EOL;
	$stmt->closeCursor();
	$stmt			= null;

	return;
}