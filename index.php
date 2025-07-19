<?php
require_once 'core/init.php';

if ( Session::exists('home')) {
    echo Session::flash('home');
}

$user = new User();


if ($user->isLoggedIn()) {
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <script defer src="/assets/js/script.js"></script>
    <title>Document</title>
</head>
<body>
    <p>Hello <a href="profile.php?user=<?php echo htmlspecialchars($user->data()->username); ?>"><?php echo htmlspecialchars($user->data()->username); ?></a>!</p>

    <ul>
        <li><a href="logout.php">Log out</a></li>
        <li><a href="update.php">Update details</a></li>
        <li><a href="changepassword.php">Change password</a></li>
    </ul>

    <?php

        if ($user->hasPermission('admin')) {
            echo '<p>You are an admin!</p>';
        }

        if ($user->hasPermission('moderator')) {
            echo '<p>You are an moderator!</p>';
        }

        } else {
            require_once('includes/cookie/cookiebanner.php');
            ?>
                <p>You need to 
                    <a href="login.php">log in</a> 
                    or 
                    <a href="register.php">register</a>
                </p>
            <?php
        }
    ?>
</body>
</html>