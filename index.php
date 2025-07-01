<?php
require_once 'core/init.php';

$user = DB::getInstance()->insert( 'users', array(
    'username' => 'Dale',
    'password' => 'password',
    'salt'     => 'salt',
    'name'     => 'Dale Garett',
    'joined'   => date('Y-m-d H:i:s'),
    'grouped'  => 1
));
