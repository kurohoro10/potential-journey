<?php
    require_once 'core/init.php';
    $token = Input::get('token');
    $username = Input::get('username');
    $password = Input::get('password');
    $remember = (Input::get('remember') === 'on') ? true : false;

    if (Input::exists()) {
        if (Token::check($token)) {
            $validate = new Validate();
            $validation = $validate->check($_POST, array(
                'username' => array('required' => true),
                'password' => array('required' => true)
            ));

            if ($validation->passed()) {
                $user = new User();
                $login = $user->login($username, $password, $remember);

                if ($login) {
                    Redirect::to('index.php');
                } else {
                    echo 'Sorry, loggin in failed';
                }
            } else {
                foreach ($validation->errors() as $error) {
                    echo htmlspecialchars($error), '<br/>';
                }
            }
        }
    }

?>

<form action="" method="post">
    <div class="field">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" autocomplete="off" />
    </div>

    <div class="field">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" autocomplete="off" />
    </div>

    <div class="field">
        <label for="remember">
            <input type="checkbox" name="remember" id="remember" />Remember me
        </label>
    </div>

    <input type="hidden" name="token" value="<?php echo htmlspecialchars(Token::generate()); ?>" />
    <input type="submit" value="Log in">
</form>