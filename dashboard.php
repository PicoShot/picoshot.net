<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: register/login.php");
    exit();
}

$username = $_SESSION["username"];
$userId = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["uploaded_file"])) {
    $uploadDir = "data/$userId/";
    $uploadedFile = $uploadDir . basename($_FILES["uploaded_file"]["name"]);

    if (move_uploaded_file($_FILES["uploaded_file"]["tmp_name"], $uploadedFile)) {
        $uploadMessage = "File uploaded successfully.";
    } else {
        $uploadMessage = "Error uploading file.";
    }
}
$userFiles = [];
if (is_dir("data/$userId")) {
    $userFiles = scandir("data/$userId");
    $userFiles = array_diff($userFiles, array(".", ".."));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Welcome, <?php echo $username; ?>!</h2>
    <p>This is your dashboard.</p>
    
    <h3>Upload File</h3>
    <form id="upload-form" action="static/upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="uploaded_file">
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>
    <div class="progress">
    <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
    const uploadForm = document.getElementById('upload-form');
    const progressBar = document.getElementById('progress-bar');

    uploadForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(uploadForm);
        const xhr = new XMLHttpRequest();

        xhr.open('POST', 'static/upload.php', true);

        xhr.upload.onprogress = function (event) {
            if (event.lengthComputable) {
                const percentComplete = (event.loaded / event.total) * 100;
                progressBar.style.width = percentComplete + '%';
            }
        };

        xhr.onload = function () {
            if (xhr.status === 200) {
                progressBar.style.width = '100%';
                setTimeout(function () {
                    progressBar.style.width = '0%';
                }, 1000);
            } else {
                alert('Error uploading file.');
            }
        };

        xhr.send(formData);
    });
});

</script>

    <?php if (isset($uploadMessage)) : ?>
        <p><?php echo $uploadMessage; ?></p>
    <?php endif; ?>
    
    <h3>Your Files</h3>
<ul>
    <?php foreach ($userFiles as $file) : ?>
        <li>
            <?php echo $file; ?>
            <a href="data/<?php echo $userId; ?>/<?php echo $file; ?>" download class="btn btn-sm btn-secondary">Download</a>
            <a href="/static/delete.php?file=<?php echo $file; ?>" class="btn btn-sm btn-danger">Delete</a>
            <button class="btn btn-sm btn-info" onclick="copyLink('<?php echo $file; ?>')">Copy Link</button>
        </li>
    <?php endforeach; ?>
</ul>

<script>
    function copyLink(filename) {
        const link = document.createElement('textarea');
        link.value = window.location.origin + '/data/<?php echo $userId; ?>/' + filename;
        document.body.appendChild(link);
        link.select();
        document.execCommand('copy');
        document.body.removeChild(link);
        alert('Link copied to clipboard!');
    }
</script>
    
    <a href="static/logout.php" class="btn btn-primary">Logout</a>
</div>

</body>
</html>
