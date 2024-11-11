<?php
include "menu.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="phpcss.css">
    <title>Assistance - Nay Store</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8e1e1;
        }
        .intro {
            text-align: center;
            padding: 30px;
            background-color: #f4b8c0;
            color: #4a4a4a;
        }
        .intro h1 {
            font-size: 36px;
        }
        .intro p {
            font-size: 18px;
        }
        .team-container {
            display: flex;
            justify-content: space-around; 
            margin-top: 20px;
            padding: 0 20px;
        }
        .team-member {
            text-align: center;
            width: 40%; 
            background-color: #fff;
            border-radius: 10px;
            padding: 10px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            height: auto; 
        }
        .team-member img {
            width: 90%; 
            height: auto; 
            border-radius: 10px; 
        }
        .team-member h4 {
            font-family: 'Cinzel', serif; 
            font-size: 24px; 
            color: #4a4a4a; 
        }
        .team-member p {
            font-family: 'Cinzel', serif; 
            font-size: 16px; 
            color: #555; 
        }
    </style>
</head>
<body>
    <div class="intro">
        <h1>Need Assistance?</h1>
        <p>Meet our experts who are ready to help you with your clothing and fashion needs.</p>
    </div>

    <div class="team-container">
        <div class="team-member">
            <img src="/labs/project/photo/aya.jpeg" alt="Aya Sabea"> 
            <h4>Aya Sabea</h4>
            <p>Jewelry designer and expert in clothing and fashion for over 5 years. I can assist you with anything you need regarding our website.</p>
            <div class="contact-info">
                <p>ayasabra17@gmail.com</p>
            </div>
        </div>
        <div class="team-member">
            <img src="/labs/project/photo/naeif.jpeg" alt="Naeif Shable"> 
            <h4>Naeif Shable</h4>
            <p>Fashion model and clothing expert. Working in the fashion industry for over 5 years. Ready to assist with everything related to the website.</p>
            <div class="contact-info">
                <p>naeifshable@gmail.com</p>
            </div>
        </div>
    </div>
</body>
</html>
