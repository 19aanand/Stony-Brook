<?php
    include("includes/header.php");

    $messageObject = new Message($con, $userLoggedIn);
    
    if(isset($_GET['u']))
    {
        $userTo = $_GET['u'];
    }

    else
    {
        $userTo = $messageObject->getMostRecentUser();

        if($userTo == false) //This if statement is true if the user logged in has not started a conversation with anyone
        {
            $userTo = 'new';
        }
    }
?>

