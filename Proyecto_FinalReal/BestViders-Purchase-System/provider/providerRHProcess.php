<?php
include "../includes/config/conn.php";
$conn = connect();

$num = $_POST['num'];  
$status = $_POST['status'];     
$num = $conn->real_escape_string($num);
$status = $conn->real_escape_string($status);

$remove = "CALL sp_RehireProvider('$num','$status')";

if ($conn->query($remove) === TRUE) {
    echo '<script type="text/javascript"> alert("Provider rehired successfully"); window.location.href="WProvider.php" </script>';
} else {
    echo '<script type="text/javascript"> alert("Error rehiring provider: ' . $conn->error . '"); window.location.href="WProvider.php" </script>';
}

$conn->close();
?>
