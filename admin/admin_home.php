<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Homepage - PSG Institute Printing Services</title>
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
            padding: 100px 20px 50px; /* Adjusted padding to account for the fixed navbar */
            display: flex;
            justify-content: center;
            align-items: center;
            border-bottom: 1px solid #eaeaea;
            text-align: center;
            flex-grow: 1;
        }
        .hero .text-content {
            max-width: 45%;
            margin-right: 20px;
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
            width: 45%;
            height: auto;
            max-height: 400px;
            flex-shrink: 0;
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
    </style>
</head>
<body>
    <?php include('navbar1.html'); ?>

    <div class="hero">
        <div class="text-content">
            <h1>Welcome to PSG Institute of Technology and Applied Research Admin Panel</h1>
            <p>Manage printing services and orders efficiently. Log in to access admin features.</p>
            <a class="button" href="admin_login.php">Admin Login</a>
        </div>
        <img src="printer.webp" alt="Printer Image">
    </div>

    <div class="footer">
        <p>&copy; 2024 PSG Institute of Technology and Applied Research. All rights reserved.</p>
    </div>
</body>
</html>
