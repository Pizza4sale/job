<?php
include("includes/head.php");

// Initialize variables to store feedback messages
$feedback_message = '';
$username = '';
$email = '';
$full_name = '';
$phone = '';
$country = '';
$address = '';
$bio = '';
$facebook = '';
$twitter = '';
$linkedin = '';

// Registration script
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $phone = trim($_POST['phone']);
    $country = trim($_POST['country']);
    $address = trim($_POST['address']);
    $bio = trim($_POST['bio']);
    $facebook = trim($_POST['facebook']);
    $twitter = trim($_POST['twitter']);
    $linkedin = trim($_POST['linkedin']);

    // Simple validation
    if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
        $feedback_message = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $feedback_message = "Invalid email format!";
    } else {
        // Check if the username or email already exists
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $feedback_message = "Username or email already exists!";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Prepare and execute the SQL statement
            $stmt = $mysqli->prepare("INSERT INTO users (full_name, username, email, password, phone, country, address, bio, facebook, twitter, linkedin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sssssssssss", $full_name, $username, $email, $hashed_password, $phone, $country, $address, $bio, $facebook, $twitter, $linkedin);
                if ($stmt->execute()) {
                    header("Location: login.php");
                    exit(); // Ensure no further code is executed after redirection
                } else {
                    $feedback_message = "Error occurred while registering. Please try again.";
                }
                $stmt->close();
            } else {
                $feedback_message = "Database query failed!";
            }
        }
        $stmt->close();
    }
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        /* Custom styles for centering the form */
        .login-page {
            display: flex;
            flex-direction: column;
            height: 100vh;
            justify-content: center;
        }
        .login-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-box {
            width: 100%;
            max-width: 500px; /* Adjust as needed */
        }
    </style>
</head>
<body class="login-page">
<div class="login-header box-shadow">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="brand-logo">
            <a href="login.php" style="text-decoration: none; color: #1b00ff;">
                <h1 style="margin: 0;">Job Application</h1>
            </a>
        </div>
        <div class="login-menu">
            <ul>
                <li><a href="login.php">Login</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="login-wrap d-flex align-items-center flex-wrap justify-content-center">
    <div class="container">
        <div class="col-md-12 col-lg-12">
            <div class="login-box bg-white box-shadow border-radius-10">
                <div class="login-title">
                    <h2 class="text-center text-primary">Register</h2>
                </div>
                <?php if ($feedback_message): ?>
                    <div class="alert alert-danger text-center"><?php echo htmlspecialchars($feedback_message); ?></div>
                <?php endif; ?>
                <form method="post" action="">
                    <div class="input-group custom">
                        <input type="text" name="full_name" class="form-control form-control-lg" placeholder="Full Name" value="<?php echo htmlspecialchars($full_name); ?>" required />
                        <div class="input-group-append custom">
                            <span class="input-group-text"><i class="dw dw-user1"></i></span>
                        </div>
                    </div>
                    <div class="input-group custom">
                        <input type="text" name="username" class="form-control form-control-lg" placeholder="Username" value="<?php echo htmlspecialchars($username); ?>" required />
                        <div class="input-group-append custom">
                            <span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
                        </div>
                    </div>
                    <div class="input-group custom">
                        <input type="email" name="email" class="form-control form-control-lg" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required />
                        <div class="input-group-append custom">
                            <span class="input-group-text"><i class="icon-copy dw dw-mail1"></i></span>
                        </div>
                    </div>
                    <div class="input-group custom">
                        <input type="password" name="password" class="form-control form-control-lg" placeholder="Password" autocomplete="new-password" required />
                        <div class="input-group-append custom">
                            <span class="input-group-text"><i class="dw dw-padlock1"></i></span>
                        </div>
                    </div>
                    <div class="input-group custom">
                        <input type="text" name="phone" class="form-control form-control-lg" placeholder="Phone" value="<?php echo htmlspecialchars($phone); ?>" />
                    </div>
                    <div class="input-group custom">
                        <input type="text" name="country" class="form-control form-control-lg" placeholder="Country" value="<?php echo htmlspecialchars($country); ?>" />
                    </div>
                    <div class="input-group custom">
                        <textarea name="address" class="form-control form-control-lg" placeholder="Address"><?php echo htmlspecialchars($address); ?></textarea>
                    </div>
                    <div class="input-group custom">
                        <textarea name="bio" class="form-control form-control-lg" placeholder="Bio"><?php echo htmlspecialchars($bio); ?></textarea>
                    </div>
                    <div class="input-group custom">
                        <input type="text" name="facebook" class="form-control form-control-lg" placeholder="Facebook Profile URL" value="<?php echo htmlspecialchars($facebook); ?>" />
                    </div>
                    <div class="input-group custom">
                        <input type="text" name="twitter" class="form-control form-control-lg" placeholder="Twitter Profile URL" value="<?php echo htmlspecialchars($twitter); ?>" />
                    </div>
                    <div class="input-group custom">
                        <input type="text" name="linkedin" class="form-control form-control-lg" placeholder="LinkedIn Profile URL" value="<?php echo htmlspecialchars($linkedin); ?>" />
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="input-group mb-0">
                                <input class="btn btn-primary btn-lg btn-block" type="submit" name="Sign_up" value="Register" />
                            </div>
                        </div>
                    </div>
                    <div class="font-16 weight-600 pt-10 pb-10 text-center" data-color="#707373">
                        OR
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-12 text-center">
                            <a href="login.php" class="btn btn-outline-primary btn-lg btn-block">Already have an account? Login</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
