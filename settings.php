<?php
include("includes/head.php");
include("includes/navbar.php");
include("includes/sidebar.php");
include("includes/script.php");

$alertMessage = '';
$alertType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Update Profile Information
    $fields = ['username', 'email', 'full_name', 'phone', 'country', 'address', 'bio', 'facebook', 'twitter', 'linkedin'];
    $profileData = [];
    foreach ($fields as $field) {
        $profileData[$field] = htmlspecialchars($_POST[$field]);
    }

    // Profile Picture Upload Logic
    $profilePicture = $_FILES['profile_picture'] ?? null;
    $fileName = $user['profile_picture']; // Default to existing picture

    if ($profilePicture && $profilePicture['error'] == UPLOAD_ERR_OK) {
        $fileName = handleFileUpload($profilePicture);
        if (!$fileName) {
            $alertMessage = "Failed to upload profile picture.";
            $alertType = "danger";
        }
    }

    // Profile Update Query
    if (empty($alertMessage)) {
        $query = "UPDATE users SET 
                    username = ?, 
                    email = ?, 
                    full_name = ?, 
                    phone = ?, 
                    country = ?, 
                    address = ?, 
                    bio = ?, 
                    facebook = ?, 
                    twitter = ?, 
                    linkedin = ?" . ($fileName ? ", profile_picture = ?" : "") . " WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        
        $stmt->bind_param("sssssssssssi", 
            $profileData['username'], 
            $profileData['email'], 
            $profileData['full_name'], 
            $profileData['phone'], 
            $profileData['country'], 
            $profileData['address'], 
            $profileData['bio'], 
            $profileData['facebook'], 
            $profileData['twitter'], 
            $profileData['linkedin'], 
            $fileName, 
            $userId
        );

        if ($stmt->execute()) {
            $alertMessage = "Profile updated successfully!";
            $alertType = "success";
        } else {
            $alertMessage = "Failed to update profile: " . $stmt->error;
            $alertType = "danger";
        }
    }

    // Add Skill if provided
    if (isset($_POST['skill_name'], $_POST['proficiency']) && !empty($_POST['skill_name'])) {
        handleInsertSkill($userId, $_POST['skill_name'], (int)$_POST['proficiency']);
    }

    // Add Certificate, Education, and Experience if provided
    handleInsertAdditionalData($userId);
}

// Fetch user data securely
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Function for file upload handling
function handleFileUpload($file) {
    $allowedTypes = ['image/jpeg', 'image/png'];
    $fileType = mime_content_type($file['tmp_name']);
    $uploadDir = 'uploads/';
    $fileName = preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', basename($file['name']));
    $filePath = $uploadDir . $fileName;

    // Validate file type and size
    if (in_array($fileType, $allowedTypes) && $file['size'] <= 2 * 1024 * 1024) {
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return $fileName;
        }
    }
    return false;
}

// Function to handle inserting additional data (certificate, education, experience)
function handleInsertAdditionalData($userId) {
    global $mysqli;

    // Add Certificate
    if (!empty($_POST['certificate_name'])) {
        $certificateName = htmlspecialchars($_POST['certificate_name']);
        $issuingOrganization = htmlspecialchars($_POST['issuing_organization']);
        $dateIssued = $_POST['date_issued'];
        $stmt = $mysqli->prepare("INSERT INTO user_certificates (user_id, certificate_name, issuing_organization, date_issued) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $userId, $certificateName, $issuingOrganization, $dateIssued);
        $stmt->execute();
    }

    // Add Education
    if (!empty($_POST['degree'])) {
        $degree = htmlspecialchars($_POST['degree']);
        $institution = htmlspecialchars($_POST['institution']);
        $graduationYear = $_POST['graduation_year'];
        $stmt = $mysqli->prepare("INSERT INTO user_education (user_id, degree, institution, graduation_year) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issi", $userId, $degree, $institution, $graduationYear);
        $stmt->execute();
    }

    // Add Experience
    if (!empty($_POST['job_title'])) {
        $jobTitle = htmlspecialchars($_POST['job_title']);
        $company = htmlspecialchars($_POST['company']);
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];
        $description = htmlspecialchars($_POST['description']);
        $stmt = $mysqli->prepare("INSERT INTO user_experience (user_id, job_title, company, start_date, end_date, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $userId, $jobTitle, $company, $startDate, $endDate, $description);
        $stmt->execute();
    }
}

// Function to handle inserting skills
function handleInsertSkill($userId, $skillName, $proficiency) {
    global $mysqli;
    $query = "INSERT INTO user_skills (user_id, skill_name, proficiency) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("isi", $userId, $skillName, $proficiency);
    $stmt->execute();
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
        <?php if ($alertMessage): ?>
            <div class="alert alert-<?php echo $alertType; ?>"><?php echo $alertMessage; ?></div>
        <?php endif; ?>

       <!-- Tab Navigation -->
<ul class="nav nav-tabs" id="settingsTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="timeline-tab" data-toggle="tab" href="#timeline" role="tab" aria-controls="timeline" aria-selected="true">
            <i class="fa fa-clock-o"></i> Timeline
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="tasks-tab" data-toggle="tab" href="#tasks" role="tab" aria-controls="tasks" aria-selected="false">
            <i class="fa fa-tasks"></i> Tasks
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="settings-tab" data-toggle="tab" href="#editProfile" role="tab" aria-controls="editProfile" aria-selected="false">
            <i class="fa fa-cogs"></i> Settings
        </a>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="profileTabContent">
    <!-- Timeline Tab -->
    <div class="tab-pane fade show active" id="timeline" role="tabpanel" aria-labelledby="timeline-tab">
        <div class="pt-3">
            <h5 class="h5 text-blue">Timeline</h5>
            <p>This section is currently empty. You can add timeline entries here to track important events.</p>
        </div>
    </div>

   <!-- Tasks Tab -->
<div class="tab-pane fade" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">
    <div class="pt-3">
        <div class="row">
            <div class="col-md-12 mb-3">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addTaskModal">
                    <i class="fa fa-plus"></i> Add New Task
                </button>
            </div>
        </div>

        <!-- Task filters -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary active" data-filter="all">All</button>
                    <button type="button" class="btn btn-outline-warning" data-filter="pending">Pending</button>
                    <button type="button" class="btn btn-outline-info" data-filter="in_progress">In Progress</button>
                    <button type="button" class="btn btn-outline-success" data-filter="completed">Completed</button>
                </div>
            </div>
        </div>

        <!-- Tasks list -->
        <div class="row">
            <div class="col-md-12">
                <div class="task-list">
                    <?php
                    $query = "SELECT * FROM user_tasks WHERE user_id = ? ORDER BY due_date ASC";
                    $stmt = $mysqli->prepare($query);
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($task = $result->fetch_assoc()) {
                            $statusClass = '';
                            $statusBadge = '';
                            switch ($task['status']) {
                                case 'pending':
                                    $statusClass = 'warning';
                                    $statusBadge = 'Pending';
                                    break;
                                case 'in_progress':
                                    $statusClass = 'info';
                                    $statusBadge = 'In Progress';
                                    break;
                                case 'completed':
                                    $statusClass = 'success';
                                    $statusBadge = 'Completed';
                                    break;
                            }

                            $priorityClass = '';
                            switch ($task['priority']) {
                                case 'low':
                                    $priorityClass = 'secondary';
                                    break;
                                case 'medium':
                                    $priorityClass = 'primary';
                                    break;
                                case 'high':
                                    $priorityClass = 'danger';
                                    break;
                            }
                            ?>
                            <div class="card mb-3 task-item" data-status="<?php echo $task['status']; ?>">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h5 class="card-title mb-1"><?php echo htmlspecialchars($task['title']); ?></h5>
                                            <p class="card-text text-muted small mb-2">
                                                <?php echo htmlspecialchars($task['description']); ?>
                                            </p>
                                            <div>
                                                <span class="badge badge-<?php echo $statusClass; ?>"><?php echo $statusBadge; ?></span>
                                                <span class="badge badge-<?php echo $priorityClass; ?> ml-2">Priority: <?php echo ucfirst($task['priority']); ?></span>
                                                <span class="ml-2"><i class="fa fa-calendar"></i> Due: <?php echo date('M d, Y', strtotime($task['due_date'])); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <div class="btn-group">
                                                <form method="POST" action="update_task_status.php">
                                                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                    <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-toggle="dropdown">
                                                        Status
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <button type="submit" name="status" value="pending" class="dropdown-item">Pending</button>
                                                        <button type="submit" name="status" value="in_progress" class="dropdown-item">In Progress</button>
                                                        <button type="submit" name="status" value="completed" class="dropdown-item">Completed</button>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- Delete Task Form -->
                                            <form method="POST" action="delete_task.php" style="display: inline;">
                                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                    } else {
                        echo '<div class="alert alert-info">No tasks found. Add your first task to get started!</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaskModalLabel">Add New Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="add_task.php">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Task Title</label>
                        <input type="text" name="task_title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="task_description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Due Date</label>
                        <input type="date" name="due_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Priority</label>
                        <select name="priority" class="form-control">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Task</button>
                </div>
            </form>
        </div>
    </div>
</div>



    <!-- Settings Tab (Edit Profile) -->
   <div class="tab-pane fade" id="editProfile" role="tabpanel" aria-labelledby="settings-tab">
    <h5 class="h5 text-blue">Edit Profile</h5>

    <form method="POST" enctype="multipart/form-data" class="pt-3">
        <!-- Profile Information Fields -->
        <?php renderTextInput("Full Name", "full_name", $user['full_name']); ?>
        <?php renderTextInput("Username", "username", $user['username']); ?>
        <?php renderEmailInput("Email Address", "email", $user['email']); ?>
        <?php renderTextInput("Phone Number", "phone", $user['phone']); ?>
        <?php renderTextInput("Country", "country", $user['country']); ?>
        <?php renderTextarea("Address", "address", $user['address']); ?>
        <?php renderTextarea("Biography", "bio", $user['bio']); ?>
        <?php renderTextInput("Facebook", "facebook", $user['facebook']); ?>
        <?php renderTextInput("Twitter", "twitter", $user['twitter']); ?>
        <?php renderTextInput("LinkedIn", "linkedin", $user['linkedin']); ?>
        
        <div class="form-group">
            <label>Profile Picture</label>
            <input type="file" name="profile_picture" class="form-control">
        </div>

        <!-- Certificates Section -->
        <h5 class="h5 text-blue mt-4">Add New Certificate (Optional)</h5>
        <?php renderTextInput("Certificate Name", "certificate_name"); ?>
        <?php renderTextInput("Issuing Organization", "issuing_organization"); ?>
        <div class="form-group">
            <label>Date Issued</label>
            <input type="date" name="date_issued" class="form-control">
        </div>

        <!-- Education Section -->
        <h5 class="h5 text-blue mt-4">Add New Education (Optional)</h5>
        <?php renderTextInput("Degree", "degree"); ?>
        <?php renderTextInput("Institution", "institution"); ?>
        <div class="form-group">
            <label>Graduation Year</label>
            <input type="number" name="graduation_year" class="form-control" min="1900" max="<?php echo date('Y'); ?>">
        </div>

        <!-- Experience Section -->
        <h5 class="h5 text-blue mt-4">Add New Experience (Optional)</h5>
        <?php renderTextInput("Job Title", "job_title"); ?>
        <?php renderTextInput("Company", "company"); ?>
        <div class="form-group">
            <label>Start Date</label>
            <input type="date" name="start_date" class="form-control">
        </div>
        <div class="form-group">
            <label>End Date</label>
            <input type="date" name="end_date" class="form-control">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Describe your role"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<?php 
// Render Functions
function renderTextInput($label, $name, $value = '') {
    echo "
    <div class='form-group'>
        <label>{$label}</label>
        <input type='text' name='{$name}' class='form-control' value='" . htmlspecialchars($value) . "'>
    </div>";
} 

function renderEmailInput($label, $name, $value = '') {
    echo "
    <div class='form-group'>
        <label>{$label}</label>
        <input type='email' name='{$name}' class='form-control' value='" . htmlspecialchars($value) . "'readonly>
    </div>";
}

function renderTextarea($label, $name, $value = '') {
    echo "
    <div class='form-group'>
        <label>{$label}</label>
        <textarea name='{$name}' class='form-control' rows='4'>" . htmlspecialchars($value) . "</textarea>
    </div>";
}
?>


<style>
    .nav-tabs .nav-link.active {
    background-color: #1b00ff;
    color: #fff;
    border-color: #1b00ff;
}
.nav-tabs .nav-link {
    color: #495057;
}
.nav-tabs .nav-link:hover {
    color: #1b00ff;
}
.task-item {
    transition: all 0.3s ease;
}
.task-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.badge {
    padding: 5px 10px;
}
</style>

<script>
    document.querySelectorAll('[data-filter]').forEach(button => {
        button.addEventListener('click', function () {
            const filter = this.getAttribute('data-filter');
            document.querySelectorAll('.task-item').forEach(task => {
                if (filter === 'all' || task.getAttribute('data-status') === filter) {
                    task.style.display = '';
                } else {
                    task.style.display = 'none';
                }
            });

            // Update the active button state
            document.querySelectorAll('[data-filter]').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });
</script>

