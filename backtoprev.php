<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back Button</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

       
        .back-button-container {
            position: fixed;
            bottom: 20px;
            left: 20px;
            width: 60px;
            height: 60px;
            background-color: white;
            border-radius: 50%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            z-index: 1000; 
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .back-button-container:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3);
        }

       
        .back-button-container svg {
            width: 30px;
            height: 30px;
            fill: #333; 
        }

     
        .content {
            height: 2000px;
            padding: 20px;
            background: linear-gradient(to bottom, #f9f9f9, #eaeaea);
        }
    </style>
</head>
<body>

<div class="back-button-container" onclick="goBack()">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <path d="M15.41 16.58 10.83 12l4.58-4.59L14 6l-6 6 6 6z"/>
    </svg>
</div>

<script>
 
    function goBack() {
        window.history.back();
    }
</script>

</body>
</html>
