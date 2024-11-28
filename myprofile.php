<?php
session_start();
include('config/db.php'); // Include database configuration file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch user data from the database
$sql = "SELECT * FROM users WHERE user_id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit;
}

// Handle profile field updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['field'])) {
    $field = $_POST['field'];
    $value = $_POST['value'];

    // Validate field input
    $allowedFields = ['email', 'username', 'phone', 'address', 'bio'];
    if (!in_array($field, $allowedFields)) {
        echo json_encode(['success' => false, 'message' => 'Invalid field.']);
        exit();
    }

    // Prepare the SQL query for updating fields
    $stmt = $conn->prepare("UPDATE users SET $field = ? WHERE user_id = ?");
    $stmt->bind_param("si", $value, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile.']);
    }
    exit();
}

// Handle profile image upload separately
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_img'])) {
    if ($_FILES['profile_img']['error'] === 0) {
        $profile_img = file_get_contents($_FILES['profile_img']['tmp_name']);

        $stmt = $conn->prepare("UPDATE users SET profile_img = ? WHERE user_id = ?");
        $stmt->bind_param("si", $profile_img, $user_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Profile image updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update profile image.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error uploading image.']);
    }
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #ffffff;
            color: #333;
        }

        .profile-container {
            max-width: 1200px;
            min-height: 80%;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            height: 150px;
            padding: 20px;
            background: url('profilebanner.jpg');
        }

        .profile-pic {
            position: relative;
        }

        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #FF7518;
            margin-right: 20px;
            cursor: pointer;
        }

        .profile-info {
            flex-grow: 1;
        }

        .profile-info h1 {
            margin: 0;
            font-size: 24px;
            color: #ffffff;
        }

        .profile-info p {
            margin: 0;
            font-size: 14px;
            color: #ffffff;
        }

        .edit-profile-btn {
            background-color: #FF7518;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }

        .edit-profile-btn:hover {
            background-color: #e76416;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
        }

        .close-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #FF7518;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .input-container {
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .input-container input,
        .input-container textarea {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }

        .input-container textarea {
            resize: vertical;
            min-height: 100px;
        }

        .field-name {
            font-size: 12px;
            color: #FF7518;
            position: absolute;
            top: -10px;
            left: 10px;
            background-color: white;
            padding: 0 5px;
        }


        .center-container {
            text-align: center;
            color: #555;
        }

        .center-container svg {
            width: 150px;
            height: 150px;
            margin-bottom: 20px;
        }

        .center-container h1 {
            font-size: 24px;
            margin: 0;
            color: #777;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <!-- Profile header with image and name -->
    <div class="profile-header">
        <div class="profile-pic">
            <?php if (!empty($user['profile_img'])): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($user['profile_img']); ?>" class="profile-img" alt="Profile Image" onclick="uploadProfileImage()">
            <?php else: ?>
                <img src="default_profile_img.jpg" class="profile-img" alt="Default Image" onclick="uploadProfileImage()">
            <?php endif; ?>
        </div>
        <div class="profile-info">
            <h1><?php echo htmlspecialchars($user['username']); ?></h1>
            <p><?php echo htmlspecialchars($user['bio']); ?></p>
        </div>
        <button class="edit-profile-btn" onclick="toggleModal()">Edit Profile</button>
    </div>

    <!-- Modal for editing profile -->
    <div class="modal" id="editProfileModal" onclick="closeModal(event)">
        <div class="modal-content">
            <h2>Edit Profile</h2>
            <div class="form-grid">
                <div class="input-container">
                    <div class="field-name">Email</div>
                    <input type="email" id="modal-email" value="<?php echo htmlspecialchars($user['email']); ?>" />
                </div>
                <div class="input-container">
                    <div class="field-name">Username</div>
                    <input type="text" id="modal-username" value="<?php echo htmlspecialchars($user['username']); ?>" />
                </div>
                <div class="input-container">
                    <div class="field-name">Phone</div>
                    <input type="text" id="modal-phone" value="<?php echo htmlspecialchars($user['phone']); ?>" />
                </div>
                <div class="input-container">
                    <div class="field-name">Address</div>
                    <input type="text" id="modal-address" value="<?php echo htmlspecialchars($user['address']); ?>" />
                </div>
                <div class="input-container">
                    <div class="field-name">Bio</div>
                    <textarea id="modal-bio"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                </div>
            </div>
            <span class="close-icon" onclick="toggleModal()">×</span>
            <button onclick="saveProfileChanges()">Save Changes</button>
        </div>
    </div>

    
</div>

<div class="center-container">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
            <circle cx="32" cy="32" r="32" fill="#FFCC80"/>
            <path d="M32 6a26 26 0 1 0 26 26A26 26 0 0 0 32 6z" fill="#FFA726"/>
            <path d="M32 6v52a26 26 0 0 0 0-52z" fill="#FF9800"/>
            <path d="M22 40s-4 6-4 12h28s0-6-4-12z" fill="#FF7043"/>
            <path d="M32 46c6 0 10 6 10 6H22s4-6 10-6z" fill="#FF5722"/>
            <circle cx="26" cy="26" r="5" fill="#FFF"/>
            <circle cx="38" cy="26" r="5" fill="#FFF"/>
            <circle cx="26" cy="26" r="2" fill="#333"/>
            <circle cx="38" cy="26" r="2" fill="#333"/>
            <path d="M28 35s4 4 8 0" stroke="#333" stroke-width="2" stroke-linecap="round" fill="none"/>
        </svg>
        <h1>Nothing here yet</h1>
    </div>

<script>
    function toggleModal() {
        const modal = document.getElementById('editProfileModal');
        modal.style.display = modal.style.display === 'flex' ? 'none' : 'flex';
    }

    function closeModal(event) {
        if (event.target === document.getElementById('editProfileModal')) {
            toggleModal();
        }
    }

    function uploadProfileImage() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.onchange = function (event) {
            const file = event.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('profile_img', file);

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Profile image updated successfully.');
                    location.reload(); // Reload to update the profile picture
                } else {
                    alert('Failed to update profile image.');
                }
            })
            .catch(error => console.error('Error:', error));
        };
        input.click();
    }

    function saveProfileChanges() {
        const email = document.getElementById('modal-email').value;
        const username = document.getElementById('modal-username').value;
        const phone = document.getElementById('modal-phone').value;
        const address = document.getElementById('modal-address').value;
        const bio = document.getElementById('modal-bio').value;

        const fields = { email, username, phone, address, bio };
        const requests = Object.entries(fields).map(([field, value]) =>
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `field=${encodeURIComponent(field)}&value=${encodeURIComponent(value)}`
            })
        );

        Promise.all(requests)
            .then(() => {
                alert('Profile updated successfully.');
                toggleModal();
                location.reload();
            })
            .catch(error => console.error('Error:', error));
    }
</script>
</body>
</html>
