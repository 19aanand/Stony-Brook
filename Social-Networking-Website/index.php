<?php
    include("includes/header.php");
?>

        <!-- Separating the class names with a space when assigning a class to a div tag allows for multiple classes to be assigned -->
        <div class = "userDetails column">

            <div class = "userDetailsLeftRight">

                <a href = 
                    "<?php
                        echo $userLoggedIn;
                    ?>">
                <img src = "
                    <?php
                        echo $user['profile_pic'];
                    ?>"> 
                </a>

                <br>

                <a href = 
                "<?php
                    echo $userLoggedIn;
                ?>">
                    <?php
                        echo $user['first_name'] . " " . $user['last_name'];
                    ?>
                </a>
                <br>
                
                <?php
                    echo "Posts: " . $user['num_posts'] . "<br>";
                    echo "Likes: " . $user['num_likes'];
                ?>
            </div>

        </div>

        <div class = "mainColumn column">

            <form class = "postForm" action = "index.php" method = "POST">

                <textarea name = "postText" id = "postText" placeholder = "What's on your mind?">
                </textarea>

                <input type = "submit" name = "post" id = "postButton" value = "Post">
                    <hr>
                </input>

            </form>
        </div>

    </div>
</body>

</html>