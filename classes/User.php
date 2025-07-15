<?php

/**
 * Class User
 *
 * Handles user-related operations such as account creation, login, logout,
 * session management, and user retrieval from the database.
 * 
 * Responsibilities:
 * - Create new user records.
 * - Find users by ID or username.
 * - Authenticate and log in users.
 * - Manage session and "remember me" cookies.
 * - Check login status.
 */
class User {
    /**
     * @var DB The database connection instance.
     */
    private $_db;

    /**
     * @var mixed Stores user data retrieved from the database.
     */
    private $_data;

    /**
     * @var string The name of the session variable used to store the user ID.
     */
    private $_sessionName;

    /**
     * @var string The name of the cookie used for persistent login ("remember me").
     */
    private $_cookieName;

    /**
     * @var bool Indicates whether the user is currently logged in.
     */
    private $_isLoggedIn = false;

    /**
     * User constructor.
     *
     * Initializes the database connection and checks for an existing session or cookie.
     * If a user is found in the session, sets the user as logged in.
     *
     * @param mixed|null $user Optional user ID or username to initialize the object with.
     * @throws Exception If the user cannot be found or database errors occur.
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
                    // Optional: handle invalid session (e.g., logout)
                }
            }
        } else {
            $this->find($user);
        }
    }

    /**
     * Creates a new user in the database.
     *
     * @param array $fields An associative array of column-value pairs for the new user.
     * @throws Exception If the database insert operation fails.
     */
    public function create($fields = array()) {
        if (!$this->_db->insert('users', $fields)) {
            throw new Exception('There was a problem creating an account.');
        }
    }

    public function update($fields = array(), $id = null) {
        if (!$id && $this->isLoggedIn()) {
            $id = $this->data()->id;
        }

        if (!$this->_db->update('users', $id, $fields)) {
            throw new Exception('There was a problem updating.');
        }
    }

    /**
     * Finds a user by ID or username.
     *
     * @param mixed $user The user ID (int) or username (string).
     * @return bool True if the user was found and data loaded, false otherwise.
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
     * Authenticates and logs in the user.
     *
     * If credentials are not passed and the user exists, it reinitializes the session.
     * If credentials are passed, it verifies them and sets up session and "remember me" cookie if needed.
     *
     * @param string|null $username The username of the user.
     * @param string|null $password The plain-text password.
     * @param bool $remember Whether to enable "remember me" persistent login.
     * @return bool True if login is successful, false otherwise.
     */
    public function login($username = null, $password = null, $remember = false) {
        if (!$username && !$password && $this->exists()) {
            Session::put($this->_sessionName, $this->data()->id);
        } else {
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
        }

        return false;
    }

    /**
     * Checks if the current user instance has valid data loaded.
     *
     * @return bool True if user data exists, false otherwise.
     */
    public function exists() {
        return (!empty($this->data())) ? true : false;
    }

    /**
     * Logs out the current user by clearing the session and cookies.
     *
     * Also removes the persistent login hash from the database.
     */
    public function logout() {
        $this->_db->delete('users_session', array('user_id', '=', $this->data()->id));

        Cookie::delete($this->_cookieName);
        Session::delete($this->_sessionName);
    }

    /**
     * Returns the user's data as an object.
     *
     * @return mixed|null User data if loaded, otherwise null.
     */
    public function data() {
        return $this->_data;
    }

    /**
     * Checks whether the user is currently logged in.
     *
     * @return bool True if logged in, false otherwise.
     */
    public function isLoggedIn() {
        return $this->_isLoggedIn;
    }
}
