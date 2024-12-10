<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Redefine generateDownloadLink function
function generateDownloadLink($files) {
    $filePaths = json_decode($files, true);
    $links = '';
    foreach ($filePaths as $path) {
        $fileName = basename($path);
        $links .= "<a href='$path' download>$fileName</a><br>";
    }
    return $links;
}

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

// Fetch orders based on date within the last 5 minutes
$currentTime = time();
$expiryTime = $currentTime - (5 * 60); // 5 minutes in seconds
$expiryDate = date("Y-m-d H:i:s", $expiryTime);

$sql = "SELECT id, created_at, files, total_cost, total_pages FROM combined_form WHERE created_at > ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $expiryDate);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
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
    flex: 1; /* This ensures the container takes up available space, pushing the footer down */
}

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }
        .footer {
    background-color: #333;
    color: white;
    text-align: center;
    padding: 10px;
    width: 100%;
    position: absolute;
    bottom: 0;
    left: 0;
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
<?php include('navbar.html'); ?>
<div class="container">
    <h1 style="text-align: center;">Order History</h1>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Files</th>
                <th>Total Cost</th>
                <th>Total Pages</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td><?php echo generateDownloadLink($row['files']); ?></td>
                    <td>$<?php echo number_format($row['total_cost'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['total_pages']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<div class="footer">
        <p>&copy; 2024 PSG Institute of Technology and Applied Research. All rights reserved.</p>
    </div>

</body>
</html>
