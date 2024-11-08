<?php
session_start();
include('../config/db.php');

// Check if user is staff
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'staff') {
    header('Location: ../index.php');
    exit;
}

// Update order status
if (isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $sql = "UPDATE orders SET status='$status' WHERE order_id='$order_id'";
    if ($conn->query($sql) === TRUE) {
        echo "Order status updated successfully.";
    } else {
        echo "Error updating status: " . $conn->error;
    }
}

// Fetch orders
$sql = "SELECT * FROM orders";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Order Status</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h2>Update Order Status</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>User ID</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Update</th>
        </tr>
        <?php if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['order_id']; ?></td>
                    <td><?php echo $row['user_id']; ?></td>
                    <td><?php echo $row['total_amount']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                            <select name="status">
                                <option value="pending" <?php if ($row['status'] === 'pending') echo 'selected'; ?>>Pending</option>
                                <option value="completed" <?php if ($row['status'] === 'completed') echo 'selected'; ?>>Completed</option>
                                <option value="cancelled" <?php if ($row['status'] === 'cancelled') echo 'selected'; ?>>Cancelled</option>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                    </td>
                </tr>
            <?php }
        } ?>
    </table>
</body>
</html>
