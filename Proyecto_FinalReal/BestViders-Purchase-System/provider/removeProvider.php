<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Provider</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
        }

        .form-card {
            padding: 2rem;
        }

        .form-group label {
            color: #1a1a1a;
            font-weight: 600;
        }

        .form-control {
            border: 2px solid #eee;
            border-radius: 8px;
            margin-bottom: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #2c2c2c;
            box-shadow: none;
        }

        .button-container .button {
            background: #1a1a1a;
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            width: 100%;
            color: white;
            transition: background-color 0.3s ease;
        }

        .button-container .button:hover {
            background: #4b4848;
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
        }

        .modal-header {
            border-bottom: none;
        }

        .modal-footer {
            border-top: none;
        }
    </style>
</head>
<body>
    <?php
    include "../includes/config/conn.php";
    include "../includes/config/functions.php";
    $query_provider = "select num, fiscalName from provider";
    $conn = connect(); 

    $num = $_REQUEST['num'];

    $infoProvider=getProviderInfo($num);

    if (!$infoProvider) {
        exit("Proveedor no encontrado.");
    }
    ?>

    <div class="card-container">
        <a href="WProvider.php" class="return-btn">
            <i class="fas fa-arrow-left me-2"></i>Return
        </a>
        <div class="form-card">
            <h2 class="mb-4">Remove Provider</h2>
            <form id="removeProviderForm" method="POST">
                <div class="form-group">
                    <label for="num">Provider Number</label>
                    <input type="number" name="num" id="num" class="form-control" value="<?=$infoProvider['num']?>" readonly>
                </div>
                <div class="form-group">
                    <label for="fiscalName">Fiscal Name</label>
                    <input type="text" name="fiscalName" id="fiscalName" class="form-control" value="<?=$infoProvider['fiscalName']?>" readonly>
                </div>
                <div class="form-group">
                    <label for="motive">Reason for Removal</label>
                    <textarea name="motive" id="motive" class="form-control" placeholder="Write the reason to remove the provider" required></textarea>
                </div>
                <input type="hidden" name="status" value="0">

                <div class="button-container mt-4">
                    <button type="button" class="button" data-bs-toggle="modal" data-bs-target="#removeProviderModal" disabled>REMOVE</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="removeProviderModal" tabindex="-1" aria-labelledby="removeProviderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeProviderModalLabel">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to remove the provider?<br>
                    <strong>Provider Number:</strong> <?=$infoProvider['num']?><br>
                    <strong>Fiscal Name:</strong> <?=$infoProvider['fiscalName']?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmRemove">Yes, remove it!</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('confirmRemove').addEventListener('click', function () {
        const form = document.getElementById('removeProviderForm');
        const formData = new FormData(form);

        const motive = document.getElementById('motive').value;
        if (motive.trim() === '') {
            alert('Please provide a reason for removal.');
            return;
        }

        fetch('providerRMProcess.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert('Provider removed successfully!');
            window.location.href = 'WProvider.php';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('There was an issue removing the provider.');
        });
 
        const modal = bootstrap.Modal.getInstance(document.getElementById('removeProviderModal'));
        modal.hide();
    });

    document.getElementById('motive').addEventListener('input', function() {
        const motive = this.value;
        const removeButton = document.querySelector('.button');
        removeButton.disabled = motive.trim() === '';
    });

    document.addEventListener('DOMContentLoaded', function() {
        const motive = document.getElementById('motive').value;
        const removeButton = document.querySelector('.button');
        removeButton.disabled = motive.trim() === '';
    });
    </script>