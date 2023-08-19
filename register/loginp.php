<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once("../static/sqlconnect.php");
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $stmt = $conn->prepare("SELECT id, username, password, banned FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($userId, $dbUsername, $dbPassword, $banned);
        $stmt->fetch();
        if ($banned) {
            $error = "Your account has been banned.";
        } else {
          
            if (password_verify($password, $dbPassword)) {
                $_SESSION["user_id"] = $userId;
                $_SESSION["username"] = $dbUsername;
                header("Location: ../dashboard.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        }
    } else {
        $error = "Invalid username or password.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Login</h2>
    <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="login.php" method="post">
    <button type="submit" class="btn btn-primary">Back to login</button>
    </form>
</div>


</body>
</html>