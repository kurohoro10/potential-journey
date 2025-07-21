<?php
require_once 'core/init.php';

if (Session::exists('home')) {
    echo Session::flash('home');
}

$user = new User();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
        <link rel="stylesheet" href="assets/css/style.css">
        <script defer src="assets/js/script.js"></script>
        <title>User Dashboard</title>
    </head>
    <body>
        <?php if ($user->isLoggedIn()) : ?>
            <p>Hello <a href="profile.php?user=<?php echo htmlspecialchars($user->data()->username); ?>">
                <?php echo htmlspecialchars($user->data()->username); ?>
            </a>!</p>

            <ul>
                <li><a href="logout.php">Log out</a></li>
                <li><a href="update.php">Update details</a></li>
                <li><a href="changepassword.php">Change password</a></li>
            </ul>

            <?php if ($user->hasPermission('admin')) : ?>
                <p>You are an admin!</p>
            <?php endif; ?>

            <?php if ($user->hasPermission('moderator')) : ?>
                <p>You are a moderator!</p>
            <?php endif; ?>

        <?php else : ?>
            <?php require_once('includes/cookie/cookiebanner.php'); ?>
            <p>You need to 
                <a href="login.php">log in</a> 
                or 
                <a href="register.php">register</a>.
            </p>
        <?php endif; ?>
    </body>
</html>
