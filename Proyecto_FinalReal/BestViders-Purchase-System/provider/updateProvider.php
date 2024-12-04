<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Provider</title>
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
    </style>
</head>
    <?php
    include "../includes/config/conn.php";
    include "../includes/config/functions.php";
    $query_provider = "select num, fiscal_name from provider";
    $conn = connect(); 

    $num = $_REQUEST['num'];

    $infoProvider = getProviderInfo($num);

    if (!$infoProvider) {
        exit("Provider not found.");
    }
    ?> 

    <div class="card-container">
        <a href="../index.php" class="return-btn">
            <i class="fas fa-arrow-left me-2"></i>Return
        </a>
        <div class="form-card">
            <form action="providerUProcess.php" method="POST">
                <h2 class="mb-4">Modify Provider</h2>

                <div class="form-group">
                    <label for="num">Provider Number</label>
                    <input type="number" name="num" id="num" class="form-control" value="<?=$infoProvider['num']?>" readonly>
                </div>

                <div class="form-group">
                    <label for="fiscalName">Fiscal Name</label>
                    <input type="text" name="fiscalName" id="fiscalName" class="form-control" value="<?=$infoProvider['fiscalName']?>" readonly>
                </div>

                <div class="form-group">
                    <label for="numTel">Phone Number</label>
                    <input type="text" name="numTel" id="numTel" class="form-control" placeholder="<?=$infoProvider['numTel']?>">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="<?=$infoProvider['email']?>">
                </div>

                <div class="button-container mt-4">
                    <button type="submit" class="button">MODIFY</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>