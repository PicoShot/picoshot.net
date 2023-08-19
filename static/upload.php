<?php
session_start();
require_once 'sqlconnect.php';

// Check if the user is logged in, otherwise terminate the script
if (!isset($_SESSION["user_id"])) {
    header("HTTP/1.1 401 Unauthorized");
    exit();
}

$userId = $_SESSION["user_id"];

$uploadDir = "../data/$userId/";

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (isset($_FILES["uploaded_file"])) {
    $uploadedFile = $_FILES["uploaded_file"];

    // Generate a random link
    $randomLink = generateRandomLink();

    // Define the file path and URL
    $actualFilename = basename($uploadedFile["name"]);
    $filePath = $uploadDir . $actualFilename;
    $fileUrl = "/data/$userId/$actualFilename"; // Store the complete path in real_file_url
    
    // Move the uploaded file
    if (move_uploaded_file($uploadedFile["tmp_name"], $filePath)) {
        // Store the file path and short URL in the database
        $sql = "INSERT INTO files (real_file_url, short_url) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $fileUrl, $randomLink);
        
        if ($stmt->execute()) {
            http_response_code(200);
        } else {
            http_response_code(500);
        }
        $stmt->close();
    } else {
        http_response_code(500);
    }
} else {
    http_response_code(400);
}

function generateRandomLink($length = 10) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $link = '';
    for ($i = 0; $i < $length; $i++) {
        $link .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $link;
}
?>
