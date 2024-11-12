<?php
include("includes/head.php");
include("includes/navbar.php");
include("includes/sidebar.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id']; // Fetch user ID from session

// Check if the task ID is passed via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ensure task_id is set and is a valid integer
    if (isset($_POST['task_id']) && is_numeric($_POST['task_id'])) {
        $taskId = (int) $_POST['task_id']; // Cast to integer for safety

        // Prepare SQL query to delete the task from the database
        $query = "DELETE FROM user_tasks WHERE id = ? AND user_id = ?";
        $stmt = $mysqli->prepare($query);
        
        // Bind the task ID and user ID parameters to the SQL query
        $stmt->bind_param("ii", $taskId, $userId);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect back to tasks page after successful deletion
            header("Location: settings.php");
            exit();
        } else {
            // Handle errors if the deletion fails
            echo "Error deleting task. Please try again.";
        }
    } else {
        // Handle the case when task_id is not valid
        echo "Invalid task ID.";
    }
}
?>
