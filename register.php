<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/form.css">
    <link rel="icon" href="http://example.com/favicon.png">
    <title>Zarejestruj się</title>
</head>

<?php

    include_once 'classes/UserManager.php';
    include_once 'classes/Database.php';

    $um = new UserManager();
    $db = new Database();

    $user_error = '';

    if (filter_input(INPUT_POST, "register_user")) {
        $user_error = $um->addUserToDB($db);
    }

    //get current user_id and redirect to dashboard
    session_start();
    $session_id = session_id();
    $user_id = $um->getLoggedInUser($db, $session_id);
    session_destroy();

    if($user_id >= 0) {
        header("location:index.php");
    }

?>

<body>
    <div class="wrapper">
        <div class="logo-block">
            <h1 class="logo">scool</h1>
        </div>
        <div class="error-block">
            <p>
            <?php
                if($user_error == "error-data") {
                    echo "Niepoprawne dane!";
                } else if ($user_error == 'error-user-exist') {
                    echo "Użytkownik istnieje";
                }
            ?>
            </p>
        </div>
        <div class="login-form">
            <form action="register.php" method="post">
                <div class="input-block">
                    <div class="input-element">
                        <img src="./icons/user.svg" alt="login" />
                        <input placeholder="login" name="username" type="text" />
                    </div>
                    <div class="input-element">
                        <img src="./icons/message.svg" alt="email" />
                        <input type="email" placeholder="email" name="email" />
                    </div>
                    <div class="input-element">
                        <img src="./icons/key.svg" alt="password" />
                        <input type="password" placeholder="hasło" name="password" minlength="6"/>
                    </div>
                </div>
                <button type="submit" class="btn" value="zarejestruj" name="register_user">zarejestruj</button>
            </form>
        </div>
        <div class="links-block">
            <a href="login.php">Masz konto? <span>Zaloguj się</span></a>
        </div>
    </div>
</body>
</html>