<?php
session_start();
include('../config/db.php');

// Query to fetch row counts for each table
$query = "SELECT 
    (SELECT COUNT(*) FROM cart) AS cart_count,
    (SELECT COUNT(*) FROM manage_order) AS manage_order_count,
    (SELECT COUNT(*) FROM menu_sections) AS menu_sections_count,
    (SELECT COUNT(*) FROM occasion) AS occasion_count,
    (SELECT COUNT(*) FROM reservations) AS reservations_count,
    (SELECT COUNT(*) FROM users) AS users_count";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Management Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f4f8; /* Light background for neumorphism */
            margin: 0;
            padding: 0;
        }

        .main-content {
            padding: 20px;
        }

        .main-content h1 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #333;
        }

        /* Stats Container */
        .stats-container {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .stat-card {
            flex: 1;
            border-radius: 12px;
            box-shadow: 10px 10px 20px rgba(0, 0, 0, 0.1), -10px -10px 20px rgba(255, 255, 255, 0.7); /* Neumorphism shadow */
            padding: 20px;
            text-align: center;
            min-width: 200px;
            transition: all 0.3s ease-in-out;
            color: #fff;
            font-weight: 700; /* Bolder text for emphasis */
        }

        .stat-card:hover {
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.2), -5px -5px 15px rgba(255, 255, 255, 0.8);
        }

        .stat-card h2 {
            font-size: 1.4rem;
        }

        .stat-card p {
            font-size: 2rem;
            margin-top: 10px;
        }

        /* Individual Stat Card Backgrounds */
        .cart-card { background-color: #ff6347; } /* Tomato */
        .manage-order-card { background-color: #4caf50; } /* Green */
        .menu-sections-card { background-color: #2196f3; } /* Blue */
        .occasion-card { background-color: #ff9800; } /* Orange */
        .reservations-card { background-color: #9c27b0; } /* Purple */
        .users-card { background-color: #009688; } /* Teal */

        /* Charts Container */
        .charts-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }

        .chart {
            flex: 1;
            max-width: 100%;
            background: #e0e5ec; /* Neumorphism background for charts */
            border-radius: 12px;
            box-shadow: 10px 10px 20px rgba(0, 0, 0, 0.1), -10px -10px 20px rgba(255, 255, 255, 0.7); /* Neumorphism shadow */
            padding: 20px;
            height: 400px;
            transition: all 0.3s ease-in-out;
        }

        .chart:hover {
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.2), -5px -5px 15px rgba(255, 255, 255, 0.8);
        }


        canvas{
            width: 50%;
            height: 50%;
        }
        /* Add responsiveness for smaller screens */
        @media (max-width: 768px) {
            .charts-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <h1>Welcome, Admin</h1>
        <div class="stats-container">
            <!-- Stats Cards with Backgrounds -->
            <div class="stat-card cart-card">
                <h2>Cart</h2>
                <p><?php echo $data['cart_count']; ?></p>
            </div>
            <div class="stat-card manage-order-card">
                <h2>Manage Orders</h2>
                <p><?php echo $data['manage_order_count']; ?></p>
            </div>
            <div class="stat-card menu-sections-card">
                <h2>Menu Sections</h2>
                <p><?php echo $data['menu_sections_count']; ?></p>
            </div>
            <div class="stat-card occasion-card">
                <h2>Occasions</h2>
                <p><?php echo $data['occasion_count']; ?></p>
            </div>
            <div class="stat-card reservations-card">
                <h2>Reservations</h2>
                <p><?php echo $data['reservations_count']; ?></p>
            </div>
            <div class="stat-card users-card">
                <h2>Users</h2>
                <p><?php echo $data['users_count']; ?></p>
            </div>
        </div>

        <!-- Charts Container -->
        <div class="charts-container">
            <!-- Pie Chart for Table Data -->
            <div class="chart">
                <canvas id="tableDataChart" class="chart"></canvas>
            </div>
            
            <!-- Bar Chart for Table Data -->
            <div class="chart">
                <canvas id="tableDataBarChart" class="chart"></canvas>
            </div>
        </div>
    </div>

    <?php include('../backtoprev.php')  ?>
    <script>
        window.onload = function() {
            // Pie Chart displaying data from tables
            const tableDataCtx = document.getElementById('tableDataChart').getContext('2d');
            const tableDataChart = new Chart(tableDataCtx, {
                type: 'pie',
                data: {
                    labels: ['Cart', 'Manage Orders', 'Menu Sections', 'Occasions', 'Reservations', 'Users'],
                    datasets: [{
                        data: [
                            <?php echo $data['cart_count']; ?>,
                            <?php echo $data['manage_order_count']; ?>,
                            <?php echo $data['menu_sections_count']; ?>,
                            <?php echo $data['occasion_count']; ?>,
                            <?php echo $data['reservations_count']; ?>,
                            <?php echo $data['users_count']; ?>
                        ],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)', // Tomato
                            'rgba(75, 192, 192, 0.6)', // Green
                            'rgba(255, 206, 86, 0.6)', // Blue
                            'rgba(153, 102, 255, 0.6)', // Orange
                            'rgba(54, 162, 235, 0.6)', // Purple
                            'rgba(255, 159, 64, 0.6)' // Teal
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true
                }
            });

            // Bar Chart displaying data from tables
            const tableDataBarCtx = document.getElementById('tableDataBarChart').getContext('2d');
            const tableDataBarChart = new Chart(tableDataBarCtx, {
                type: 'bar',
                data: {
                    labels: ['Cart', 'Manage Orders', 'Menu Sections', 'Occasions', 'Reservations', 'Users'],
                    datasets: [{
                        label: 'Table Data Count',
                        data: [
                            <?php echo $data['cart_count']; ?>,
                            <?php echo $data['manage_order_count']; ?>,
                            <?php echo $data['menu_sections_count']; ?>,
                            <?php echo $data['occasion_count']; ?>,
                            <?php echo $data['reservations_count']; ?>,
                            <?php echo $data['users_count']; ?>
                        ],
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        };
    </script>
</body>
</html>
