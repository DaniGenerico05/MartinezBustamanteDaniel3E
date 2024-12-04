<?php
session_start();
if (!isset($_SESSION['num'])) {
    header("Location: login.php");
    exit();
}
?>
    <section id="main">
        <?php
        switch ($_SESSION['role']) {
            case 'RH':
                include "includes/home/rh_home.php";
                break;
            case 'PR':
                include "includes/home/pr_home.php";
                break;
            case 'ST':
                include "includes/home/st_home.php";
                break;
            default:
                include "includes/home/home.php";
                break;
        }
        ?>
    </section>
    <?php include "includes/footer.php"; ?>
