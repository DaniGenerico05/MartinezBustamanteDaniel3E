<?php
include "../includes/config/conn.php";
$db = connect();

$monthsQuery = "SELECT DISTINCT budgetMonth, budgetYear FROM budget ORDER BY budgetYear DESC, budgetMonth DESC";
$monthsResult = mysqli_query($db, $monthsQuery);
$months = [];
while ($row = mysqli_fetch_assoc($monthsResult)) {
    $months[] = $row;
}

$currentMonth = date('n');
$currentYear = date('Y');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Charts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            min-height: 100vh;
            background-image: url('https://4kwallpapers.com/images/wallpapers/macos-monterey-stock-black-dark-mode-layers-5k-4480x2520-5889.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            padding: 2rem;
            color: #fff;
        }

        .content-wrapper {
            background: rgba(0, 0, 0, 0.8);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin: 0 auto;
            max-width: 1400px;
        }

        .chart-container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
        }

        .return-btn {
            display: inline-block;
            background: #fff;
            color: #000;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            margin-bottom: 1.5rem;
        }

        .date-navigation {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .nav-btn {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
        }

        .nav-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .date-display {
            font-size: 1.2rem;
            font-weight: 500;
            min-width: 200px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="content-wrapper">
        <a href="../index.php" class="return-btn">Return</a>

        <div class="chart-container">
            <h3>Budget for Area</h3>
            <div style="height: 400px;">
                <canvas id="budgetChart"></canvas>
            </div>
        </div>

        <div class="date-navigation">
            <button id="prevMonth" class="nav-btn">←</button>
            <div id="currentDate" class="date-display"></div>
            <button id="nextMonth" class="nav-btn">→</button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let budgetChart;
        let currentMonthIndex = 0;
        const months = <?php echo json_encode($months); ?>;
        
        // Find index of current month
        currentMonthIndex = months.findIndex(m => 
            m.budgetMonth == <?php echo $currentMonth; ?> && 
            m.budgetYear == <?php echo $currentYear; ?>
        );
        if (currentMonthIndex === -1) currentMonthIndex = 0;

        function updateChart() {
            const month = months[currentMonthIndex];
            const monthName = new Date(month.budgetYear, month.budgetMonth - 1).toLocaleString('default', { month: 'long' });
            document.getElementById('currentDate').textContent = `${monthName} ${month.budgetYear}`;
            
            // Update navigation buttons
            document.getElementById('prevMonth').disabled = currentMonthIndex >= months.length - 1;
            document.getElementById('nextMonth').disabled = currentMonthIndex <= 0;
            
            $.ajax({
                url: 'get_budget_data.php',
                method: 'GET',
                data: { month: month.budgetMonth, year: month.budgetYear },
                success: function(response) {
                    const data = JSON.parse(response);
                    renderChart(data);
                }
            });
        }

        function renderChart(data) {
            const ctx = document.getElementById('budgetChart').getContext('2d');
            
            if (budgetChart) {
                budgetChart.destroy();
            }

            budgetChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.area_name),
                    datasets: [
                        {
                            label: 'Initial Amount',
                            data: data.map(item => item.initialAmount),
                            backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        },
                        {
                            label: 'Remaining Amount',
                            data: data.map(item => item.budgetRemain),
                            backgroundColor: 'rgba(75, 192, 192, 0.8)',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                },
                                color: '#fff'
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#fff'
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: '#fff'
                            }
                        }
                    }
                }
            });
        }

        document.getElementById('prevMonth').addEventListener('click', () => {
            if (currentMonthIndex < months.length - 1) {
                currentMonthIndex++;
                updateChart();
            }
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            if (currentMonthIndex > 0) {
                currentMonthIndex--;
                updateChart();
            }
        });

        // Initial load
        document.addEventListener('DOMContentLoaded', function() {
            updateChart();
        });
    </script>
</body>
</html>