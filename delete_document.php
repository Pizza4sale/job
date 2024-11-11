<?php
include("includes/head.php");
include("includes/navbar.php");
include("includes/sidebar.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if the document ID is set
if (!isset($_GET['id'])) {
    header("Location: manage_documents.php");
    exit;
}

$documentId = $_GET['id'];
$userId = $_SESSION['user_id'];

// Fetch the document information
$query = "SELECT document_name FROM Documents WHERE id = ? AND user_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("ii", $documentId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $document = $result->fetch_assoc();
    $filePath = "uploads/" . $document['document_name'];

    // Delete the document from the filesystem
    if (file_exists($filePath)) {
        unlink($filePath); // Delete the file
    }

    // Delete the document from the database
    $deleteQuery = "DELETE FROM Documents WHERE id = ? AND user_id = ?";
    $deleteStmt = $mysqli->prepare($deleteQuery);
    $deleteStmt->bind_param("ii", $documentId, $userId);
    $deleteStmt->execute();

    if ($deleteStmt->affected_rows > 0) {
        header("Location: manage_documents.php?message=deleted");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Failed to delete document from database.</div>";
    }
    $deleteStmt->close();
} else {
    echo "<div class='alert alert-danger'>Document not found.</div>";
}
$stmt->close();
?>

<?php include("includes/script.php"); ?>
