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
    $fileName = $user['profile_picture']; // Keep the existing file by default

if ($_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
    $allowedTypes = ['image/jpeg', 'image/png'];
    $fileType = mime_content_type($_FILES['profile_picture']['tmp_name']);
    
    // Validate file type and size
    if (in_array($fileType, $allowedTypes) && $_FILES['profile_picture']['size'] <= 2 * 1024 * 1024) {
        $fileName = time() . '-' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', basename($_FILES['profile_picture']['name']));
        $uploadFile = $uploadDir . $fileName;
        
        if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
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

