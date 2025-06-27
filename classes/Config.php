<?php
/**
 * Class Config
 *
 * Provides access to global configuration settings using a path-based lookup.
 * 
 * This class assumes configuration values are stored in the global `$GLOBALS['config']` array
 * in a nested associative structure (e.g., `$config['mysql']['host']`).
 *
 * Example usage:
 *   Config::get('mysql/host') // returns the value of $GLOBALS['config']['mysql']['host']
 */
class Config {
    /**
     * Retrieves a configuration value by its slash-separated path.
     *
     * @param string|null $path The path to the configuration value, e.g., 'mysql/host'.
     * @return mixed The configuration value if found, or false if not found or if $path is null.
     */
    public static function get( $path = null ) {
        if ( !$path ) return false;

        $config = $GLOBALS['config'];
        $path = explode( '/', $path );

        foreach ( $path as $bit ) {
            if ( isset( $config[ $bit ] ) ) {
                $config = $config[ $bit ];
            } else {
                return false;
            }
        }

        return $config;
    }
}
