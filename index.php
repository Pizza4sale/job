<?php
include("includes/head.php");
include("includes/navbar.php");
include("includes/sidebar.php");
include("includes/script.php");

// Fetch application statistics and breakdown by status in one query
$query = "
    SELECT 
        COUNT(*) AS total, 
        SUM(CASE WHEN status = 'Interview' THEN 1 ELSE 0 END) AS interview_count,
        SUM(CASE WHEN status = 'Offer' THEN 1 ELSE 0 END) AS offer_count,
        SUM(CASE WHEN interview_date >= CURDATE() THEN 1 ELSE 0 END) AS upcoming_interviews,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending_count,
        SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) AS rejected_count
    FROM Applications 
    WHERE user_id = ?";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

// Assign fetched values to variables with default 0 if no data
$totalApplications = isset($data['total']) ? $data['total'] : 0;
$interviewCount = isset($data['interview_count']) ? $data['interview_count'] : 0;
$offerCount = isset($data['offer_count']) ? $data['offer_count'] : 0;
$upcomingInterviewsCount = isset($data['upcoming_interviews']) ? $data['upcoming_interviews'] : 0;
$pendingCount = isset($data['pending_count']) ? $data['pending_count'] : 0;
$rejectedCount = isset($data['rejected_count']) ? $data['rejected_count'] : 0;
?>

<!-- Main content -->
<div class="page-container">
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="title">
                                <h4>Job Application Dashboard</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="index.php">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                       Dashboard
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Widgets -->
                <div class="row">
                    <!-- Total Applications Widget -->
                    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                        <div class="card-box height-100-p widget-style3">
                            <div class="d-flex flex-wrap">
                                <div class="widget-data">
                                    <div class="weight-700 font-24 text-dark"><?php echo htmlspecialchars($totalApplications); ?></div>
                                    <div class="font-14 text-secondary weight-500">Total Applications</div>
                                </div>
                                <div class="widget-icon">
                                    <div class="icon" data-color="#FF7380FF">
                                        <i class="dw dw-clipboard" data-toggle="tooltip" title="Total Applications"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Interviews Scheduled Widget -->
                    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                        <div class="card-box height-100-p widget-style3">
                            <div class="d-flex flex-wrap">
                                <div class="widget-data">
                                    <div class="weight-700 font-24 text-dark"><?php echo htmlspecialchars($interviewCount); ?></div>
                                    <div class="font-14 text-secondary weight-500">Interviews Scheduled</div>
                                </div>
                                <div class="widget-icon">
                                    <div class="icon" data-color="#FF7380FF">
                                        <i class="dw dw-user" data-toggle="tooltip" title="Interviews Scheduled"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job Offers Widget -->
                    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                        <div class="card-box height-100-p widget-style3">
                            <div class="d-flex flex-wrap">
                                <div class="widget-data">
                                    <div class="weight-700 font-24 text-dark"><?php echo htmlspecialchars($offerCount); ?></div>
                                    <div class="font-14 text-secondary weight-500">Job Offers</div>
                                </div>
                                <div class="widget-icon">
                                    <div class="icon" data-color="#FF7380FF">
                                        <i class="dw dw-like" data-toggle="tooltip" title="Job Offers"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Interviews Widget -->
                    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                        <div class="card-box height-100-p widget-style3">
                            <div class="d-flex flex-wrap">
                                <div class="widget-data">
                                    <div class="weight-700 font-24 text-dark"><?php echo htmlspecialchars($upcomingInterviewsCount); ?></div>
                                    <div class="font-14 text-secondary weight-500">Upcoming Interviews</div>
                                </div>
                                <div class="widget-icon">
                                    <div class="icon" data-color="#FF7380FF">
                                        <i class="dw dw-calendar" data-toggle="tooltip" title="Upcoming Interviews"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Application Status Breakdown (Pie Chart) -->
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Application Status Breakdown</h5>
                            </div>
                            <div class="card-body">
                                <div id="applicationStatusChart"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Job Applications Section -->
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Recent Job Applications</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                // Fetch recent job applications
                                $query = "SELECT * FROM Applications WHERE user_id = ? ORDER BY application_date DESC LIMIT 5";
                                $stmt = $mysqli->prepare($query);
                                $stmt->bind_param("i", $userId);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                // Check if there are applications
                                if ($result->num_rows > 0) {
                                    echo '<table class="table table-striped table-bordered table-hover table-responsive">';
                                    echo '<thead><tr><th>Job Title</th><th>Application Date</th><th>Status</th><th>Action</th></tr></thead>';
                                    echo '<tbody>';
                                    while ($application = $result->fetch_assoc()) {
                                        echo '<tr>';
                                        echo '<td>' . htmlspecialchars($application['job_title']) . '</td>';
                                        echo '<td>' . (new DateTime($application['application_date']))->format('F j, Y') . '</td>';
                                        echo '<td>' . htmlspecialchars($application['status']) . '</td>';
                                        echo '<td><a href="edit_application.php?id=' . $application['id'] . '" class="btn btn-primary" data-toggle="tooltip" title="Edit Application">Edit</a></td>';
                                        echo '</tr>';
                                    }
                                    echo '</tbody>';
                                    echo '</table>';
                                } else {
                                    echo '<p>No recent applications to display.</p>';
                                }

                                $stmt->close();
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    // ApexCharts for Application Status Breakdown
    var options = {
        chart: {
            type: 'pie',
            height: 300
        },
        series: [<?php echo $pendingCount; ?>, <?php echo $interviewCount; ?>, <?php echo $offerCount; ?>, <?php echo $rejectedCount; ?>],
        labels: ['Pending', 'Interview', 'Offer', 'Rejected'],
        colors: ['#FFC107', '#28A745', '#17A2B8', '#DC3545']
    };

    var chart = new ApexCharts(document.querySelector("#applicationStatusChart"), options);
    chart.render();
</script>
