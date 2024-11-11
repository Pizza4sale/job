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

// Fetch applications for the logged-in user
$query = "SELECT id, job_title, application_date, status, salary_range, job_type, location, 
          resume_link, cover_letter_link, portfolio_link, skills_required, source, 
          application_method, recruiter_name, recruiter_email, recruiter_phone, 
          interview_date, interview_time, follow_up_date, feedback_received, 
          feedback_notes, notes, last_updated 
          FROM Applications WHERE user_id = ? ORDER BY application_date DESC";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$applications = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!-- Main content -->
<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Application Management</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="index.php">Home</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Application Management
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-md-6 col-sm-12 text-right">
                        <a href="add_application.php" class="btn btn-primary">Add New Application</a>
                    </div>
                </div>
            </div>
<!-- Display messages -->
<?php if (isset($_GET['message'])): ?>
                <div class="alert alert-info text-center">
                    <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
            <?php endif; ?>
            <!-- Applications Table -->
            <div class="card-box mb-30">
                <div class="pb-20"></div>
                <div class="pd-ltr-20">
                    <div class="card-box pd-20 box-shadow">
                        <h4 class="text-center">Your Applications</h4>
                        <?php if (empty($applications)): ?>
                            <p class="text-center">No applications found.</p>
                        <?php else: ?>
                            <table class="table hover data-table-export nowrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Job Title</th>
                                        <th>Application Date</th>
                                        <th>Status</th>
                                        <th>Salary Range</th>
                                        <th>Job Type</th>
                                        <th>Location</th>
                                        <th>Resume Link</th>
                                        <th>Cover Letter Link</th>
                                        <th>Portfolio Link</th>
                                        <th>Skills Required</th>
                                        <th>Source</th>
                                        <th>Application Method</th>
                                        <th>Recruiter Name</th>
                                        <th>Recruiter Email</th>
                                        <th>Recruiter Phone</th>
                                        <th>Interview Date</th>
                                        <th>Interview Time</th>
                                        <th>Follow-up Date</th>
                                        <th>Feedback Received</th>
                                        <th>Feedback Notes</th>
                                        <th>Notes</th>
                                        <th>Last Updated</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applications as $application): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($application['id']); ?></td>
                                            <td><?php echo htmlspecialchars($application['job_title']); ?></td>
                                            <td><?php echo date('F j, Y', strtotime($application['application_date'])); ?></td>
                                            <td><?php echo htmlspecialchars($application['status']); ?></td>
                                            <td><?php echo htmlspecialchars($application['salary_range']); ?></td>
                                            <td><?php echo htmlspecialchars($application['job_type']); ?></td>
                                            <td><?php echo htmlspecialchars($application['location']); ?></td>
                                            <td>
                                                <?php if ($application['resume_link']): ?>
                                                    <a href="<?php echo htmlspecialchars($application['resume_link']); ?>" target="_blank" title="View Resume">View Resume</a>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($application['cover_letter_link']): ?>
                                                    <a href="<?php echo htmlspecialchars($application['cover_letter_link']); ?>" target="_blank" title="View Cover Letter">View Cover Letter</a>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($application['portfolio_link']): ?>
                                                    <a href="<?php echo htmlspecialchars($application['portfolio_link']); ?>" target="_blank" title="View Portfolio">View Portfolio</a>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($application['skills_required']); ?></td>
                                            <td><?php echo htmlspecialchars($application['source']); ?></td>
                                            <td><?php echo htmlspecialchars($application['application_method']); ?></td>
                                            <td><?php echo htmlspecialchars($application['recruiter_name']); ?></td>
                                            <td><?php echo htmlspecialchars($application['recruiter_email']); ?></td>
                                            <td><?php echo htmlspecialchars($application['recruiter_phone']); ?></td>
                                            <td><?php echo $application['interview_date'] ? date('F j, Y', strtotime($application['interview_date'])) : 'N/A'; ?></td>
                                            <td><?php echo htmlspecialchars($application['interview_time']); ?></td>
                                            <td><?php echo $application['follow_up_date'] ? date('F j, Y', strtotime($application['follow_up_date'])) : 'N/A'; ?></td>
                                            <td><?php echo $application['feedback_received'] ? 'Yes' : 'No'; ?></td>
                                            <td><?php echo htmlspecialchars($application['feedback_notes']); ?></td>
                                            <td><?php echo htmlspecialchars($application['notes']); ?></td>
                                            <td><?php echo date('F j, Y', strtotime($application['last_updated'])); ?></td>
                                            <td>
                                                <a href="edit_application.php?id=<?php echo htmlspecialchars($application['id']); ?>" class="btn btn-sm btn-warning">Edit</a>
                                                <a href="delete_application.php?id=<?php echo htmlspecialchars($application['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this application?');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.data-table').DataTable(); // Initialize DataTable
});
</script>

<?php
include("includes/script.php");
?>
