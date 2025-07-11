<?php

/**
 * class User
 * 
 * This class handles user-related operations such as creating a user, finding a user,
 * logging in, and checking if a user is logged in. It interacts with the database
 * to perform these operations and manages user sessions.
 */
class User {
    /**
     * @var DB The database connection instance
     */
    private $_db;

    /**
     * @var mixed Holds user data after a successful find operation 
     * 
     */
    private $_data;

    /**
     * @var string The name of the session variable used to store username or user ID
     */
    private $_sessionName;

    /**
     * @var string The name of the cookie used for user sessions
     */
    private $_cookieName;

    /**
     * @var boolean Indicates whether the user is currently logged in
     */
    private $_isLoggedIn = false;

        /**
     * User constructor.
     *
     * Initializes the database connection.
     *
     * @param mixed $user Optional parameter to specify a user ID or username.
     * If provided, attempts to find the user and set the logged-in status.
     * If not provided, checks the session for an existing user.
     * 
     * @throws Exception If the user cannot be found or if there is an issue with the database connection.
     */
    public function __construct($user = null) {
        $this->_db = DB::getInstance();
        $this->_sessionName = Config::get('session/session_name');
        $this->_cookieName = Config::get('remember/cookie_name');

        if (!$user) {
            if (Session::exists($this->_sessionName)) {
                $user = Session::get($this->_sessionName);
                if ($this->find($user)) {
                    $this->_isLoggedIn = true;
                } else {
                    // Process logout
                }
            }
        } else {
            $this->find($user);
        }
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

    /**
     * Finds a user by ID or username.
     *
     * @param mixed $user The user ID or username to search for.
     * @return bool Returns true if the user is found, false otherwise.
     */
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

    /**
     * Logs in a user by checking the provided username and password.
     *
     * @param string|null $username The username of the user.
     * @param string|null $password The password of the user.
     * @return bool Returns true if login is successful, false otherwise.
     */
    public function login($username = null, $password = null, $remember = false) {
        $user = $this->find($username);
        
        if ($user) {
            if ($this->data()->password === Hash::make($password, $this->data()->salt)) {
                Session::put($this->_sessionName, $this->data()->id);

                if ($remember) {
                    $hash = Hash::unique();
                    $hashCheck = $this->_db->get('users_session', array('user_id', '=', $this->data()->id));

                    if (!$hashCheck->count()) {
                        $this->_db->insert('users_session', array(
                            'user_id' => $this->data()->id,
                            'hash' => $hash
                        ));
                    } else {
                        $hash = $hashCheck->first()->hash;
                    }

                    Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
                }

                return true;
            }
        }
        return false;
    }

    /**
     * Logs out the user by deleting the session variable.
     *
     * This method effectively ends the user's session and clears any stored user data.
     */
    public function logout() {
        Session::delete($this->_sessionName);
    }
    
    public function data() {
        return $this->_data;
    }

    public function isLoggedIn() {
        return $this->_isLoggedIn;
    }
}
