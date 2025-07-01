<?php
require_once 'core/init.php';

$user = DB::getInstance()->update( 'users', 3, array(
    'username' => 'asdasdsadsa',
    'password' => 'adsasdsadsa',
    'salt'     => 'asdsadsad',
    'name'     => 'asdasdsa',
    'joined'   => date('Y-m-d H:i:s'),
    'grouped'  => 1
));
