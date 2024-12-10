<?php
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
}

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming form fields are properly named and sanitized
    $copies = $_POST['copies'] ?? '';
    $pages = $_POST['pages'] ?? '';
    $custom_pages = $_POST['custom_pages'] ?? '';
    $color = isset($_POST['color']) ? true : false;
    $orientation = $_POST['orientation'] ?? '';
    $sides = $_POST['sides'] ?? '';
    $paper_type = $_POST['paper_type'] ?? '';
    $message = $_POST['message'] ?? '';

    // File handling
    $fileNames = [];
    $fileCount = count($_FILES['files']['name']);
    for ($i = 0; $i < $fileCount; $i++) {
        $fileName = $_FILES['files']['name'][$i];
        $fileTempName = $_FILES['files']['tmp_name'][$i];
        $fileDest = 'uploads/' . $fileName; // Directory where files will be saved
        move_uploaded_file($fileTempName, $fileDest);
        $fileNames[] = $fileName; // Store file names in an array
    }

    // Store form data and file names in session for use in student.php
    $_SESSION['form_data'] = [
        'copies' => $copies,
        'pages' => $pages,
        'custom_pages' => $custom_pages,
        'files' => $fileNames, // Store file names in an array
        'color' => $color,
        'orientation' => $orientation,
        'sides' => $sides,
        'paper_type' => $paper_type,
        'message' => $message
    ];

    // Redirect to student.php
    header("Location: student.php");
    exit;
} else {
    // Redirect to combined_form.html if accessed directly without POST request
    header("Location: combined_form.html");
    exit;
}
?>
