<?php
/**
 * Class Database
 * 
 * This class provides methods for interacting with a MySQL database using PDO. It includes methods for
 * connecting to the database, executing SELECT queries, inserting multiple rows, logging messages, and
 * truncating tables.
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
 class Database {
    private $dbh;

    /**
     * Database constructor.
     * 
     * @param array $dbConfig The database configuration.
     */
    public function __construct($dbConfig) {
        $this->connect($dbConfig);
    }

    /**
     * Connects to the database using the provided configuration.
     * 
     * @param array $dbConfig The database configuration.
     * @return void
     */
    private function connect(array $dbConfig): void {
        try {
            if (empty($dbConfig['db_pdo_driver_name']) || empty($dbConfig['db_hostname']) || empty($dbConfig['db_database']) || empty($dbConfig['db_username']) || empty($dbConfig['db_password'])) {
                throw new InvalidArgumentException('Invalid database configuration');
            }

            $dsn = $dbConfig['db_pdo_driver_name'] . ':host=' . $dbConfig['db_hostname'] . ';dbname=' . $dbConfig['db_database'] . ';charset=utf8mb4';
            $this->dbh = new PDO(
                $dsn,
                $dbConfig['db_username'],
                $dbConfig['db_password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_PERSISTENT => false
                ]
            );

        } catch (PDOException $e) {
            $this->logError('Caught PDOException: ' . $e->getMessage());
            $this->dbh = null;

        } catch (InvalidArgumentException $e) {
            $this->logError('Caught InvalidArgumentException: ' . $e->getMessage());
            $this->dbh = null;
        }
    }

    /**
     * Logs an error message.
     * 
     * @param string $message The error message to log.
     * @return void
     */
    private function logError(string $message): void {
        echo date("[G:i:s] ") . $message . PHP_EOL;
    }

	/**
	* Executes a SELECT query and returns the fetched rows.
	*
	* @param string $sql The SQL query to execute.
	* @param array $params The parameters to bind to the SQL query.
	* @return array The fetched rows as an associative array.
	*/
	public function query($sql, $params = []) {
	
		try {
			$stmt = $this->dbh->prepare($sql);
			$stmt->execute($params);
			$fetched_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			return $fetched_rows;
	
		} catch (PDOException $e) {
			logError('Caught PDOException: ' . $e->getMessage());
			return [];
		}
	}
}