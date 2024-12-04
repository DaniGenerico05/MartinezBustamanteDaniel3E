<?php
include "../includes/config/conn.php";
$conn = connect();

// Obtener los parámetros enviados
$num = $_POST['num'];  
$status = $_POST['status'];  

// Escapar los valores para prevenir inyección SQL
$num = $conn->real_escape_string($num);
$status = $conn->real_escape_string($status);

// Llamada al procedimiento almacenado para recontratar al empleado
$rehire = "CALL sp_RehireEmployee('$num','$status')";

if ($conn->query($rehire) === TRUE) {
    echo '<script type="text/javascript"> 
            alert("Employee rehired successfully."); 
            window.location.href="WEmployees.php"; 
          </script>';
} else {
    echo '<script type="text/javascript"> 
            alert("Error rehiring employee: ' . $conn->error . '"); 
            window.location.href="WEmployees.php"; 
          </script>';
}

$conn->close();
?>
