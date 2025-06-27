<?php
/**
 * Escape a string for safe output in HTML.
 *
 * @param string $string The string to escape.
 * @return string The escaped string.
 */
function escape( $string ) {
    return htmlentities( $string, ENT_QOUTES, 'UTF-8' );
}