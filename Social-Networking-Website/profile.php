<?php
    include("includes/header.php");

    if(isset($_GET['profile_username'])) //Gets username in url
    {
        $username = $_GET['profile_username'];
        $userDetailsQuery = mysqli_query($con, "SELECT * FROM users WHERE username = '$username'");
        $userArray = mysqli_fetch_array($userDetailsQuery);
        $numOfFriends = (substr_count($userArray['friend_array'], ","))-1;
    }

    if(isset($_POST['removeFriend']))
    {
        $user = new User($con, $userLoggedIn);
        $user->removeFriend($username);
    }

    if(isset($_POST['addFriend']))
    {
        $user = new User($con, $userLoggedIn);
        $user->sendRequest($username);
    }

    if(isset($_POST['respondRequest']))
    {
        header("Location: Requests.php");
    }
?>
        <style type = "text/css">
            .wrapper
            {
                margin-left: 0px;
                padding-left: 0px;
            }
        </style>

        <div class = "profileLeft">
            <br>
            <br>
            <img src = "<?php echo $userArray['profile_pic']; ?>"> <!-- Acquires path of user's profile picture and displays it-->

            <div class = "profileInfo">
                <p>
                    <?php
                        echo "Posts: " . $userArray['num_posts'] . "<br><br>";
                        echo "Likes: " . $userArray['num_likes'] . "<br><br>";
                        echo "Friends: " . $numOfFriends . "<br><br>";

                    ?>
                </p>
            </div>

            <form action = "<?php echo $username; ?>" method = "POST">
                <?php
                    $profileUserObject = new User($con, $username);

                    if($profileUserObject->isClosed())
                    {
                        header("Location: UserClosed.php");
                    }

                    $loggedInUserObject = new User($con, $userLoggedIn);

                    if($userLoggedIn != $username)
                    {
                        if($loggedInUserObject->isFriend($username))
                        {
                            echo '<input type = "submit" name = "removeFriend" class = "danger" value = "Remove Friend"><br>';
                        }

                        else if($loggedInUserObject->didReceiveRequest($username))
                        {
                            echo '<input type = "submit" name = "respondRequest" class = "warning" value = "Respond to Request"><br>';
                        }

                        else if($loggedInUserObject->didSendRequest($username))
                        {
                            echo '<input type = "submit" name = "" class = "default" value = "Request Sent"><br>';
                        }

                        else
                        {
                            echo '<input type = "submit" name = "addFriend" class = "success" value = "Add Friend"><br>';
                        }
                    }
                ?>
            </form>

            <input type = "submit" class = "deepGreen" data-toggle = "modal" data-target = "#post_form" 
                    value = "Post Something">

            <?php
                if($userLoggedIn != $username)
                {
                    echo '<div class = "profileInfoBottom">';
                    $numMutualFriends = $loggedInUserObject->getMutualFriends($username);

                    if($numMutualFriends != 1)
                    {
                        echo $numMutualFriends . " Mutual Friends";
                    }

                    else
                    {
                        echo $numMutualFriends . " Mutual Friend";
                    }
                    echo '</div>';
                }

            ?>

        </div>

        <br>
        <br>
        <div class = "profileMainColumn column">

            <div id = "postsArea"></div>

            <img id = "loading" src = "assets/images/icons/loading.gif">
        </div>

        <!-- Modal -->
        <div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Post something!</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p>This will appear on the user's profile page and their newsfeed as well for your friends to see.</p>

                    <form class = "profile_post" action = "" method = "POST">
                        <div class = "form-group">
                            <textarea class = "form-control" name = "post_body"></textarea>

                            <input type = "hidden" name = "user_from" value = "<?php echo $userLoggedIn; ?>">
                            <input type = "hidden" name = "user_to" value = "<?php echo $username; ?>">
                        </div>
                    </form>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" name = "post_button" id = "submit_profile_post">Post</button>
                </div>
                </div>
            </div>
        </div>

        <script>
            var userLoggedIn = '<?php echo $userLoggedIn;?>';
            var profileUsername = '<?php echo $username;?>';

            $(document).ready(function()
            {
                $('#loading').show();
                //var page = $('.posts_area').find('.nextPage').val() || 1; 
                //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'

                //Original ajax script for loading first posts
                $.ajax
                ({
                    url: "includes/handlers/AjaxLoadProfilePosts.php",
                    type: "POST",
                    data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
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
                            url: "includes/handlers/AjaxLoadProfilePosts.php",
                            type: "POST",
                            data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
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