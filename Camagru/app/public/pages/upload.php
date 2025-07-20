<div class="uopload-container">
    <h1>Upload Your Photos</h1>
    <p>Share your creativity with the world by uploading your photos.</p>
    <form action="upload_handler.php" method="post" enctype="multipart/form-data">
        <input type="file" name="photo" accept="image/*" required>
        <button type="submit" class="btn btn-primary">Upload Photo</button>
    </form>
    <p>Supported formats: JPG, PNG, GIF</p>
</div>
<link rel="stylesheet" href="css/upload.css">