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

                $bodyArray = preg_split("/\s+/", $body);

                foreach($bodyArray as $key => $value) //$key represents index in array
                {
                    if(strpos($value, "www.youtube.com/watch?v=") !== false)
                    {
                        $link = preg_split("!&!", $value);

                        $value = preg_replace("!watch\?v=!", "embed/", $link[0]);
                        $value = "<br><iframe width = \'420\' height = \'315\' src = \'" . $value 
                            . "\'></iframe><br>";
                        $bodyArray[$key] = $value;
                    }
                }

                $body = implode(" ", $bodyArray);

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
                
                $returnedId = mysqli_insert_id($this->con);

                //Insert notification
                if($userTo != 'none')
                {
                    $notificaiton = new Notification($this->con, $addedBy);
                    $notificaiton->insertNotification($returnedId, $userTo, "profilePost");
                }

                //Update post count for user
                $numPosts = $this->userObject->getNumPosts();
                $numPosts+=1;

                $update_query = mysqli_query($this->con, "UPDATE users SET num_posts = '$numPosts' WHERE username = '$addedBy'");

                $stopWords = "a about above across after again against all almost alone along already
                also although always among am an and another any anybody anyone anything anywhere are 
                area areas around as ask asked asking asks at away b back backed backing backs be became
                because become becomes been before began behind being beings best better between big 
                both but by c came can cannot case cases certain certainly clear clearly come could
                d did differ different differently do does done down down downed downing downs during
                e each early either end ended ending ends enough even evenly ever every everybody
                everyone everything everywhere f face faces fact facts far felt few find finds first
                for four from full fully further furthered furthering furthers g gave general generally
                get gets give given gives go going good goods got great greater greatest group grouped
                grouping groups h had has have having he her here herself high high high higher
                highest him himself his how however i im if important in interest interested interesting
                interests into is it its itself j just k keep keeps kind knew know known knows
                large largely last later latest least less let lets like likely long longer
                longest m made make making man many may me member members men might more most
                mostly mr mrs much must my myself n necessary need needed needing needs never
                new new newer newest next no nobody non noone not nothing now nowhere number
                numbers o of off often old older oldest on once one only open opened opening
                opens or order ordered ordering orders other others our out over p part parted
                parting parts per perhaps place places point pointed pointing points possible
                present presented presenting presents problem problems put puts q quite r
                rather really right right room rooms s said same saw say says second seconds
                see seem seemed seeming seems sees several shall she should show showed
                showing shows side sides since small smaller smallest so some somebody
                someone something somewhere state states still still such sure t take
                taken than that the their them then there therefore these they thing
                things think thinks this those though thought thoughts three through
                thus to today together too took toward turn turned turning turns two
                u under until up upon us use used uses v very w want wanted wanting
                wants was way ways we well wells went were what when where whether
                which while who whole whose why will with within without work
                worked working works would x y year years yet you young younger
                youngest your yours z lol haha omg hey ill iframe wonder else like 
                hate sleepy reason for some little yes bye choose";

                $stopWords = preg_split("/[\s,]+/", $stopWords);

                $noPunctuation = preg_replace("/[^a-zA-Z 0-9]+/", "", $body);

                //Predicting if YouTube video is posted by searching for the words involved in the HTML code for embeeding a YouTube video
                if(strpos($noPunctuation, "height") === false && strpos($noPunctuation, "width") === false
                    && strpos($noPunctuation, "http") === false)
                {
                    $noPunctuation = preg_split("/[\s,]+/", $noPunctuation);

                    foreach($stopWords as $value)
                    {
                        foreach($noPunctuation as $key => $value2)
                        {
                            if(strtolower($value) == strtolower($value2))
                            {
                                $noPunctuation[$key] = "";
                            }
                        }
                    }

                    foreach($noPunctuation as $value)
                    {
                        $this->calculateTrend(ucfirst($value));
                    }
                }

            }
        }


        public function calculateTrend($term)
        {
            if($term != "")
            {
                $query = mysqli_query($this->con, "SELECT * FROM trends WHERE title = '$term'");

                if(mysqli_num_rows($query) == 0)
                {
                    $insertQuery = mysqli_query($this->con, "INSERT INTO trends VALUES('',
                        '$term', '1')");
                }

                else
                {
                    $insertQuery = mysqli_query($this->con, "UPDATE trends SET hits = hits+1
                        WHERE title = '$term'");
                }
            }
        }


        public function loadProfilePosts($data, $limit)
        {
            $page = $data['page'];
            $profileUser = $data['profileUsername'];
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
            $date = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted = 'no' AND ((added_by = '$profileUser') OR 
                user_to = '$profileUser') /*AND user_to = 'none'*/ ORDER BY id DESC");

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

                    if($userLoggedIn = "$addedBy")
                    {
                        $deleteButton = "<button class = 'delete_button btn-danger' id = 'post$id'>X</button>";
                    }

                    else
                    {
                        $deleteButton = "";
                    }

                    $userDetailsQuery = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE
                        username = '$addedBy'");
                    $userRow = mysqli_fetch_array($userDetailsQuery);
                    $firstName = $userRow['first_name'];
                    $lastName = $userRow['last_name'];
                    $profilePic = $userRow['profile_pic'];


                ?>

                <script>
                    function toggle<?php echo $id;?>()
                    {
                        var target = $(event.target);
                        if(!target.is("a"))
                        {
                            var element = document.getElementById("toggleComment<?php echo $id; ?>");

                            if(element.style.display == "block")
                            {
                                element.style.display = "none";
                            }

                            else
                            {
                                element.style.display = "block";
                            }
                        }
                    }
                </script>

                    <?php
                    
                        $commentsCheck = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id = '$id'");
                        $commentsCheckNum = mysqli_num_rows($commentsCheck);


                        //Timeframe
                        $dateTimeNow = date("Y-m-d H:i:s");
                        $startDate = new DateTime($dateTime);
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

                        $str .= "<div class = 'statusPost' onClick = 'javascript:toggle$id()'>
                            <div class = 'postProfilePic'>
                                <img src = '$profilePic' width = '50'>
                            </div>

                            <div class = 'postedBy' style = 'color: #ACACAC;'>
                                <a href = '$addedBy'>$firstName $lastName</a> &nbsp;&nbsp;&nbsp;&nbsp; $timeMessage
                                $deleteButton
                            </div>

                            <div id = 'postBody'>
                                $body
                                <br>
                                <br>
                                <br>
                            </div>

                            <div class = 'newsfeedPostOptions'>
                                Comments($commentsCheckNum)&nbsp;&nbsp;&nbsp;

                                <iframe src = 'like.php?post_id=$id' scrolling = 'no'></iframe>
                            </div>

                        </div>

                        <div class = 'postComment' id = 'toggleComment$id' style = 'display:none;'>
                            <iframe src = 'CommentFrame.php?post_id=$id' id = 'comment_iframe'></iframe>
                        </div>

                        <hr>";

                    ?>
                    
                    <script>
                        $(document).ready(function()
                        {
                            $('#post<?php echo $id; ?>').on('click', function()
                            {
                                bootbox.confirm("Are you sure you would like to delete this post?", function(result)
                                {
                                    $.post("includes/form_handlers/DeletePosts.php?post_id=<?php echo $id; ?>", {result: result});

                                    if(result)
                                    {
                                        location.reload();
                                    }
                                });
                            });
                        });
                    </script>

                    <?php

                } //End while loop

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

                    $userLoggedObject = new User($this->con, /*$userLoggedIn$*/$userLoggedIn);
                    if($userLoggedObject->isFriend($addedBy))
                    {
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

                        if($userLoggedIn == "$addedBy")
                        {
                            $deleteButton = "<button class = 'delete_button btn-danger' id = 'post$id'>X</button>";
                        }

                        else
                        {
                            $deleteButton = "";
                        }

                        $userDetailsQuery = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE
                            username = '$addedBy'");
                        $userRow = mysqli_fetch_array($userDetailsQuery);
                        $firstName = $userRow['first_name'];
                        $lastName = $userRow['last_name'];
                        $profilePic = $userRow['profile_pic'];


                    ?>

                    <script>
                        function toggle<?php echo $id;?>()
                        {
                            var target = $(event.target);
                            if(!target.is("a"))
                            {
                                var element = document.getElementById("toggleComment<?php echo $id; ?>");

                                if(element.style.display == "block")
                                {
                                    element.style.display = "none";
                                }

                                else
                                {
                                    element.style.display = "block";
                                }
                            }
                        }
                    </script>

                    <?php
                    
                        $commentsCheck = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id = '$id'");
                        $commentsCheckNum = mysqli_num_rows($commentsCheck);


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

                        $str .= "<div class = 'statusPost' onClick = 'javascript:toggle$id()'>
                            <div class = 'postProfilePic'>
                                <img src = '$profilePic' width = '50'>
                            </div>

                            <div class = 'postedBy' style = 'color: #ACACAC;'>
                                <a href = '$addedBy'>$firstName $lastName</a> $userTo  &nbsp;&nbsp;&nbsp;&nbsp; $timeMessage
                                $deleteButton
                            </div>

                            <div id = 'postBody'>
                                $body
                                <br>
                                <br>
                                <br>
                            </div>

                            <div class = 'newsfeedPostOptions'>
                                Comments($commentsCheckNum)&nbsp;&nbsp;&nbsp;

                                <iframe src = 'like.php?post_id=$id' scrolling = 'no'></iframe>
                            </div>

                        </div>

                        <div class = 'postComment' id = 'toggleComment$id' style = 'display:none;'>
                            <iframe src = 'CommentFrame.php?post_id=$id' id = 'comment_iframe'></iframe>
                        </div>

                        <hr>";

                    }

                    ?>
                    
                    <script>
                        $(document).ready(function()
                        {
                            $('#post<?php echo $id; ?>').on('click', function()
                            {
                                bootbox.confirm("Are you sure you would like to delete this post?", function(result)
                                {
                                    $.post("includes/form_handlers/DeletePosts.php?post_id=<?php echo $id; ?>", {result: result});

                                    if(result)
                                    {
                                        location.reload();
                                    }
                                });
                            });
                        });
                    </script>

                    <?php

                } //End while loop

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

        public function getSinglePost($postId)
        {
            $userLoggedIn = $this->userObject->getUsername();

            $query = mysqli_query($this->con, "UPDATE notifications SET opened = 'yes' WHERE
                user_to = '$userLoggedIn' AND link LIKE '%=$postId'");

            $str = ""; //String to return
            $date = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted = 'no' AND id = '$postId'");

            if(mysqli_num_rows($date) > 0)
            {
                $row = mysqli_fetch_array($date);
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
                    return;
                }

                $userLoggedObject = new User($this->con, /*$userLoggedIn$*/$userLoggedIn);
                if($userLoggedObject->isFriend($addedBy))
                {
                    if($userLoggedIn == "$addedBy")
                    {
                        $deleteButton = "<button class = 'delete_button btn-danger' id = 'post$id'>X</button>";
                    }

                    else
                    {
                        $deleteButton = "";
                    }

                    $userDetailsQuery = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE
                        username = '$addedBy'");
                    $userRow = mysqli_fetch_array($userDetailsQuery);
                    $firstName = $userRow['first_name'];
                    $lastName = $userRow['last_name'];
                    $profilePic = $userRow['profile_pic'];


                ?>

                <script>
                    function toggle<?php echo $id;?>()
                    {
                        var target = $(event.target);
                        if(!target.is("a"))
                        {
                            var element = document.getElementById("toggleComment<?php echo $id; ?>");

                            if(element.style.display == "block")
                            {
                                element.style.display = "none";
                            }

                            else
                            {
                                element.style.display = "block";
                            }
                        }
                    }
                </script>

                <?php
                
                    $commentsCheck = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id = '$id'");
                    $commentsCheckNum = mysqli_num_rows($commentsCheck);


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

                    $str .= "<div class = 'statusPost' onClick = 'javascript:toggle$id()'>
                                <div class = 'postProfilePic'>
                                    <img src = '$profilePic' width = '50'>
                                </div>

                                <div class = 'postedBy' style = 'color: #ACACAC;'>
                                    <a href = '$addedBy'>$firstName $lastName</a> $userTo  &nbsp;&nbsp;&nbsp;&nbsp; $timeMessage
                                    $deleteButton
                                </div>

                                <div id = 'postBody'>
                                    $body
                                    <br>
                                    <br>
                                    <br>
                                </div>

                                <div class = 'newsfeedPostOptions'>
                                    Comments($commentsCheckNum)&nbsp;&nbsp;&nbsp;

                                    <iframe src = 'like.php?post_id=$id' scrolling = 'no'></iframe>
                                </div>

                            </div>

                            <div class = 'postComment' id = 'toggleComment$id' style = 'display:none;'>
                                <iframe src = 'CommentFrame.php?post_id=$id' id = 'comment_iframe'></iframe>
                            </div>

                            <hr>";
                    
                    ?>
                    
                        <script>
                            $(document).ready(function()
                            {
                                $('#post<?php echo $id; ?>').on('click', function()
                                {
                                    bootbox.confirm("Are you sure you would like to delete this post?", function(result)
                                    {
                                        $.post("includes/form_handlers/DeletePosts.php?post_id=<?php echo $id; ?>", {result: result});

                                        if(result)
                                        {
                                            location.reload();
                                        }
                                    });
                                });
                            });
                        </script>

            <?php

                }

                else
                {
                    echo "<p>You cannot see this post because you are not friends with this user.</p>";
                    return;
                }

            }

            else
            {
                echo "<p>No post found. If you clicked a link, then it may be broken.</p>";
                return;
            }

            echo $str;
        }
    }
?>