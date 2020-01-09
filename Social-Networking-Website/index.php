<?php
    include("includes/header.php");

    if(isset($_POST['post']))
    {
        $post = new Post($con, $userLoggedIn);
        $post->submitPost($_POST['postText'], 'none');
        header("Location: index.php");
    }
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

            <?php
                //$post = new Post($con, $userLoggedIn);
                //$post->loadPostsFriends();

            ?>

            <div id = "postsArea"></div>

            <img id = "loading" src = "assets/images/icons/loading.gif">

        </div>

        <script>
            var userLoggedIn = '<?php echo $userLoggedIn;?>';


            $(document).ready(function()
            {
                $('#loading').show();
                //var page = $('.posts_area').find('.nextPage').val() || 1; 
                //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'

                //Original ajax script for loading first posts
                $.ajax
                ({
                    url: "includes/handlers/AjaxLoadPosts.php",
                    type: "POST",
                    data: "page=1&userLoggedIn=" + userLoggedIn,
                    //data: "page=" + page + "&userLoggedIn" + userLoggedIn,
                    cache: false,

                    success: function(data)
                    {
                        $('#loading').hide();
                        $('#postsArea').html(data);
                    }
                });

                $(window).scroll(function()
                {
                    var height = $('#postsArea').height(); //Div containing posts
                    var scrollTop = $(this).scrollTop();
                    var page = $('#postsArea').find('.nextPage').val();
                    var noMorePosts = $('#postsArea').find('.noMorePosts').val();

                    if((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false')
                    {
                        $('#loading').show();
                        

                        var ajaxRequest = $.ajax
                        ({
                            url: "includes/handlers/AjaxLoadPosts.php",
                            type: "POST",
                            data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
                            cache: false,

                            success: function(response)
                            {
                                $('#postsArea').find('.nextPage').remove(); //Removes current .nextPage
                                $('#postsArea').find('.noMorePosts').remove();


                                $('#loading').hide();
                                $('#postsArea').append(response);
                            }
                        });
                    } //End if

                    return false;

                }); //End (window).scroll(function())
            });

        </script>

    </div>
</body>

</html>