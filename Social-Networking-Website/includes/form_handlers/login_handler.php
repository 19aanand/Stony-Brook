<?php

    if(isset($_POST['login_button']))
    {
        $email = filter_var($_POST['login_email'], FILTER_SANITIZE_EMAIL); //Sanitizes email

        $_SESSION['login_email'] = $email; //Stores email into session variable
        $password = md5($_POST['login_password']); //Get password

        $checkDatabaseQuery = mysqli_query($con, "SELECT * FROM users WHERE email='$email' AND password ='$password'");
        $checkLoginQuery = mysqli_num_rows($checkDatabaseQuery);

        if($checkLoginQuery == 1)
        {
            $row = mysqli_fetch_array($checkDatabaseQuery);
            $username = $row['username'];

            //Opens the user's account if the account is closed
            $userClosedQuery = mysqli_query($con, "SELECT * FROM users WHERE email = '$email' AND user_closed = 'yes'");
            if(mysqli_num_rows($userClosedQuery) == 1)
            {
                //Sets user_closed for the user trying to login to account
                $reopenAccount = mysqli_query($con, "UPDATE users SET user_closed = 'no' WHERE email = '$email'");
            }


            $_SESSION['username'] = $username; //Creates new session variable that stores the user's username
            header("Location: index.php");
            exit();
        }

        else
        {
            array_push($error_array, "Email or password was incorrect.<br>");
        }
    }

?>