<?php  
    require 'config/config.php';
    include("includes/classes/User.php");
    include("includes/classes/Post.php");
    include("includes/classes/Message.php");
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
    <title>AA Networking</title>

    <!-- JavaScript -->
    <script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src = "assets/js/bootstrap.js"></script>
    <script src = "assets/js/bootbox.min.js"></script>
    <script src = "assets/js/AANetworking.js"></script>
    <script src = "assets/js/jquery.jcrop.js"></script>
    <script src = "assets/js/jcrop_bits.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel = "stylesheet" type = "text/css" href = "assets/css/bootstrap.css">
    <link rel = "stylesheet" type = "text/css" href = "assets/css/style.css">
    <link rel="stylesheet" type = "text/css" href = "assets/css/jquery.jcrop.css">
    

</head>

<body>

    <div class = "top_bar">

        <div class = "logo">
            <a href = "index.php">AA Networking</a>
        </div>

        <div class = "search">
            <form action = "Search.php" method = "GET" name = "searchFor">
                <input type = "text" onkeyup = "getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" 
                    name = "q" placeholder = "Search for user" autocomplete = "off" id = "searchTextInput">

                <div class = "buttonHolder">
                    <img src = "assets/images/icons/magnifyingGlass.png">
                </div>
            </form>

            <div class = "searchResults">
            
            </div>

            <div class = "searchResultsFooterEmpty">

            </div>

        </div>

        <nav>

            <?php
                //Unread messages
                $messages = new Message($con, $userLoggedIn);
                $numMessages = $messages->getUnreadNumber();

                //Unread notifications
                $notifications = new Notification($con, $userLoggedIn);
                $numNotifications = $notifications->getUnreadNumber();

                $userObject = new User($con, $userLoggedIn);
                $numFriendRequests = $userObject->getNumFriendRequests();
            ?>

            <a href = 
                "<?php
                    echo $userLoggedIn;
                ?>">
                <?php
                    echo $user['first_name'];
                ?>
            </a>  
            
            <!-- Home icon -->
            <a href = "index.php">
                <i class="fa fa-home fa-lg"></i>
            </a>    

            <!-- Messages icon -->
            <a href = "javascript:void(0);" onclick = "getDropdownData('<?php echo $userLoggedIn; ?>', 'message')">
                <i class="fa fa-envelope fa-lg" aria-hidden="true"></i>

                <?php
                    if($numMessages > 0)
                    {
                        echo '<span class = "notificationBadge" id = "unreadMessages">' . $numMessages . '</span>';
                    }
                ?>
            </a>

            <!-- Notifications icon -->
            <a href = "javascript:void(0);" onclick = "getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
                <i class="fa fa-bell fa-lg" aria-hidden="true"></i>

                <?php
                    if($numNotifications > 0)
                    {
                        echo '<span class = "notificationBadge" id = "unreadNotifications">' . $numNotifications . '</span>';
                    }
                ?>
            </a>    

            <!-- Add friends icon -->
            <a href = "Requests.php">
                <i class="fa fa-users fa-lg" aria-hidden="true"></i>

                <?php
                    if($numFriendRequests > 0)
                    {
                        echo '<span class = "notificationBadge" id = "unreadRequests">' . $numFriendRequests . '</span>';
                    }
                ?>
            </a>

            <!-- Settings icon -->
            <a href = "#">
                <i class="fa fa-cog fa-lg" aria-hidden="true"></i>
            </a>
            
            <a href="includes/handlers/logout.php">
                <i class = "fa fa-sign-out fa-lg" aria-hidden = "true"></i>
            </a>



        </nav>

        <div class = "dropdownDataWindow" style = "height: 0px; border: none;">
            <input type = "hidden" id = "dropdownDataType" value = "">
        </div>
    </div>

        <script>
            var userLoggedIn = '<?php echo $userLoggedIn;?>';
            var dropdownInProgress = false;

            $(".dropdownDataWindow").scroll(function()
            {
                var bottomElement = $(".dropdownDataWindow").last();

                var noMoreData = $(".dropdownDataWindow").find(".noMoreDropdownData").val();
                

                if(isElementInView(bottomElement[0]) && noMoreData == "false")
                {
                    loadComments();
                }
            });

            function loadComments()
            {
                if(dropdownInProgress)
                {
                    return;
                }

                dropdownInProgress = true;
                var page = $('.dropdownDataWindow').find('.nextPageDropdownData').val() || 1;
                var pageName; //Holds name of page to sent ajax request to
                var type = $('#dropdownDataType').val();
                alert(type);

                if(type == 'notification')
                {
                    pageName = "AjaxLoadNotifications.php";
                }

                else if(type == 'message')
                {
                    pageName = "AjaxLoadMessages.php";
                }


                $.ajax
                ({
                    url: "includes/handlers/" + pageName,
                    type: "POST",
                    data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
                    cache: false,

                    success: function(response)
                    {
                        $('.dropdownDataWindow').find('.nextPageDropdownData').remove(); //Removes current .nextPage
                        $('.dropdownDataWindow').find('.noMoreDropdownData').remove();

                        $('.dropdownDataWindow').append(response);

                        dropdownInProgress = false;
                    }
                });
            }

            //Check if the element is in view
            function isElementInView(el) 
            {
                var rect = el.getBoundingClientRect();
    
                return(
                    rect.top >= 0 &&
                    rect.left >= 0 &&
                    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
                    rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
                );
            }

                
                /*

                OLD SCROLLING: IGNORE

                $(document).ready(function()
                {
                    $(window).scroll(function()
                    {
                        var innerHeight = $('#dropdownDataWindow').innerHeight(); //Div containing data
                        var scrollTop = $('.dropdownDataWindow').scrollTop();
                        var page = $('.dropdownDataWindow').find('.nextPageDropdownData').val();
                        var noMoreData = $('.dropdownDataWindow').find('.noMoreDropdownData').val();

                        if((scrollTop + innerHeight >= $('.dropdownDataWindow')[0].scrollHeight) && noMoreData == 'false')
                        {
                            var pageName; //Holds name of page to send ajax request to
                            var type = $('#dropdownDataType').val();

                            if(type == 'notification')
                            {
                                pageName = "AjaxLoadNotifications.php";
                            }

                            else if(type == 'message')
                            {
                                pageName = "AjaxLoadMessages.php";
                            }

                            var ajaxRequest = $.ajax
                            ({
                                url: "includes/handlers/" + pageName,
                                type: "POST",
                                data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
                                cache: false,

                                success: function(response)
                                {
                                    $('.dropdownDataWindow').find('.nextPageDropdownData').remove(); //Removes current .nextPage
                                    $('.dropdownDataWindow').find('.noMoreDropdownData').remove();

                                    $('.dropdownDataWindow').append(response);
                                }
                            });
                        } //End if

                        return false;

                    }); //End (window).scroll(function())
                });
                */

        </script>

    <div class = "wrapper">
