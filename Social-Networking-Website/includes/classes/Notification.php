<?php

class Notification
{
    
    private $userObject;
    private $con;

    public function __construct($con, $user)
    {
        $this->con = $con;
        $this->userObject = new User($con, $user);
    }

    public function getUnreadNumber()
    {
        $userLoggedIn = $this->userObject->getUsername();
        $query = mysqli_query($this->con, "SELECT * FROM notifications WHERE viewed = 'no' AND
        user_to = '$userLoggedIn'");

        return mysqli_num_rows($query);
    }

    public function insertNotification($postId, $userTo, $type)
    {
        $userLoggedIn = $this->userObject->getUsername();
        $userLoggedInName = $this->userObject->getFirstAndLastName();

        $dateTime = date("Y-m-d H:i:s");

        switch($type)
        {
            case 'comment':
                $message = $userLoggedInName . " commented on your post.";
                break;

            case 'like':
                $message = $userLoggedInName . " liked your post.";
                break;

            case 'profilePost':
                $message = $userLoggedInName . " posted on your profile.";
                break;

            case 'commentNonOwner':
                $message = $userLoggedInName . " commented on a post you commented on.";
                break;

            case 'profileComment':
                $message = $userLoggedInName . " commented on your profile post.";
                break;
        }

        $link = "post.php?id=" . $postId;

        $insertQuery = mysqli_query($this->con, "INSERT INTO notifications VALUES('', '$userTo',
            '$userLoggedIn', '$message', '$link', '$dateTime', 'no', 'no')");


    }

    public function getNotifications($data, $limit)
    {
        $page = $data['page'];
        $userLoggedIn = $this->userObject->getUsername();
        $returnString = "";

        if($page == 1)
        {
            $start = 0;
        }

        else
        {
            $start = ($page - 1) * $limit;
        }

        $setViewedQuery = mysqli_query($this->con, "UPDATE notifications SET viewed = 'yes'
            WHERE user_to = '$userLoggedIn'");

        $query = mysqli_query($this->con, "SELECT * FROM notifications WHERE user_to = '$userLoggedIn'
            ORDER BY id DESC");

        $numRows = mysqli_num_rows($query);

        if($numRows == 0)
        {
            echo "You have no notifications.";
            return;
        }

        $numIterations = 0; //Number of messages that have been checked
        $count = 1; //Number of messages that have been posted


        while($row = mysqli_fetch_array($query))
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

            $userFrom = $row['user_from'];

            $userDataQuery = mysqli_query($this->con, "SELECT * FROM users WHERE username = '$userFrom'");
            $userData = mysqli_fetch_array($userDataQuery);


            //Timeframe
            $dateTimeNow = date("Y-m-d H:i:s");
            $startDate = new DateTime($row['datetime']);
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


            $opened = $row['opened'];
            $style = ($row['opened'] == "no") ? "background-color:#DDEDFF;" : "";


            $returnString .= "<a href = '" . $row['link'] . "'> 
                                <div class = 'resultDisplay resultDisplayNotification' style = '" . $style . "'>
                                    <div class = 'notificationsProfilePic'>
                                        <img src = '" . $userData['profile_pic'] . "'>
                                    </div>
                                    <p class = 'timestampSmaller' id = 'grey'>" . $timeMessage . "</p>" . $row['message'] . "
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
            $returnString .= "<input type = 'hidden' class = 'noMoreDropdownData' value = 'true'><p style = 'text-align: center;'>No more notifications to load.</p>";
        }

        $returnString .= "<input type = 'hidden' class = 'nextPageDropdownData' 
                value = '" . ($page + 1) . "'><input type = 'hidden' class = 'noMoreDropdownData'
                value = 'false'>";

        return $returnString;
    }

}

?>