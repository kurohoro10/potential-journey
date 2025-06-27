<?php
class Config {
    /**
     * Get a configuration value by its path.
     *
     * @param string|null $path The path to the configuration value.
     * @return mixed The configuration value or false if not found.
     */
    public static function get( $path = null ) {
        if ( ! $path ) return false;

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