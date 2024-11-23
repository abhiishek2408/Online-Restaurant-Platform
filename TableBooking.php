<?php
session_start(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('config/db.php');


$partySize = $reservationDate = $userId = $AvlSlots = $eventType = $specialRequests = "";
$errors = [];
$message = ""; 


function displayErrors($errors)
{
    return implode('<br>', array_map(fn($error) => "<p class='error'>$error</p>", $errors));
}

// Check if the form is submitted via AJAX
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajax'])) {
    $partySize = htmlspecialchars(trim($_POST['party_size']));
    $reservationDate = htmlspecialchars(trim($_POST['reservation_date']));
    $AvlSlots = htmlspecialchars(trim($_POST['slots']));
    $eventType = htmlspecialchars(trim($_POST['event_type']));
    $specialRequests = htmlspecialchars(trim($_POST['special_requests']));


    if (empty($partySize) || !is_numeric($partySize)) {
        $errors[] = "Valid party size is required.";
    }

    if (empty($reservationDate) || !DateTime::createFromFormat('Y-m-d', $reservationDate)) {
        $errors[] = "Valid reservation date is required.";
    }

    if (empty($eventType)) {
        $errors[] = "Event type is required.";
    }

    if (empty($AvlSlots)) {
        $errors[] = "Slots option is required.";
    }


    if (empty($errors)) {
        if (isset($_SESSION['user']['user_id'])) {
            $userId = (int)$_SESSION['user']['user_id']; 
        } else {
            $errors[] = "User is not logged in.";
        }


        $sql = "SELECT * FROM reservations WHERE user_id = ? AND reservation_date = ? AND slots = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("iss", $userId, $reservationDate, $AvlSlots);

        if (!$stmt->execute()) {
            die("Error executing statement: " . $stmt->error);
        }


        $result = $stmt->get_result();

    
        if ($result->num_rows > 0) {
            $errors[] = "You have already booked a reservation for this date and slot.";
        } else {

            $sqlInsert = "INSERT INTO reservations (user_id, party_size, reservation_date, slots, event_type, special_requests) 
                          VALUES (?, ?, ?, ?, ?, ?)";
            $insertStmt = $conn->prepare($sqlInsert);

            if ($insertStmt === false) {
                die("Error preparing insert statement: " . $conn->error);
            }

            $insertStmt->bind_param(
                "isssss",
                $userId,
                $partySize,
                $reservationDate,
                $AvlSlots,
                $eventType,
                $specialRequests
            );

            if ($insertStmt->execute()) {
                $message = "Your reservation has been successfully made!";
                echo json_encode(['success' => true, 'message' => $message]);
            } else {
                echo json_encode(['success' => false, 'errors' => ["Execution Error: " . $insertStmt->error]]);
            }

            $insertStmt->close(); 
        }

     
        $stmt->close();
    }


    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
    }

    exit; 
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
          
        /* Basic CSS Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}



        body {
                font-family: 'Roboto', sans-serif;
                background-color: #fff; 
                color: #000; 
                transition: background-color 0.3s, color 0.3s;
            }


   
           

       



       


        .booking-form-container {
            width: 100%;
            max-width: 1300px;
            margin: 0 auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            text-align: center;
            position: relative;
           
         
           
        }


        .table-book {
            display: flex;
            height: 100vh;
            width: 100%;
            justify-content: space-between;
        }

        /* Banner image style */
        .banner {
            flex: 1; /* Banner takes up half the space */
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #fff; /* Background color in case image fails to load */

            width: 50%;
            max-width: 600px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            opacity: 1;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin: 20px 30px;
            border-radius: 20px;
        }

       /* Apply general styles to the image */
.banner img {
    max-width: 100%;          /* Ensures the image takes up full width */
    height: 100%;             /* Keep the image aspect ratio */
    object-fit: cover;        /* Ensure the image is not distorted */
    border-radius: 15px;      /* Apply rounded corners to the image */
    transition: transform 0.3s ease-in-out;
    border: 4px solid transparent;  /* Initially no visible border */
    border-radius: 25px;
}

/* On hover, change the border color and animate */

.banner img:hover {
    transform: translateY(-10px); /* Moves the image up by 10px */
}


        


        .table-booking-container {
            flex: 1;
            width: 50%;
            max-width: 600px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            opacity: 1;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin: 20px 30px;
            padding: 20px;
            border-radius: 20px;
        }

.table-booking-container:hover {

            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
        }


        .table-booking-container:hover {
            animation: hoverEffect 0.3s ease-in-out forwards;
        }



        .table-booking-container  h2 {
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
            color: #000;
            border-radius: 4px;
        }

        select {
            display: flex;
            margin-top: 10px;
            padding: 10px;
            width: 20%;
            font-size: 1rem;
            border: 1px solid #000;
            color: #000;
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
            border: 1px solid #fff;
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
            border-radius: 25px;
            cursor: pointer;
            font-size: 1.1rem;
        }

        #reserve-now:hover {
            background-color: #ffffff;
            color: #000;
            border: 2px solid #FF7518;
        }
    </style>
</head>

<body>

<?php include("Navbar.php") ?>

   

    <div class="booking-form-container" id="book-section">

     <div class="table-book">
    <div class="banner">
            <img src="https://cdn.pixabay.com/photo/2022/01/10/04/37/event-6927353_1280.jpg" alt="Event Booking Banner">
        </div>
        
        <div class='table-booking-container'>
            <h2>To help us find the best table for you, select the preferred party size, date, and time of your reservation.</h2>

        <form id="booking-form" method="POST" action="">
            <div class="label-content">
                <label for="event-type">Event</label>
                <label for="reservation-date">Date</label>
                <label for="slots">Slots</label>
            </div>

            <div class="label-container">
                <select id="event-type" name="event_type" required>
                    <option value="" selected disabled hidden>Select an Event Type</option>
                    <option value="birthday">Birthday</option>
                    <option value="wedding">Wedding</option>
                    <option value="corporate">Corporate Event</option>
                    <option value="anniversary">Anniversary</option>
                    <option value="baby-shower">Baby Shower</option>
                    <option value="other">Other</option>
                </select>

                <input type="date" id="reservation_date" name="reservation_date" required>

                <select id="slots" name="slots" required>
                    <option value="1" selected disabled hidden>Select given slots</option>
                    <?php for ($hour = 1; $hour < 24; $hour += 2): ?>
                        <?php
                        $end_hour = ($hour + 2) % 24;
                        ?>
                        <option value="<?php echo str_pad($hour, 2, '0', STR_PAD_LEFT); ?>:00 - <?php echo str_pad($end_hour, 2, '0', STR_PAD_LEFT); ?>:00">
                            <?php echo str_pad($hour, 2, '0', STR_PAD_LEFT); ?>:00 - <?php echo str_pad($end_hour, 2, '0', STR_PAD_LEFT); ?>:00
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="label-content" style="margin-left:4%">
                <label for="party-size">Party Size</label>
                <label for="special-requests">Special Request</label>
            </div>

            <div class="label-container">
                <select id="party-size" name="party_size" required>
                    <option value="" selected disabled hidden>Select no. of guests</option>
                    <option value="2">2 guests</option>
                    <option value="3">3 guests</option>
                    <option value="4">4 guests</option>
                    <option value="5">5 guests</option>
                    <option value="6">6 guests</option>
                    <option value="7">7 guests</option>
                    <option value="8">8 guests</option>
                    <option value="9">9 guests</option>
                    <option value="10+">10+ guests</option>
                </select>

                <input type="text" id="special-requests" name="special_requests" rows="1" placeholder="Let us know any special arrangements or requests.">
            </div>

            <input type="checkbox" id="agree" name="agree" required>
            <label for="agree">I agree to the <a style="color: #ff7518; text-decoration:none" href="terms.html">terms and conditions</a>.</label><br>

            <button id="reserve-now" type="submit">Book Now</button>
        </form>

        <div id="success-message"></div>
        <div id="error-message"></div>
    </div>
    </div>
    </div>
    <?php include('Footer.php'); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
          $(document).ready(function() {
            $('#bookOnlineBtn').click(function(event) {
                event.preventDefault();
                    $('.slider-container').hide();
                    $('.feature-container').hide();
                    $('.Bistrofy-container').hide();
                    $('#testimonial').hide();
                    $('#user-data').hide();
                    $('#aboutContent').hide();
                    $('#book-section').show();
                    $('.occasion-container').hide();
                
                 
            });
        });



        $(document).ready(function() {
            $('#booking-form').on('submit', function(event) {
                event.preventDefault(); 

                var formData = $(this).serialize() + '&ajax=1'; // Include an AJAX flag

                $.ajax({
                    type: 'POST',
                    url: '', // URL of the PHP script
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
        
                        $('#success-message').html('');
                        $('#error-message').html('');

                        if (response.success) {
                            $('#success-message').html(response.message);
                        } else {
                            if (response.errors.length > 0) {
                                var errorHtml = '<h2>Errors:</h2><ul>';
                                $.each(response.errors, function(index, error) {
                                    errorHtml += '<li>' + error + '</li>';
                                });
                                errorHtml += '</ul>';
                                $('#error-message').html(errorHtml);
                            }
                        }
                    },
                    error: function() {
                        $('#error-message').html('<h2>An unexpected error occurred. Please try again.</h2>');
                    }
                });
            });
        });

    </script>
</body>

</html>