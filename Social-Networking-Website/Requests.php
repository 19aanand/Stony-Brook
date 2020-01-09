<?php
    include("includes/heaader.php");
    
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

                }

                if(isset($_POST['ignore_request' . $userFrom]))
                {
                    
                }
            }
        }
    ?>
</div>