<?php
require_once 'static/sqlconnect.php';

if (isset($_GET['short_url'])) {
    $shortUrl = $_GET['short_url'];


    $sql = "SELECT real_file_url FROM files WHERE short_url = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $shortUrl);
    $stmt->execute();
    $stmt->bind_result($realFileUrl);

    if ($stmt->fetch()) {

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($realFileUrl));
        readfile($realFileUrl);
        exit;
    } else {
        echo "File not found.";
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}
?>
