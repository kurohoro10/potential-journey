<?php
require_once 'core/init.php';

$user = DB::getInstance()->get( "users", array( 'username', '=', 'alex' ) );

if ( ! $user->count() ) {
    echo 'No user';
} else {
    foreach ( $user->results() as $user ) {
        echo $user->username . '<br>';
    }
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