<?php
include "menu.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="phpcss.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet"> 
    <title>Nay Store: Your Perfect Fit Awaits!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8e1e1;
        }
        .intro {
            text-align: center;
            padding: 50px;
            background-color: #f4b8c0;
            color: #4a4a4a;
        }
        .intro h1 {
            font-size: 36px;
        }
        .intro p {
            font-size: 18px;
            margin: 20px 0;
        }
        .info-boxes {
            display: flex;
            justify-content: space-around;
            margin: 40px 20px;
        }
        .info-box {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 40%;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .info-box h3 {
            color: #4a4a4a;
        }
        .info-box p {
            font-size: 16px;
            color: #555;
        }
        .image-container {
            text-align: center;
            margin: 20px 0;
        }
        .image-container img {
            width: 100%; 
            max-width:1400px; 
            border-radius: 10px; 
        }
        .assistance-container {
            background-color: #f4b8c0; 
            padding: 20px; 
            border-radius: 10px; 
            display: flex;
            align-items: center; 
            margin: 20px; 
            width: 500%; 
    max-width: 1430px; 
        }
        .assistance-text {
            flex: 1; 
            font-family: 'Cinzel', serif; 
            font-size: 28px; 
            font-weight: bold; 
            color: #4a4a4a; 
            margin-right: 20px; 
            transition: color 0.3s; 
        }
        .assistance-text:hover {
            color: #c93b4b; 
        }
        .assistance-image {
    width: 450px; 
    height: auto; 
    border-radius: 15px; 
}

        .support-message {
            font-family: 'Cinzel', serif; 
          
            font-size: 18px; 
            margin-top: 10px; 
            color: #4a4a4a; 
            text-align: center; 
        }
    </style>
</head>
<body>
    <div class="intro">
        <h1>Providing You with a Delightful Shopping Experience</h1>
        <p>At Nay Store, we focus on helping you find the perfect fit that matches your unique style and body shape. Enjoy personalized shopping like never before!</p>
    </div>

    <div class="info-boxes">
        <div class="info-box">
            <h3>Body Shape Selection</h3>
            <p>Our body shape selection tool helps you find the best clothing options that suit your figure. Choose your shape, and let us guide you to styles that enhance your natural beauty.</p>
        </div>
        <div class="info-box">
            <h3>Skin Tone Matching</h3>
            <p>Select your skin tone to receive tailored recommendations for colors that complement your complexion. We make it easy to look your best with the right hues!</p>
        </div>
    </div>

    <div class="image-container">
        <img src="/labs/project/photo/nay19.png" alt="Nay Store Collection">
    </div>

    <div class="assistance-container">
        <div class="assistance-text">
            <a href="assistance.php" style="text-decoration: none; color: inherit;">Click here if you need any assistance from Aya & Naeif</a>
        </div>
     
        <img src="/labs/project/photo/n5.png" alt="N5 Image" class="assistance-image">
    </div>
    
    <div class="support-message">
        We are here to assist you with any inquiries you may have! Don't hesitate to reach out.
    </div>
</body>
</html>
