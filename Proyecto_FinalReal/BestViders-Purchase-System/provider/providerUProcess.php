<?php
include "../includes/config/conn.php";
$conn = connect();

$num = $_POST['num'];
$numTel = $_POST['numTel'];
$email = $_POST['email'];



$update = "update provider SET numTel = '$numTel', email = '$email' where num = $num";

if($conn->query($update) === TRUE){
    echo '<script type="text/javascript"> alert("Updated Succesfully"); window.location.href="WProvider.php" </script>';
}else{
    echo '<script type="text/javascript"> alert("Error Updating the provider"); window.location.href="WProvider.php" </script>';
}


$conn ->close();
?>