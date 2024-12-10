<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Check if order ID is set in session
if (!isset($_SESSION['last_order_id'])) {
    header("Location: home.php");
    exit;
}

$orderID = $_SESSION['last_order_id'];

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$database = "mydatabase"; // Change to your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch order details
$sql = "SELECT * FROM combined_form WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderID);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

$stmt->close();
$conn->close();

// Check if order details were found
if (!$order) {
    echo "Order not found.";
    exit;
}

// Extract data
$createdAtDate = date("Y-m-d", strtotime($order['created_at']));
$totalPages = calculateTotalPages($order['pages'], $order['custom_pages']);
$totalCost = $order['total_cost']; // Assuming 'total_cost' is stored correctly in the database

// Function to calculate total pages
function calculateTotalPages($pages, $customPages) {
    if ($pages === 'custom') {
        return $customPages;
    } else {
        return $pages;
    }
}

// Function to generate downloadable file link
function generateDownloadLink($files) {
    $filePaths = json_decode($files, true);
    $links = '';
    foreach ($filePaths as $path) {
        $fileName = basename($path);
        $links .= "<a href='$path' download>$fileName</a><br>";
    }
    return $links;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="styles.css"> <!-- Adjust path as needed -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .order-details {
            margin-top: 20px;
            text-align: center;
        }

        .order-details h2 {
            margin-bottom: 10px;
        }

        .order-details p {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }

        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            width: 30%;
        }

        td {
            width: 70%;
        }

        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            text-align: center;
        }

        .button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include('nav_order.html'); ?>

    <div class="container">
        <h1 style="text-align: center;">Order Details</h1>
        <table>
            <tr>
                <th>Order ID</th>
                <td><?php echo htmlspecialchars($order['id']); ?></td>
            </tr>
            <tr>
                <th>Date</th>
                <td><?php echo htmlspecialchars($createdAtDate); ?></td>
            </tr>
            <tr>
                <th>Files</th>
                <td><?php echo generateDownloadLink($order['files']); ?></td>
            </tr>
            <tr>
                <th>Total Cost</th>
                <td>$<?php echo number_format($totalCost, 2); ?></td>
            </tr>
            <tr>
                <th>Total Pages</th>
                <td><?php echo htmlspecialchars($totalPages); ?></td>
            </tr>
        </table>

        <div class="order-details">
            <h2>Thank you for your order!</h2>
            <p>We will process your order soon.</p>
        </div>

        <div class="button-container">
            <a href="combined_form.php" class="button">Order Again</a> 
        </div>
    </div>
</body>
</html>


