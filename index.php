<?php
require_once 'core/init.php';

$user = DB::getInstance()->query( "SELECT username FROM users WHERE username = ?", array( 'billy' ) );

if ( ! $user->count() ) {
    echo 'No user';
} else {
    echo 'OK!';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>test</h1>
</body>
</html>