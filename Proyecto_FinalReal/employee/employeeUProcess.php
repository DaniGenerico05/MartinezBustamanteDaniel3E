<?php
include "../includes/config/conn.php";
$conn = connect();

$num = $_POST['num'];
$numTel = $_POST['numTel'];
$email = $_POST['email'];


$query = "SELECT numTel, email FROM employee WHERE num = ?";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Error preparing the query: " . $conn->error);
}

$stmt->bind_param("i", $num);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $employee = $result->fetch_assoc();

    $numTel = empty($numTel) ? $employee['numTel'] : $numTel;
    $email = empty($email) ? $employee['email'] : $email;
} else {
    echo '<script type="text/javascript"> 
            alert("Employee not found"); 
            window.location.href = "WEmployees.php"; 
          </script>';
    exit();
}

$update = "UPDATE employee SET numTel = ?, email = ? WHERE num = ?";
$stmt = $conn->prepare($update);
if ($stmt === false) {
    die("Error preparing the query: " . $conn->error);
}

$stmt->bind_param("ssi", $numTel, $email, $num);

if ($stmt->execute()) {
    echo '<script type="text/javascript"> 
            alert("Updated Successfully"); 
            window.location.href = "WEmployees.php"; 
          </script>';
} else {
    echo '<script type="text/javascript"> 
            alert("Error Updating the employee: ' . $stmt->error . '"); 
            window.location.href = "WEmployees.php"; 
          </script>';
}

$stmt->close();
$conn->close();
?>
