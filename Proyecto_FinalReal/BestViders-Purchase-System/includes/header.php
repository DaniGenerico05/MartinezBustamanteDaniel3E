<?php
if (!isset($_SESSION['num'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BestViders - Human Resources</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hover-card {
            transition: transform 0.3s ease-in-out;
        }
        .hover-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <header class="bg-dark text-white py-3">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <a class="navbar-brand fs-1 fw-bold me-auto" href="#">BestViders</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <?php
                    switch ($_SESSION['role']) {
                        case 'RH':
                            include "menus/rh_menu.php";
                            break;
                        case 'PR':
                            include "menus/pur_menu.php";
                            break;
                        case 'ST':
                            include "menus/st_menu.php";
                            break;
                        default:
                            include "menus/home_menu.php";
                            break;
                    }
                    ?>
                </div>
            </nav>
        </div>
    </header>