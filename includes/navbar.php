<?php
// Start the session only if it hasn't been started yet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user data, including the username
$query = "SELECT username, profile_picture FROM users WHERE id = ?";
$stmt = $mysqli->prepare($query);
if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    // Log any errors for debugging
    error_log("Database error: " . $mysqli->error);
}

?>

<div class="header">
    <div class="header-left">
        <div class="menu-icon bi bi-list"></div>
        <div class="search-toggle-icon bi bi-search" data-toggle="header_search"></div>
        <div class="header-search">
            <form action="search.php" method="GET">
                <div class="form-group mb-0">
                    <i class="dw dw-search2 search-icon"></i>
                    <input type="text" class="form-control search-input" name="query" placeholder="Search Here" id="searchInput" />
                </div>
                <div id="searchResults"></div>
            </form>
        </div>
    </div>
    <div class="header-right">

        <div class="user-info-dropdown">
            <div class="dropdown">
                <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                    <span class="user-icon">
                        <img src="uploads/<?php echo htmlspecialchars($user['profile_picture'] ?? 'default.png'); ?>" alt="Profile Picture" />
                    </span>
                    <span class="user-name"><?php echo htmlspecialchars($user['username']); ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                    <a class="dropdown-item" href="profile.php"><i class="dw dw-user1"></i> Profile</a>
                    <a class="dropdown-item" href="settings.php"><i class="dw dw-settings2"></i> Settings</a>
                    <a class="dropdown-item" href="logout.php"><i class="dw dw-logout"></i> Log Out</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* CSS for standard scrollbar and additional styles */
.standard-scrollbar {
    overflow-y: auto;
    max-height: 350px; /* Adjust the maximum height as needed */
    padding: 10px; /* Add padding for a cleaner look */
}

/* Style for user icon */
.user-icon img {
    width: 50px; /* Set the desired width */
    height: 50px; /* Set the desired height */
    border-radius: 50%; /* Make it circular */
    object-fit: cover; /* Ensure the image covers the space without distortion */
}

/* Style for individual notifications */
#notification-list p {
    margin: 0 0 15px; /* Add spacing between notifications */
    padding: 15px;
    background-color: #fff; /* Background color for notifications */
    border-radius: 8px; /* Rounded corners for notifications */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Add a subtle box shadow */
    transition: background-color 0.3s ease; /* Smooth background color transition */

    /* Text styles */
    font-size: 14px;
    color: #333;
}

#notification-list a {
    text-decoration: none;
    color: #007bff; /* Link color */
    font-weight: bold;
}

#notification-list a:hover {
    background-color: #f5f5f5; /* Background color on hover */
}

#notification-list p.no-notifications {
    text-align: center;
    font-style: italic;
    color: #888;
}
</style>
