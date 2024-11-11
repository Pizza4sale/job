<?php
include("includes/head.php");
include("includes/navbar.php");
include("includes/sidebar.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Check if the application ID is provided
if (!isset($_GET['id'])) {
    header("Location: applications.php");
    exit;
}

$applicationId = intval($_GET['id']); // Sanitize application ID input

// Fetch the application details for editing
$query = "SELECT * FROM Applications WHERE id = ? AND user_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("ii", $applicationId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: applications.php");
    exit;
}

$application = $result->fetch_assoc();
$stmt->close();

// Handle form submission for updating the application
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect and sanitize form data
    $jobTitle = htmlspecialchars($_POST['job_title']);
    $applicationDate = htmlspecialchars($_POST['application_date']);
    $status = htmlspecialchars($_POST['status']);
    $salaryRange = htmlspecialchars($_POST['salary_range']);
    $jobType = htmlspecialchars($_POST['job_type']);
    
    $location = htmlspecialchars($_POST['location']) ?: ''; // Set to empty string if empty
    $resumeLink = htmlspecialchars($_POST['resume_link']);
    $coverLetterLink = htmlspecialchars($_POST['cover_letter_link']);
    $portfolioLink = htmlspecialchars($_POST['portfolio_link']);
    $skillsRequired = htmlspecialchars($_POST['skills_required']);
    $source = htmlspecialchars($_POST['source']);
    $applicationMethod = htmlspecialchars($_POST['application_method']);
    $recruiterName = htmlspecialchars($_POST['recruiter_name']);
    $recruiterEmail = htmlspecialchars($_POST['recruiter_email']);
    $recruiterPhone = htmlspecialchars($_POST['recruiter_phone']);
    $interviewDate = htmlspecialchars($_POST['interview_date']);
    $interviewTime = htmlspecialchars($_POST['interview_time']);
    $followUpDate = htmlspecialchars($_POST['follow_up_date']);
    $feedbackReceived = isset($_POST['feedback_received']) ? 1 : 0;
    $feedbackNotes = htmlspecialchars($_POST['feedback_notes']);
    $notes = htmlspecialchars($_POST['notes']);

    // Update the application in the database
    $updateQuery = "UPDATE Applications SET job_title = ?, application_date = ?, status = ?, salary_range = ?, 
                    job_type = ?, location = ?, resume_link = ?, cover_letter_link = ?, portfolio_link = ?, 
                    skills_required = ?, source = ?, application_method = ?, recruiter_name = ?, 
                    recruiter_email = ?, recruiter_phone = ?, interview_date = ?, interview_time = ?, 
                    follow_up_date = ?, feedback_received = ?, feedback_notes = ?, notes = ? 
                    WHERE id = ? AND user_id = ?";
    $updateStmt = $mysqli->prepare($updateQuery);
    $updateStmt->bind_param("sssssisssssssssssssssii", $jobTitle, $applicationDate, $status, $salaryRange,
                             $jobType, $location, $resumeLink, $coverLetterLink, $portfolioLink,
                             $skillsRequired, $source, $applicationMethod, $recruiterName, 
                             $recruiterEmail, $recruiterPhone, $interviewDate, $interviewTime,
                             $followUpDate, $feedbackReceived, $feedbackNotes, $notes,
                             $applicationId, $userId);
    
    if ($updateStmt->execute()) {
        $_SESSION['message'] = "Application updated successfully."; // Store success message
        header("Location: applications.php");
        exit;
    } else {
        $error = "Failed to update application. Please try again.";
        error_log("Error updating application ID $applicationId: " . $mysqli->error); // Log error for debugging
    }
    $updateStmt->close();
}
?>

<!-- Main content -->
<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Edit Application</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="index.php">Home</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Edit Application
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Edit Application Form -->
            <div class="card-box mb-30">
                <div class="pb-20"></div>
                <div class="pd-ltr-20">
                    <div class="card-box pd-20 box-shadow">
                        <h4 class="text-center">Application Details</h4>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php elseif (isset($_SESSION['message'])): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['message']); ?></div>
                            <?php unset($_SESSION['message']); // Clear message after display ?>
                        <?php endif; ?>
                        <form method="post">
    <div class="form-group">
        <label for="job_title">Job Title</label>
        <input type="text" class="form-control" id="job_title" name="job_title" 
               value="<?php echo htmlspecialchars($application['job_title']); ?>" 
               placeholder="e.g., Software Engineer" required>
    </div>
    <div class="form-group">
        <label for="application_date">Application Date</label>
        <input type="date" class="form-control" id="application_date" name="application_date" 
               value="<?php echo htmlspecialchars($application['application_date']); ?>" 
               required>
    </div>
    <div class="form-group">
        <label for="status">Status</label>
        <input type="text" class="form-control" id="status" name="status" 
               value="<?php echo htmlspecialchars($application['status']); ?>" 
               placeholder="e.g., Interviewing" required>
    </div>
    <div class="form-group">
        <label for="salary_range">Salary Range</label>
        <input type="text" class="form-control" id="salary_range" name="salary_range" 
               value="<?php echo htmlspecialchars($application['salary_range']); ?>" 
               placeholder="e.g., 50000-60000">
    </div>
    <div class="form-group">
        <label for="job_type">Job Type</label>
        <input type="text" class="form-control" id="job_type" name="job_type" 
               value="<?php echo htmlspecialchars($application['job_type']); ?>" 
               placeholder="e.g., Full-time, Part-time">
    </div>
    <div class="form-group">
        <label for="location">Location</label>
        <input type="text" class="form-control" id="location" name="location" 
               value="<?php echo htmlspecialchars($application['location']); ?>" 
               placeholder="e.g., Remote, On-site" required>
    </div>
    <div class="form-group">
        <label for="resume_link">Resume Link</label>
        <input type="url" class="form-control" id="resume_link" name="resume_link" 
               value="<?php echo htmlspecialchars($application['resume_link']); ?>" 
               placeholder="e.g., https://example.com/resume.pdf">
    </div>
    <div class="form-group">
        <label for="cover_letter_link">Cover Letter Link</label>
        <input type="url" class="form-control" id="cover_letter_link" name="cover_letter_link" 
               value="<?php echo htmlspecialchars($application['cover_letter_link']); ?>" 
               placeholder="e.g., https://example.com/cover_letter.pdf">
    </div>
    <div class="form-group">
        <label for="portfolio_link">Portfolio Link</label>
        <input type="url" class="form-control" id="portfolio_link" name="portfolio_link" 
               value="<?php echo htmlspecialchars($application['portfolio_link']); ?>" 
               placeholder="e.g., https://example.com/portfolio">
    </div>
    <div class="form-group">
        <label for="skills_required">Skills Required</label>
        <input type="text" class="form-control" id="skills_required" name="skills_required" 
               value="<?php echo htmlspecialchars($application['skills_required']); ?>" 
               placeholder="e.g., PHP, JavaScript, HTML, CSS">
    </div>
    <div class="form-group">
        <label for="source">Source</label>
        <input type="text" class="form-control" id="source" name="source" 
               value="<?php echo htmlspecialchars($application['source']); ?>" 
               placeholder="e.g., LinkedIn, Company Website">
    </div>
    <div class="form-group">
        <label for="application_method">Application Method</label>
        <input type="text" class="form-control" id="application_method" name="application_method" 
               value="<?php echo htmlspecialchars($application['application_method']); ?>" 
               placeholder="e.g., Online, Referral">
    </div>
    <div class="form-group">
        <label for="recruiter_name">Recruiter Name</label>
        <input type="text" class="form-control" id="recruiter_name" name="recruiter_name" 
               value="<?php echo htmlspecialchars($application['recruiter_name']); ?>" 
               placeholder="e.g., John Doe">
    </div>
    <div class="form-group">
        <label for="recruiter_email">Recruiter Email</label>
        <input type="email" class="form-control" id="recruiter_email" name="recruiter_email" 
               value="<?php echo htmlspecialchars($application['recruiter_email']); ?>" 
               placeholder="e.g., recruiter@example.com">
    </div>
    <div class="form-group">
        <label for="recruiter_phone">Recruiter Phone</label>
        <input type="tel" class="form-control" id="recruiter_phone" name="recruiter_phone" 
               value="<?php echo htmlspecialchars($application['recruiter_phone']); ?>" 
               placeholder="e.g., +1234567890">
    </div>
    <div class="form-group">
        <label for="interview_date">Interview Date</label>
        <input type="date" class="form-control" id="interview_date" name="interview_date" 
               value="<?php echo htmlspecialchars($application['interview_date']); ?>">
    </div>
    <div class="form-group">
        <label for="interview_time">Interview Time</label>
        <input type="time" class="form-control" id="interview_time" name="interview_time" 
               value="<?php echo htmlspecialchars($application['interview_time']); ?>">
    </div>
    <div class="form-group">
        <label for="follow_up_date">Follow Up Date</label>
        <input type="date" class="form-control" id="follow_up_date" name="follow_up_date" 
               value="<?php echo htmlspecialchars($application['follow_up_date']); ?>">
    </div>
    <div class="form-group">
        <label for="feedback_received">Feedback Received</label>
        <input type="checkbox" id="feedback_received" name="feedback_received" 
               value="1" <?php echo ($application['feedback_received'] ? 'checked' : ''); ?>>
    </div>
    <div class="form-group">
        <label for="feedback_notes">Feedback Notes</label>
        <textarea class="form-control" id="feedback_notes" name="feedback_notes" 
                  placeholder="e.g., Positive feedback from the interviewer"><?php echo htmlspecialchars($application['feedback_notes']); ?></textarea>
    </div>
    <div class="form-group">
        <label for="notes">Notes</label>
        <textarea class="form-control" id="notes" name="notes" 
                  placeholder="Additional notes or comments"><?php echo htmlspecialchars($application['notes']); ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Update Application</button>
    <a href="applications.php" class="btn btn-secondary">Cancel</a>
</form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("includes/script.php"); ?>
