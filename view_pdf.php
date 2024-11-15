<?php
include("includes/head.php");
include("includes/navbar.php");
include("includes/sidebar.php");

if (!isset($_GET['id'])) {
    header("Location: manage_documents.php");
    exit;
}

$documentId = $_GET['id'];
$userId = $_SESSION['user_id'];

$query = "SELECT document_name FROM Documents WHERE id = ? AND user_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("ii", $documentId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $document = $result->fetch_assoc();
    $filePath = "D:/bluestacts/htdocs/job/uploads/" . $document['document_name'];

    if (!file_exists($filePath)) {
        echo "<div class='alert alert-danger'>Error: File does not exist. <a href='manage_documents.php'>Back to Documents</a></div>";
        exit;
    }

    $webPath = "/job/uploads/" . htmlspecialchars($document['document_name']);
    $fileExtension = strtolower(pathinfo($document['document_name'], PATHINFO_EXTENSION));
} else {
    echo "<div class='alert alert-danger'>Document not found. <a href='manage_documents.php'>Back to Documents</a></div>";
    exit;
}
?>

<!-- Custom Styling -->
<style>
    .document-container {
        max-width: 80%;
        margin: auto;
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }
    .document-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #ddd;
    }
    .document-header h4 {
        margin: 0;
        color: #343a40;
    }
    .iframe-container {
        position: relative;
        padding-top: 56.25%;
        overflow: hidden;
        border: 1px solid #ddd;
        border-radius: 8px;
    }
    .iframe-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
        border-radius: 8px;
    }
    .back-button {
        background-color: #007bff;
        color: #fff;
        padding: 8px 16px;
        border-radius: 4px;
        text-decoration: none;
    }
    .back-button:hover {
        background-color: #0056b3;
        text-decoration: none;
    }
    .download-button {
        margin-top: 20px;
        display: inline-block;
        padding: 8px 16px;
        background-color: #28a745;
        color: white;
        border-radius: 4px;
        text-decoration: none;
    }
    .download-button:hover {
        background-color: #218838;
    }
</style>
<!-- Main content -->
<div class="page-container">
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="title">
                                <h4>View Document</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="index.php">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                       View Document
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

            <div class="card-box mb-30">
                <div class="pd-ltr-20">
                    <div class="document-container">
                        <div class="document-header">
                            <h4>View Document: <?php echo htmlspecialchars($document['document_name']); ?></h4>
                            <a href="manage_documents.php" class="back-button">Back to Documents</a>
                        </div>

                        <?php if (in_array($fileExtension, ['pdf', 'jpg', 'jpeg', 'png'])): ?>
                            <div class="iframe-container">
                                <iframe src="<?php echo $webPath; ?>" width="100%" height="600px"></iframe>
                            </div>
                        <?php else: ?>
                            <p>This document cannot be displayed directly in the browser. You can <a href="<?php echo $webPath; ?>" class="download-button" download>download it</a> to view.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("includes/script.php"); ?>
