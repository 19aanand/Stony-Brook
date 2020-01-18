<?php
    if(isset($_POST['updateDetails']))
    {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];

        $emailCheckQuery = mysqli_query($con, "SELECT * FROM users WHERE email = '$email'");
        
        $row = mysqli_fetch_array($emailCheckQuery);
        $matchedUser = $row['username'];

        if($matchedUser == "" || $matchedUser == $userLoggedIn)
        {
            $message = "Details updated!<br><br>";

            $query = mysqli_query($con, "UPDATE users SET first_name = '$firstName', 
                last_name = '$lastName', email = '$email' WHERE username = '$userLoggedIn'");
        }

        else
        {
            $message = "The email you entered is already being used.<br><br>";
        }
    }

    else
    {
        $message = "";
    }


    if(isset($_POST['changePassword']))
    {
        $oldPassword = strip_tags($_POST['oldPassword']);
        $newPassword = strip_tags($_POST['newPassword']);
        $confirmNewPassword = strip_tags($_POST['confirmNewPassword']);

        $passwordQuery = mysqli_query($con, "SELECT password FROM users WHERE username = '$userLoggedIn'");
        $row = mysqli_fetch_array($passwordQuery);
        $databasePassword = $row['password'];

        if(md5($oldPassword) == $databasePassword)
        {
            if($newPassword == $confirmNewPassword)
            {
                if(strlen($newPassword) <= 4)
                {
                    $passwordMessage = "Your new password must have a length greater than 4 characters.<br><br>";
                }

                else
                {
                    $newPasswordMd5 = md5($newPassword);

                    $updatePasswordQuery = mysqli_query($con, "UPDATE users SET password = '$newPasswordMd5'
                        WHERE username = '$userLoggedIn'");
                    $passwordMessage = "Password has been changed.<br><br>";
                }
            }

            else
            {
                $passwordMessage = "Your two new passwords need to match.<br><br>";
            }
        }

        else
        {
            $passwordMessage = "Old password is incorrect.<br><br>";
        }
    }

    else
    {
        $passwordMessage = "";
    }


    if(isset($_POST['closeAccount']))
    {
        header("Location: CloseAccount.php");
    }

    else
    {

    }

?>