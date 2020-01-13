<?php
    include("includes/header.php");

    $messageObject = new Message($con, $userLoggedIn);
    $row;

    $query2 = mysqli_query($con, "SELECT * FROM messages WHERE 
        user_from = '$userLoggedIn' ORDER BY id DESC LIMIT 1");
    $userToProper = mysqli_fetch_array($query2)['user_to'];

    

    if(isset($_REQUEST['u']))
    {
        $userTo = $_REQUEST['u'];
        $query = mysqli_query($con, "INSERT INTO messages VALUES('', '$userTo', '$userLoggedIn',
            '', '', '', '', '')");

    }

    else
    {
        $userTo = $messageObject->getMostRecentUser();

        if($userTo == false) //This if statement is true if the user logged in has not started a conversation with anyone
        {
            $userTo = "new";
        }    
    }

    if($userTo != "new")
    {

        $userToObject = new User($con, $userTo);
    }


    if(isset($_POST['postMessage']))
    {
        if(isset($_POST['messageBody']))
        {
            $body = mysqli_real_escape_string($con, $_POST['messageBody']);
            $date = date("Y-m-d H:i:s");
            
            $query3 = mysqli_query($con, "SELECT * FROM messages WHERE user_from = '$userLoggedIn' 
                AND user_to = '$userToProper' ORDER BY id DESC LIMIT 1");
            $userToInsert = mysqli_fetch_array($query3)['user_to'];

            $messageObject->sendMessage($userToInsert, $body, $date);  
            
            header("Location: messages.php?u=" . $userToInsert);
        }
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

<div class = "mainColumn column" id = "mainColumnMessages">
    
    <?php
        $query4 = mysqli_query($con, "SELECT * FROM messages WHERE user_to = '$userTo' AND
            user_from = '$userLoggedIn' ORDER BY id DESC LIMIT 1");
        $usernameHolder = mysqli_fetch_array($query4)['user_to'];
        $userToObject = new User($con, $usernameHolder);

        if($userTo != "new")
        {  
            echo "<h4>You and <a href = '$userTo'>" . $userToObject->getFirstAndLastName() . "</a></h4><hr><br>";            
        
            echo "<div class = 'loadedMessages' id = 'scrollMessages'>";
                echo $messageObject->getMessages($usernameHolder);
            echo "</div>";
        }

        else
        {
            echo "<h4>New message</h4>";
        }
    ?>

        

    <div class = "messagePost" id = "mainColumnMessages">

        <form action = "messages.php" method = "POST">
            <?php
                if($userTo == "new")
                {
                    echo "Select the friend you would like to message.<br><br>";
                    ?>

                    To: <input type = 'text' onkeyup = 'getUsers(this.value, "<?php echo $userLoggedIn; ?>")' name = 'q' placeholder = 'Name' autocomplete = 'off' id = 'searchTextInput'> 
                    
                    <?php
                    echo "<div class = 'results'></div>";
                }

                else
                {
                    echo "<br><textarea name = 'messageBody' id = 'messageTextarea' placeholder = 'Write your message.'></textarea>";
                    echo "<input type = 'submit' name = 'postMessage' class = 'info' id = 'messageSubmit' value = 'Send'>";
                }

            ?>
        </form>
    </div>

    <script>
        var div = document.getElementById("scrollMessages");
        
        if(div != null) 
        {
            div.scrollTop = div.scrollHeight;
        }
    </script>

</div>

<div class = "userDetails column" id = "conversations">
        <h4>Conversations</h4>

        <div class = "loadedConversations" style = "max-height: 225px; overflow: scroll;">
            <?php
                echo $messageObject->getConversations();
            ?>
        </div>
        <br>
        <a href = "messages.php?u=new">New Message</a>
</div>