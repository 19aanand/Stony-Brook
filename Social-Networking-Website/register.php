<?php  
    $con = mysqli_connect("localhost", "root", "", "social");

    if(mysqli_connect_errno())
    {
        echo "Failed to connect: " . mysqli_connect_errno();
    }

    $query = mysqli_query($con, "INSERT INTO test VALUES(NULL, 'VishySwishy')");

    //Variables will now be declared to avoid errors
    $fname = ""; //First name
    $lname = ""; //Last name 
    $em = ""; //E-mail
    $em2 = ""; //E-mail 2
    $password = ""; //Password 
    $password2 = ""; //Password 2
    $date = ""; //Sign-up date
    $error_array = ""; //Holds any error messages

    if(isset($_POST['register_button']))
    {
        //Registration form values

        //Converting the format of the first name
        $fname = strip_tags($_POST['reg_fname']); //Remove html tags
        $fname = str_replace(' ', '', $fname); //Remove spaces
        $fname = ucfirst(strtolower($fname)); //Converts fname to lowercase and then converts first character to uppercase

        //Converting the format of the last name
        $lanme = strip_tags($_POST['reg_lname']); //Remove html tags
        $lname = str_replace(' ', '', $lname); //Remove spaces
        $lname = ucfirst(strtolower($lname)); //Converts lname to lowercase and then converts first character to uppercase
        
        //Converting the format of $email
        $em = strip_tags($_POST['reg_email']); //Remove html tags
        $em = str_replace(' ', '', $em); //Remove spaces
        $em = ucfirst(strtolower($em)); //Converts lname to lowercase and then converts first character to uppercase

        //Converting the format of $email2
        $em2 = strip_tags($_POST['reg_email2']); //Remove html tags
        $em2 = str_replace(' ', '', $em2); //Remove spaces
        $em2 = ucfirst(strtolower($em2)); //Converts lname to lowercase and then converts first character to uppercase

        //Converting the format of $password
        $password = strip_tags($_POST['reg_password']); //Remove html tags

        //Converting the format of $password2
        $password2 = strip_tags($_POST['reg_password2']); //Remove html tags

        //Gets and stores the current date
        $date = date("Y-m-d"); //Retrieves current date and stores value in $date

    }

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
    
    <form action="register.php" method="POST">
        <input type="text" name="reg_fname" placeholder="First Name" required>
        <br>
        <input type="text" name="reg_lname" placeholder="Last Name" required>
        <br>
        <input type="email" name="reg_email" placeholder="Email" required>
        <br>
        <input type="email" name="reg_email2" placeholder="Confirm Email" required>
        <br>
        <input type="password" name="reg_password" placeholder="Password" required>
        <br>
        <input type="password" name="reg_password2" placeholder="Confirm Password" required>
        <br>
        <input type="submit" name="register_button" value="Register">

    </form>

</body>
</html>