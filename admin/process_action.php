<?php
session_start();

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
        $mail->Username = 'your-email@gmail.com'; // Your Gmail address
        $mail->Password = 'your-app-password'; // Your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // SMTP Debugging
        $mail->SMTPDebug = 0; // Set to 2 for more verbose output

        // Recipients
        $mail->setFrom('your-email@gmail.com', 'Admin');
        $mail->addAddress($recipientEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        // Commented out in production
        // echo "Email sent successfully!";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

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
        $table = 'rejected'; // Updated to 'rejected'
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
