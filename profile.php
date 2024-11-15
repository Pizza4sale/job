<?php
include("includes/head.php");
include("includes/navbar.php");
include("includes/sidebar.php");
include("includes/script.php");

$alertMessage = '';
$alertType = '';

// Fetch user data securely
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    } else {
        $alertMessage = "Failed to fetch user data!";
        $alertType = "danger";
    }
}

?>
<style>
    /* Resume Styles */
.resume-header {
    display: flex;
    align-items: center;
    margin-bottom: 30px;
}

.profile-pic {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 20px;
    border: 3px solid #1b00ff;
}

.resume-header h3 {
    font-size: 24px;
    margin: 0;
    color: #333;
}

.resume-header p {
    font-size: 16px;
    margin: 5px 0;
    color: #666;
}

.resume-header strong {
    color: #1b00ff;
}

.card-box {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    background-color: #f9f9f9;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.card-box h4 {
    color: #1b00ff;
    font-size: 20px;
    margin-bottom: 15px;
}

.card-box ul {
    list-style: none;
    padding-left: 0;
}

.card-box ul li {
    margin-bottom: 20px;
}

.card-box ul li h5 {
    color: #333;
    font-size: 18px;
    margin-bottom: 5px;
}

.card-box ul li p {
    font-size: 14px;
    color: #555;
}

.card-box ul li p strong {
    color: #1b00ff;
}

.resume-social p {
    font-size: 16px;
    color: #555;
}

.resume-social a {
    text-decoration: none;
    color: #1b00ff;
    margin-right: 10px;
    transition: color 0.3s ease;
}

.resume-social a:hover {
    color: #333;
}

.progress {
    background-color: #e0e0e0;
    border-radius: 5px;
    height: 20px;
}

.progress-bar {
    background-color: #1b00ff;
    height: 100%;
    border-radius: 5px;
    text-align: center;
    color: white;
}

@media (max-width: 768px) {
    .resume-header {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .profile-pic {
        margin-bottom: 20px;
    }

    .card-box {
        padding: 15px;
    }

    .card-box h4 {
        font-size: 18px;
    }

    .card-box ul li h5 {
        font-size: 16px;
    }

    .resume-social a {
        padding: 6px 12px;
        font-size: 14px;
    }
}
</style>
<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <h4>Resume</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="index.php">Home</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Resume
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Display Alert for Errors or Success -->
            <?php if ($alertMessage): ?>
                <div class="alert alert-<?php echo $alertType; ?>" role="alert">
                    <?php echo $alertMessage; ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Resume Section -->
                <div class="col-md-12">
                    <div class="pd-20 card-box">
                        <div class="resume-header">
                            <div class="col-md-3">
                                <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-pic">
                            </div>
                            <div class="col-md-9">
                                <h3 class="text-blue"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                                <p><strong>Country:</strong> <?php echo htmlspecialchars($user['country']); ?></p>
                                <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($user['address'])); ?></p>
                                <p><strong>Bio:</strong> <?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
                                <div class="resume-social">
                                    <a href="<?php echo htmlspecialchars($user['facebook']); ?>" target="_blank">Facebook</a>
                                    <a href="<?php echo htmlspecialchars($user['twitter']); ?>" target="_blank">Twitter</a>
                                    <a href="<?php echo htmlspecialchars($user['linkedin']); ?>" target="_blank">LinkedIn</a>
                                </div>
                            </div>
                        </div>

                        <!-- Skills Section with Icons -->
                        <div class="card-box">
                            <h4><i class="fa fa-cogs" aria-hidden="true"></i> Skills</h4>
                            <ul class="skills-list">
                                <?php
                                $query = "SELECT skill_name, proficiency FROM user_skills WHERE user_id = ?";
                                $stmt = $mysqli->prepare($query);
                                $stmt->bind_param("i", $userId);
                                if ($stmt->execute()) {
                                    $result = $stmt->get_result();
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $skillName = htmlspecialchars($row['skill_name']);
                                            $proficiency = (int)$row['proficiency'];
                                            echo "<li>
                                                    <strong>{$skillName}</strong>
                                                    <div class='progress'>
                                                        <div class='progress-bar' role='progressbar' style='width: {$proficiency}%' aria-valuenow='{$proficiency}' aria-valuemin='0' aria-valuemax='100'>{$proficiency}%</div>
                                                    </div>
                                                  </li>";
                                        }
                                    } else {
                                        echo "<li>No skills listed.</li>";
                                    }
                                } else {
                                    echo "<li>Error fetching skills.</li>";
                                }
                                ?>
                            </ul>
                        </div>

                        <!-- Experience Section with Icons -->
                        <div class="card-box">
    <h4><i class="fa fa-briefcase" aria-hidden="true"></i> Experience</h4>
    <ul>
        <?php
        $query = "SELECT job_title, company, start_date, end_date, description FROM user_experience WHERE user_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<li>
                            <h5>{$row['job_title']} - <em>{$row['company']}</em></h5>
                            <p><strong>From:</strong> {$row['start_date']} <strong>To:</strong> {$row['end_date']}</p>
                            <p>" . nl2br(htmlspecialchars($row['description'])) . "</p>
                          </li>";
                }
            } else {
                echo "<li>No experience listed.</li>";  
            }
        } else {
            echo "<li>Error fetching experience.</li>";
        }
        ?>
    </ul>
</div>


                        <!-- Education Section with Icons -->
                        <div class="card-box">
    <h4><i class="fa fa-graduation-cap" aria-hidden="true"></i> Education</h4>
    <ul>
        <?php
        $query = "SELECT degree, institution, graduation_year FROM user_education WHERE user_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<li>
                            <h5>{$row['degree']} - <em>{$row['institution']}</em></h5>
                            <p><strong>Graduated:</strong> {$row['graduation_year']}</p>
                          </li>";
                }
            } else {
                echo "<li>No education listed.</li>"; 
            }
        } else {
            echo "<li>Error fetching education.</li>";
        }
        ?>
    </ul>
</div>


                        <!-- Certificates Section with Icons -->
                        <div class="card-box">
    <h4><i class="fa fa-certificate" aria-hidden="true"></i> Certificates</h4>
    <ul>
        <?php
        $query = "SELECT certificate_name, date_issued FROM user_certificates WHERE user_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<li>
                            <h5>{$row['certificate_name']}</h5>
                            <p><strong>Issued:</strong> {$row['date_issued']}</p>
                          </li>";
                }
            } else {
                echo "<li>No certificates listed.</li>";  
            }
        } else {
            echo "<li>Error fetching certificates.</li>";
        }
        ?>
    </ul>
</div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
