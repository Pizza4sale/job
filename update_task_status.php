<?php
include("includes/head.php");
include("includes/navbar.php");
include("includes/sidebar.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id']; // Use the user_id from session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure task_id is set and is a valid integer
    if (isset($_POST['task_id']) && is_numeric($_POST['task_id'])) {
        $taskId = (int) $_POST['task_id']; // Safely cast task_id to integer
        $status = $_POST['status'];

        // Validate status
        if (in_array($status, ['pending', 'in_progress', 'completed'])) {

            // Check if the task belongs to the logged-in user
            $query = "SELECT id FROM user_tasks WHERE id = ? AND user_id = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ii", $taskId, $userId);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // Task belongs to the logged-in user, proceed with updating the status
                $updateQuery = "UPDATE user_tasks SET status = ? WHERE id = ?";
                $stmt = $mysqli->prepare($updateQuery);
                $stmt->bind_param("si", $status, $taskId);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    // Redirect back to tasks page after successfully updating the status
                    header("Location: settings.php");
                    exit();
                } else {
                    // Handle case when no rows were affected (status not updated)
                    echo "Failed to update task status. Please try again.";
                }
            } else {
                // Handle case when the task does not belong to the current user
                echo "You do not have permission to update this task.";
            }
        } else {
            // Handle invalid status
            echo "Invalid status value.";
        }
    } else {
        // Handle missing or invalid task ID
        echo "Invalid task ID.";
    }
}
?>
