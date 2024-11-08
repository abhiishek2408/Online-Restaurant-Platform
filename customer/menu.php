<?php
session_start();
include('../config/db.php');

// Fetch menu items
$sql = "SELECT * FROM menu_items";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Menu</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h2>Our Menu</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Description</th>
            <th>Order</th>
        </tr>
        <?php if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['category']; ?></td>
                    <td><?php echo $row['price']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td>
                        <form method="POST" action="order.php">
                            <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                            <input type="number" name="quantity" min="1" required>
                            <button type="submit">Order</button>
                        </form>
                    </td>
                </tr>
            <?php }
        } ?>
    </table>

<?php
// Array of all sections with respective dishes
$sections = [
        [
            "title" => "Wine and Beverage Menu",
            
            "dishes" => [
                ["name" => "Kingfisher Beer", "description" => "Popular Indian lager beer.", "price" => "$5", "vegan" => true, "rating" => 4.6, "time" => "0 mins"],
                ["name" => "Sula Sauvignon Blanc", "description" => "Crisp white wine from India.", "price" => "$10", "vegan" => false, "rating" => 4.7, "time" => "0 mins"],
                ["name" => "Tamarind Juice", "description" => "Sweet and tangy juice from tamarind.", "price" => "$4", "vegan" => true, "rating" => 4.5, "time" => "0 mins"],
                ["name" => "Mint Lemonade", "description" => "Refreshing drink made with mint and lemons.", "price" => "$4", "vegan" => true, "rating" => 4.8, "time" => "0 mins"],
                ["name" => "Chaas", "description" => "Spiced buttermilk drink.", "price" => "$3", "vegan" => false, "rating" => 4.5, "time" => "0 mins"],
                ["name" => "Nimbu Pani", "description" => "Refreshing Indian lemonade.", "price" => "$3", "vegan" => true, "rating" => 4.7, "time" => "0 mins"],
                ["name" => "Lassi", "description" => "Traditional yogurt drink, sweet or salty.", "price" => "$5", "vegan" => false, "rating" => 4.8, "time" => "5 mins"],
                ["name" => "Pomegranate Juice", "description" => "Fresh juice made from pomegranates.", "price" => "$4", "vegan" => true, "rating" => 4.6, "time" => "0 mins"],
                ["name" => "Coconut Water", "description" => "Fresh coconut water, refreshing and hydrating.", "price" => "$4", "vegan" => true, "rating" => 4.8, "time" => "0 mins"],
                ["name" => "Chai (Tea)", "description" => "Spiced Indian tea served hot.", "price" => "$2", "vegan" => true, "rating" => 4.9, "time" => "5 mins"],
                ["name" => "Kesar Badam Milk", "description" => "Saffron and almond flavored milk.", "price" => "$5", "vegan" => false, "rating" => 4.7, "time" => "5 mins"]
            ]
        ]
];



// Loop through each section to display
foreach ($sections as $section) {
    echo "<section class='menu-category' style='display:none' id='wine=menu'>";

    echo "<h2>{$section['title']}</h2>";
    echo "<div class='menu-items-container'>";
    
    foreach ($section['dishes'] as $item) {
        $itemName = urlencode($item['name']);
        $itemDescription = urlencode($item['description']);
        $itemPrice = urlencode($item['price']);
        $itemRating = urlencode($item['rating']);
        $itemTime = urlencode($item['time']);
        $itemVegan = urlencode($item['vegan']);

        echo "
        <div class='menu-item'  onclick=\"window.location='customer/detail.php?name={$itemName}&description={$itemDescription}&price={$itemPrice}&rating={$itemRating}&time={$itemTime}&vegan={$itemVegan}'\">
            <img src='https://b.zmtcdn.com/data/dish_photos/054/7c1c9c67d06e7d1b765096abc58f2054.jpeg' alt='{$item['name']}' />
            
            <div class='item-info'>
                <div class='name-rating'>
                    <h3>{$item['name']}</h3>
                    <div class='rating'>Rating: {$item['rating']} ⭐</div>
                </div>

                <div class='description-price'>
                    <p>{$item['description']}</p>
                    <span class='price'>{$item['price']}</span>
                </div>

                <div class='vegan-time'>
                    <span class='icon'>" . ($item['vegan'] ? "🌱 Vegan" : "🍗 Non-Vegan") . "</span>
                    <div class='time'>Prep Time: {$item['time']}</div>
                </div>
            </div>
            
            <button class='order-button'>Add to Cart</button>
        </div>";
    }

    echo "</div>";
    echo "</section>";
}
?>



<?php
// Desserts Menu Data
$sections = [
    [
        "title" => "Desserts Menu",
        "dishes" => [
            ["name" => "Gulab Jamun", "description" => "Deep-fried dough balls soaked in sugar syrup.", "price" => "$6", "vegan" => false, "rating" => 4.9, "time" => "15 mins"],
            ["name" => "Rasgulla", "description" => "Spongy cheese balls in sugar syrup, a Bengali delight.", "price" => "$5", "vegan" => false, "rating" => 4.8, "time" => "10 mins"],
            ["name" => "Kheer", "description" => "Rice pudding flavored with cardamom and garnished with nuts.", "price" => "$4", "vegan" => false, "rating" => 4.7, "time" => "20 mins"],
            ["name" => "Jalebi", "description" => "Crispy, spiral-shaped sweet soaked in sugar syrup.", "price" => "$5", "vegan" => true, "rating" => 4.6, "time" => "15 mins"],
            ["name" => "Ras Malai", "description" => "Soft cheese patties in sweetened milk, flavored with saffron.", "price" => "$7", "vegan" => false, "rating" => 4.9, "time" => "20 mins"],
            ["name" => "Mango Mousse", "description" => "Light and airy mousse made with fresh mango pulp.", "price" => "$6", "vegan" => false, "rating" => 4.8, "time" => "15 mins"],
            ["name" => "Pista Barfi", "description" => "Fudge made with pistachios, rich and nutty flavor.", "price" => "$5", "vegan" => true, "rating" => 4.7, "time" => "15 mins"],
            ["name" => "Coconut Ladoo", "description" => "Sweet coconut balls rolled in desiccated coconut.", "price" => "$5", "vegan" => true, "rating" => 4.6, "time" => "10 mins"],
            ["name" => "Chikki", "description" => "Crunchy brittle made with jaggery and nuts.", "price" => "$4", "vegan" => true, "rating" => 4.5, "time" => "10 mins"],
            ["name" => "Kaju Katli", "description" => "Delicate cashew fudge, rich and creamy.", "price" => "$8", "vegan" => false, "rating" => 4.9, "time" => "15 mins"],
            ["name" => "Lemon Tart", "description" => "Tangy lemon filling in a crisp tart shell.", "price" => "$5", "vegan" => false, "rating" => 4.7, "time" => "20 mins"]
        ]
    ]
];



foreach ($sections as $section) {
    echo "<section class='menu-category' style='display: none' id='Desserts-Menu'>";
    echo "<h2>{$section['title']}</h2>";
    echo "<div class='menu-items-container'>";
    
    foreach ($section['dishes'] as $item) {
        $itemName = urlencode($item['name']);
        $itemDescription = urlencode($item['description']);
        $itemPrice = urlencode($item['price']);
        $itemRating = urlencode($item['rating']);
        $itemTime = urlencode($item['time']);
        $itemVegan = urlencode($item['vegan']);

        echo "
        <div class='menu-item' onclick=\"window.location='customer/detail.php?name={$itemName}&description={$itemDescription}&price={$itemPrice}&rating={$itemRating}&time={$itemTime}&vegan={$itemVegan}'\">
            <img src='https://b.zmtcdn.com/data/dish_photos/054/7c1c9c67d06e7d1b765096abc58f2054.jpeg' alt='{$item['name']}' />
            
            <div class='item-info'>
                <div class='name-rating'>
                    <h3>{$item['name']}</h3>
                    <div class='rating'>Rating: {$item['rating']} ⭐</div>
                </div>

                <div class='description-price'>
                    <p>{$item['description']}</p>
                    <span class='price'>{$item['price']}</span>
                </div>

                <div class='vegan-time'>
                    <span class='icon'>" . ($item['vegan'] ? "🌱 Vegan" : "🍗 Non-Vegan") . "</span>
                    <div class='time'>Prep Time: {$item['time']}</div>
                </div>
            </div>
            
            <button class='order-button'>Add to Cart</button>
        </div>";
    }

    echo "</div>";
    echo "</section>";
}
?>
</body>
</html>
