<?php
    include("includes/header.php");

    if(isset($_POST['post']))
    {
        $uploadOk = 1;
        $imageName = $_FILES['fileToUpload']['name'];
        $errorMessage = "";

        if($imageName != "")
        {
            $targetDirectory = "assets/images/posts/";
            $imageName = $targetDirectory . uniqid() . basename($imageName);
            $imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

            if($_FILES['fileToUpload']['size'] > 10000000)
            {
                $errorMessage = "Sorry, the file uploaded was too large.";
                $uploadOk = 0;
            }

            if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png"
                && strtolower($imageFileType) != "jpg")
            {
                $errorMessage = "Sorry, only jpeg, jpg, and png files can be uploaded.";
                $uploadOk = 0;
            }

            if($uploadOk) //The integer value of 1 corresponds to true in boolean logic
            {
                if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName))
                {
                    //Image uploaded sucessfully
                }

                else
                {
                    //Image was not uploaded successfully
                    $uploadOk = 0;
                }
            }
        
        } //End of outermost if statement

        if($uploadOk)
        {
            $post = new Post($con, $userLoggedIn);
            $post->submitPost($_POST['postText'], 'none', $imageName);
            header("Location: index.php");
        }

        else
        {
            echo "<div style = 'text-align:center' class = 'alert alert-danger'>
                    $errorMessage
                </div>";
        }
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

        
            <form class = "postForm" action = "index.php" method = "POST" enctype = "multipart/form-data">

                <input type = "file" name = "fileToUpload" id = "fileToUpload">
                    
                </input>

                <textarea name = "postText" id = "postText" placeholder = "What's on your mind?">
                </textarea>
                

                <input type = "submit" name = "post" id = "postButton" value = "Post">
                </input>


                <hr>

            </form>


            <div id = "postsArea"></div>

            <img id = "loading" src = "assets/images/icons/loading.gif">

        </div>

        <div class = "userDetails column">
            <div class = "trends">

                <h4>Popular</h4>

                <?php
                    $query = mysqli_query($con, "SELECT * FROM trends ORDER BY hits DESC LIMIT 9");

                    foreach($query as $key => $row) //Declaration is identical to $row = mysqli_fetch_array($query)
                    {
                        $word = $row['title'];
                        $wordDot = strlen($word) >= 14 ? "..." : "";
                        
                        $trimmedWord = str_split($word, 14);
                        $trimmedWord = $trimmedWord[0];

                        echo "<div style = 'padding: 1px;'>";
                        echo ($key+1) . ") " . $trimmedWord . $wordDot;
                        echo "<br><br></div>";
                    }
                ?>
            </div>
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