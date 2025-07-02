<?php
class Input {
    /**
     * Get a value from the $_POST or $_GET superglobals.
     *
     * @param string $key The key to retrieve.
     * @param string $type The type of input ('post' or 'get').
     * @return mixed The value if found, null otherwise.
     */
    public static function exists( $type = 'post' ) {
        switch ( $type ) {
            case 'post':
                return ( ! empty( $_POST ) ) ? true : false;
            break;
            case 'get':
                return ( ! empty( $_GET ) ) ? true : false;
            break;
            default:
                return false;
            break;
        }
    }

    /**
     * Get the value of a specific item from POST or GET data.
     *
     * @param string $item The name of the item to retrieve.
     * @return mixed The value of the item if it exists, false otherwise.
     */
    public static function get( $item ) {
        if ( isset( $_POST[$item] ) ) {
            return $_POST[$item];
        } else if ( isset( $_GET[$item] ) ) {
            return $_GET[$item];
        }
        return '';
    }
}