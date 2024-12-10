<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Check if form data is received
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['formData'], $_POST['pageOption'], $_POST['totalCost'], $_POST['totalPages'])) {
    // Decode and retrieve form data
    $formData = json_decode($_POST['formData'], true);
    $pageOption = $_POST['pageOption'];
    $totalCost = floatval($_POST['totalCost']); // Sanitize and convert to float
    $totalPages = intval($_POST['totalPages']); // Sanitize and convert to integer

    // Extract necessary data for database insertion
    $name = $_SESSION['username'] ?? ''; // Assuming username is stored in session
    $files = isset($formData['files']) ? json_encode($formData['files']) : '';
    $color = isset($formData['color']) ? ($formData['color'] ? 1 : 0) : 0;
    $orientation = isset($formData['orientation']) ? $formData['orientation'] : '';
    $copies = isset($formData['copies']) ? intval($formData['copies']) : 0;
    $sides = isset($formData['sides']) ? $formData['sides'] : '';
    $paperType = isset($formData['paper_type']) ? $formData['paper_type'] : '';
    $pages = isset($formData['pages']) ? $formData['pages'] : '';
    $customPages = isset($formData['custom_pages']) ? $formData['custom_pages'] : '';
    $message = isset($formData['message']) ? $formData['message'] : '';

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

    // Prepare SQL statement
    $sql = "INSERT INTO combined_form (name, files, color, orientation, copies, sides, paper_type, pages, custom_pages, message, payment_status, total_cost, total_pages)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Success', ?, ?)";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    // Check if prepare() succeeded
    if ($stmt === false) {
        die('Error preparing statement: ' . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("ssisisssssdd", $name, $files, $color, $orientation, $copies, $sides, $paperType, $pages, $customPages, $message, $totalCost, $totalPages);

    // Execute SQL statement
    if ($stmt->execute()) {
        // Successfully inserted into database
        $orderID = $stmt->insert_id; // Get the ID of the inserted order
        $_SESSION['last_order_id'] = $orderID; // Store order ID in session
        
        $stmt->close();
        $conn->close();
        
        // Redirect to order.php to display the order details
        header("Location: order.php");
        exit();
    } else {
        // Error inserting into database
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Redirect to home.php if form data is not properly received
    header("Location: home.php");
    exit();
}
?>
