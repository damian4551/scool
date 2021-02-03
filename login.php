<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/form.css">
    <link rel="icon" href="http://example.com/favicon.png">
    <title>Zaloguj się</title>
</head>

<?php

    include_once 'classes/UserManager.php';
    include_once 'classes/Database.php';

    $um = new UserManager();
    $db = new Database();

    $user_error = '';


    if (filter_input(INPUT_POST, "login_user")) {

        $user_id = $um->loginUser($db);
        /*
        if ($user_id == -1) {
            $user_error = 'Niepoprawne dane!';
        } else {
            header("location:index.php");
        }
        */
    }

    //get current user_id and redirect to dashboard
    session_start();
    $session_id = session_id();
    $logged_user_id = $um->getLoggedInUser($db, $session_id);
    session_destroy();

    if($logged_user_id >= 0) {
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
                if($user_error) {
                    echo $user_error;
                }
            ?>
            </p>
        </div>
        <div class="login-form">
            <form action="login.php" method="post">
                <div class="input-block">
                    <div class="input-element">
                        <img src="./icons/user.svg" alt="username" />
                        <input placeholder="login/email" name="username" />
                    </div>
                    <div class="input-element">
                        <img src="./icons/key.svg" alt="password" />
                        <input type="password" placeholder="hasło" name="password" />
                    </div>
                </div>
                <input type="submit" class="btn" value="zaloguj" name="login_user" />
            </form>
        </div>
        <div class="links-block">
            <a href="register.php">Nie masz konta? <span>Zarejestruj się</span></a>
            <!-- <a href="#">Zapomniałeś hasło? <span>Zresetuj</span></a> -->
        </div>
    </div>
    
</body>
</html>