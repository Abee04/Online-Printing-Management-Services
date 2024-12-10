<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Include database connection
include('connection.php');

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Function to send email
function sendEmail($recipientEmail, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tmadhumitha24@gmail.com'; // Your Gmail address
        $mail->Password = 'shze gwbv rgui rpek'; // Your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // SMTP Debugging
        $mail->SMTPDebug = 2; // Set to 2 for less verbose, 3 or 4 for more detailed

        // Recipients
        $mail->setFrom('tmadhumitha24@gmail.com', 'Admin');
        $mail->addAddress($recipientEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        echo "Email sent successfully!";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Fetch all records from the combined_form table
$sql = "SELECT * FROM combined_form";
$result = $conn->query($sql);

// Update status based on button click
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $action = $_POST['action'];

    // Fetch the row to be moved
    $select_sql = "SELECT * FROM combined_form WHERE id=?";
    $stmt = $conn->prepare($select_sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        die("No record found for ID $id");
    }

    $username = $row['name']; // Fetch the username from the combined_form table

    // Fetch the user's email from the signup table based on the username
    $email_query = "SELECT email FROM signup WHERE username=?";
    $email_stmt = $conn->prepare($email_query);

    if (!$email_stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $email_stmt->bind_param("s", $username);
    $email_stmt->execute();
    $email_result = $email_stmt->get_result();
    $user = $email_result->fetch_assoc();
    $recipientEmail = $user['email'];  // User's email fetched from the signup table

    echo "Recipient Email: " . $recipientEmail; // Debugging line

    if ($action == 'accept') {
        $status = 'Accepted';
        $table = 'accepted';
        $subject = 'Print Order Accepted';
        $message = 'Your print order has been successfully processed.';

        // Insert into accepted table
        $insert_sql = "INSERT INTO $table (name, files, color, orientation, copies, sides, paper_type, pages, custom_pages, total_pages, total_cost, message, payment_status, status, created_at, accepted_at) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($insert_sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sssissssssssss", $row['name'], $row['files'], $row['color'], $row['orientation'], $row['copies'], $row['sides'], $row['paper_type'], $row['pages'], $row['custom_pages'], $row['total_pages'], $row['total_cost'], $row['message'], $row['payment_status'], $status);
    } elseif ($action == 'reject') {
        $status = 'Rejected';
        $table = 'reject'; // Updated to 'reject'
        $subject = 'Print Order Rejected';
        $message = 'Unfortunately, your print order was rejected.';

        // Insert into reject table with rejected_at timestamp
        $insert_sql = "INSERT INTO $table (name, files, color, orientation, copies, sides, paper_type, pages, custom_pages, total_pages, total_cost, message, payment_status, status, created_at, rejected_at) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($insert_sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sssissssssssss", $row['name'], $row['files'], $row['color'], $row['orientation'], $row['copies'], $row['sides'], $row['paper_type'], $row['pages'], $row['custom_pages'], $row['total_pages'], $row['total_cost'], $row['message'], $row['payment_status'], $status);
    }

    // Execute the insert query
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    // Send email to the user
    sendEmail($recipientEmail, $subject, $message);

    // Delete from the combined_form table
    $delete_sql = "DELETE FROM combined_form WHERE id=?";
    $stmt = $conn->prepare($delete_sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    header("Location: admin_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            background-color: #4CAF50;
            color: white;
        }
        .btn-container {
            display: flex;
            gap: 10px; /* Adjust gap between buttons as needed */
        }
        .custom-btn {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            border: 2px solid transparent;
            cursor: pointer;
        }
        .accept-btn {
            color: #4CAF50;
            border-color: #4CAF50;
            background-color: white;
        }
        .reject-btn {
            color: #F44336;
            border-color: #F44336;
            background-color: white;
        }
        .accept-btn:hover, .reject-btn:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <?php include('navbar1.html'); ?>
    <div class="container">
        <h1 class="text-center">Admin Dashboard</h1>
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
                    <th>Created At</th>
                    <th>Action</th>
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
                        // Handle multiple files stored in the 'files' column
                        $files = json_decode($row["files"], true); // Assuming 'files' is stored as a JSON array
                        if ($files && is_array($files)) {
                            echo "<td>";
                            foreach ($files as $file) {
                                $fileUrl = '/project/uploads/' . urlencode($file);
                                echo "<a href='#' onclick='printFile(\"$fileUrl\")'>" . htmlspecialchars($file) . "</a><br>";
                            }
                            echo "</td>";
                        } else {
                            echo "<td>No files</td>";
                        }

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
                        echo "<td>" . $row["created_at"] . "</td>";
                        echo "<td>";
                        echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' class='btn-container'>";
                        echo "<input type='hidden' name='id' value='" . $row["id"] . "'>";
                        echo "<button type='submit' name='action' value='accept' class='btn custom-btn accept-btn'>Accept</button>";
                        echo "<button type='submit' name='action' value='reject' class='btn custom-btn reject-btn'>Reject</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='17' class='text-center'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-2w+2KwO+PBM08GkM0IBsUkmLX0k3ZW4fojcAq3m3t6kxnM7bDo9QJ0Fk3rbDfi3gS" crossorigin="anonymous"></script>
</body>
</html>
