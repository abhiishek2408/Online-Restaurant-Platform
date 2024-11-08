<?php
session_start();
include('../config/db.php');

// Handle delete request
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "User deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting user: " . $stmt->error;
    }
    $stmt->close();
}

// Query to fetch all users
$query = "SELECT user_id, username, email, phone, address, bio, created_at, profile_img FROM users";
$result = $conn->query($query);

if ($result) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Error fetching data: " . $conn->error;
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #2C2C2C;
            color: #FFFFFF;
            margin: 0;
            padding: 0;
        }
        h2 {
            color: #FF7518;
            text-align: center;
            margin-top: 20px;
        
        }


        
        @keyframes containerFadeIn {
            0% {
                opacity: 0;
                transform: scale(0.9);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

  
        @keyframes buttonBounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-5px);
            }

            60% {
                transform: translateY(-3px);
            }
        }


        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
            animation: containerFadeIn 0.8s ease-out forwards;
        }

        table:hover {
           
    transform: scale(1.02); /* Small scaling effect */
   
}
        th, td {
            border: 1px solid #FF7518;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #FF7518;
            color: white;
        }
        img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            animation: buttonBounce 0.8s ease-out l;
        }
        .btn {
            padding: 5px 10px;
            margin: 2px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            animation: buttonBounce 0.8s ease-out forwards;
        }
        .update-btn {
            background-color: #4CAF50; /* Green */
            color: white;
            transition: background-color 0.3s;
            animation: buttonBounce 0.8s ease-out forwards;
        }
        .update-btn:hover {
            background-color: #45a049;
        }
        .delete-btn {
            background-color: #f44336; /* Red */
            color: white;
            transition: background-color 0.3s;
        }
        .delete-btn:hover {
            background-color: #d32f2f;
        }
        .alert {
            text-align: center;
            margin: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .alert.success {
            background-color: #4CAF50;
            color: white;
        }
        .alert.error {
            background-color: #f44336;
            color: white;
        }
        @media (max-width: 600px) {
            th, td {
                font-size: 14px;
            }
            img {
                width: 40px;
                height: 40px;
            }
        }

        .back-button {
            background-color: white;
            color: #FF7518;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            position: fixed;
            bottom: 20px; /* Positioned at the bottom */
            left: 20px; /* Positioned on the left side */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            opacity: 0.95;
            z-index: 1;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease, background-color 0.2s ease;
            width: auto;
        }

        /* Hover effect for subtle animation */
        .back-button:hover {
            background-color: #e06a15;
            color: #FFFFFF;
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
            opacity: 1;
        }

        /* Optional button text fade-in animation */
        .back-button::after {
            content: 'Back to Previous';
            opacity: 1;
        }
    </style>
</head>
<body>

<h2>User Management</h2>

<?php
if (isset($_SESSION['message'])) {
    echo "<div class='alert success'>".$_SESSION['message']."</div>";
    unset($_SESSION['message']);
}
if (isset($_SESSION['error'])) {
    echo "<div class='alert error'>".$_SESSION['error']."</div>";
    unset($_SESSION['error']);
}
?>

<div style="overflow-x:auto;">
<table>
    <thead>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Bio</th>
            <th>Created At</th>
            <th>Profile Image</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($users)) : ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td><?php echo htmlspecialchars($user['address']); ?></td>
                    <td><?php echo htmlspecialchars($user['bio']); ?></td>
                    <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                    <td>
                        <?php if (!empty($user['profile_img'])): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($user['profile_img']); ?>" alt="Profile Image">
                        <?php else: ?>
                            <img src="path/to/default-image.jpg" alt="Default Image"> <!-- Placeholder image -->
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="update_user.php?user_id=<?php echo htmlspecialchars($user['user_id']); ?>" class="btn update-btn">Update</a>
                        <a href="?delete=<?php echo htmlspecialchars($user['user_id']); ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9">No users found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
</div>

<button class="back-button" onclick="history.back()">Back to Previous</button> 
</body>
</html>
