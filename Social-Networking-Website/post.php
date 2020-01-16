<?php
    include("includes/header.php");

    if(isset($_GET['id']))
    {
        $id = $_GET['id'];
    }

    else
    {
        $id = 0;
    }
?>


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

            <div class = "mainColumn column" id = "mainColumn">
                <div class = "postsArea">
                    <?php
                        $post = new Post($con, $userLoggedIn);
                        $post->getSinglePost($id);

                    ?>
                </div>
            </div>

</div>

