<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('config/db.php');

// Initialize variables for form data
$first_name = $last_name = $email = $phone = $event_type = $event_date = $message = '';
$error_message = '';
$success_message = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from form and session
    $user_id = $_SESSION['user']['user_id'] ?? null; // Ensure user_id exists in session
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $event_type = $_POST['event_type'];
    $event_date = $_POST['event_date'];
    $message = $_POST['message'];
    $state = 'active'; // Default state

    // Debugging output - optional, can be removed in production
    echo "User ID: $user_id<br>";
    echo "First Name: $first_name<br>";
    echo "Last Name: $last_name<br>";
    echo "Email: $email<br>";
    echo "Phone: $phone<br>";
    echo "Event Type: $event_type<br>";
    echo "Event Date: $event_date<br>";
    echo "Message: $message<br>";
    echo "State: $state<br>";

    // Prepare and bind statement
    $stmt = $conn->prepare("INSERT INTO occasion (user_id, first_name, last_name, email, phone, event_type, event_date, message, state) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // Bind parameters
    $stmt->bind_param("issssssss", $user_id, $first_name, $last_name, $email, $phone, $event_type, $event_date, $message, $state);

    // Execute statement
    if ($stmt->execute()) {
        $success_message = "New occasion recorded successfully.";
    } else {
        $error_message = "Error: " . $stmt->error; // Show error if execution fails
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        /* Importing the Roboto font family */
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        /* Global Styles */

        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #000;
            transition: background-color 0.3s, color 0.3s;
        }



        .booking-form-container {
            width: 80%;
            /* Centered layout */
            max-width: 600px;
            /* Limit width for large screens */
            margin: 0 auto;
            padding: 30px;
            /* Spacing inside the container */
            background-color: #ffffff;
            /* White background */
            border-radius: 10px;
            /* Rounded corners for modern design */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            /* Subtle shadow */
            text-align: center;
            display: none;
            /* Visible by default for this example */
            opacity: 1;
            /* Fully visible */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            /* Smooth transitions */
            position: relative;
            z-index: 10;
        }





        .booking-form-container h2 {
            width: 100%;
            color: #FF7518;
        }

        .label-container {
            display: flex;
            padding: 20px;
            justify-content: center;
            background-color: #fff;
            border-radius: 8px;

        }


        label {
            margin-top: 20px;
            font-size: 1rem;

        }

        .label-content {

            display: flex;

            margin-left: 10px;
            border-radius: 8px;
            justify-content: center;
            color: #000;
            gap: 15%;
            border-radius: 5px;

        }

        input[type="date"],
        input[type="text"] {

            margin-top: 10px;
            padding: 10px;
            width: 20%;
            font-size: 1rem;
            border: 1px solid #000;
            margin-left: 10px;
            color: grey;
            border-radius: 4px;
        }

        select {
            display: flex;
            margin-top: 10px;
            padding: 10px;
            width: 20%;
            font-size: 1rem;
            border: 1px solid #ddd;
            color: grey;
            border-radius: 4px;
            margin-left: 10px;
        }

        #time-slots {
            margin-top: 20px;

        }


        .time-slot {
            display: inline-block;
            margin: 5px;
            padding: 10px 20px;
            color: white;
            border: 1px solid #000;
            border-radius: 5px;
            cursor: pointer;
            background-color: #ff6347;

        }


        .time-slot.selected {
            background-color: #ffffff;
            color: #000;
        }

        #reserve-now {
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 10px 30px;
            background-color: #FF7518;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1rem;
        }

        #reserve-now:hover {
            background-color: #ffffff;
            color: #000;
            border: 2px solid #FF7518;
        }



        .cart-modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .cart-modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .cart-close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .cart-close:hover,
        .cart-close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }


        .occasion-book-container {
            text-align: center;
            padding: 40px;
            background-color: #fff;
            border-radius: 10px;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;


        }

        h1 {
            font-size: 36px;
            color: #2c3e50;
            margin-bottom: 15px;
            font-weight: 700;
        }

        p {
            font-size: 20px;
            color: #34495e;
            line-height: 1.6;

        }

        .description {
            margin-top: 10%;

            max-width: 600px;

            margin-left: auto;

            margin-right: auto;

        }


        .service-content {
            display: flex;
            justify-content: center;

            margin-top: 40px;

            flex-wrap: wrap;

            gap: 0;

        }

        .service-box {
            background-color: #f9f9f9;

            border: 1px solid #ddd;

            border-radius: 8px;

            padding: 20px;

            flex: 0 1 300px;

            height: 400px;

            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);

            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);

            text-align: center;
            margin: 0;

        }

        .service-box:hover {

            transform: translateY(-5px);

        }

        .service-box img {
            width: 100%;

            height: 200px;

            object-fit: cover;

            border-radius: 8px 8px 0 0;

        }

        .service-box h4 {
            margin-top: 15px;

            color: #2c3e50;

        }

        .service-box p {
            color: #555;

            font-size: 16px;

            line-height: 1.4;

        }


        .start-planning-button {
            display: inline-block;

            margin-top: 30px;

            padding: 15px 30px;

            font-size: 18px;

            color: #fff;

            background-color: #FF7518;

            border: 2px solid white;
            border-radius: 25px;

            text-decoration: none;

            transition: background-color 0.3s;

            cursor: pointer;
        }

        .start-planning-button:hover {
            background-color: white;
            color: #2C2C2C;
            border: 2px solid #FF7518;
        }






        /* Container */
        .container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
            
        }

        /* Booking Form */
        .booking-form {
            display: none;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
          
        }

        .booking-form input,
        .booking-form textarea,
        .booking-form select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #000;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
            transition: border-color 0.3s ease-in-out;
        }

        .booking-form input:focus,
        .booking-form textarea:focus,
        .booking-form select:focus {
            border-color: #FF7518;
            /* Highlight border on focus */
        }

        .booking-form textarea {
            height: 120px;
            resize: none;
        }

        .booking-form button {
            padding: 12px 25px;
            font-size: 16px;
            color: #fff;
            background-color: #FF7518;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: block;
            margin: 20px auto;
        }

        .booking-form button:hover {
            background-color: #333;
            /* Darker background on hover */
            transform: translateY(-5px) scale(1.05);
        }

        /* Form Title */
        .form-title {
            font-size: 28px;
            color: #2c3e50;
            margin-top: 20px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 500;
            /* Make the title more prominent */
        }

        /* Additional Boxes Layout */
        .additional-boxes {
            display: flex;
            justify-content: space-between;
            gap: 25px;
            margin-top: 30px;
        }


        .additional-box {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 25px;
            flex: 0 1 45%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            text-align: center;
            transform: translateY(50px) scale(0.95); /* Start from a slightly smaller size and moved down */
    opacity: 0; /* Start invisible */
    animation: appear 0.6s ease-out forwards; /* Apply the animation */
}

@keyframes appear {
    from {
        transform: translateY(50px) scale(0.95);
        opacity: 0;
    }
    to {
        transform: translateY(0) scale(1);
        opacity: 1;
    }
}

        .additional-box img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .additional-box h3 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 20px;
            text-align: center;
        }

        .additional-box h4 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .additional-box p {
            color: #555;
            font-size: 16px;
        }

        /* Right Details */
        .right-details {
            text-align: left;
            font-size: 16px;
        }

        .right-details h4 {
            margin-bottom: 10px;
            font-size: 18px;
            color: #333;
        }

        .right-details p {
            margin: 5px 0;
            color: #777;
        }

        /* Flexbox for Form Row */
        .form-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .form-row input,
        .form-row select {
            width: calc(50% - 10px);
        }

        /* Occasion Form */
        .occasion-form {
    width: 60%;
    background-color: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    margin: 0 auto;
    text-align: center;
    box-sizing: border-box;
    border: 1px solid #ddd;
    transform: translateY(50px) scale(0.95); /* Start from a slightly smaller size and moved down */
    opacity: 0; /* Start invisible */
    animation: appear 0.6s ease-out forwards; /* Apply the animation */
}

@keyframes appear {
    from {
        transform: translateY(50px) scale(0.95);
        opacity: 0;
    }
    to {
        transform: translateY(0) scale(1);
        opacity: 1;
    }
}


        /* Optional: Add smooth transition to text color or other elements */
        .occasion-form h2 {
            transition: color 0.3s ease;
        }

        .occasion-form:hover h2 {
            color: #FF7518;
            /* Change title color on hover */
        }


        .occasion-form h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .occasion-form .form-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .occasion-form .form-row input,
        .occasion-form .form-row select {
            width: calc(50% - 10px);
            padding: 14px;
            font-size: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
        }

        .occasion-form textarea {
            width: 100%;
            padding: 14px;
            font-size: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            margin-bottom: 25px;
            resize: none;
            height: 120px;
        }

        .occasion-form button {
            padding: 14px 30px;
            font-size: 16px;
            color: #fff;
            background-color: #FF7518;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: block;
            margin: 20px auto;
        }

        .occasion-form button:hover {
            background-color: #333;
            transform: translateY(-4px);
        }

        .occasion-form .success-message,
        .occasion-form .error-message {
            margin-top: 20px;
            padding: 12px;
            text-align: center;
            border-radius: 8px;
            font-size: 16px;
        }

        .occasion-form .success-message {
            background-color: #4CAF50;
            color: white;
        }

        .occasion-form .error-message {
            background-color: #F44336;
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .occasion-form {
                width: 90%;
                padding: 20px;
            }

            .occasion-form .form-row {
                flex-direction: column;
            }

            .occasion-form .form-row input,
            .occasion-form .form-row select {
                width: 100%;
            }

            .additional-boxes {
                flex-direction: column;
                gap: 20px;
            }

            .additional-box {
                width: 100%;
            }
        }







        .Bistrofy-container {
            text-align: center;
            background-color: #000;
            display: inline-block;
            height: 60%;
            padding: 30px;
            align-items: center;
            justify-content: center;


        }

        .Bistrofy-header {
            font-size: 2.5rem;
            max-width: 600px;
            margin: 0 auto 40px;
            line-height: 1.5;
            color: #FF7518;
            font-family: "Roboto", sans-serif;
            justify-content: center;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
        }


        .Bistrofy-book-button {
            display: inline-block;
            padding: 10px 30px;
            background-color: white;
            color: #2C2C2C;
            border: 2px solid #FF7518;
            border-radius: 25px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            margin-bottom: 20px;
        }

        .Bistrofy-book-button:hover {
            background-color: #FF7518;
            color: white;
            border: 2px solid white;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
            transform: translateY(-2px);
        }

        .Bistrofy-dining {
            display: block;
            margin: 0 auto;
            max-width: 100%;
            height: auto;
        }

        .Bistrofy-dining {
            width: 50%;
            height: 40%;
            display: block;
            margin: 20px auto;
            max-width: 90%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .Bistrofy-dining:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);

        }


        .occasion-container {
            font-family: 'Roboto', sans-serif;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
            background-color: #fff;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.15);

        }


        .occasion-image-section {
            flex: 1;
            margin-right: 40px;
        }

        .occasion-image-section img {
            width: 100%;
            max-width: 500px;
            border-radius: 8px;
            object-fit: cover;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            margin-top: 22px;

        }

        .occasion-image-section img:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);

        }


        .occasion-text-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 0 20px;
        }

        .occasion-icons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            color: #ffffff;
        }

        .occasion-text-section h2 {
            font-size: 2em;
            font-weight: 700;
            color: #FF7518;
            margin-bottom: 15px;
            line-height: 1.2;
        }

        .occasion-description {
            font-size: 1em;
            font-weight: 300;
            color: #000;
            margin-bottom: 25px;
            line-height: 1.6;
        }


        .occasion-button {
            padding: 12px 24px;
            background: none;
            border: 2px solid #ffffff;
            color: #ffffff;
            cursor: pointer;
            font-size: 1em;
            display: inline-block;
            padding: 10px 30px;
            background-color: white;
            color: #2C2C2C;
            border: 2px solid #FF7518;
            border-radius: 25px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            margin-bottom: 20px;
            font-family: 'Roboto', sans-serif;
            font-weight: 500;
            transition: background-color 0.3s, color 0.3s;
            align-self: start;
            margin-top: 10px;
        }

        .occasion-button:hover {
            background-color: #FF7518;
            color: white;
            border: 2px solid white;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
            transform: translateY(-2px);
        }


        .occasion-contact-info {
            margin-top: 40px;
        }

        .occasion-contact-info p {
            font-size: 0.95em;
            line-height: 1.6;
            margin-bottom: 10px;
            font-weight: 400;
            color: #000;
            font-family: 'Roboto', sans-serif;

        }

        .occasion-contact-info strong {
            font-weight: 500;
            color: #ffffff;
        }

        .success-message{
            font-size: medium;
            font-family: 'Roboto' sans-serif;
            color: green;
        }
    </style>
</head>

<body>

<?php  include('Navbar.php'); ?>



    <div class="occasion-book-container" id="main-content">
        <h1>Are you planning an event?</h1>
        <p class="description">Explore our private dining experience. We offer various menus tailored to your occasion and budget, from corporate events to private gatherings.</p>

        <div class="service-content">
            <div class="service-box">
                <img src="corporateevent.jpg" alt="Corporate Events">
                <h4>Corporate Events</h4>
                <p>Professional catering services for your corporate needs, ensuring a successful event.</p>
            </div>
            <div class="service-box">
                <img src="privategathering.jpg" alt="Private Gatherings">
                <h4>Private Gatherings</h4>
                <p>Intimate catering solutions for personal celebrations and gatherings.</p>
            </div>
            <div class="service-box">
                <img src="weedingevent.jpg" alt="Weddings">
                <h4>Weddings</h4>
                <p>Elegant catering services to make your wedding day unforgettable with exquisite cuisine.</p>
            </div>
        </div>

        <a href="#" class="start-planning-button">Start Planning</a>
    </div>



    <div class="container booking-form" id="booking-form">

        <h2 class="form-title">Book your Event</h2>

        <div class="additional-boxes">
            <div class="additional-box">
                <img src="Eventpic.jpg" alt="Get in Touch">
                <h4>Get in Touch</h4>
                <p>Have questions? Reach out to our team for assistance.</p>
            </div>

           





            <form id="form" method="POST" action="" class="occasion-form">
                <h2 class="form-title">Get in Touch</h2>
                <div class="form-row">
                    <input type="text" name="first_name" placeholder="First Name*" required>
                    <input type="text" name="last_name" placeholder="Last Name*" required>
                </div>
                <div class="form-row">
                    <input type="email" name="email" placeholder="Email*" required>
                    <input type="tel" name="phone" placeholder="Phone">
                </div>
                <div class="form-row">
                    <select name="event_type" required>
                        <option value="private">Private Event</option>
                        <option value="corporate">Corporate Event</option>
                    </select>
                    <input type="date" name="event_date" required>
                </div>
                <textarea name="message" placeholder="Write a message" required></textarea>
                <button type="submit">Submit</button>

                <?php if (!empty($success_message)) : ?>
                    <div class="success-message"><?php echo $success_message; ?></div>
                <?php elseif (!empty($error_message)) : ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>

            </form>

        </div>
    </div>

<?php include('Footer.php'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.start-planning-button').on('click', function(event) {
                event.preventDefault();
                $('.occasion-book-container').hide();
                $('#testimonial').hide();

                $('.booking-form').show();
            });


            $('#form').on('submit', function(event) {
                event.preventDefault();
                $.post('', $(this).serialize(), function(data) {
                    alert("Your booking details have been submitted successfully!");
                    $('#form')[0].reset();
                    
                });
            });
        });
    </script>
</body>

</html>