<?php  
    require 'config/config.php';
    include("includes/classes/User.php");
    include("includes/classes/Post.php");
    include("includes/classes/Notification.php");

    if(isset($_SESSION['username']))
    {
        $userLoggedIn = $_SESSION['username'];
        $userDetailsQuery = mysqli_query($con, "SELECT * FROM users WHERE username = '$userLoggedIn'");
        $user = mysqli_fetch_array($userDetailsQuery);
    }

    else
    {
        header("Location: register.php");
    }
?>

<html>
    <head>
        <title></title>

        <link rel = "stylesheet" type = "text/css" href = "assets/css/style.css">
    </head>

    <body style = "background-color: #ffffff; font-family: Arial, Helvetica, Sans-serif; font-size: 12px;">
        

        <script>
            function toggle()
            {
                var element = document.getElementById("commentsSection");

                if(element.style.display == "block")
                {
                    element.style.display = "none";
                }

                else
                {
                    element.style.display = "block";
                }
            }
        </script>

        <?php
            //Get ID of post
            if(isset($_GET['post_id']))
            {
                $post_id = $_GET['post_id'];
            }

            $userQuery = mysqli_query($con, "SELECT added_by, user_to FROM posts WHERE id = $post_id");
            $row = mysqli_fetch_array($userQuery);

            $postedTo = $row['added_by'];
            $userTo = $row['user_to'];

            if(isset($_POST['postComment' . $post_id]))
            {
                $postBody = $_POST['postBody'];
                $postBody = mysqli_escape_string($con, $postBody);
                $dateTimeNow = date("Y-m-d H:i:s");
                $insertPost = mysqli_query($con, "INSERT INTO comments VALUES('', '$postBody', '$userLoggedIn', '$postedTo', 
                    '$dateTimeNow', 'no', '$post_id')");

                if($postedTo != $userLoggedIn)
                {
                    $notificaiton = new Notification($con, $userLoggedIn);
                    $notificaiton->insertNotification($post_id, $postedTo, "comment");
                }

                if($userTo != 'none' && $userTo != $userLoggedIn)
                {
                    $notification = new Notification($con, $userLoggedIn);
                    $notification->insertNotification($post_id, $userTo, "profileComment");
                }

                $getCommenters = mysqli_query($con, "SELECT * FROM comments WHERE post_id = '$post_id'");
                $notifiedUsers = array();

                while($row = mysqli_fetch_array($getCommenters))
                {
                    if($row['posted_by'] != $postedTo && $row['posted_by'] != $userTo &&
                        $row['posted_by'] != $userLoggedIn && !in_array($row['posted_by'], $notifiedUsers))
                    {
                        //Insert notification
                        $notification = new Notification($con, $userLoggedIn);
                        $notification->insertNotification($post_id, $row['posted_by'], "commentNonOwner");

                        array_push($notifiedUsers, $row['posted_by']);
                    }
                }

                echo "<p>Comment posted!</p>";
            }
        ?>

        <form action = "CommentFrame.php?post_id=<?php echo $post_id; ?>" id = "commentForm" 
            name = "postComment<?php echo $post_id;?>" method = "POST" >

            <textarea name = "postBody"></textarea>
                
            <input type = "submit" name = "postComment<?php echo $post_id; ?>"value = "Post">

        </form>

        <!-- Load comments on post -->

        <?php
            $getComments = mysqli_query($con, "SELECT * FROM comments WHERE post_id = '$post_id' ORDER BY id ASC");
            $count = mysqli_num_rows($getComments);

            if($count != 0)
            {
                while($comment = mysqli_fetch_array($getComments))
                {
                    $commentBody = $comment['post_body'];
                    $postedTo = $comment['posted_to'];
                    $postedBy = $comment['posted_by'];
                    $dateAdded = $comment['date_added'];
                    $removed = $comment['removed'];

                    //Timeframe
                    $timeMessage = "";
                    $dateTimeNow = date("Y-m-d H:i:s");
                    $startDate = new DateTime($dateAdded);
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

                    $userObject = new User($con, $postedBy);

                ?>

                    <div class = "commentSection">
                        <a href = "<?php echo $postedBy?>" target = "_parent">
                            <img src=
                                "<?php
                                    echo $userObject->getProfilePicture();
                                ?>"
                            title = 
                                "<?php
                                    echo $postedBy;
                                ?>"
                            style = "float: left;" height = "30">
                        </a>

                        <a href="<?php echo $postedBy?>" target = "_parent">
                            <b>
                                <?php
                                    echo $userObject->getFirstAndLastName();
                                ?>
                            </b>
                        </a>

                        &nbsp;&nbsp;&nbsp;&nbsp;
                        
                        <?php
                            echo $timeMessage . "<br>" . $commentBody;
                        ?>

                        <hr>
                    </div>

                <?php
                    
                }
            }

            else
            {
                echo "<center><br><br>No comments to show!</center>";
            }
        ?>



    </body>
</html>