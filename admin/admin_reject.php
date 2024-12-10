<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Include database connection
include('connection.php');

// Delete records older than 2 minutes
$delete_old_records_sql = "DELETE FROM reject WHERE rejected_at < NOW() - INTERVAL 1000 MINUTE";
$conn->query($delete_old_records_sql);

// Fetch all records from the history table
$sql = "SELECT * FROM reject";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin History</title>
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
            background-color: #f44336; /* Red color */
            color: white;
        }
    </style>
</head>
<body>
    <?php include('navbar1.html'); ?>
    <div class="container">
        <h1 class="text-center">Admin History</h1>
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
                    <th>Payment Status</th>
                    <th>Rejected At</th>
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

                        echo "<td>" . $row["payment_status"] . "</td>";
                        echo "<td>" . $row["rejected_at"] . "</td>";
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
