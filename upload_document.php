<?php

include("includes/head.php");
include("includes/navbar.php");
include("includes/sidebar.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $documentName = $_FILES['document']['name'];
    $documentTmpName = $_FILES['document']['tmp_name'];
    $uploadDate = date('Y-m-d H:i:s');
    
    // Specify allowed file types
    $allowedFileTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    $fileExtension = strtolower(pathinfo($documentName, PATHINFO_EXTENSION));

    // Check if the uploaded file type is allowed
    if (in_array($fileExtension, $allowedFileTypes)) {
        // Sanitize the file name
        $documentName = preg_replace("/[^a-zA-Z0-9_\-\.]/", "_", $documentName);
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($documentName);

        // Move uploaded file to a specific directory
        if (move_uploaded_file($documentTmpName, $uploadFile)) {
            // Save document info to the database
            $query = "INSERT INTO Documents (user_id, document_name, upload_date) VALUES (?, ?, ?)";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("iss", $userId, $documentName, $uploadDate);

            if ($stmt->execute()) {
                header("Location: manage_documents.php?message=uploaded");
                exit;
            } else {
                $error = "Failed to upload document. Please try again.";
            }
            $stmt->close();
        } else {
            $error = "Failed to move uploaded file.";
        }
    } else {
        $error = "Invalid file type. Only PDF, DOC, DOCX, JPG, and PNG files are allowed.";
    }
}
?>

<!-- Main content -->
<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Upload Document</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-box mb-30">
                <div class="pd-ltr-20">
                    <div class="card-box pd-20 box-shadow">
                        <h4 class="text-center">Upload Your Document</h4>
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="document">Select Document:</label>
                                <input type="file" name="document" id="document" required class="form-control">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Upload Document</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include("includes/script.php");
?>
