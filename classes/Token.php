<?php
/**
 * Class Token
 *
 * This class handles the generation and validation of CSRF (Cross-Site Request Forgery) tokens.
 * It ensures that forms or requests come from authenticated users by storing a unique token in the session
 * and checking it upon submission.
 *
 * Methods:
 * - generate(): Creates a unique token, stores it in the session, and returns it.
 * - check($token): Verifies that the provided token matches the one stored in the session.
 *                  If matched, it deletes the token (to prevent reuse) and returns true.
 *                  Otherwise, it returns false.
 */
class Token {
    /**
     * Generate a CSRF token and store it in the session.
     *
     * @return string The generated token.
     */
    public static function generate() {
        return Session::put( Config::get( 'session/token_name' ), md5( uniqid() ) );
    }

    /**
     * Check the provided token against the one stored in the session.
     *
     * @param string $token The token to validate.
     * @return bool True if the token is valid, false otherwise.
     */
    public static function check( $token ) {
        $tokenName = Config::get( 'session/token_name' );

        if ( Session::exists( $tokenName ) && $token === Session::get( $tokenName ) ) {
            Session::delete( $tokenName );
            return true;
        }
        return false;
    }
}
