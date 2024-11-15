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

// Check if the document ID is provided
if (!isset($_GET['id'])) {
    header("Location: manage_documents.php");
    exit;
}

$documentId = intval($_GET['id']); // Sanitize document ID input

// Fetch the document details
$query = "SELECT * FROM Documents WHERE id = ? AND user_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("ii", $documentId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Document not found or doesn't belong to user
    header("Location: manage_documents.php");
    exit;
}

$document = $result->fetch_assoc();
$stmt->close();

// Update document name on form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newDocumentName = trim($_POST['document_name']); // Trim whitespace

    // Validate document name
    if (strlen($newDocumentName) < 3 || strlen($newDocumentName) > 100) {
        $error = "Document name must be between 3 and 100 characters.";
    } else {
        // Get the current file path
        $currentFilePath = 'uploads/' . $document['document_name'];
        $newFilePath = 'uploads/' . $newDocumentName;

        // Update the document in the database
        $updateQuery = "UPDATE Documents SET document_name = ? WHERE id = ? AND user_id = ?";
        $updateStmt = $mysqli->prepare($updateQuery);
        $updateStmt->bind_param("sii", $newDocumentName, $documentId, $userId);

        if ($updateStmt->execute()) {
            // Rename the file in the directory
            if (rename($currentFilePath, $newFilePath)) {
                header("Location: manage_documents.php?message=updated");
                exit;
            } else {
                $error = "Failed to rename the file. Please try again.";
            }
        } else {
            $error = "Failed to update document. Please try again.";
        }
        $updateStmt->close();
    }
}
?>

<!-- Main content -->
<div class="page-container">
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="title">
                                <h4>Edit Document</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="index.php">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                       Edit Document
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

            <div class="card-box mb-30">
                <div class="pd-ltr-20">
                    <div class="card-box pd-20 box-shadow">
                        <h4 class="text-center">Edit Document</h4>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <form action="" method="POST">
                            <div class="form-group">
                                <label for="document_name">Document Name:</label>
                                <input type="text" name="document_name" id="document_name" 
                                       value="<?php echo htmlspecialchars($document['document_name']); ?>" 
                                       required class="form-control">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Update Document</button>
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
