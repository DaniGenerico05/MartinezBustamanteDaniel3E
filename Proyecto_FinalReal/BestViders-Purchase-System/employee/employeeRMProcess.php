<?php
include "../includes/config/conn.php"; // Asegúrate de que este archivo tiene la conexión correcta
$conn = connect(); // Establece la conexión con la base de datos

// Verificar si los datos han sido enviados por POST
if (isset($_POST['num']) && isset($_POST['status'])) {
    $num = intval($_POST['num']); // Recibimos el num
    $status = intval($_POST['status']); // Recibimos el status (0 para inactivo)
} else {
    exit('Error: Missing num or status parameters.');
}

// Depuración: Verificar valores antes de continuar
if (!is_int($num) || !is_int($status)) {
    exit('Error: Invalid num or status parameters.');
}

// Llamada al procedimiento almacenado para remover el empleado
$remove = "CALL sp_RemoveEmployee('$num', '$status')";

if ($conn->query($remove) === TRUE) {
    // Confirmación de éxito
    echo '<script type="text/javascript"> 
            alert("Employee removed successfully."); 
            window.location.href="WEmployees.php"; 
          </script>';
} else {
    // Manejar error de la consulta
    echo '<script type="text/javascript"> 
            alert("Error removing employee: ' . $conn->error . '"); 
            window.location.href="WEmployees.php"; 
          </script>';
}
// Cerrar la conexión
$conn->close();
?>
