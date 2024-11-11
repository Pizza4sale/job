<?php
include("includes/head.php");


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

session_start(); // Start session to use session variables

if (isset($_GET['id'])) {
    $applicationId = intval($_GET['id']);
    $userId = $_SESSION['user_id'];

    // Prepare the delete statement
    $query = "DELETE FROM Applications WHERE id = ? AND user_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ii", $applicationId, $userId);
    
    if ($stmt->execute()) {
        // Set success message in session
        $_SESSION['message'] = "Application deleted successfully.";
    } else {
        // Set error message in session
        $_SESSION['message'] = "Error deleting application.";
    }

    $stmt->close();
    // Redirect back to application management page without showing the ID
    header("Location: applications.php");
    exit;
} else {
    // Redirect back with an error if no ID is provided
    $_SESSION['message'] = "No application ID provided.";
    header("Location: applications.php");
    exit;
}
?>
