<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../static/sqlconnect.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    // Check if the username or email already exists in the database
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $error = "Username or email already exists.";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Get the user's IP address
        $ip = $_SERVER["REMOTE_ADDR"];
        
        // Get the current date and time
        $createdTime = date("Y-m-d H:i:s");
        
        // Set the 'banned' column to 0 by default
        $banned = 0;
        $randomId = uniqid();
        
        // Create a prepared statement
        $stmt = $conn->prepare("INSERT INTO users (id, username, email, password, ip, created_time, banned) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $randomId, $username, $email, $hashedPassword, $ip, $createdTime, $banned);
        
        if ($stmt->execute()) {
            // Get the user's ID
            $userId = $stmt->insert_id;
            
            // Create a folder for the user's data using the PHP-generated ID as the folder name
            $userFolder = "../data/" . $randomId;
            if (!file_exists($userFolder)) {
                mkdir($userFolder, 0777, true); // Create directory with permissions
            }
            
            // Registration successful
            header("Location: login.php");
            exit();
        } else {
            $error = "Registration failed. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Register</h2>
    <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="register.php" method="post">
    <button type="submit" class="btn btn-primary">Back to register</button>
    </form>
    
</div>

</body>
</html>