<?php
if (isset($_GET['file'])) {
    $file = $_GET['file'];
    
    // Assuming your files are stored in the 'uploads' folder
    $filePath = 'uploads/' . basename($file); // Prevent directory traversal by using basename

    // Check if the file exists in the uploads directory
    if (file_exists($filePath)) {
        // Get the MIME type of the file (like PDF, DOCX, etc.)
        $fileType = mime_content_type($filePath);

        // Set headers to display the file in the browser
        header("Content-Type: $fileType");
        header("Content-Disposition: inline; filename=\"" . basename($file) . "\"");

        // Output the file contents
        readfile($filePath);
    } else {
        echo "File not found in the uploads directory.";
    }
} else {
    echo "No file specified.";
}
?>
