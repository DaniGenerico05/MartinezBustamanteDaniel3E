<?php
require '../includes/config/conn.php';

$db = connect();

$area_query = "SELECT code, name FROM area";
$areas = mysqli_query($db, $area_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    $initialAmount = $_POST['initialAmount'];
    $budgetMonth = $_POST['budgetMonth'];
    $budgetYear = $_POST['budgetYear'];
    $area = $_POST['area'];

    $currentYear = date('Y');

    if ($budgetYear < $currentYear) {
        $error_message = "The budget year can not be before the current year.";
    } else {
        $currentDate = new DateTime();
        $budgetDate = new DateTime("$budgetYear-$budgetMonth-01");

        // Validación de fecha
        if ($budgetDate < $currentDate) {
            $error_message = "The selected date cannot be earlier than the current date.";
        } else {
    
            $code_check_query = "SELECT COUNT(*) FROM budget WHERE code = ?";
            $stmt = mysqli_prepare($db, $code_check_query);
            mysqli_stmt_bind_param($stmt, 's', $code);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $count);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            if ($count > 0) {
                $error_message = "The budget code already exists. Please enter another code.";
            } else {
               
                $area_check_query = "SELECT COUNT(*) FROM budget WHERE budgetYear = ? AND budgetMonth = ? AND area = ?";
                $stmt = mysqli_prepare($db, $area_check_query);
                mysqli_stmt_bind_param($stmt, 'iis', $budgetYear, $budgetMonth, $area);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $count);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);

                if ($count > 0) {
                    $error_message = "There is already a budget for this month, year and area. Please verify.";
                } else {
                   
                    $stmt = mysqli_prepare($db, "CALL Sp_RegistrarBudget(?, ?, ?, ?, ?)");
                    if (!$stmt) {
                        die('Error preparando la consulta: ' . mysqli_error($db));
                    }

                    mysqli_stmt_bind_param($stmt, 'sdiss', $code, $initialAmount, $budgetMonth, $budgetYear, $area);

                    if (mysqli_stmt_execute($stmt)) {
                        $result = mysqli_stmt_get_result($stmt);
                        $row = mysqli_fetch_assoc($result);
                        $message = $row['message'];
                        if (strpos($message, 'Error') === false) {
                            $success_message = $message;
                        } else {
                            $error_message = $message;
                        }
                    } else {
                        $error_message = "Error: " . mysqli_stmt_error($stmt);
                    }

                    mysqli_stmt_close($stmt);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Budget</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
            color: #fff;
            text-decoration: none;
        }

        .form-card {
            padding: 2rem;
        }

        .button-container {
            margin-top: 2rem;
            text-align: center;
        }

        .submit-btn {
            background: #1a1a1a;
            color: #fff;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background: #333;
        }

        .error-message {
            color: #dc3545;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        label {
            font-weight: bold;
            color: #333;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: -10px;
        }

        .col {
            flex: 1;
            padding: 10px;
            min-width: 200px;
        }
    </style>
</head>
<body>
    <div class="card-container">
        <a href="../index.php" class="return-btn">Return</a>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <h2 style="text-align: center; margin-bottom: 30px;">Budget Information</h2>
            
            <form id="budgetForm" method="POST">
                <div class="row">
                    <div class="col">
                        <label for="code">Budget Code</label>
                       
                        <input type="text" name="code" id="code" 
                               value="<?php echo isset($_POST['code']) ? htmlspecialchars($_POST['code']) : ''; ?>"
                               placeholder="Enter budget code" required>
                    </div>

                    <div class="col">
                        <label for="initialAmount">Initial Amount</label>
                        <input type="number" step="0.01" name="initialAmount" id="initialAmount" 
                               value="<?php echo isset($_POST['initialAmount']) ? htmlspecialchars($_POST['initialAmount']) : ''; ?>"
                               placeholder="Enter initial amount" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <label for="budgetMonth">Budget Month</label>
                        <select name="budgetMonth" id="budgetMonth" required>
                            <option value="" disabled <?php echo !isset($_POST['budgetMonth']) ? 'selected' : ''; ?>>Select month</option>
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo (isset($_POST['budgetMonth']) && $_POST['budgetMonth'] == $i) ? 'selected' : ''; ?>>
                                    <?php echo date('F', mktime(0, 0, 0, $i, 1)); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col">
                        <label for="budgetYear">Budget Year</label>
                        <input type="number" name="budgetYear" id="budgetYear" 
                               value="<?php echo isset($_POST['budgetYear']) ? htmlspecialchars($_POST['budgetYear']) : date('Y'); ?>"
                               placeholder="Enter budget year" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <label for="area">Area</label>
                        <select name="area" id="area" required>
                            <option value="" disabled <?php echo !isset($_POST['area']) ? 'selected' : ''; ?>>Select area</option>
                            <?php 
                            mysqli_data_seek($areas, 0);
                            while($area = mysqli_fetch_assoc($areas)): 
                                $selected = (isset($_POST['area']) && $_POST['area'] == $area['code']) ? 'selected' : '';
                            ?>
                                <option value="<?php echo htmlspecialchars($area['code']); ?>" <?php echo $selected; ?>>
                                    <?php echo htmlspecialchars($area['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="button-container">
                    <button type="submit" class="submit-btn">Add New Budget</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Success</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php echo isset($success_message) ? $success_message : ''; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='../index.php'">Return to Home</button>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('budgetForm').reset();" data-dismiss="modal">Add Another Budget</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (isset($success_message)): ?>
    <script>
        $(document).ready(function() {
            $('#successModal').modal('show');
        });
    </script>
    <?php endif; ?>
</body>
</html>
