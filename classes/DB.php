<?php
/**
 * Class DB
 *
 * A singleton database handler class for interacting with a MySQL database using PDO.
 * Provides methods for querying, inserting, updating, deleting, and retrieving results.
 *
 * Features:
 * - Singleton pattern to maintain a single database connection.
 * - Parameterized queries to help prevent SQL injection.
 * - Chainable query execution with support for binding values.
 * - Utility methods for common operations (get, insert, update, delete).
 *
 * Usage:
 *   $db = DB::getInstance();
 *   $db->query("SELECT * FROM users WHERE id = ?", [1]);
 *   $results = $db->results();
 *
 * Dependencies:
 * - Requires a `Config` class with static `get()` method for fetching DB configuration.
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

    /**
     * Prepares and executes a SQL query with optional parameters.
     *
     * @param string $sql    The SQL query to prepare and execute.
     * @param array  $params Optional parameters to bind to the query.
     * @return DB
     */
    public function query( $sql, $params = array() ) {
        $this->_error = false;
        try {
            if ( $this->_query = $this->_pdo->prepare( $sql ) ) {
                $x = 1;
                if ( count( $params ) ) {
                    foreach ( $params as $param ) {
                        $this->_query->bindValue( $x, $param );
                        $x++;
                    }
                }
                
                if ( $this->_query->execute() ) {
                    $this->_results = $this->_query->fetchAll( PDO::FETCH_OBJ );
                    $this->_count = $this->_query->rowCount();
                } else {
                    $this->_error = true;
                }
            }
        } catch (PDOException $e) {
            $this->_error = true;
            die("Query Error: " . $e->getMessage());
        }

        return $this;
    }

    /**
     * Generic method to handle SELECT and DELETE queries with a WHERE clause.
     *
     * @param string $action The SQL action (e.g., 'SELECT *', 'DELETE').
     * @param string $table  The table to query.
     * @param array  $where  An array containing the field, operator, and value for the WHERE clause.
     * @return DB|bool Returns the DB instance on success, or false on failure.
     */
    public function action( $action, $table, $where = array() ) {
        if ( count( $where ) === 3) {
            $operators = array( '>', '<', '>=', '<=', '=', '!=', '<>' );

            $field    = $where[0];
            $operator = $where[1];
            $value    = $where[2];

            if ( in_array( $operator, $operators) ) {
                $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";

                if ( ! $this->query( $sql, array( $value ) )->error() ) {
                    return $this;
                }
            }
        }

        return false;
    }

    public function get( $table, $where ) {
        return $this->action( 'SELECT *', $table, $where );
    }

    public function delete( $table, $where ) {
        return $this->action( 'DELETE', $table, $where );
    }

    /**
     * Inserts a new record into the specified table.
     *
     * @param string $table  The table to insert into.
     * @param array  $fields An associative array of field names and values to insert.
     * @return bool Returns true on success, false on failure.
     */
    public function insert( $table, $fields = array() ) {
        $keys = array_keys( $fields );
        $values = '';
        $x = 1;

        // Add commas to values placeholder except the last value
        foreach ($fields as $field) {
            $values .= '?';
            if ( $x < count( $fields ) ) {
                $values .= ', ';
            }
            $x++;
        }

        $sql = "INSERT INTO {$table} (`" . implode('`, `', $keys) . "`) VALUES ({$values})";

        if ( ! $this->query( $sql, array_values( $fields ) )->error() ) {
            return true;
        }

        return false;
    }

    /**
     * Updates an existing record in the specified table.
     *
     * @param string $table  The table to update.
     * @param int    $id     The ID of the record to update.
     * @param array  $fields An associative array of field names and values to update.
     * @return bool Returns true on success, false on failure.
     */
    public function update( $table, $id, $fields ) {
        $set = '';
        $x = 1;

        foreach( $fields as $name => $value ) {
            $set .= "{$name} = ?";
            if ( $x < count( $fields ) ) {
                $set .= ', ';
            }
            $x++;
        }

        $sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";

        if ( ! $this->query( $sql, $fields )->error() ) {
            return true;
        }
        return false;
    }

    public function results() {
        return $this->_results;
    }

    public function first() {
        return $this->results()[0];
    }

    public function error() {
        return $this->_error;
    }

    public function count() {
        return $this->_count;
    }
}
