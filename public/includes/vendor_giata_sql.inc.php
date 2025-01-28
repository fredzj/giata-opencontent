<?php
/**
 * SCRIPT: vendor_giata_sql.inc.php
 * PURPOSE: Database routines for Giata dashboard.
 * 
 * This file contains functions for interacting with the database, including
 * executing SELECT queries, fetching configuration values, inserting multiple
 * rows into a table, and opening a database connection.
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
 * Executes a SELECT query and returns the fetched rows.
 *
 * @param PDO $dbh The PDO database connection handle.
 * @param string $sql The SQL query to execute.
 * @param array $params The parameters to bind to the SQL query.
 * @return array The fetched rows as an associative array.
 */
function dbget($dbh, $sql, $params = []) {

    try {
        $stmt = $dbh->prepare($sql);
        $stmt->execute($params);
        $fetched_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $fetched_rows;

    } catch (PDOException $e) {
        logError('Caught PDOException: ' . $e->getMessage());
        return [];
    }
}

function dbget_giata_accommodations($dbh) {

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
	
	return	dbget($dbh, $sql);
}

function dbget_giata_chains($dbh) {
    $sql = "
    SELECT giataId, name
    FROM vendor_giata_chains
    ORDER BY 1";
    return dbget($dbh, $sql);
}

function dbget_giata_cities($dbh) {
    $sql = "
    SELECT giataId, name
    FROM vendor_giata_cities
    ORDER BY 1";
    return dbget($dbh, $sql);
}

function dbget_giata_definitions_attributes($dbh) {
    $sql = "
    SELECT a.id, a.label, a.valueType,
           CASE WHEN u1.label = u2.label THEN u1.label ELSE CONCAT(u1.label, ', ', u2.label) END AS units
    FROM vendor_giata_definitions_attributes a
    LEFT JOIN vendor_giata_definitions_units u1 ON u1.id = SUBSTRING_INDEX(a.units, '|', 1)
    LEFT JOIN vendor_giata_definitions_units u2 ON u2.id = SUBSTRING_INDEX(a.units, '|', -1)
    ORDER BY 1";
    return dbget($dbh, $sql);
}

function dbget_giata_definitions_contexttree($dbh) {
    $sql = "
    SELECT t1.id, t1.label, t2.label AS parent,
           GROUP_CONCAT(DISTINCT df.label ORDER BY 1 SEPARATOR ', ') AS facts
    FROM vendor_giata_definitions_contexttree t1
    LEFT JOIN vendor_giata_definitions_contexttree t2 ON t2.id = t1.parentContextTreeId
    LEFT JOIN vendor_giata_definitions_contexttree_facts f ON f.contextTreeId = t1.id
    JOIN vendor_giata_definitions_facts df ON df.id = f.factId
    GROUP BY 1";
    return dbget($dbh, $sql);
}

function dbget_giata_definitions_facts($dbh) {
    $sql = "
    SELECT id, label
    FROM vendor_giata_definitions_facts
    ORDER BY 1";
    return dbget($dbh, $sql);
}

function dbget_giata_definitions_motif_types($dbh) {
    $sql = "
    SELECT id, label
    FROM vendor_giata_definitions_motif_types
    ORDER BY 1";
    return dbget($dbh, $sql);
}

function dbget_giata_definitions_units($dbh) {
    $sql = "
    SELECT id, label
    FROM vendor_giata_definitions_units
    ORDER BY 1";
    return dbget($dbh, $sql);
}

function dbget_giata_destinations($dbh) {
    $sql = "
    SELECT giataId, name
    FROM vendor_giata_destinations
    ORDER BY 1";
    return dbget($dbh, $sql);
}

function dbget_giata_roomtypes($dbh) {
    $sql = "
    SELECT r.variantId, v.label AS variant, r.category, r.name, r.type, r.view, r.image_relations
    FROM vendor_giata_roomtypes r
    LEFT JOIN vendor_giata_variants v ON v.variantId = r.variantId
    ORDER BY 1";
    return dbget($dbh, $sql);
}

function dbget_giata_texts($dbh) {
    $sql = "
    SELECT giata_id, last_update, sequence, title, paragraph
    FROM vendor_giata_texts
    ORDER BY 1, 3";
    return dbget($dbh, $sql);
}

/**
 * Opens a database connection using the provided configuration.
 *
 * @param array $dbconfig The database configuration.
 * @return PDO|null The PDO database connection handle, or null on failure.
 */
function dbopen($dbconfig) {

    try {
        // Validate configuration
        if (empty($dbconfig['db_pdo_driver_name']) || empty($dbconfig['db_hostname']) || empty($dbconfig['db_database']) || empty($dbconfig['db_username']) || empty($dbconfig['db_password'])) {
            throw new InvalidArgumentException('Invalid database configuration');
        }

        // Create PDO instance
        $dsn = $dbconfig['db_pdo_driver_name'] . ':host=' . $dbconfig['db_hostname'] . ';dbname=' . $dbconfig['db_database'] . ';charset=utf8mb4';
        $dbh = new PDO(
            $dsn,
            $dbconfig['db_username'],
            $dbconfig['db_password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => false
            ]
        );

        return $dbh;

    } catch (PDOException $e) {
        logError('Caught PDOException: ' . $e->getMessage());
        return null;

    } catch (InvalidArgumentException $e) {
        logError('Caught InvalidArgumentException: ' . $e->getMessage());
        return null;
    }
}
