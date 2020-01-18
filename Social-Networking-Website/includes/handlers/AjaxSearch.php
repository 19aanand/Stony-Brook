<?php
    include("../../config/config.php");
    include("../../includes/classes/User.php");

    $query = $_POST['query'];
    $userLoggedIn = $_POST['userLoggedIn'];

    $names = explode(" ", $query);

    //If query contains underscore, then assume the user is searching for a username
    if(strpos($query, '_') !== false)
    {
        $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%'
            AND user_closed = 'no' LIMIT 8");
    }

    //If there are two words, assume they are first and last names respectively
    else if(count($names) == 2)
    {
        $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE 
            '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed = 'no' LIMIT 8");
    }

    //If query has only one word, then search first names and last names
    else
    {
        $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE 
            '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed = 'no' LIMIT 8");
    }


    if($query != "")
    {
        while($row = mysqli_fetch_array($usersReturnedQuery))
        {
            $user = new User($con, $userLoggedIn);

            if($row['username'] != $userLoggedIn)
            {
                $mutualFriends = $user->getMutualFriends($row['username']) . " friends in common";
            }

            else
            {
                $mutualFriends = "";
            }

            echo "<div class = 'resultDisplay'>
                    <a href = '" . $row['username'] . "' style = 'color: #1485BD'>
                        <div class = 'liveSearchProfilePic'>
                            <img src = '" . $row['profile_pic'] . "'>
                        </div>

                        <div class = 'liveSearchText'>
                            " . $row['first_name'] . " " . $row['last_name'] . "
                            <p>" . $row['username'] . "</p>
                            <p id = 'grey'>" . $mutualFriends . "</p>
                        </div>
                    </a>
                </div>";
        }
    }
?>