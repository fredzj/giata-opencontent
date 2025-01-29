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
     */
    private function connect($dbConfig) {
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
     * Returns the PDO database connection handle.
     * 
     * @return PDO|null The PDO database connection handle, or null on failure.
     */
    public function getConnection() {
        return $this->dbh;
    }

    /**
     * Executes a SELECT query and returns the fetched rows.
     * 
     * @param string $sql The SQL query to execute.
     * @param array $params The parameters to bind to the SQL query.
     * @return array The fetched rows as an associative array.
     */
    public function select($sql, $params = []) {
        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute($params);
            $fetchedRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $fetchedRows;

        } catch (PDOException $e) {
            $this->logError('Caught PDOException: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Inserts multiple rows into a table.
     * 
     * @param string $table The name of the table to insert into.
     * @param array $columns The columns to insert values into.
     * @param array $values The values to insert.
     */
    public function insert($table, $columns, $values) {
        if (count($values) > 0) {
            $columns = implode(", ", $columns);

            if (mb_substr($values[0], 0, 1) == '(') {
                $values = implode(", ", $values);
            } else {
                $values = "('" . implode("', '", $values) . "')";
            }

            try {
                $sql = "INSERT IGNORE INTO $table ($columns) VALUES $values";
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();
                $stmt->closeCursor();
            } catch (PDOException $e) {
                $this->logError('Caught PDOException: ' . $e->getMessage() . ' SQL:' . $sql);
            }
        }
    }

    /**
     * Logs a message to the database.
     * 
     * @param string $level The log level (e.g., 'INFO', 'ERROR').
     * @param string $label A short label for the log entry.
     * @param string $description A detailed description of the log entry.
     */
    public function log($level, $label, $description) {
        $outputColumns = ['level', 'label', 'description'];
        $outputValues = [$level, $label, $description];
        $this->insert('log', $outputColumns, $outputValues);
    }

    /**
     * Truncates a table in the database.
     * 
     * @param string $tableName The name of the table to truncate.
     */
    public function truncate($tableName) {
        try {
            $sql = 'TRUNCATE `' . $tableName . '`';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            echo date("[G:i:s] ") . '- truncated table ' . htmlspecialchars($tableName, ENT_QUOTES, 'UTF-8') . PHP_EOL;
            $stmt->closeCursor();

        } catch (PDOException $e) {
            $this->logError('Caught PDOException: ' . $e->getMessage());
        }
    }

    /**
     * Logs an error message.
     * 
     * @param string $message The error message to log.
     */
    private function logError($message) {
        echo date("[G:i:s] ") . $message . PHP_EOL;
    }
}