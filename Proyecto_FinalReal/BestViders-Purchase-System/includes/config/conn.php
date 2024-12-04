<?php 
function connect(): mysqli {
    $db = mysqli_connect("127.0.0.1", "root", "123_456", "bestviders");

    if (!$db) {
        die("Connection failed: " . mysqli_connect_error());
    } else {
        //echo "Connected successfully";
    }
    return $db;
}
?>