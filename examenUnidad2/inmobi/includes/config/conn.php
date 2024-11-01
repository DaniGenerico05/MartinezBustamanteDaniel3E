<?php 
    function connect(): mysqli{
        $db = mysqli_connect("localhost", "root", "", "bienesraices",null,"/var/run/mysqld/mysqld.sock");
        
        // Check if the connection was successful
        if (!$db) {
            die("Connection failed: " . mysqli_connect_error());
        } else {
            echo "Connected successfully!";
        }

        return $db;
    }
?>
