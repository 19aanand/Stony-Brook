<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Document</title>
        <link rel="stylesheet" type = "text/css" href="assets/css/style.css">
    </head>
    <body>
        <?php  
            require 'config/config.php';
            include("includes/classes/User.php");
            include("includes/classes/Post.php");

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

            if(isset($_GET['post_id']))
            {
                $post_id = $_GET['post_id'];
            }

            $getLikes = mysqli_query($con, "SELECT likes, added_by FROM posts WHERE id = '$post_id'");
            $row = mysqli_fetch_array($getLikes);
            $totalLikes = $row['likes'];
            $userLiked = $row['added_by'];

            $userDetailsQuery = mysqli_query($con, "SELECT * FROM users WHERE username = '$userLiked'");
            $row = mysqli_fetch_array($userDetailsQuery);


            //Like button 


            //Unlike button


            //Check for previous likes
            $checkQuery = mysqli_query($con, "SELECT * FROM likes WHERE username = '$userLoggedIn' AND post_id = '$post_id'");
            $numRows = mysqli_num_rows($checkQuery);

            if($numRows > 0)
            {
                echo '<form action = "like.php?post_id=' . $post_id . '" method = "POST">
                    <input type = "submit" class = "commentUnlike" name = "unlikeButton" value = "Unlike">

                        <div class = "likeValue">
                            ' . $totalLikes . ' Likes
                        </div>
                    </form>

                    ';
            }

            else
            {
                echo '<form action = "like.php?post_id=' . $post_id . '" method = "POST">
                    <input type = "submit" class = "commentLike" name = "likeButton" value = "Like">

                        <div class = "likeValue">
                            ' . $totalLikes . ' Likes
                        </div>
                    </form>

                    ';
            }
        ?>

    </body>
</html>