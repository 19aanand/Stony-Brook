<?php  
    require 'config/config.php';
    require 'includes/form_handlers/register_handler.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome to the AA Website</title>
</head>
<body>
    
    <!-- Login area of register page/!-->
    <form action = "register.php" method = "POST">
        <input type = "email" name = "login_email" placeholder = "Email Address">
        <br>
        <input type = "password" name = "login_password" placeholder = "Password">
        <br>
        <input type="submit" name="submit_button" value="Login">

    </form>


    <form action="register.php" method="POST">
        <input type="text" name="reg_fname" placeholder="First Name" 
        value=
        "<?php
            if(isset($_SESSION['reg_fname']))
            {
                echo $_SESSION['reg_fname'];
            }
        ?>" 
        required>
        <br>

        <?php
            if(in_array("Your first name must be between 2 and 25 characters.<br>", $error_array))
            {
                echo "Your first name must be between 2 and 25 characters.<br>";
            }
        ?>


        <input type="text" name="reg_lname" placeholder="Last Name" 
        value=
        "<?php
            if(isset($_SESSION['reg_lname']))
            {
                echo $_SESSION['reg_lname'];
            }
        ?>" 
        required>
        <br>

        <?php
            if(in_array("Your last name must be between 2 and 25 characters.<br>", $error_array))
            {
                echo "Your last name must be between 2 and 25 characters.<br>";
            }
        ?>


        <input type="email" name="reg_email" placeholder="Email" 
        value=
        "<?php
            if(isset($_SESSION['reg_email']))
            {
                echo $_SESSION['reg_email'];
            }
        ?>" 
        required>
        <br>


        <input type="email" name="reg_email2" placeholder="Confirm Email" 
        value=
        "<?php
            if(isset($_SESSION['reg_email2']))
            {
                echo $_SESSION['reg_email2'];
            }
        ?>" 
        required>
        <br>

        <?php
            if(in_array("The entered email has already been used.<br>", $error_array))
            {
                echo "The entered email has already been used.<br>";
            }

            else if(in_array("Invalid email format.<br>", $error_array))
            {
                echo "Invalid email format.<br>";
            }

            else if(in_array("The emails do not match each other.<br>", $error_array))
            {
                echo "The emails do not match each other.<br>";
            }
        ?>


        <input type="password" name="reg_password" placeholder="Password" required>
        <br>


        <input type="password" name="reg_password2" placeholder="Confirm Password" required>
        <br>

        <?php
            if(in_array("Your passwords do not match.<br>", $error_array))
            {
                echo "Your passwords do not match.<br>";
            }

            else if(in_array("Your password can only contain English letters or numbers.<br>", $error_array))
            {
                echo "Your password can only contain English letters or numbers.<br>";
            }

            else if(in_array("Your password must be between 5 and 30 characters.<br>", $error_array))
            {
                echo "Your password must be between 5 and 30 characters.<br>";
            }
        ?>


        <input type="submit" name="register_button" value="Register">
        <br>

        <?php
            if(in_array("<span style = 'color: #14C800'>You're all set! Go ahead and login!</span><br>", $error_array))
            {
                echo "<span style = 'color: #14C800'>You're all set! Go ahead and login!</span><br>";
            }
        ?>

    </form>

</body>
</html>