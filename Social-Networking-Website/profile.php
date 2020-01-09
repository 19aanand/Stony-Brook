<?php
    include("includes/header.php");

    if(isset($_GET['profile_username'])) //Gets username in url
    {
        $username = $_GET['profile_username'];
        $userDetailsQuery = mysqli_query($con, "SELECT * FROM users WHERE username = '$username'");
        $userArray = mysqli_fetch_array($userDetailsQuery);
        $numOfFriends = (substr_count($userArray['friend_array'], ","))-1;
    }

    if(isset($_POST['removeFriend']))
    {
        $user = new User($con, $userLoggedIn);
        $user->removeFriend($username);
    }

    if(isset($_POST['addFriend']))
    {
        $user = new User($con, $userLoggedIn);
        $user->sendRequest($username);
    }

    if(isset($_POST['respondRequest']))
    {
        header("Location: Requests.php");
    }
?>
        <style type = "text/css">
            .wrapper
            {
                margin-left: 0px;
                padding-left: 0px;
            }
        </style>

        <div class = "profileLeft">
            <br>
            <br>
            <img src = "<?php echo $userArray['profile_pic']; ?>"> <!-- Acquires path of user's profile picture and displays it-->

            <div class = "profileInfo">
                <p>
                    <?php
                        echo "Posts: " . $userArray['num_posts'] . "<br><br>";
                        echo "Likes: " . $userArray['num_likes'] . "<br><br>";
                        echo "Friends: " . $numOfFriends . "<br><br>";

                    ?>
                </p>
            </div>

            <form action = "<?php echo $username; ?>" method = "POST">
                <?php
                    $profileUserObject = new User($con, $username);

                    if($profileUserObject->isClosed())
                    {
                        header("Location: UserClosed.php");
                    }

                    $loggedInUserObject = new User($con, $userLoggedIn);

                    if($userLoggedIn != $username)
                    {
                        if($loggedInUserObject->isFriend($username))
                        {
                            echo '<input type = "submit" name = "removeFriend" class = "danger" value = "Remove Friend"><br>';
                        }

                        else if($loggedInUserObject->didReceiveRequest($username))
                        {
                            echo '<input type = "submit" name = "respondRequest" class = "warning" value = "Respond to Request"><br>';
                        }

                        else if($loggedInUserObject->didSendRequest($username))
                        {
                            echo '<input type = "submit" name = "" class = "default" value = "Request Sent"><br>';
                        }

                        else
                        {
                            echo '<input type = "submit" name = "addFriend" class = "success" value = "Add Friend"><br>';
                        }
                    }
                ?>
            </form>
        </div>

        <br>
        <br>
        <div class = "mainColumn column">

            <?php
                echo $username;
            ?>
        </div>

    </div>
</body>

</html>