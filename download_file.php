<?php
include('connection.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "SELECT files FROM combined_form WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($file);
    $stmt->fetch();
    $stmt->close();

    if ($file) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="file_' . $id . '"');
        header('Content-Length: ' . strlen($file));
        echo $file;
        exit;
    }
}
echo "File not found.";
?>
