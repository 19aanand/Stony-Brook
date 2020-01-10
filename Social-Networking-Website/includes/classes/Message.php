<?php
    class Message
    {
        private $userObject;
        private $con;

        public function __construct($con, $user)
        {
            $this->con = $con;
            $this->userObject = new User($con, $user);
        }

        public function getMostRecentUser()
        {
            $userLoggedIn = $this->userObject->getUsername();

            $query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to = '$userLoggedIn' OR
                user_from = '$userLoggedIn' ORDER BY id DESC LIMIT 1");

            if(mysqli_num_rows($query) == 0)
            {
                return false;
            }

            $row = mysqli_fetch_array($query);

            $userTo = row['user_to'];
            $userFrom = row['user_from'];

            if($userFrom != $userLoggedIn)
            {
                return $userFrom;
            }

            else
            {
                return $userTo;
            }
        }
    }
?>