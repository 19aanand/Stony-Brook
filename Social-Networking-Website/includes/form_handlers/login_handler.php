<?php

    if(isset($_POST['login_button']))
    {
        $email = filter_var($_POST['login_email'], FILTER_SANITIZE_EMAIL); //Sanitizes email

        $_SESSION['login_email'] = $email; //Stores email into session variable
        $password = md5($_POST['log_password']); //Get password

        $checkDatabaseQuery = mysqli_query($con, "SELECT * FROM users WHERE email='$email' AND password ='$password'");
        $checkLoginQuery = mysqli_num_rows($checkDatabaseQuery);

        if($checkLoginQuery == 1)
        {
            $row = mysqli_fetch_array($checkDatabaseQuery);
            $username = $row['username'];

            $_SESSION['username'] = $username; //Creates new session variable that stores the user's username
            header("Location: index.php");
            exit();
        }
    }

?>