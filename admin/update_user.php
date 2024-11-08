<?php
session_start();
include('../config/db.php');

// Check if user_id is set in the URL
if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    
    // Fetch the user data
    $stmt = $conn->prepare("SELECT user_id, username, email, phone, address, bio, profile_img FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        $_SESSION['error'] = "User not found.";
        header("Location: user.php"); // Redirect back to user list
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Prepare the update statement
        $username = $_POST['username'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $bio = $_POST['bio'];

        // Handle profile image upload
        if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] == 0) {
            $image = file_get_contents($_FILES['profile_img']['tmp_name']);
            $stmt = $conn->prepare("UPDATE users SET username=?, email=?, phone=?, address=?, bio=?, profile_img=? WHERE user_id=?");
            $stmt->bind_param("ssssssi", $username, $email, $phone, $address, $bio, $image, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?, email=?, phone=?, address=?, bio=? WHERE user_id=?");
            $stmt->bind_param("sssssi", $username, $email, $phone, $address, $bio, $user_id);
        }

        // Execute the statement
        if ($stmt->execute()) {
            $_SESSION['message'] = "User updated successfully!";
            header("Location: .php"); // Redirect back to user list
            exit();
        } else {
            $_SESSION['error'] = "Error updating user: " . $stmt->error;
        }
        $stmt->close();
    }
} else {
    $_SESSION['error'] = "No user ID provided.";
    header("Location: user.php"); // Redirect back to user list
    exit();
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
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
        .container {
            max-width: 600px;
            margin: 70px auto;
            padding: 40px;
            background-color: #3D3D3D;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
            animation: containerFadeIn 0.8s ease-out forwards;
        }

    .container:hover {
    
    transform: scale(1.02); /* Small scaling effect */
   
}

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #FF7518;
            border-radius: 5px;
            background-color: #2C2C2C;
            color: #FFFFFF;
        }
        input[type="file"] {
            margin: 10px 0;
            padding: 5px;
        }

        textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #FF7518;
            border-radius: 5px;
            background-color: #2C2C2C;
            color: #FFFFFF;
            resize: none;
        }
        button {
            padding: 10px 15px;
            background-color: #FF7518;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #e86e0e;
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
    </style>
</head>
<body>

<div class="container">
    <h2>Update User</h2>

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

    <form action="" method="post" enctype="multipart/form-data">
        <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <input type="tel" name="phone" placeholder="Phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
        <input type="text" name="address" placeholder="Address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
        <textarea name="bio" placeholder="Bio" rows="4"><?php echo htmlspecialchars($user['bio']); ?></textarea>
        <label for="profile_img">Profile Image:</label>
        <input type="file" name="profile_img" accept="image/*">
        <button type="submit">Update User</button>
        <a href="user.php" style="color: white;text-decoration: none;margin-left:15px">Back prev</a>
    </form>
</div>

</body>
</html>
