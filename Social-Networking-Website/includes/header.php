<?php  
    require 'config/config.php';
    include("includes/classes/User.php");
    include("includes/classes/Post.php");
    include("includes/classes/Message.php");

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

        <nav>
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
            </a>

            <!-- Notifications icon -->
            <a href = "#">
                <i class="fa fa-bell fa-lg" aria-hidden="true"></i>
            </a>    

            <!-- Add friends icon -->
            <a href = "Requests.php">
                <i class="fa fa-users fa-lg" aria-hidden="true"></i>
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

    <div class = "wrapper">
