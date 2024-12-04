<div class="collapse navbar-collapse justify-content-end" id="navbarNav">
    <ul class="navbar-nav">
        <!-- Dropdown Orders -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="ordersDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Budgets
            </a>
            <ul class="dropdown-menu" aria-labelledby="ordersDropdown">
            <li><a class="dropdown-item" href="budget/createBudget.php">Add New Budget</a></li>
            <li><a class="dropdown-item" href="budget/WBudget.php">Check Budgets</a></li>
            </ul>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="employeesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Employees
            </a>
            <ul class="dropdown-menu" aria-labelledby="employeesDropdown">
                <li><a class="dropdown-item" href="employees/WEmployees.php">History Employees</a></li>
                <li><a class="dropdown-item" href="employees/inactive_Employees.php">Inactive Employees</a></li>
                <li><a class="dropdown-item" href="employees/active_Employees.php">Active Employees</a></li>
                <li><a class="dropdown-item" href="employees/createEmployee.php">Add Employee</a></li>
            </ul>
        <li class="nav-item">
            <a class="nav-link btn btn-outline-danger ms-3" href="logout.php" role="button">
                Logout
            </a>
        </li>
    </ul>
</div>
