<?php
/**
 * Class Session
 *
 * A simple static class for handling PHP session data.
 * It provides convenient methods to check, set, get, and delete session variables.
 *
 * Methods:
 * - exists($name): Check if a session variable exists.
 * - put($name, $value): Set a session variable.
 * - get($name): Retrieve a session variable.
 * - delete($name): Remove a session variable if it exists.
 */
class Session {
    /**
     * Check if a session variable exists.
     *
     * @param string $name The name of the session variable.
     * @return bool True if the variable exists, false otherwise.
     */
    public static function exists($name) {
        return isset($_SESSION[$name]);
    }

    /**
     * Set a session variable.
     *
     * @param string $name The name of the session variable.
     * @param mixed $value The value to assign.
     * @return mixed The value that was set.
     */
    public static function put($name, $value) {
        return $_SESSION[$name] = $value;
    }

    /**
     * Get the value of a session variable.
     *
     * @param string $name The name of the session variable.
     * @return mixed|null The value of the variable, or null if not set.
     */
    public static function get($name) {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    /**
     * Delete a session variable if it exists.
     *
     * @param string $name The name of the session variable.
     */
    public static function delete($name) {
        if (self::exists($name)) {
            unset($_SESSION[$name]);
        }
    }
}