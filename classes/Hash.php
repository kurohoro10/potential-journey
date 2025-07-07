<?php

/**
 * Class Hash
 *
 * Provides static methods for hashing, generating salts, and producing unique identifiers.
 * Primarily used for password hashing and secure token generation.
 */
class Hash {
    /**
     * Hashes a string with an optional salt using SHA-256.
     *
     * @param string $string The input string to hash.
     * @param string $salt Optional salt to concatenate to the string before hashing.
     * @return string The resulting SHA-256 hash.
     */
    public static function make($string, $salt = '') {
        return hash('sha256', $string . $salt);
    }

    /**
     * Generates a cryptographically secure random salt.
     *
     * @param int $length The length of the raw binary salt before base64 encoding.
     * @return string A base64-encoded salt string.
     * @throws Exception If random_bytes() fails to generate secure random data.
     */
    public static function salt($length) {
        return base64_encode(random_bytes($length));
    }

    /**
     * Generates a unique hashed identifier.
     * Useful for token creation, password resets, etc.
     *
     * @return string A SHA-256 hash of a unique ID.
     */
    public static function unique() {
        return self::make(uniqid('', true)); // More entropy
    }
}
