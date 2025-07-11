<?php
/**
 * Class Cookie
 * 
 * This class provides static methods to manage cookies in a PHP application.
 * It includes functionality to check for the existence of a cookie, retrieve it,
 * create/set it with an expiry time, and delete it.
 */
class Cookie {
    /**
     * Check if a cookie with the given name exists.
     *
     * @param string $name The name of the cookie.
     * @return bool True if the cookie exists, false otherwise.
     */
    public static function exists($name) {
        return isset($_COOKIE[$name]) ? true : false;
    }

    /**
     * Retrieve the value of a cookie.
     *
     * @param string $name The name of the cookie.
     * @return mixed The value of the cookie.
     */
    public static function get($name) {
        return $_COOKIE[$name];
    }

    /**
     * Create or update a cookie.
     *
     * @param string $name The name of the cookie.
     * @param string $value The value to store in the cookie.
     * @param int $expiry The lifetime of the cookie in seconds.
     * @return bool True if the cookie was successfully set, false otherwise.
     */
    public static function put($name, $value, $expiry) {
        if (setcookie($name, $value, time() + $expiry, '/')) {
            return true;
        }
        return false;
    }

    /**
     * Delete a cookie by setting its expiration time to the past.
     *
     * @param string $name The name of the cookie.
     */
    public static function delete($name) {
        self::put($name, '', time() - 1);
    }
}