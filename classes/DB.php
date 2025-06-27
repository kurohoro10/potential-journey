<?php
/**
 * Class DB
 *
 * Implements the Singleton pattern for database access using PDO.
 * Ensures only one instance of the DB connection is created and reused throughout the application.
 *
 * Properties:
 * - $_pdo: PDO object for database connection
 * - $_query: Last executed PDOStatement
 * - $_error: Boolean indicating if the last query had an error
 * - $_results: Stores the result set of the last query
 * - $_count: Number of rows affected or returned
 *
 * Methods:
 * - __construct(): Private constructor initializes the PDO connection
 * - getInstance(): Returns the single instance of the DB class
 */
class DB {
    private static $_instance = null;
    private $_pdo,
            $_query,
            $_error = false,
            $_results,
            $_count = 0;

    /**
     * Private constructor to prevent direct object creation.
     * Establishes a PDO database connection using Config values.
     */
    private function __construct() {
        try {
            $this->_pdo = new PDO(
                'mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db'),
                Config::get('mysql/username'),
                Config::get('mysql/password')
            );
        } catch (\PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * Returns the single instance of the DB class.
     *
     * @return DB
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new DB();
        }
        return self::$_instance;
    }
}
