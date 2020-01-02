<?php
    //Variables will now be declared to avoid errors
    $fname = ""; //First name
    $lname = ""; //Last name 
    $em = ""; //E-mail
    $em2 = ""; //E-mail 2
    $password = ""; //Password 
    $password2 = ""; //Password 2
    $date = ""; //Sign-up date
    $error_array = array(); //Holds any error messages

    if(isset($_POST['register_button']))
    {
        //Registration form values

        //Converting the format of the first name
        $fname = strip_tags($_POST['reg_fname']); //Remove html tags
        $fname = str_replace(' ', '', $fname); //Remove spaces
        $fname = ucfirst(strtolower($fname)); //Converts fname to lowercase and then converts first character to uppercase
        $_SESSION['reg_fname'] = $fname; //Stores value of fname into the Session variable

        //Converting the format of the last name
        $lname = strip_tags($_POST['reg_lname']); //Remove html tags
        $lname = str_replace(' ', '', $lname); //Remove spaces
        $lname = ucfirst(strtolower($lname)); //Converts lname to lowercase and then converts first character to uppercase
        $_SESSION['reg_lname'] = $lname; //Stores value of lname into the Session variable

        //Converting the format of $email
        $em = strip_tags($_POST['reg_email']); //Remove html tags
        $em = str_replace(' ', '', $em); //Remove spaces
        $em = ucfirst(strtolower($em)); //Converts em to lowercase and then converts first character to uppercase
        $_SESSION['reg_email'] = $em; //Stores value of em into the Session variable

        //Converting the format of $email2
        $em2 = strip_tags($_POST['reg_email2']); //Remove html tags
        $em2 = str_replace(' ', '', $em2); //Remove spaces
        $em2 = ucfirst(strtolower($em2)); //Converts em to lowercase and then converts first character to uppercase
        $_SESSION['reg_email2'] = $em2; //Stores value of em2 into the Session variable

        //Converting the format of $password
        $password = strip_tags($_POST['reg_password']); //Remove html tags

        //Converting the format of $password2
        $password2 = strip_tags($_POST['reg_password2']); //Remove html tags

        //Gets and stores the current date
        $date = date("Y-m-d"); //Retrieves current date and stores value in $date

        if($em == $em2) //Verifies that the 2 entered emails match each other
        {
            if(filter_var($em, FILTER_VALIDATE_EMAIL)) //Verfies that the email entered is properly formatted
            {
                $em = filter_var($em, FILTER_VALIDATE_EMAIL);

                //Check if email already exists in Users table
                $e_check = mysqli_query($con, "SELECT email FROM users WHERE email = '$em'");

                $num_rows = mysqli_num_rows($e_check);

                if($num_rows > 0)
                {
                    array_push($error_array, "The entered email has already been used.<br>");
                }
            }

            else
            {
                array_push($error_array, "Invalid email format.<br>");
            }
        }

        else
        {
            array_push($error_array, "The emails do not match each other.<br>");
        }

        if(strlen($fname) > 25 || strlen($fname) < 2)
        {
            array_push($error_array, "Your first name must be between 2 and 25 characters.<br>");
        }

        if(strlen($lname) > 25 || strlen($lname) < 2)
        {  
            array_push($error_array, "Your last name must be between 2 and 25 characters.<br>");
        }

        $passwordLength = strlen($password);

        if($password != $password2)
        {
            array_push($error_array, "Your passwords do not match.<br>");
        }

        else if(preg_match('/[^A-Za-z0-9]/', $password))
        {
            array_push($error_array, "Your password can only contain English letters or numbers.<br>");
        }

        else if($passwordLength < 5 || $passwordLength > 30)
        {
            array_push($error_array, "Your password must be between 5 and 30 characters.<br>");
        }

        if(empty($error_array))
        {
            $password = md5($password); //Encrypts password before inserting it into database

            //Generate username by concatenating first name and last name
            $username = strtolower($fname . "_" . $lname);
            
            //Store all instances where the username is present in the users table
            $checkUsernameQuery = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");

            //Checks to see if the username is already being used by another user
            $i = 0;
            //If the username exists, then add number to the end of the username
            while(mysqli_num_rows($checkUsernameQuery) != 0)
            {
                $tempUsername = $username;
                $i+=1; //Increment i by 1
                $tempUsername = $username . "_" . $i; //Add the number to the end of the username
                $checkUsernameQuery = mysqli_query($con, "SELECT username FROM users WHERE username='$tempUsername'");
            }

            if($i>0)
            {
                $username = $tempUsername;
            }


            
            //Assigning a profile picture for the user
            $rand = rand(1, 2); //Generates random number between 1 and 2

            if($rand == 1)
            {
                $profilePicture = "assets/images/profile_pictures/defaults/head_emerald.png";
            }
            else if($rand == 2)
            {
                $profilePicture = "assets/images/profile_pictures/defaults/head_red.png";               
            }

            $query = mysqli_query($con, "INSERT INTO users VALUES ('', '$fname', '$lname', '$username', '$em', '$password',
                '$date', '$profilePicture', '0', '0', 'no', ',')");

            //Confirmation message that the user's account has been created
            array_push($error_array, "<span style = 'color: #14C800'>You're all set! Go ahead and login!</span><br>");

            //Clear session variables
            $_SESSION['reg_fname'] = "";
            $_SESSION['reg_lname'] = "";
            $_SESSION['reg_email'] = "";
            $_SESSION['reg_email2'] = "";
        }
    }
?>