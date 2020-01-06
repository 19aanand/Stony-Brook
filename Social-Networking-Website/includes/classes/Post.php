<?php
    class Post
    {
        private $userObject;
        private $con;

        public function __construct($con, $user)
        {
            $this->con = $con;
            $this->userObject = new User($con, $user);
        }


        public function submitPost($body, $userTo)
        {
            $body = strip_tags($body); //Remove html tags

            //Negates any effect a single quote in the post might have on an SQL query            
            $body = mysqli_real_escape_string($this->con, $body); 

            //Adds new line to body where line carriage and new line are located
            $body = str_replace('\r\n', '\n', $body);
            
            //Replaces all new lines to line breaks
            $body = nl2br($body);

            $checkEmpty = preg_replace('/\s+/', '', $body); //Deletes all spaces

            if($checkEmpty != "")
            {
                //Current date and time
                $dateAdded = date("Y-m-d H:i:s");

                //Get username
                $addedBy = $this->userObject->getUsername();

                //If user is not on own profile, and userTo is none
                if($userTo == $addedBy)
                {
                    $userTo = "none";
                }

                //Insert post
                $query = mysqli_query($this->con, "INSERT INTO posts VALUES('', '$body', '$addedBy', '$userTo', '$dateAdded', 
                    'no', 'no', '0')");
                
                $returned_id = mysqli_insert_id($this->con);

                //Insert notification


                //Update post count for user
                $numPosts = $this->userObject->getNumPosts();
                $numPosts+=1;

                $update_query = mysqli_query($this->con, "UPDATE users SET num_posts = '$numPosts' WHERE username = '$addedBy'");
            }
        }


        public function loadPostsFriends($data, $limit)
        {
            $page = $data['page'];
            $userLoggedIn = $this->userObject->getUsername();

            if($page == 1)
            {
                $start = 0;
            }

            else
            {
                $start = ($page - 1) * $limit;
            }

            $str = ""; //String to return
            $date = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted = 'no' ORDER BY id DESC");

            if(mysqli_num_rows($date) > 0)
            {
                $numIterations = 0; //Number of results checked, but not necessarily posted
                $count = 1;

                while($row = mysqli_fetch_array($date))
                {
                    $id = $row['id'];
                    $body = $row['body'];
                    $addedBy = $row['added_by'];
                    $dateTime = $row['date_added'];

                    //Preprae userTo so that it can be included even if not posted to a user
                    if($row['user_to'] == "none")
                    {
                        $userTo = "";
                    }

                    else
                    {
                        $userToObject = new User($this->con, $row['user_to']);
                        $userToName = $userToObject->getFirstAndLastName();
                        $userTo = "to <a href = '" . $row['user_to'] . "'>" . $userToName . "</a>";
                    }

                    //Now we check if the user that made the post has their account closed
                    $addedByObject = new User($this->con, $addedBy);
                    if($addedByObject->isClosed())
                    {
                        continue;
                    }

                    if($numIterations++ < $start)
                    {
                        continue;
                    }

                    //Once 10 posts have been loaded, break
                    if($count > $limit)
                    {
                        break;
                    }

                    else
                    {
                        $count++;
                    }

                    $userDetailsQuery = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE
                        username = '$addedBy'");
                    $userRow = mysqli_fetch_array($userDetailsQuery);
                    $firstName = $userRow['first_name'];
                    $lastName = $userRow['last_name'];
                    $profilePic = $userRow['profile_pic'];

                    //Timeframe
                    $dateTimeNow = date("Y-m-d H:i:s");
                    $startDate = new DateTime($dateTime);
                    $endDate = new DateTime($dateTimeNow);
                    $interval = $startDate->diff($endDate); //Difference between the two dates

                    if($interval->y >= 1)   //If interval is greater than or equal to a year
                    {
                        if($interval == 1)
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

                    $str .= "<div class = 'statusPost'>
                        <div class = 'postProfilePic'>
                            <img src = '$profilePic' width = '50'>
                        </div>

                        <div class = 'postedBy' style = 'color: #ACACAC;'>
                            <a href = '$addedBy'>$firstName $lastName</a> $userTo  &nbsp;&nbsp;&nbsp;&nbsp; $timeMessage
                        </div>

                        <div id = 'postBody'>
                            $body<br>
                        </div>

                    </div>
                    <hr>";

                }

                if($count > $limit)
                {
                    $str .= "<input type = 'hidden' class = 'nextPage' value = '" . ($page + 1) . "'>
                                <input type = 'hidden' class = 'noMorePosts' value = 'false'>";
                }

                else
                {
                    $str .= "<input type = 'hidden' class = 'noMorePosts' value = 'true'><p style = 'text-align: center;'>
                                No more posts to show!</p>";  
                }

            }

            echo $str;
        }

    }
?>