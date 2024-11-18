
<?php
include("includes/head.php");

// Feedback message
$feedback_message = '';

// Retrieve the current step (default to Step 1)
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

// Initialize session variables for registration data if not already set
if (!isset($_SESSION['register_data'])) {
    $_SESSION['register_data'] = [
        'user_info' => [
            'full_name' => '',
            'username' => '',
            'email' => '',
            'password' => '',
            'phone' => '',
            'country' => '',
            'address' => '',
            'facebook' => '',
            'twitter' => '',
            'linkedin' => '',
        ],
        'education' => [],
        'experience' => [],
        'certificates' => [],
    ];
}

// Reference for easier updates
$register_data = &$_SESSION['register_data'];

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($step) {
        case 1: // Step 1: Basic Details
            $register_data['user_info']['full_name'] = trim($_POST['full_name']);
            $register_data['user_info']['username'] = trim($_POST['username']);
            $register_data['user_info']['email'] = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $register_data['user_info']['password'] = $_POST['password'];

            // Simple password strength check (minimum 8 characters)
            if (strlen($register_data['user_info']['password']) < 8) {
                $feedback_message = "Password must be at least 8 characters!";
            } elseif (empty($register_data['user_info']['full_name']) || empty($register_data['user_info']['username']) || empty($register_data['user_info']['email']) || empty($register_data['user_info']['password'])) {
                $feedback_message = "All fields in Step 1 are required!";
            } elseif (!filter_var($register_data['user_info']['email'], FILTER_VALIDATE_EMAIL)) {
                $feedback_message = "Invalid email format!";
            } else {
                // Check if email or username already exists
                $email = $register_data['user_info']['email'];
                $username = $register_data['user_info']['username'];
                $stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR username = ?");
                $stmt->bind_param("ss", $email, $username);
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();

                if ($count > 0) {
                    $feedback_message = "Email or Username is already taken!";
                } else {
                    header("Location: register.php?step=2");
                    exit();
                }
            }
            break;

        case 2: // Step 2: Contact Information
            $register_data['user_info']['phone'] = trim($_POST['phone']);
            $register_data['user_info']['country'] = trim($_POST['country']);
            $register_data['user_info']['address'] = trim($_POST['address']);

            if (empty($register_data['user_info']['phone']) || empty($register_data['user_info']['country']) || empty($register_data['user_info']['address'])) {
                $feedback_message = "All fields in Step 2 are required!";
            } else {
                header("Location: register.php?step=3");
                exit();
            }
            break;

        case 3: // Step 3: Social Profiles
            $register_data['user_info']['facebook'] = trim($_POST['facebook']);
            $register_data['user_info']['twitter'] = trim($_POST['twitter']);
            $register_data['user_info']['linkedin'] = trim($_POST['linkedin']);
            header("Location: register.php?step=4");
            exit();
            break;

        case 4: // Step 4: Education
            $register_data['education'][] = [
                'degree' => trim($_POST['degree']),
                'institution' => trim($_POST['institution']),
                'graduation_year' => trim($_POST['graduation_year']),
            ];
            header("Location: register.php?step=5");
            exit();
            break;

        case 5: // Step 5: Experience
            $register_data['experience'][] = [
                'job_title' => trim($_POST['job_title']),
                'company' => trim($_POST['company']),
                'start_date' => trim($_POST['start_date']),
                'end_date' => trim($_POST['end_date']),
                'description' => trim($_POST['description']),
            ];
            header("Location: register.php?step=6");
            exit();
            break;

        case 6: // Step 6: Certificates
            $register_data['certificates'][] = [
                'certificate_name' => trim($_POST['certificate_name']),
                'issuing_organization' => trim($_POST['issuing_organization']),
                'date_issued' => trim($_POST['date_issued']),
            ];

            // Insert data into the database
            $mysqli->begin_transaction();
            try {
                // Insert user information
                $stmt = $mysqli->prepare("INSERT INTO users (full_name, username, email, password, phone, country, address, facebook, twitter, linkedin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param(
                    "ssssssssss",
                    $register_data['user_info']['full_name'],
                    $register_data['user_info']['username'],
                    $register_data['user_info']['email'],
                    password_hash($register_data['user_info']['password'], PASSWORD_BCRYPT),
                    $register_data['user_info']['phone'],
                    $register_data['user_info']['country'],
                    $register_data['user_info']['address'],
                    $register_data['user_info']['facebook'],
                    $register_data['user_info']['twitter'],
                    $register_data['user_info']['linkedin']
                );
                $stmt->execute();
                $user_id = $stmt->insert_id;

                // Insert education records
                $stmt = $mysqli->prepare("INSERT INTO user_education (user_id, degree, institution, graduation_year) VALUES (?, ?, ?, ?)");
                foreach ($register_data['education'] as $edu) {
                    $stmt->bind_param("isss", $user_id, $edu['degree'], $edu['institution'], $edu['graduation_year']);
                    $stmt->execute();
                }

                // Insert experience records
                $stmt = $mysqli->prepare("INSERT INTO user_experience (user_id, job_title, company, start_date, end_date, description) VALUES (?, ?, ?, ?, ?, ?)");
                foreach ($register_data['experience'] as $exp) {
                    $stmt->bind_param("isssss", $user_id, $exp['job_title'], $exp['company'], $exp['start_date'], $exp['end_date'], $exp['description']);
                    $stmt->execute();
                }

                // Insert certificates
                $stmt = $mysqli->prepare("INSERT INTO user_certificates (user_id, certificate_name, issuing_organization, date_issued) VALUES (?, ?, ?, ?)");
                foreach ($register_data['certificates'] as $cert) {
                    $stmt->bind_param("isss", $user_id, $cert['certificate_name'], $cert['issuing_organization'], $cert['date_issued']);
                    $stmt->execute();
                }

                $mysqli->commit();
                unset($_SESSION['register_data']); // Clear session data
                header("Location: login.php");
                exit();
            } catch (Exception $e) {
                $mysqli->rollback();
                $feedback_message = "Error occurred during registration: " . $e->getMessage();
            }
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Account</title>
    <style>
        /* General Body Styling */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f3f3f3;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-image: url('https://www.transparenttextures.com/patterns/asfalt-light.png');
    background-size: cover;
}

/* Container Styling */
.container {
    background: #fff;
    padding: 30px 50px;
    border-radius: 12px;
    box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 500px;
    text-align: center;
    transition: transform 0.3s ease-in-out;
}

/* Hover effect for container */
.container:hover {
    transform: scale(1.02);
}

/* Heading Styling */
.container h1 {
    margin-bottom: 30px;
    font-size: 28px;
    color: #1b00ff;
    font-weight: bold;
}

/* Progress Bar Styling */
.step-navigation {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}

.step-navigation span {
    font-size: 16px;
    font-weight: bold;
    color: #999;
}

.step-navigation .active {
    color: #1b00ff;
}

/* Form Styling */
form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Label Styling */
form label {
    font-weight: 600;
    font-size: 16px;
    color: #333;
    text-align: left;
}

/* Input and Textarea Styling */
form input[type="text"],
form input[type="email"],
form input[type="password"],
form input[type="number"],
form input[type="date"],
form input[type="url"],
form textarea {
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 16px;
    outline: none;
    transition: all 0.3s ease;
}

/* Focus Effect */
form input[type="text"]:focus,
form input[type="email"]:focus,
form input[type="password"]:focus,
form input[type="number"]:focus,
form input[type="date"]:focus,
form input[type="url"]:focus,
form textarea:focus {
    border-color: #1b00ff;
    box-shadow: 0 0 5px rgba(27, 0, 255, 0.3);
}

/* Submit Button */
form input[type="submit"] {
    background-color: #1b00ff;
    color: #fff;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    font-size: 18px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

form input[type="submit"]:hover {
    background-color: #0e00c0;
}

/* Error/Success Message */
.feedback {
    color: red;
    font-weight: bold;
    margin-bottom: 20px;
}

/* Login Link Styling */
.login-link {
    margin-top: 20px;
    font-size: 16px;
    color: #333;
}

.login-link a {
    color: #1b00ff;
    text-decoration: none;
}

.login-link a:hover {
    text-decoration: underline;
}
    </style>
</head>
<body>

    <div class="container">
        <h1>Create Your Account (Step <?php echo $step; ?>)</h1>

        <!-- Step Navigation -->
        <div class="step-navigation">
            <span class="<?php echo $step == 1 ? 'active' : ''; ?>">Step 1</span>
            <span class="<?php echo $step == 2 ? 'active' : ''; ?>">Step 2</span>
            <span class="<?php echo $step == 3 ? 'active' : ''; ?>">Step 3</span>
            <span class="<?php echo $step == 4 ? 'active' : ''; ?>">Step 4</span>
            <span class="<?php echo $step == 5 ? 'active' : ''; ?>">Step 5</span>
            <span class="<?php echo $step == 6 ? 'active' : ''; ?>">Step 6</span>
        </div>

        <form action="register.php?step=<?php echo $step; ?>" method="post">
            <?php if ($feedback_message): ?>
                <div class="feedback"><?php echo $feedback_message; ?></div>
            <?php endif; ?>

            <!-- Step Forms -->
            <?php if ($step == 1): ?>
    <label for="full_name">Full Name</label>
    <input type="text" name="full_name" value="<?php echo htmlspecialchars($register_data['user_info']['full_name']); ?>" placeholder="Enter your full name" required>

    <label for="username">Username</label>
    <input type="text" name="username" value="<?php echo htmlspecialchars($register_data['user_info']['username']); ?>" placeholder="Choose a username" required>

    <label for="email">Email</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($register_data['user_info']['email']); ?>" placeholder="Enter your email" required>

    <label for="password">Password</label>
    <input type="password" name="password" placeholder="Create a password" required>

    <input type="submit" value="Next">
<?php elseif ($step == 2): ?>
    <label for="phone">Phone</label>
    <input type="text" name="phone" value="<?php echo htmlspecialchars($register_data['user_info']['phone']); ?>" placeholder="Enter your phone number" required>

    <label for="country">Country</label>
    <input type="text" name="country" value="<?php echo htmlspecialchars($register_data['user_info']['country']); ?>" placeholder="Enter your country" required>

    <label for="address">Address</label>
    <textarea name="address" placeholder="Enter your address" required><?php echo htmlspecialchars($register_data['user_info']['address']); ?></textarea>

    <input type="submit" value="Next">
<?php elseif ($step == 3): ?>
    <label for="facebook">Facebook</label>
    <input type="url" name="facebook" value="<?php echo htmlspecialchars($register_data['user_info']['facebook']); ?>" placeholder="Enter your Facebook URL">

    <label for="twitter">Twitter</label>
    <input type="url" name="twitter" value="<?php echo htmlspecialchars($register_data['user_info']['twitter']); ?>" placeholder="Enter your Twitter URL">

    <label for="linkedin">LinkedIn</label>
    <input type="url" name="linkedin" value="<?php echo htmlspecialchars($register_data['user_info']['linkedin']); ?>" placeholder="Enter your LinkedIn URL">

    <input type="submit" value="Next">
<?php elseif ($step == 4): ?>
    <label for="degree">Degree</label>
    <input type="text" name="degree" placeholder="Enter your degree" required>

    <label for="institution">Institution</label>
    <input type="text" name="institution" placeholder="Enter your institution" required>

    <label for="graduation_year">Graduation Year</label>
    <input type="number" name="graduation_year" placeholder="Enter your graduation year" required>

    <input type="submit" value="Next">
<?php elseif ($step == 5): ?>
    <label for="job_title">Job Title</label>
    <input type="text" name="job_title" placeholder="Enter your job title" required>

    <label for="company">Company</label>
    <input type="text" name="company" placeholder="Enter your company name" required>

    <label for="start_date">Start Date</label>
    <input type="date" name="start_date" required>

    <label for="end_date">End Date</label>
    <input type="date" name="end_date" required>

    <label for="description">Description</label>
    <textarea name="description" placeholder="Describe your role" required></textarea>

    <input type="submit" value="Next">
<?php elseif ($step == 6): ?>
    <label for="certificate_name">Certificate Name</label>
    <input type="text" name="certificate_name" placeholder="Enter certificate name" required>

    <label for="issuing_organization">Issuing Organization</label>
    <input type="text" name="issuing_organization" placeholder="Enter organization name" required>

    <label for="date_issued">Date Issued</label>
    <input type="date" name="date_issued" required>

    <input type="submit" value="Submit">
<?php endif; ?>

        </form>

        <!-- Login Option -->
        <div class="login-link">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>

</body>
</html>
