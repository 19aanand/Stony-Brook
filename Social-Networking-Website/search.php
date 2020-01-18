<?php

    include("includes/header.php");

    if(isset($_GET['q']))
    {
        $query = $_GET['q'];
    }

    else
    {
        $query = "";
    }


    if(isset($_GET['type']))
    {
        $type = $_GET['type'];
    }

    else
    {
        $type = "name";
    }

?>


<div class = "mainColumn column" id = "mainColumn">
    <?php

        if($query == "")
        {
            echo "You must enter something else in the search box.";
        }

        else
        {

            //If query contains underscore, then assume the user is searching for a username
            if($type == "username")
            {
                $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%'
                    AND user_closed = 'no'");
            }

            else
            {
                $names = explode(" ", $query);

                //If there are two words, assume they are first and last names respectively
                if(count($names) == 3)
                {
                    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE 
                        '$names[0]%' AND last_name LIKE '$names[2]%') AND user_closed = 'no'");
                }

                else if(count($names) == 2)
                {
                    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE 
                        '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed = 'no'");
                }

                //If query has only one word, then search first names and last names
                else
                {
                    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE 
                        '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed = 'no' LIMIT 8");
                }

            }


            //Check if the results were found
            if(mysqli_num_rows($usersReturnedQuery) == 0)
            {
                echo "We cannot find anyone with a " . $type . " like: " . $query;
            }

            else
            {
                echo mysqli_num_rows($usersReturnedQuery) . " results found: <br> <br>";
            }

            echo "<p id = 'grey'>Try searching for: </p>";
            echo "<a href = 'search.php?q=' " . $query . "&type=name'>Names</a>, <a href = 'search.php?q=' " . $query . "&type=username'>Usernames</a><br><br><hr id = 'searchHr'>";

            while($row = mysqli_fetch_array($usersReturnedQuery))
            {
                $userObject = new User($con, $user['username']);

                $button = "";
                $mutualFriends = "";

                if($user['username'] != $row['username'])
                {
                    //Generate a button depending on friendship status
                    if($userObject->isFriend($row['username']))
                    {
                        $button = "<input type = 'submit' name = '" . $row['username'] . "' class = 'danger' value = 'Remove Friend'>";
                    }

                    else if($userObject->didReceiveRequest($row['username']))
                    {
                        $button = "<input type = 'submit' name = '" . $row['username'] . "' class = 'warning' value = 'Respond to Request'>";
                    }

                    else if($userObject->didSendRequest($row['username']))
                    {
                        $button = "<input type = 'submit' name = '" . $row['username'] . "' class = 'default' value = 'Cancel Request'>";
                    }

                    else
                    {
                        $button = "<input type = 'submit' name = '" . $row['username'] . "' class = 'success' value = 'Add Friend'>";
                    }

                    $mutualFriends = $userObject->getMutualFriends($row['username']) . " friends in common";

                    //Button forms
                    if(isset($_POST[$row['username']]))
                    {
                        if($userObject->isFriend($row['username']))
                        {
                            $userObject->removeFriend($row['username']);
                            header("Location: search.php?q=" . $query);
                        }

                        else if($userObject->didReceiveRequest($row['username']))
                        {
                            header("Location: Requests.php");
                        }

                        else if($userObject->didSendRequest($row['username']))
                        {
                            //Delete friend request
                            $username = $row['username'];
                            $query2 = mysqli_query($con, "DELETE FROM friend_requests WHERE
                                user_to = '$username' AND user_from = '$userLoggedIn'");
                            header("Location: search.php?q=" . $query);
                        }

                        else
                        {
                            $userObject->sendRequest($row['username']);
                            header("Location: search.php?q=" . $query);
                        }

                    }
                }

                echo "<div class = 'searchPage'>
                        <div class = 'searchPageFriendButtons'>
                            <form action = '' method = 'POST'>
                                " . $button . "
                                <br>
                            </form>
                        </div>

                        <div class = 'resultProfilePic'>
                            <a href = '" . $row['username'] . "'><img src = '" . $row['profile_pic'] . "' style = 'height: 100px;'></a>
                        </div>

                        <a href = '" . $row['username'] . "'>" . $row['first_name']. " " . $row['last_name'] . "</a>
                            <p id = 'grey'>" . $row['username'] . "</p>
                        </a>
                        <br>

                        " . $mutualFriends . "<br>

                    </div>
                    <hr id = 'searchHr'>";
            } //End of while loop
        }


    ?>
</div>