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

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from the form
    $taskTitle = $_POST['task_title'];
    $taskDescription = $_POST['task_description'];
    $dueDate = $_POST['due_date'];
    $priority = $_POST['priority'];

    // Sanitize input to prevent SQL injection
    $taskTitle = htmlspecialchars($taskTitle);
    $taskDescription = htmlspecialchars($taskDescription);
    $dueDate = htmlspecialchars($dueDate);
    $priority = htmlspecialchars($priority);

    // Validate input
    if (!empty($taskTitle) && !empty($dueDate) && !empty($priority)) {
        // Prepare SQL query to insert the task into the database
        $query = "INSERT INTO user_tasks (user_id, title, description, due_date, priority, status) 
                  VALUES (?, ?, ?, ?, ?, 'pending')"; // Default status is 'pending'
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("issss", $userId, $taskTitle, $taskDescription, $dueDate, $priority);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect back to the tasks page after adding the task
            header("Location: settings.php");
            exit();
        } else {
            echo "Error adding task. Please try again.";
        }
    } else {
        echo "Please fill in all required fields.";
    }
}
?>
