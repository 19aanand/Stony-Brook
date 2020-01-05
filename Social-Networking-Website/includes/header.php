<?php  
    require 'config/config.php';

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

    <!-- CSS -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel = "stylesheet" type = "text/css" href = "assets/css/bootstrap.css">
    <link rel = "stylesheet" type = "text/css" href = "assets/css/style.css">
    

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
            <a href = "#">
                <i class="fa fa-envelope" aria-hidden="true"></i>
            </a>

            <!-- Notifications icon -->
            <a href = "#">
                <i class="fa fa-bell" aria-hidden="true"></i>
            </a>    

            <!-- Add friends icon -->
            <a href = "#">
                <i class="fa fa-user-plus" aria-hidden="true"></i>
            </a>

            <!-- Settings icon -->
            <a href = "#">
                <i class="fa fa-cog" aria-hidden="true"></i>
            </a>



        </nav>

    </div>

    <div class = "wrapper">
