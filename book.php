<?php
session_start(); // Start the session

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Table</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* General styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #0c3d45;
            color: white;
            text-align: center;
        }

        .user-info{
            color:#000;
        }

        .booking-form-container {
            width: 100%;
            height: 100vh;
            /* Full viewport height */
            margin: 0 auto;
            padding: 20px;
            background-color: #000;
            border-radius: 8px;

        }

        .label-container {
            display: flex;
            padding: 20px;
            justify-content: center;
            background-color: #000;
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
            color: white;
            gap: 15%;
            border-radius: 5px;

        }

        input[type="date"] {

            margin-top: 10px;
            padding: 10px;
            width: 20%;
            font-size: 1rem;
            border: 1px solid #fff;
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
            border: 1px solid #fff;
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
            padding: 10px 30px;
            background-color: #ff6347;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1rem;
        }

        #reserve-now:hover {
            background-color: #ffffff;
            color: #000;
        }
    </style>
</head>

<body>



    <div class="booking-form-container">
    <?php if (isset($_SESSION['user'])): ?>
            <div class="user-info">
                <h3>Hello, <?php echo htmlspecialchars($_SESSION['user']['username']); ?>!</h3>
                <h3>id: <?php echo htmlspecialchars($_SESSION['user']['user_id']); ?>!</h3>
                <p>You are logged in as a <?php echo htmlspecialchars($_SESSION['user']['role']); ?>.</p>
            </div>
        <?php else: ?>
            <p>You are not logged in.</p>
        <?php endif; ?>
        <h2>To help us find the best table for you, select the preferred party size, date, and time of your reservation.</h2>

        <div class="label-content">
            <label for="party-size">Party size</label>
            <label for="reservation-date">Date</label>
            <label for="reservation-time">Time</label>
        </div>

        <!-- Party size dropdown -->
        <form id="booking-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="label-container">

                <select id="party-size" name="party_size">
                    <option value="2">2 guests</option>
                    <option value="3">3 guests</option>
                    <option value="4">4 guests</option>
                    <option value="5">5 guests</option>
                    <option value="6">6 guests</option>
                </select>

                <input type="date" id="reservation-date" value="2024-10-27" name="reservation_date">

                <select id="reservation-time" name="reservation_time">
                    <option value="08:00">8:00 AM</option>
                    <option value="08:15">8:15 AM</option>
                    <option value="08:30">8:30 AM</option>
                    <option value="08:45">8:45 AM</option>
                    <option value="09:00">9:00 AM</option>
                    <option value="09:15">9:15 AM</option>
                    <option value="09:30">9:30 AM</option>
                    <option value="09:45">9:45 AM</option>
                </select>

            </div>
            <!-- Time slot availability -->
            <p>Choose an available time slot:</p>
            <div id="time-slots">
                <div class="time-slot" data-time="08:00">8:00 AM</div>
                <div class="time-slot" data-time="08:15">8:15 AM</div>
                <div class="time-slot" data-time="08:30">8:30 AM</div>
                <div class="time-slot" data-time="08:45">8:45 AM</div>
                <div class="time-slot" data-time="09:00">9:00 AM</div>
                <div class="time-slot" data-time="09:15">9:15 AM</div>
                <div class="time-slot" data-time="09:30">9:30 AM</div>
                <div class="time-slot" data-time="09:45">9:45 AM</div>
            </div>

            <button id="reserve-now" type="submit">RESERVE NOW</button>
        </form>
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the form data using POST
    $party_size = isset($_POST['party_size']) ? $_POST['party_size'] : '';
    $reservation_date = isset($_POST['reservation_date']) ? $_POST['reservation_date'] : '';
    $reservation_time = isset($_POST['reservation_time']) ? $_POST['reservation_time'] : '';

    // Validate the inputs (optional, for security)
    if (!empty($party_size) && !empty($reservation_date) && !empty($reservation_time)) {
        // Sanitize the inputs for security
        $party_size = htmlspecialchars($party_size);
        $reservation_date = htmlspecialchars($reservation_date);
        $reservation_time = htmlspecialchars($reservation_time);

        // Here you can handle the booking logic
        // Example: You could store the booking in a database or send an email confirmation.

        // Display a success message or redirect to a confirmation page
        echo "<div style='padding: 10px; border: 1px solid #ccc; background-color: #f9f9f9;'>
        <p style='font-weight: bold; font-size: 18px; color: green; margin-top: 5px; display: inline;'>Reservation successful! </p>
        <p style='font-size: 16px; color: #333; margin-top: 5px; display: inline;'>Party size: $party_size guests </p>
        <p style='font-size: 16px; color: #333; margin-top: 5px; display: inline;'>Reservation date: $reservation_date </p>
        <p style='font-size: 16px; color: #333; margin-top: 5px; display: inline;'>Reservation time: $reservation_time</p>
      </div>";

    } else {
        echo "Please fill in all fields.";
    }
}
?>
    </div>

    <script>
        $(document).ready(function() {
            // Function to handle time slot selection
            $('.time-slot').click(function() {
                // Remove the selected class from all time slots
                $('.time-slot').removeClass('selected');

                // Add the selected class to the clicked time slot
                $(this).addClass('selected');

                // Update the time picker with the selected time
                let selectedTime = $(this).data('time');
                $('#reservation-time').val(selectedTime);
            });


            $('#reserve-now').click(function() {
                let partySize = $('#party-size').val();
                let reservationDate = $('#reservation-date').val();
                let reservationTime = $('#reservation-time').val();

                // Display form data for debugging
                console.log(`Party size: ${partySize}, Date: ${reservationDate}, Time: ${reservationTime}`);
            });
        });
    </script> <!-- Link your custom JS -->
</body>

</html>