<?php
    include("includes/header.php");
    include("includes/form_handlers/SettingsHandler.php");
?>

<div class = "mainColumn column">
    <h4>Account Settings</h4>

    <?php
        echo "<img src = '" . $user['profile_pic'] . "' id = 'smallProfilePic'>";
    ?>

    <br>
    <a href = "Upload.php">Upload new profile picture</a>
    <br><br><br>

    Modify the values and click 'Update Details'<br><br>

    <?php
        $userDataQuery = mysqli_query($con, "SELECT first_name, last_name, email FROM users
            WHERE username = '$userLoggedIn'");
        $row = mysqli_fetch_array($userDataQuery);

        $firstName = $row['first_name'];
        $lastName = $row['last_name'];
        $email = $row['email']
    ?>

    <form action = "Settings.php" method = "POST">
        First Name: <input type = "text" name = "firstName" id = "settingsInput" value = <?php echo $firstName; ?>>
        <br>
        Last Name: <input type = "text" name = "lastName" id = "settingsInput" value = <?php echo $lastName ?>>
        <br>
        Email: <input type = "text" name = "email" id = "settingsInput" value = <?php echo $email; ?>>
        <br>
        <?php
            echo $message;
        ?>
        <input type = "submit" name = "updateDetails" id = "saveDetails" value = "Update Details" class = "info settingsSubmit">
        <br>
    </form>

    <h4>Change password</h4>
    <form action = "Settings.php" method = "POST">
        Old Password: <input type = "password" name = "oldPassword" id = "settingsInput" placeholder = "Enter old password">
        <br>
        New Password: <input type = "password" name = "newPassword" id = "settingsInput" placeholder = "Enter new password">
        <br>
        Confirm New Password: <input type = "password" name = "confirmNewPassword" id = "settingsInput" placeholder = "Confirm new password">
        <br>
        <?php
            echo $passwordMessage;
        ?>
        <input type = "submit" name = "changePassword" id = "savePassword" value = "Change Password" class = "info settingsSubmit">
        <br>
    </form>

    <h4>Close Account</h4>
        <form action = "Settings.php" method = "POST">
            <input type = "submit" name = "closeAccount" id = "closeAccount" value = "Close Account" class = "danger settingsSubmit">
        </form>
</div>