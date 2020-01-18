<?php
    include("includes/header.php");

    if(isset($_POST['cancel']))
    {
        header("Location: Settings.php");
    }

    if(isset($_POST['closeAccount']))
    {
        $closeQuery = mysqli_query($con, "UPDATE users SET user_closed = 'yes' 
            WHERE username = '$userLoggedIn'");

        session_destroy();
        header("Location: register.php");
    }

?>


<div class = "mainColumn column">
    <h4>Close Account</h4>

    Are you sure you want to close your account?<br><br>
    Closing your account will hide your profile and activity from other users.<br><br>
    You can re-open your account at any time by logging in.<br><br>

    <form action = "CloseAccount.php" method = "POST">
        <input type = "submit" name = "closeAccount" id = "closeAccount" value = "Close Account" class = "danger settingsSubmit">
        <input type = "submit" name = "cancel" id = "cancel" value = "Cancel" class = "info settingsSubmit">
    </form>
</div>