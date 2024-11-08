<?php
session_start();
include('../config/db.php');

// Check if user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

if (isset($_POST['add_item'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $_POST['image']; // Image path can be handled with file upload logic

    $sql = "INSERT INTO menu_items (name, category, price, description, image) VALUES ('$name', '$category', '$price', '$description', '$image')";
    if ($conn->query($sql) === TRUE) {
        echo "Menu item added successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch existing menu items
$sql = "SELECT * FROM menu_items";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Menu</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h2>Manage Menu</h2>
    <form method="POST" action="">
        <label for="name">Name:</label>
        <input type="text" name="name" required>
        <label for="category">Category:</label>
        <input type="text" name="category" required>
        <label for="price">Price:</label>
        <input type="number" step="0.01" name="price" required>
        <label for="description">Description:</label>
        <textarea name="description" required></textarea>
        <label for="image">Image URL:</label>
        <input type="text" name="image" required>
        <button type="submit" name="add_item">Add Item</button>
    </form>

    <h3>Existing Menu Items</h3>
    <table>
        <tr>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Description</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['category']; ?></td>
                    <td><?php echo $row['price']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><img src="<?php echo $row['image']; ?>" alt="Image" style="width: 50px; height: 50px;"></td>
                    <td>
                        <a href="edit-menu.php?id=<?php echo $row['item_id']; ?>">Edit</a>
                        <a href="delete-menu.php?id=<?php echo $row['item_id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php }
        } ?>
    </table>
</body>
</html>
