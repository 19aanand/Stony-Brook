<?php
    include("includes/header.php");
    
?>

<div class = "mainColumn column" id = "mainColumn">
    <h4>Friend Requests</h4>

    <?php
        $query = mysqli_query($con, "SELECT * FROM friend_requests WHERE user_to = '$userLoggedIn'");
        
        if(mysqli_num_rows($query) == 0)
        {
            echo "You have no friend requests at this time!";
        }

        else
        {
            while($row = mysqli_fetch_array($query))
            {
                $userFrom = $row['user_from'];
                $userFromObject = new User($con, $userFrom);

                echo $userFromObject->getFirstAndLastName() . " sent you a friend request!";

                $userFromFriendArray = $userFromObject->getFriendArray();

                if(isset($_POST['accept_request' . $userFrom]))
                {
                    $addFriendQuery = mysqli_query($con, "UPDATE users SET friend_array = CONCAT(friend_array, '$userFrom,')
                        WHERE username = '$userLoggedIn'");

                    $addFriendQuery = mysqli_query($con, "UPDATE users SET friend_array = CONCAT(friend_array, '$userLoggedIn,')
                        WHERE username = '$userFrom'");

                    $deleteQuery = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to = '$userLoggedIn' AND 
                        user_from = '$userFrom'");

                    echo " You are now friends with " . $userFrom . "!";
                    header("Locations: Requests.php");
                }

                if(isset($_POST['ignore_request' . $userFrom]))
                {
                    $deleteQuery = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to = '$userLoggedIn' AND 
                        user_from = '$userFrom'");
                    echo " Request deleted.";
                    header("Location: Requests.php");
                }

                ?>
                <form action = "Requests.php" method = "POST">
                    <input type = "submit" name = "accept_request<?php echo $userFrom; ?>" id = "accept_button"
                        value = "Accept Request">

                    <input type = "submit" name = "ignore_request<?php echo $userFrom; ?>" id = "ignore_button" 
                        value = "Ignore Request">
                </form>

                <?php
            }
        }
    ?>
</div>