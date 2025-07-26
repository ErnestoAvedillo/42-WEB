<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File - Camagru</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/footer.css">
</head>

<body>
    <?php
    $pageTitle = "Upload - Camagru";
    include 'views/header.php';
    ?>

    <?php
    $pageTitle = "sidebar - Camagru";
    include 'views/side_bar.php';
    ?>

    <main id="mainContent">
        <h1>Upload File</h1>

        <?php
        require_once '/database/User.php';

        $message = '';
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
            // Create database connection
            $database = new Database();
            $manager = $database->connect();
            $user = new User($manager, $database->getDatabase());

            $file = $_FILES['file'];
            $filename = $file['name'];
            $fileType = $file['type'];
            $fileSize = $file['size'];
            $fileTmp = $file['tmp_name'];

            // Validate file
            if (!$user->validateFileType($fileType)) {
                $error = "File type not allowed. Allowed types: JPG, PNG, GIF, PDF, DOC, DOCX, TXT";
            } elseif (!$user->validateFileSize($fileSize)) {
                $error = "File too large. Maximum size: 5MB";
            } else {
                // Read file content
                $fileContent = file_get_contents($fileTmp);

                // For demo purposes, we'll use a dummy user ID
                // In real application, you'd get this from session
                $userId = '507f1f77bcf86cd799439011'; // Dummy ObjectId

                // Store file
                $fileId = $user->storeFile($userId, $filename, $fileContent, $fileType, $fileSize);

                if ($fileId) {
                    $message = "File uploaded successfully! File ID: " . $fileId;
                } else {
                    $error = "Error uploading file.";
                }
            }
        }
        ?>

        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="success-message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form class="login-form-text" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file">Choose file:</label>
                <input type="file" id="file" name="file" required accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt">
            </div>
            <button type="submit">Upload File</button>
        </form>

        <div class="file-info">
            <h3>Supported file types:</h3>
            <ul>
                <li>Images: JPG, JPEG, PNG, GIF</li>
                <li>Documents: PDF, DOC, DOCX, TXT</li>
                <li>Maximum size: 5MB</li>
            </ul>
        </div>
    </main>

    <?php include 'views/footer.php'; ?>
    <script src="js/hide_bar.js"></script>
</body>

</html>