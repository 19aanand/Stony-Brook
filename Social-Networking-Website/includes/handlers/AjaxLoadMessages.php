<?php
    include("../../config/config.php");
    include("../classes/User.php");
    include("../classes/Message.php");

    $limit = 7; //Number of messages to load
    $messageObject = new Message($con, $_REQUEST['user']);

    echo $messageObject->getConversationsDropdown($_REQUEST, $limit);

?>