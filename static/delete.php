<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["file"])) {
    $fileToDelete = $_GET["file"];
    $filePath = "../data/$userId/$fileToDelete";

    if (file_exists($filePath)) {
        unlink($filePath); // Delete the file
        header("Location: ../dashboard.php"); // Redirect back to dashboard
        exit();
    }
}
?>
