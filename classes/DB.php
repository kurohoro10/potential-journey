<?php
/**
 * Class DB
 *
 * A Singleton class for database access using PDO. This class wraps PDO functionality
 * and provides methods for executing parameterized queries securely.
 *
 * Key Features:
 * - Singleton pattern ensures only one PDO connection is created.
 * - Simplified query execution and error handling.
 * - Supports SELECT and DELETE operations via the `action()` helper method.
 *
 * Properties:
 * @property PDO           $_pdo       PDO instance for database connection.
 * @property PDOStatement  $_query     Last prepared PDOStatement.
 * @property bool          $_error     Flag indicating whether the last query resulted in an error.
 * @property array         $_results   Result set from the last executed query (as an array of objects).
 * @property int           $_count     Number of rows returned or affected by the last query.
 *
 * Methods:
 * - __construct()        : Private constructor that initializes the PDO connection using Config values.
 * - getInstance()        : Returns the singleton instance of the DB class.
 * - query($sql, $params) : Prepares and executes a SQL statement with optional parameters.
 * - action($action, $table, $where): Generic method to handle SELECT and DELETE queries with a WHERE clause.
 * - get($table, $where)  : Performs a SELECT * FROM table WHERE ... query.
 * - delete($table, $where): Performs a DELETE FROM table WHERE ... query.
 * - error()              : Returns true if the last query had an error.
 * - count()              : Returns the number of rows affected or returned by the last query.
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
