<?php
require 'includes/config/conn.php';
session_start();

$showPasswordChangeModal = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = connect();

    if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];
        $num = $_SESSION['num'];

        if (strlen($newPassword) < 5) {
            $error = "The new password must be at least 5 characters long.";
        } elseif ($newPassword === $confirmPassword) {
            $query = "UPDATE user SET password = ? WHERE num = ?";
            $stmt = mysqli_prepare($db, $query);
            mysqli_stmt_bind_param($stmt, "ss", $newPassword, $num);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                $success = "Password changed successfully. Please log in again.";
                session_destroy();
            } else {
                $error = "There was an error changing the password. Please try again.";
            }
        } else {
            $error = "Passwords do not match.";
        }
    } else {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $query = "SELECT *
                    FROM vw_employee_user
                    WHERE email = ?";

        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if ($password === $user['password']) { // Direct password comparison
                if ($user['status'] != 1) {  // Validate that the status is 1
                    $error = "Your account is not active. Please contact support.";
                } else {
                    $_SESSION['user_name'] = $user['firstName'] . ' ' . $user['lastName'];
                    $_SESSION['num'] = $user['num'];
                    $_SESSION['role'] = $user['area'];
        
                    if ($password === "1234567890") {
                        $showPasswordChangeModal = true;
                    } else {
                        header("Location: index.php");
                        exit();
                    }
                }
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }
    }
    mysqli_close($db);
}
?>


<title>BESTVIDERS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="includes/css/login.css">
<section class="login-container">
    <div class="login-card">
        <div class="login-image">
            <h3 class="mb-4">Welcome to BESTVIDERS</h3>
            <p class="mb-4">Your trusted logistics partner for efficient and reliable delivery services.</p>
        </div>
        
        <div class="login-form">
            <h2 class="text-center mb-4">BESTVIDERS</h2>
            
            <?php if (isset($error)) : ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (isset($success)) : ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" 
                        placeholder="Enter your email address" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" 
                        placeholder="Enter your password" required>
                </div>

                <button type="submit" class="btn btn-login">Login</button>
            </form>
        </div>
    </div>
</section>

<!-- Modal to Change Password -->
<div class="modal fade" id="passwordChangeModal" tabindex="-1" aria-labelledby="passwordChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordChangeModalLabel">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="change_password">
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="newPassword" required minlength="5">
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required minlength="5">
                    </div>
                    <button type="submit" class="btn btn-change-password">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    <?php if ($showPasswordChangeModal): ?>
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('passwordChangeModal'));
        myModal.show();
    });
    <?php endif; ?>
</script>
</body>
</html>