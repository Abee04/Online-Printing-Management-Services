<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Include database connection
include('connection.php');

// Define the file size threshold (e.g., 3 GB)
define('SIZE_THRESHOLD', 3 * 1024 * 1024 * 1024); // 3 GB in bytes

// Function to get file size from the file path
function getFileSize($filePath) {
    return file_exists($filePath) ? filesize($filePath) : 0;
}

// Calculate the total size of files in the accepted table
$totalSize = 0;
$sql = "SELECT file_path FROM accepted"; // Replace 'file_path' with your column name for file paths
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fileSize = getFileSize($row['file_path']);
        $totalSize += $fileSize;
    }
}

// If the total size exceeds the threshold, delete all records from the accepted table
if ($totalSize > SIZE_THRESHOLD) {
    $delete_all_records_sql = "DELETE FROM accepted";
    $conn->query($delete_all_records_sql);
}

// Fetch all records from the accepted table
$sql = "SELECT * FROM accepted";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Accepted</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding-top: 56px; /* Height of the navbar */
        }
        .container {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #007bff; /* Blue color */
            color: white;
        }
    </style>
</head>
<body>
    <?php include('navbar1.html'); ?>
    <div class="container">
        <h1 class="text-center">Admin Accepted</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Files</th>
                    <th>Color</th>
                    <th>Orientation</th>
                    <th>Copies</th>
                    <th>Sides</th>
                    <th>Paper Type</th>
                    <th>Pages</th>
                    <th>Custom Pages</th>
                    <th>Total Pages</th>
                    <th>Total Cost</th>
                    <th>Message</th>
                    <th>Payment Status</th>
                    <th>Status</th>
                    <th>Accepted At</th>
                    <th>Received At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td><a href='download_file.php?id=" . $row["id"] . "'>Download</a></td>";
                        echo "<td>" . ($row["color"] ? 'Yes' : 'No') . "</td>";
                        echo "<td>" . $row["orientation"] . "</td>";
                        echo "<td>" . $row["copies"] . "</td>";
                        echo "<td>" . $row["sides"] . "</td>";
                        echo "<td>" . $row["paper_type"] . "</td>";
                        echo "<td>" . $row["pages"] . "</td>";
                        echo "<td>" . $row["custom_pages"] . "</td>";
                        echo "<td>" . $row["total_pages"] . "</td>";
                        echo "<td>" . $row["total_cost"] . "</td>";
                        echo "<td>" . $row["message"] . "</td>";
                        echo "<td>" . $row["payment_status"] . "</td>";
                        echo "<td>" . $row["status"] . "</td>";
                        echo "<td>" . $row["accepted_at"] . "</td>";
                        echo "<td>" . $row["created_at"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='17' class='text-center'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
