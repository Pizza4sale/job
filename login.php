<?php
include("includes/head.php");

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];  // Use a single field for both email and username
    $password = $_POST['password'];

    // Prepare and execute the SQL statement to check if the login is either an email or a username
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $login, $login); // Bind both email and username to the query
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit(); // Ensure no further code is executed after redirection
        } else {
            $error_message = "Incorrect email/username or password. Please try again.";
        }

        $stmt->close();
    } else {
        $error_message = "Database query failed!";
    }
}
?>

<style>
    .login-wrap {
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-box {
        width: 100%;
        max-width: 500px;
        padding: 20px;
    }

    .logo-text h1 {
        margin: 0;
        font-size: 2em; /* Adjust size as needed */
        color: #1b00ff; /* Your preferred color */
        font-weight: bold; /* Optional styling */
    }
</style>

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
                <li><a href="register.php">Register</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="login-wrap d-flex align-items-center justify-content-center">
    <div class="login-box bg-white box-shadow border-radius-10">
        <div class="login-title">
            <h2 class="text-center text-primary">Login To Job Application</h2>
        </div>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert" aria-live="assertive"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="input-group custom">
                <input type="text" name="login" class="form-control form-control-lg" placeholder="Email or Username" required />
                <div class="input-group-append custom">
                    <span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
                </div>
            </div>
            <div class="input-group custom">
                <input type="password" name="password" class="form-control form-control-lg" placeholder="Password" autocomplete="current-password" required>
                <div class="input-group-append custom">
                    <span class="input-group-text"><i class="dw dw-padlock1"></i></span>
                </div>
            </div>
            <div class="row pb-30">
                <div class="col-6">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="customCheck1" />
                        <label class="custom-control-label" for="customCheck1">Remember</label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="forgot-password">
                        <a href="forgotpass.php">Forgot Password?</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="input-group mb-0">
                        <input class="btn btn-primary btn-lg btn-block" type="submit" name="Sign_in" value="Sign In" />
                    </div>
                    <div class="font-16 weight-600 pt-10 pb-10 text-center" data-color="#707373">
                        OR
                    </div>
                    <div class="input-group mb-0">
                        <a class="btn btn-outline-primary btn-lg btn-block" href="register.php">Register To Create Account</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>
