<?php
    ob_start(); //Turns on output buffering

    session_start(); //Begins a session that allows the webpage to store the valid values the user enters in the text boxes
    //in the event that one of those values is of the incorrect format

    $timezone = date_default_timezone_set("America/New_York");

    $con = mysqli_connect("localhost", "root", "", "social");

    if(mysqli_connect_errno())
    {
        echo "Failed to connect: " . mysqli_connect_errno();
    }
?>