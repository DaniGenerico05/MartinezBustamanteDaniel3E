<?php
include "../includes/config/conn.php";
$conn = connect();

$num = $_POST['num'];  
$motive = $_POST['motive'];  
$status = $_POST['status'];  


if (empty($motive)) {
    echo '<script type="text/javascript"> alert("Reason is required."); window.location.href="WProvider.php" </script>';
    exit();
}

// Escapar los valores para evitar inyecciones SQL
$num = $conn->real_escape_string($num);
$motive = $conn->real_escape_string($motive);
$status = $conn->real_escape_string($status);

$remove = "CALL sp_RemoveProvider('$num', '$motive', '$status')";

if ($conn->query($remove) === TRUE) {
    echo '<script type="text/javascript"> alert("Provider removed successfully"); window.location.href="WProvider.php" </script>';
} else {
    echo '<script type="text/javascript"> alert("Error removing provider: ' . $conn->error . '"); window.location.href="WProvider.php" </script>';
}

$conn->close();
?>
