<div class="collapse navbar-collapse justify-content-end" id="navbarNav">
    <ul class="navbar-nav">
    <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="requestsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Budget and Invoice
            </a>
            <ul class="dropdown-menu" aria-labelledby="requestsDropdown">
                <li><a class="dropdown-item" href="invoice/WInvoice.php">History Invoice</a></li>
                <li><a class="dropdown-item" href="budget/WBudget.php">Budgets</a></li>
            </ul>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="ordersDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Orders
            </a>
            <ul class="dropdown-menu" aria-labelledby="ordersDropdown">
                <li><a class="dropdown-item" href="order/WOrder_toAprove.php">Pending Orders</a></li>
                <li><a class="dropdown-item" href="order/WOrder_aprov.php">Approved Orders</a></li>
                <li><a class="dropdown-item" href="order/WOrder_reject.php">Reject Orders</a></li>
                <li><a class="dropdown-item" href="order/WOrder_comp.php">Complete Orders</a></li>
            </ul>
        </li>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="requestsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Resquest
            </a>
            <ul class="dropdown-menu" aria-labelledby="requestsDropdown">
                <li><a class="dropdown-item" href="request/WRequest.php">Check Requests</a></li>
                <li><a class="dropdown-item" href="request/createRequest.php">Create Request</a></li>
            </ul>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="providersDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Providers
            </a>
            <ul class="dropdown-menu" aria-labelledby="providersDropdown">
                <li><a class="dropdown-item" href="provider/createProvider.php">Add Provider</a></li>
                <li><a class="dropdown-item" href="provider/WProvider.php">Check All Providers</a></li>
                <li><a class="dropdown-item" href="provider/WProviderRM.php">Check Removed Providers</a></li>
                <li><a class="dropdown-item" href="provider/WAssocProvider.php">Check Associated Providers</a></li>
            </ul>
        </li>
                <!-- Logout Button -->
                <li class="nav-item">
            <a class="nav-link btn btn-outline-danger ms-3" href="logout.php" role="button">
                Logout
            </a>
        </li>
    </ul>
</div>