<?php

/**
 * Class User
 *
 * Handles user-related operations such as creating new user records.
 * Relies on a database abstraction layer (DB::getInstance()) to interact with the database.
 */
class User {
    /**
     * @var DB The database connection instance
     */
    private $_db;

    /**
     * User constructor.
     *
     * Initializes the database connection.
     *
     * @param mixed $user Optional parameter for future use (e.g., loading a specific user)
     */
    public function __construct($user = null) {
        $this->_db = DB::getInstance();
    }

    /**
     * Creates a new user record in the 'users' table.
     *
     * @param array $fields Key-value pairs representing column names and their values.
     * @throws Exception If the insert operation fails.
     */
    public function create($fields = array()) {
        if (!$this->_db->insert('users', $fields)) {
            throw new Exception('There was a problem creating an account.');
        }
    }
}
