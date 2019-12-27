<?php  
    $con = mysqli_connect("localhost", "root", "", "social");

    if(mysqli_connect_errno())
    {
        echo "Failed to connect: " . mysqli_connect_errno();
    }

    $query = mysqli_query($con, "INSERT INTO test VALUES(NULL, 'VishySwishy')");

?>


<html>

<head>
    <title>Homepage</title>
</head>

<body>
    Hello Adit!
</body>

</html>