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
    private $_db,
            $_data,
            $_sessionName;

    /**
     * User constructor.
     *
     * Initializes the database connection.
     *
     * @param mixed $user Optional parameter for future use (e.g., loading a specific user)
     */
    public function __construct($user = null) {
        $this->_db = DB::getInstance();
        $this->_sessionName = Config::get('session/session_name');
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

    public function find($user = null) {
        if ($user) {
            $field = (is_numeric($user)) ? 'id' : 'username';
            $data = $this->_db->get('users', array($field, '=', $user));

            if ($data->count()) {
                $this->_data  = $data->first();
                return true;
            }
        }
        return false;
    }

    public function login($username = null, $password = null) {
        $user = $this->find($username);
        
        if ($user) {
            if ($this->data()->password === Hash::make($password, $this->data()->salt)) {
                Session::put($this->_sessionName, $this->data()->id);
                return true;
            }
        }
        return false;
    }

    private function data() {
        return $this->_data;
    }
}
