<?php
session_start();
include('../config/db.php');

// Check if user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Fetch data for reports
$sales = $conn->query("SELECT SUM(price) AS total_sales FROM orders INNER JOIN order_details ON orders.id = order_details.order_id");
$total_sales = $sales->fetch_assoc()['total_sales'];

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Reports</title>
</head>
<body>
    <h2>Reports</h2>
    <p>Total Sales: <?php echo $total_sales; ?></p>
</body>
</html>
