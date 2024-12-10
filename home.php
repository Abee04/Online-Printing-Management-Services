<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="stylesheet" href="navstyles.css">
    <style>
        body {
            font-family: "Arial", sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f4f4f4;
        }
        .hero {
            background-color: #ffffff;
            padding: 50px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-bottom: 1px solid #eaeaea;
            text-align: center; /* Center align the text */
            flex-grow: 1; /* Make the hero section grow to fill available space */
        }
        .hero .text-content {
            max-width: 45%;
            margin-right: 20px; /* Add space between text and image */
        }
        .hero h1 {
            font-size: 28px;
            margin-bottom: 20px;
        }
        .hero p {
            font-size: 16px;
            margin-bottom: 40px;
            color: #666;
        }
        .hero img {
            width: 45%; /* Adjust the width as needed */
            height: auto; /* Maintain the aspect ratio */
            max-height: 400px; /* Set a maximum height */
            flex-shrink: 50; /* Prevent the image from shrinking */
        }
        .button {
            background-color: #4a90e2;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
        }
        .button:hover {
            background-color: #357abd;
        }
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
            position: relative;
            bottom: 0;
            width: 100%;
        }
        .footer a {
            color: #4a90e2;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        /* Responsive design */
     @media (max-width: 768px) {
    .container {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
        padding: 10px;
    }

    .main-content {
        width: 100%;
        padding: 10px;
    }
}
    </style>
</head>
<body>
<header>
        <div class="logo-title">
            <img src="logo.jpeg" alt="PSG Logo" class="logo">
            <h1>PSG Institute of Technology and Applied Research</h1>
        </div>
        <nav>
            <ul>
                <li><a href="home.php">Dashboard</a></li>
                <li><a href="order.php">My Order</a></li>
                <li><a href="history.php">History</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="contactus.php">Contact Us</a></li>
            </ul>
        </nav>
    </header>

    <div class="hero">
        <div class="text-content">
            <h1>Welcome to PSG Institute of Technology and Applied Research Printing Services</h1>
            <p>Providing high-quality printing services to students and faculty. Log in to get started.</p>
            <a class="button" href="login.php">Get Started</a>
        </div>
        <img src="printer1.webp" alt="Printer Image">
    </div>

    <div class="footer">
        <p>&copy; 2024 PSG Institute of Technology and Applied Research. All rights reserved.</p>
    </div>
</body>
</html>