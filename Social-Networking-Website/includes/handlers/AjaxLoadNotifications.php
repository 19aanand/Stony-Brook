<?php
    include("../../config/config.php");
    include("../classes/User.php");
    include("../classes/Notification.php");

    $limit = 6; //Number of messages to load
    $notificationObject = new Notification($con, $_REQUEST['user']);

    echo $notificationObject->getNotifications($_REQUEST, $limit);

?>