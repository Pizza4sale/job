<?php
include("includes/head.php");
include("includes/navbar.php");
include("includes/sidebar.php");
include("includes/script.php");

$alertMessage = '';
$alertType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    // Profile update logic
    $userId = $_SESSION['user_id'];
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $fullName = htmlspecialchars($_POST['full_name']);
    $phone = htmlspecialchars($_POST['phone']);
    $country = htmlspecialchars($_POST['country']);
    $address = htmlspecialchars($_POST['address']);
    $bio = htmlspecialchars($_POST['bio']);
    $facebook = htmlspecialchars($_POST['facebook']);
    $twitter = htmlspecialchars($_POST['twitter']);
    $linkedin = htmlspecialchars($_POST['linkedin']);
    $profilePicture = $_FILES['profile_picture'];
    
    $uploadDir = 'uploads/';
    $fileName = null;

    // Handle profile picture upload
    if ($profilePicture['error'] == UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png'];
        $fileType = mime_content_type($profilePicture['tmp_name']);
        
        // Validate file type and size
        if (in_array($fileType, $allowedTypes) && $profilePicture['size'] <= 2 * 1024 * 1024) {
            $fileName = time() . '-' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', basename($profilePicture['name']));
            $uploadFile = $uploadDir . $fileName;

            if (!move_uploaded_file($profilePicture['tmp_name'], $uploadFile)) {
                $alertMessage = "Failed to upload profile picture.";
                $alertType = "danger";
            }
        } else {
            $alertMessage = "Invalid file type or size exceeds 2MB.";
            $alertType = "danger";
        }
    }

    // Prepare the update query if no errors
    if (empty($alertMessage)) {
        $query = "UPDATE users SET username = ?, email = ?, full_name = ?, phone = ?, country = ?, address = ?, bio = ?, facebook = ?, twitter = ?, linkedin = ?" . ($fileName ? ", profile_picture = ?" : "") . " WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        
        if ($fileName) {
            $stmt->bind_param("sssssssssssi", $username, $email, $fullName, $phone, $country, $address, $bio, $facebook, $twitter, $linkedin, $fileName, $userId);
        } else {
            $stmt->bind_param("ssssssssssi", $username, $email, $fullName, $phone, $country, $address, $bio, $facebook, $twitter, $linkedin, $userId);
        }

        if ($stmt && $stmt->execute()) {
            $alertMessage = "Profile updated successfully!";
            $alertType = "success";
        } else {
            $alertMessage = "Failed to update profile. Error: " . $stmt->error;
            $alertType = "danger";
        }
    }

    // Handle adding skills if provided
    if (isset($_POST['skill_name']) && isset($_POST['proficiency']) && !empty($_POST['skill_name'])) {
        $skillName = htmlspecialchars($_POST['skill_name']);
        $proficiency = (int)$_POST['proficiency'];

        // Add new skill to the database
        $query = "INSERT INTO user_skills (user_id, skill_name, proficiency) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("isi", $userId, $skillName, $proficiency);

        if ($stmt->execute()) {
            $alertMessage = "Profile and skill updated successfully!";
            $alertType = "success";
        } else {
            $alertMessage = "Failed to add skill.";
            $alertType = "danger";
        }
    }
}

// Fetch user data securely
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}

?>

<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <h4>Profile</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="index.php">Home</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Profile
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
                    <div class="pd-20 card-box height-100-p">
                        <div class="profile-photo">
                            
                            <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="" class="avatar-photo"/>
                        </div>
                        <h5 class="text-center h5 mb-0"><?php echo htmlspecialchars($user['full_name']); ?></h5>
                        <p class="text-center text-muted font-14">
                            <?php echo htmlspecialchars($user['bio']); ?>
                        </p>
                        <div class="profile-info">
                            <h5 class="mb-20 h5 text-blue">Contact Information</h5>
                            <ul>
                                <li><span>Email Address:</span> <?php echo htmlspecialchars($user['email']); ?></li>
                                <li><span>Phone Number:</span> <?php echo htmlspecialchars($user['phone']); ?></li>
                                <li><span>Country:</span> <?php echo htmlspecialchars($user['country']); ?></li>
                                <li><span>Address:</span> <?php echo nl2br(htmlspecialchars($user['address'])); ?></li>
                            </ul>
                        </div>

                        <div class="profile-social">
                            <h5 class="mb-20 h5 text-blue">Social Links</h5>
                            <ul class="clearfix">
                                <li>
                                    <a href="<?php echo htmlspecialchars($user['facebook']); ?>" class="btn" data-bgcolor="#3b5998" data-color="#ffffff" target="_blank" rel="noopener noreferrer">
                                        <i class="fa fa-facebook"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo htmlspecialchars($user['twitter']); ?>" class="btn" data-bgcolor="#1da1f2" data-color="#ffffff" target="_blank" rel="noopener noreferrer">
                                        <i class="fa fa-twitter"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo htmlspecialchars($user['linkedin']); ?>" class="btn" data-bgcolor="#007bb5" data-color="#ffffff" target="_blank" rel="noopener noreferrer">
                                        <i class="fa fa-linkedin"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="profile-skills">
                            <h5 class="mb-20 h5 text-blue">Key Skills</h5>
                            <?php
                            $query = "SELECT skill_name, proficiency FROM user_skills WHERE user_id = ?";
                            $stmt = $mysqli->prepare($query);
                            $stmt->bind_param("i", $userId);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $skillName = htmlspecialchars($row['skill_name']);
                                    $proficiency = (int)$row['proficiency'];
                                    echo "
                                    <h6 class='mb-5 font-14'>{$skillName}</h6>
                                    <div class='progress mb-20' style='height: 6px'>
                                        <div class='progress-bar' role='progressbar' style='width: {$proficiency}%;'></div>
                                    </div>
                                    ";
                                }
                            } else {
                                echo "<p class='text-muted'>No skills added yet.</p>";
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
                    <div class="pd-20 card-box height-100-p">
                        <h5 class="h5 text-blue">Edit Profile</h5>
                        <?php if ($alertMessage): ?>
                            <div class="alert alert-<?php echo $alertType; ?>"><?php echo $alertMessage; ?></div>
                        <?php endif; ?>

                        <!-- Profile Update and Add Skill Form -->
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Country</label>
                                <input type="text" name="country" class="form-control" value="<?php echo htmlspecialchars($user['country']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address" class="form-control" rows="4" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Biography</label>
                                <textarea name="bio" class="form-control" rows="4"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Facebook</label>
                                <input type="text" name="facebook" class="form-control" value="<?php echo htmlspecialchars($user['facebook']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Twitter</label>
                                <input type="text" name="twitter" class="form-control" value="<?php echo htmlspecialchars($user['twitter']); ?>">
                            </div>
                            <div class="form-group">
                                <label>LinkedIn</label>
                                <input type="text" name="linkedin" class="form-control" value="<?php echo htmlspecialchars($user['linkedin']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Profile Picture</label>
                                <input type="file" name="profile_picture" class="form-control">
                            </div>
                            <h5 class="h5 text-blue">Add New Skill (Optional)</h5>
                            <div class="form-group">
                                <label>Skill Name</label>
                                <input type="text" name="skill_name" class="form-control" placeholder="Skill name">
                            </div>
                            <div class="form-group">
                                <label>Proficiency (%)</label>
                                <input type="number" name="proficiency" class="form-control" placeholder="Proficiency level" min="1" max="100">
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
