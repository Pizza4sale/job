<?php
ob_start();

date_default_timezone_set('Asia/Manila');
session_start();
include("includes/db.php");

// Set the session timeout to 30 minutes
define('SESSION_TIMEOUT', 1800); // 30 minutes in seconds

// Check if the session variable for the last activity timestamp is set
if (isset($_SESSION['last_activity'])) {
    // Check if the user has been inactive for too long
    if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        // Expire the session and redirect to login or perform other actions
        session_unset();
        session_destroy();
        header("Location: login.php"); // Replace with your login page
        exit();
    }
}


// Update the last activity timestamp in the session
$_SESSION['last_activity'] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Job</title>

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="vendors/styles/core.css" />
    <link rel="stylesheet" type="text/css" href="vendors/styles/icon-font.min.css" />
    <link rel="stylesheet" type="text/css" href="src/plugins/datatables/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" type="text/css" href="src/plugins/datatables/css/responsive.bootstrap4.min.css" />
    <link rel="stylesheet" type="text/css" href="src/plugins/fancybox/dist/jquery.fancybox.css" />
    <link rel="stylesheet" type="text/css" href="src/plugins/dropzone/src/dropzone.css" />
    <link rel="stylesheet" type="text/css" href="src/plugins/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" type="text/css" href="src/plugins/cropperjs/dist/cropper.css" />
    <link rel="stylesheet" type="text/css" href="src/plugins/fullcalendar/fullcalendar.css" />
    <link rel="stylesheet" type="text/css" href="vendors/styles/style.css" />
</head>
