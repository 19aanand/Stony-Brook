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

            $userTo = $row['user_to'];
            $userFrom = $row['user_from'];

            if($userFrom != $userLoggedIn)
            {
                return $userFrom;
            }

            else
            {
                return $userTo;
            }
        }

        public function sendMessage($userTo, $body, $date)
        {
            if($body != "")
            {
                $userLoggedIn = $this->userObject->getUsername();
                $query = mysqli_query($this->con, "INSERT INTO messages VALUES('', '$userTo', '$userLoggedIn', '$body', '$date', 
                    'no', 'no', 'no')");

                $finalQuery = mysqli_query($this->con, "DELETE FROM messages WHERE user_to = '$userTo'
                    AND user_from = '$userLoggedIn' AND body = ''");
            }
        }

        public function getMessages($otherUser)
        {
            $userLoggedIn = $this->userObject->getUsername();
            $data = "";

            $query = mysqli_query($this->con, "UPDATE messages SET opened = 'yes' WHERE user_to = '$userLoggedIn'
                AND user_from = '$otherUser'");

            $getMessagesQuery = mysqli_query($this->con, "SELECT * FROM messages WHERE (user_to = '$userLoggedIn'
                AND user_from = '$otherUser') OR (user_from = '$userLoggedIn' AND user_to = '$otherUser')
                AND body <> ''");

            while($row = mysqli_fetch_array($getMessagesQuery))
            {
                $userTo = $row['user_to'];
                $userFrom = $row['user_from'];
                $body = $row['body'];
                
                if($body != '')
                {
                    $divTop = ($userTo != $userLoggedIn) ? "<div class = 'message' id = 'green'>" : 
                        "<div class = 'message' id = 'blue'>";

                    $data = $data . $divTop . $body . "</div><br>";
                }
            }
            return $data;
        }

        public function getConversations()
        {
            $userLoggedIn = $this->userObject->getUsername();
            $returnString = "";
            $conversations = array();
            $query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to = '$userLoggedIn'
                OR user_from = '$userLoggedIn' AND body != '' ORDER BY id DESC");

            while($row = mysqli_fetch_array($query))
            {
                $userToPush = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];
                
                if(!in_array($userToPush, $conversations))
                {
                    array_push($conversations, $userToPush);
                }
            }

            foreach($conversations as $username)
            {
                $userFoundObject = new User($this->con, $username);
                $latestMessageDetails = $this->getLatestMessage($userLoggedIn, $username);

                $dots = (strlen($latestMessageDetails[1]) >= 12) ? "..." : "";
                $split = str_split($latestMessageDetails[1], 12);
                $split = $split[0] . $dots;

                $returnString .= "<a href = 'messages.php?u=$username'> 
                    <div class = 'userFoundMessages'>
                        <img src = '" . $userFoundObject->getProfilePicture() . "' style = 'border-radius: 5px; margin-right: 5px;'>
                    " . $userFoundObject->getFirstAndLastName() . "
                    <br><span class = 'timestamp_smaller' id = 'grey'>" . $latestMessageDetails[2] . "</span>
                    <p id = 'grey' style = 'margin: 0;'>" . $latestMessageDetails[0] . $split . " </p> 
                    </div>
                </a>";
            }

            return $returnString;
        }

        public function getLatestMessage($userLoggedIn, $user2)
        {
            $detailsArray = array();
            $timeMessage = "";

            $query = mysqli_query($this->con, "SELECT body, user_to, date FROM messages WHERE 
                (user_to = '$userLoggedIn' AND user_from = '$user2' AND body != '') OR 
                (user_to = '$user2' AND user_from = '$userLoggedIn' AND body != '')
                ORDER BY id DESC LIMIT 1");

            $row = mysqli_fetch_array($query);
            $sentBy = ($row['user_to'] == $userLoggedIn) ? "They said: " : "You said: ";

            //Timeframe
            $dateTimeNow = date("Y-m-d H:i:s");
            $startDate = new DateTime($row['date']);
            $endDate = new DateTime($dateTimeNow);
            $interval = $startDate->diff($endDate); //Difference between the two dates

            if($interval->y >= 1)   //If interval is greater than or equal to a year
            {
                if($interval->y == 1)
                {
                    $timeMessage = $interval->y . " year ago"; //1 year ago
                }

                else
                {
                    $timeMessage = $interval->y . " years ago"; //Over 1 year ago   
                }
            }

            else if($interval->m >= 1)
            {
                if($interval->d == 0)
                {
                    $days = "0 days ago";
                }

                else if($interval->d ==1)
                {
                    $days = $interval->d . " day ago";
                }

                else
                {
                    $days = $interval->d . " days ago";
                }

                if($interval->m == 1)
                {
                    $timeMessage = $interval->m . " month and " . $days;
                }

                else
                {
                    $timeMessage = $interval->m . " months and " . $days;
                }
            }

            else if($interval->d >= 1)
            {
                if($interval->d == 1)
                {
                    $timeMessage = "Yesterday";
                }

                else
                {
                    $timeMessage = $interval->d . " days ago";
                }
            }

            else if($interval->h >= 1)
            {
                if($interval->h == 1)
                {
                    $timeMessage = $interval->h . " hour ago";
                }

                else
                {
                    $timeMessage = $interval->h . " hours ago";
                }
            }

            else if($interval->i >= 1)
            {
                if($interval->i == 1)
                {
                    $timeMessage = $interval->i . " minute ago";
                }

                else
                {
                    $timeMessage = $interval->i . " minutes ago";
                }
            }

            else
            {
                if($interval->s <= 30)
                {
                    $timeMessage = "Just now";
                }

                else
                {
                    $timeMessage = $interval->s . " seconds ago";
                }
            }

            array_push($detailsArray, $sentBy);
            array_push($detailsArray, $row['body']);
            array_push($detailsArray, $timeMessage);

            return $detailsArray;
        }

        public function getConversationsDropdown($data, $limit)
        {
            $page = $data['page'];
            $userLoggedIn = $this->userObject->getUsername();
            $returnString = "";
            $conversations = array();

            if($page == 1)
            {
                $start = 0;
            }

            else
            {
                $start = ($page - 1) * $limit;
            }

            $setViewedQuery = mysqli_query($this->con, "UPDATE messages SET viewed = 'yes'
                WHERE user_to = '$userLoggedIn'");

            $query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to = '$userLoggedIn'
                OR user_from = '$userLoggedIn' AND body != '' ORDER BY id DESC");

            while($row = mysqli_fetch_array($query))
            {
                $userToPush = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];
                
                if(!in_array($userToPush, $conversations))
                {
                    array_push($conversations, $userToPush);
                }
            }

            $numIterations = 0; //Number of messages that have been checked
            $count = 1; //Number of messages that have been posted

            $this->numOfConversations = sizeof($conversations);

            foreach($conversations as $username)
            {

                if($numIterations++ < $start)
                {
                    continue;
                }

                if($count > $limit)
                {
                    break;
                }

                else
                {
                    $count++;
                }

                $isUnreadQuery = mysqli_query($this->con, "SELECT * FROM messages
                    WHERE user_to = '$userLoggedIn' AND user_from = '$username' ORDER BY id DESC");
                $row = mysqli_fetch_array($isUnreadQuery);
                $style = ($row['opened'] == "no") ? "background-color:#DDEDFF;" : "";

                $userFoundObject = new User($this->con, $username);
                $latestMessageDetails = $this->getLatestMessage($userLoggedIn, $username);

                $dots = (strlen($latestMessageDetails[1]) >= 12) ? "..." : "";
                $split = str_split($latestMessageDetails[1], 12);
                $split = $split[0] . $dots;

                $returnString .= "<a href = 'messages.php?u=$username'> 
                    <div class = 'userFoundMessages' style = '" . $style . "'>
                        <img src = '" . $userFoundObject->getProfilePicture() . "' style = 'border-radius: 5px; margin-right: 5px;'>
                    " . $userFoundObject->getFirstAndLastName() . "
                    <br><span class = 'timestamp_smaller' id = 'grey'>" . $latestMessageDetails[2] . "</span>
                    <p id = 'grey' style = 'margin: 0;'>" . $latestMessageDetails[0] . $split . " </p> 
                    </div>
                </a>";

            }

            //If posts were loaded
            if($count > $limit)
            {
                $returnString .= "<input type = 'hidden' class = 'nextPageDropdownData' 
                    value = '" . ($page + 1) . "'><input type = 'hidden' class = 'noMoreDropdownData'
                    value = 'false'>";
            }

            else
            {
                $returnString .= "<input type = 'hidden' class = 'noMoreDropdownData' value = 'true'><p style = 'text-align: center;'>No more messages to load.</p>";
            }

            $finalQuery = mysqli_query($this->con, "DELETE FROM messages WHERE body = ''");

            $returnString .= "<input type = 'hidden' class = 'nextPageDropdownData' 
                    value = '" . ($page + 1) . "'><input type = 'hidden' class = 'noMoreDropdownData'
                    value = 'false'>";

            return $returnString;
        }
        
        public function getUnreadNumber()
        {
            $userLoggedIn = $this->userObject->getUsername();
            $query = mysqli_query($this->con, "SELECT * FROM messages WHERE viewed = 'no' AND
                user_to = '$userLoggedIn'");

            return mysqli_num_rows($query);
        }

        
    }
?>

