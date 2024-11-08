<?php
session_start();
include 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    echo "You must be logged in to view your reservations.";
    exit();
}

$user_id = $_SESSION['user']['user_id']; // Get the logged-in user's ID

// Fetch reservations for the logged-in user
$stmt = $conn->prepare("SELECT * FROM reservations WHERE user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$reservations = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Link your styles here -->
</head>
<body>

<h2>Your Reservations</h2>
<table>
    <thead>
        <tr>
            <th>Party Size</th>
            <th>Date</th>
            <th>Time</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($reservations as $reservation): ?>
            <tr>
                <td><?= htmlspecialchars($reservation['party_size']) ?></td>
                <td><?= htmlspecialchars($reservation['reservation_date']) ?></td>
                <td><?= htmlspecialchars($reservation['reservation_time']) ?></td>
                <td><?= htmlspecialchars($reservation['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
