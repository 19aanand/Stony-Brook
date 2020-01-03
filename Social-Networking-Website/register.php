<?php  
    require 'config/config.php';
    require 'includes/form_handlers/register_handler.php';
    require 'includes/form_handlers/login_handler.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AA Networking: Login</title>
    <link rel = "stylesheet" type = "text/css" href = "assets/css/register_style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src = "assets/js/register.js"></script>
</head>
<body>
    <?php
        if(isset($_POST['register_button']))
        {
            echo '
                <script>
                    $(document).ready(function()
                    {
                        $("#first").hide();
                        $("#second").show();
                    });
                </script>
            
            ';
        }
    ?>
    
    <div class = "wrapper">

        <div class = "login_box">
            
            <div class = "login_header">
                <h1>Welcome to AA Networking!</h1>
                Login or sign up below
            </div>
            <!-- Login area of register page/!-->
            <div id = "first">
                <form action = "register.php" method = "POST">

                    <input type = "email" name = "login_email" placeholder = "Email Address"
                    value = 
                    "<?php
                        if(isset($_SESSION['login_email']))
                        {
                            echo $_SESSION['login_email'];
                        }
                    ?>"
                    required>
                    <br>


                    <input type = "password" name = "login_password" placeholder = "Password" required>
                    <br>

                    <!--Login button/!-->
                    <input type="submit" name="login_button" value="Login">
                    <br>
                    <?php
                        if(in_array("Email or password was incorrect.<br>", $error_array))
                        {
                            echo "Email or password was incorrect.<br>";   
                        }
                    ?>
                    <br>
                    <a href = "#" id = "signup" class = "signup">Need an account? Reigster here!</a>    
                </form>
            </div>

            <!-- Sign-up area of page/!-->
            <div id = "second">
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
                    <br>
                
                    <a href = "#" id = "login" class = "login">Already have an account? Login here!</a>


                </form>
            </div>
        <div>

    </div>
</body>
</html>