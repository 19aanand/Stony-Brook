<?php
    class User
    {
        private $user;
        private $con;

        public function __construct($con, $user)
        {
            $this->con = $con;
            $userDetailsQuery = mysqli_query($con, "SELECT * FROM users WHERE username = '$user'");
            $this->user = mysqli_fetch_array($userDetailsQuery);
        }

        public function getUsername()
        {
            return $this->user['username'];
        }

        public function getFirstAndLastName()
        {
            $username = $this->user['username'];
            $query = mysqli_query($this->con, "SELECT first_name, last_name FROM users WHERE username = '$username'");
            $row = mysqli_fetch_array($query);
            return $row['first_name'] . " " . $row['last_name'];
        }

        public function getNumPosts()
        {
            $username = $this->user['username'];
            $query = mysqli_query($this->con, "SELECT num_posts FROM users WHERE username = '$username'");
            $row = mysqli_fetch_array($query);
            return $row['num_posts'];
        }

        public function getProfilePicture()
        {
            $username = $this->user['username'];
            $query = mysqli_query($this->con, "SELECT profile_pic FROM users WHERE username = '$username'");
            $row = mysqli_fetch_array($query);
            return $row['profile_pic'];
        }

        public function isClosed()
        {
            $username = $this->user['username'];
            $query = mysqli_query($this->con, "SELECT user_closed FROM users WHERE username = '$username'");
            $row = mysqli_fetch_array($query);

            if($row['user_closed'] == 'yes')
            {
                return true;
            }

            else
            {
                return false;
            }
        }

        public function isFriend($usernameToCheck)
        {
            $usernameComma = "," . $usernameToCheck . ",";

            if(strstr($this->user['friend_array'], $usernameComma) || $usernameToCheck == $this->user['username'])
            {
                return true;
            }

            else
            {
                return false;
            }
        }

        public function didReceiveRequest($userFrom)
        {
            $userTo = $this->user['username'];
            $checkRequestQuery = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to = '$userTo' AND 
                user_from = '$userFrom'");

            if(mysqli_num_rows($checkRequestQuery) > 0)
            {
                return true;
            }

            else
            {
                return false;
            }
        }

        public function didSendRequest($userTo)
        {
            $userFrom = $this->user['username'];
            $checkRequestQuery = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to = '$userTo' AND 
                user_from = '$userFrom'");

            if(mysqli_num_rows($checkRequestQuery) > 0)
            {
                return true;
            }

            else
            {
                return false;
            }
        }

        public function removeFriend($userToRemove)
        {
            $loggedInUser = $this->user['username'];

            $query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username = '$userToRemove'");
            $row = mysqli_fetch_array($query);

            $friendArrayUsername = $row['friend_array'];

            $newFriendArray = str_replace($userToRemove . ",", "", $this->user['friend_array']);
            $removeFriend = mysqli_query($this->con, "UPDATE users SET friend_array = '$newFriendArray' WHERE 
                username = '$loggedInUser'");

            $newFriendArray = str_replace($this->user['username'] . ",", "", $friendArrayUsername);
            $removeFriend = mysqli_query($this->con, "UPDATE users SET friend_array = '$newFriendArray' WHERE
                username = '$userToRemove'");


        }

        public function sendRequest($userTo)
        {
            $userFrom = $this->user['username'];
            $query = mysqli_query($this->con, "INSERT INTO friend_requests VALUES('', '$userTo', '$userFrom')");
        }

        public function getFriendArray()
        {
            return $this->user['friend_array'];
        }

        public function getMutualFriends($userToCheck)
        {
            $mutualFriends = 0;
            $userArray = $this->user['friend_array'];
            $userArrayExplode = explode(",", $userArray);

            $query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username = '$userToCheck'");
            $row = mysqli_fetch_array($query);
            $userToCheckArray = $row['friend_array'];
            $userToCheckArrayExplode = explode(",", $userToCheckArray);

            foreach($userArrayExplode as $element)
            {
                foreach($userToCheckArrayExplode as $element2)
                {
                    if($element == $element2 && $element != "")
                    {
                        $mutualFriends++;
                    }
                }
            }

            /*
            foreach($userArrayExplode as $element)
            {
                if(in_array($element, $userToCheckArrayExplode) && $element != "")
                {
                    $mutualFriends++;
                }
            }
            */

            return $mutualFriends;
        }
    }
?>