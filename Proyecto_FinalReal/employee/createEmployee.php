<?php
require '../includes/config/conn.php';

$db = connect();
$charge_query = "SELECT code, name FROM charge";
$charges = mysqli_query($db, $charge_query);

$area_query = "SELECT code, name FROM area";
$areas = mysqli_query($db, $area_query); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $surName = $_POST['surName'];
    $numTel = $_POST['numTel'];
    $email = $_POST['email'];
    $charge = $_POST['charge'];
    $area = $_POST['area'];

    $check_query = "SELECT * FROM employee WHERE email = ? OR numTel = ?";
    $stmt_check = mysqli_prepare($db, $check_query);

    if (!$stmt_check) {
        die('Error doing the query: ' . mysqli_error($db));
    }

    mysqli_stmt_bind_param($stmt_check, 'ss', $email, $numTel);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        $error_message = "Email or phone number already in use.";
    } else {
        $stmt = mysqli_prepare($db, "CALL Sp_RegistrarEmpleado(?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            die('Error doing the query: ' . mysqli_error($db));
        }

        mysqli_stmt_bind_param($stmt, 'sssssss', $firstName, $lastName, $surName, $numTel, $email, $charge, $area);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                    alert('Registration successful');
                    if (confirm('Do you want to register another employee?')) {
                        document.getElementById('employeeForm').reset(); // reset form if user wants to add another
                        window.location.reload(); // reload page to maintain form state
                    } else {
                        window.location.href = '../index.php'; // redirect to main page
                    }
                </script>";
        } else {
            echo "Error: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_stmt_close($stmt_check);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add new Employee</title>
    <style>
        body {
            min-height: 100vh;
            background-image: url('https://4kwallpapers.com/images/wallpapers/macos-monterey-stock-black-dark-mode-layers-5k-4480x2520-5889.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: Arial, sans-serif;
        }

        .card-container {
            width: 100%;
            max-width: 800px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 1rem;
        }

        .return-btn {
            background: #1a1a1a;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            margin: 20px;
            transition: background-color 0.3s ease;
        }

        .return-btn:hover {
            background: #333;
        }

        .form-card {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }

        .button-container {
            margin-top: 2rem;
        }

        button {
            background: #1a1a1a;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background: #333;
        }
    </style>
</head>
<body>
    <div class="card-container">
        <a href="../index.php" class="return-btn">
            <i class="fas fa-arrow-left me-2"></i>Return
        </a>
        <div class="form-card">
            <!-- Mostrar el mensaje de error si existe -->
            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form id="employeeForm" method="POST">
                <h2>Employee Information</h2>

                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" name="firstName" id="firstName" placeholder="Enter first name" pattern="[A-Za-z]+" minlength="2" required value="<?php echo isset($_POST['firstName']) ? $_POST['firstName'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" name="lastName" id="lastName" placeholder="Enter last name" pattern="[A-Za-z]+" minlength="2" required value="<?php echo isset($_POST['lastName']) ? $_POST['lastName'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="surName">Second Last Name</label>
                    <input type="text" name="surName" id="surName" placeholder="Enter second last name" pattern="[A-Za-z]+" minlength="2" required value="<?php echo isset($_POST['surName']) ? $_POST['surName'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="numTel">Phone Number</label>
                    <input type="text" name="numTel" id="numTel" placeholder="Enter phone number" pattern="[0-9]+" maxlength="10" minlength="10" required value="<?php echo isset($_POST['numTel']) ? $_POST['numTel'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="Enter email address" required value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="charge">Charge</label>
                    <select name="charge" id="charge" required>
                        <option value="" disabled selected>Select charge</option>
                        <?php while($charge = mysqli_fetch_assoc($charges)): ?>
                            <option value="<?php echo htmlspecialchars($charge['code']); ?>" <?php echo (isset($_POST['charge']) && $_POST['charge'] == $charge['code']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($charge['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="area">Area</label>
                    <select name="area" id="area" required>
                        <option value="" disabled selected>Select area</option>
                        <?php while($area = mysqli_fetch_assoc($areas)): ?>
                            <option value="<?php echo htmlspecialchars($area['code']); ?>" <?php echo (isset($_POST['area']) && $_POST['area'] == $area['code']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($area['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="button-container">
                    <button type="submit">Add Employee</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
