<?php
require 'includes/config/conn.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = connect();
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];
    $num = $_SESSION['num'];
    if ($newPassword === $confirmPassword) {
        $query = "UPDATE user SET password = '$newPassword' WHERE num = '$num'";
        $result = mysqli_query($db, $query);
        if ($result) {
            echo "<script>
                    alert('Cambio de contraseña exitoso. Favor de volver a iniciar sesión.');
                    window.location.href = 'login.php';
                  </script>";
            header("Location: login.php");
            exit();
        } else {
            $error = "There was an error changing the password. Please try again.";
        }
    } else {
        $error = "Passwords do not match.";
    }
    mysqli_close($db);
}
?>
<section id="login-cont">
    <link rel="stylesheet" href="includes/css/change.css">
    <div id="login-card">
        <h2 id="logo-text">BESTVIDERS</h2>
        <div id="imglogin"> 
            <img class="loginimg" src="includes/images/logo.jpeg" alt="User icon"/>
        </div>
        <?php if (isset($error)) : ?>
            <p class="error-message"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <div id="formLogin">
                <div class="input-group">
                    <label for="newPassword">New Password</label>
                    <input type="password" id="newPassword" name="newPassword" placeholder="Enter your new password" required>
                </div>
                <div class="input-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your new password" required>
                </div>
            </div>   
            <div id="divbtn"> 
                <button id="btnlog" type="submit">Change Password</button>
            </div>
        </form> 
    </div>
</section>
