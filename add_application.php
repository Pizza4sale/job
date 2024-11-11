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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $jobTitle = filter_var($_POST['job_title'], FILTER_SANITIZE_STRING);
    $companyName = filter_var($_POST['company_name'], FILTER_SANITIZE_STRING);
    $applicationDate = $_POST['application_date'];
    $status = $_POST['status'];
    $salaryRange = filter_var($_POST['salary_range'], FILTER_SANITIZE_STRING);
    $jobType = $_POST['job_type'];
    $location = filter_var($_POST['location'], FILTER_SANITIZE_STRING);
    $resumeLink = filter_var($_POST['resume_link'], FILTER_SANITIZE_URL);
    $coverLetterLink = filter_var($_POST['cover_letter_link'], FILTER_SANITIZE_URL);
    $portfolioLink = filter_var($_POST['portfolio_link'], FILTER_SANITIZE_URL);
    $skillsRequired = filter_var($_POST['skills_required'], FILTER_SANITIZE_STRING);
    $source = filter_var($_POST['source'], FILTER_SANITIZE_STRING);
    $applicationMethod = filter_var($_POST['application_method'], FILTER_SANITIZE_STRING);
    $recruiterName = filter_var($_POST['recruiter_name'], FILTER_SANITIZE_STRING);
    $recruiterEmail = filter_var($_POST['recruiter_email'], FILTER_SANITIZE_EMAIL);
    $recruiterPhone = filter_var($_POST['recruiter_phone'], FILTER_SANITIZE_STRING);
    $interviewDate = $_POST['interview_date'];
    $interviewTime = $_POST['interview_time'];
    $followUpDate = $_POST['follow_up_date'];
    $feedbackReceived = isset($_POST['feedback_received']) ? 1 : 0;
    $feedbackNotes = filter_var($_POST['feedback_notes'], FILTER_SANITIZE_STRING);
    $notes = filter_var($_POST['notes'], FILTER_SANITIZE_STRING);

    // Insert data into the Applications table
    $query = "INSERT INTO Applications (user_id, job_title, company_name, application_date, status, salary_range, job_type, location, resume_link, cover_letter_link, portfolio_link, skills_required, source, application_method, recruiter_name, recruiter_email, recruiter_phone, interview_date, interview_time, follow_up_date, feedback_received, feedback_notes, notes)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $mysqli->prepare($query);
    if ($stmt === false) {
        error_log("Prepare failed: " . htmlspecialchars($mysqli->error));
        echo "<script>alert('Error preparing statement');</script>";
        exit;
    }

    // Update the bind_param types according to your fields
    $stmt->bind_param("issssssssssssssssssssss", 
        $userId, 
        $jobTitle, 
        $companyName, 
        $applicationDate, 
        $status, 
        $salaryRange, 
        $jobType, 
        $location, 
        $resumeLink, 
        $coverLetterLink, 
        $portfolioLink, 
        $skillsRequired, 
        $source, 
        $applicationMethod, 
        $recruiterName, 
        $recruiterEmail, 
        $recruiterPhone, 
        $interviewDate, 
        $interviewTime, 
        $followUpDate, 
        $feedbackReceived, 
        $feedbackNotes, 
        $notes
    );

    if ($stmt->execute()) {
        echo "<script>alert('Application added successfully!'); window.location.href = 'applications.php';</script>";
    } else {
        error_log("Execute failed: " . htmlspecialchars($stmt->error));
        echo "<script>alert('Error adding application');</script>";
    }
    $stmt->close();
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
                            <h4>Add application</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="index.php">Home</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Add application</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="card-box mb-30">
                <div class="pb-20"></div>
                <div class="pd-ltr-20">
                    <div class="card-box pd-20 box-shadow">
                        <h4 class="text-center">Add New application</h4>
                <form method="POST">
                    <div class="form-group">
                        <label>Job Title</label>
                        <input type="text" name="job_title" class="form-control" placeholder="e.g., Software Engineer" required>
                    </div>
                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" name="company_name" class="form-control" placeholder="e.g., ABC Corp" required>
                    </div>
                    <div class="form-group">
                        <label>Application Date</label>
                        <input type="date" name="application_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="Applied">Applied</option>
                            <option value="Interview">Interview</option>
                            <option value="Offer">Offer</option>
                            <option value="Rejected">Rejected</option>
                            <option value="Accepted">Accepted</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Salary Range</label>
                        <input type="text" name="salary_range" class="form-control" placeholder="e.g., PHP 30,000 - PHP 40,000">
                    </div>
                    <div class="form-group">
                        <label>Job Type</label>
                        <select name="job_type" class="form-control">
                            <option value="Full-Time">Full-Time</option>
                            <option value="Part-Time">Part-Time</option>
                            <option value="Internship">Internship</option>
                            <option value="Contract">Contract</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <select name="location" class="form-control">
                            <option value="Pampanga">Pampanga</option>
                            <option value="Metro Manila">Metro Manila</option>
                            <option value="Cebu">Cebu</option>
                            <option value="Davao">Davao</option>
                            <option value="Iloilo">Iloilo</option>
                            <option value="Remote">Remote</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Resume Link</label>
                        <input type="url" name="resume_link" class="form-control" placeholder="http://example.com/myresume.pdf">
                    </div>
                    <div class="form-group">
                        <label>Cover Letter Link</label>
                        <input type="url" name="cover_letter_link" class="form-control" placeholder="http://example.com/mycoverletter.pdf">
                    </div>
                    <div class="form-group">
                        <label>Portfolio Link</label>
                        <input type="url" name="portfolio_link" class="form-control" placeholder="http://example.com/myportfolio">
                    </div>
                    <div class="form-group">
    <label>Skills Required</label>
    <input type="text" name="skills_required" class="form-control" id="skills-input" placeholder="Enter skills (e.g., PHP, JavaScript)" oninput="showSuggestions(this.value)">
    <div id="suggestions-box" class="suggestions"></div>
    <small class="form-text text-muted">Start typing to see suggestions. Use commas to separate skills.</small>
</div>


                    <div class="form-group">
                        <label>Source</label>
                        <select name="source" class="form-control">
                            <option value="JobStreet">JobStreet</option>
                            <option value="Indeed">Indeed</option>
                            <option value="LinkedIn">LinkedIn</option>
                            <option value="Referral">Referral</option>
                            <option value="Company Website">Company Website</option>
                            <option value="Recruitment Agency">Recruitment Agency</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Application Method</label>
                        <select name="application_method" class="form-control">
                            <option value="Online Application">Online Application</option>
                            <option value="Walk-In">Walk-In</option>
                            <option value="Email">Email</option>
                            <option value="Phone">Phone</option>
                            <option value="Referral">Referral</option>
                            <option value="Recruitment Agency">Recruitment Agency</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Recruiter Name</label>
                        <input type="text" name="recruiter_name" class="form-control" placeholder="e.g., John Doe">
                    </div>
                    <div class="form-group">
                        <label>Recruiter Email</label>
                        <input type="email" name="recruiter_email" class="form-control" placeholder="e.g., johndoe@example.com">
                    </div>
                    <div class="form-group">
                        <label>Recruiter Phone</label>
                        <input type="tel" name="recruiter_phone" class="form-control" placeholder="e.g., +639123456789">
                    </div>
                    <div class="form-group">
                        <label>Interview Date</label>
                        <input type="date" name="interview_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Interview Time</label>
                        <input type="time" name="interview_time" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Follow-up Date</label>
                        <input type="date" name="follow_up_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Feedback Received</label>
                        <input type="checkbox" name="feedback_received">
                    </div>
                    <div class="form-group">
                        <label>Feedback Notes</label>
                        <textarea name="feedback_notes" class="form-control" placeholder="e.g., Great discussion, waiting for final decision."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" placeholder="Any additional notes or comments"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Application</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include("includes/script.php"); ?>
<script>
const skillsSuggestions = [
    // Programming Languages
    "PHP",
    "JavaScript",
    "HTML",
    "CSS",
    "SQL",
    "Python",
    "Java",
    "C#",
    "Ruby",
    "Go",
    "Swift",
    "Kotlin",
    "Rust",
    "TypeScript",
    "Perl",
    "Scala",

    // Frameworks & Libraries
    "React",
    "Node.js",
    "Django",
    "Flask",
    "Spring",
    "Angular",
    "Vue.js",
    "Bootstrap",
    "jQuery",
    "Express.js",
    "Laravel",
    "ASP.NET",
    "Ruby on Rails",
    "TensorFlow",

    // Data Science & Analysis
    "Machine Learning",
    "Data Analysis",
    "Data Visualization",
    "R",
    "TensorFlow",
    "Pandas",
    "NumPy",
    "Power BI",
    "Tableau",
    "Excel",
    "Statistical Analysis",
    "Data Mining",
    "Artificial Intelligence",
    "Big Data",

    // DevOps & Cloud Computing
    "Git",
    "AWS",
    "Azure",
    "Docker",
    "Kubernetes",
    "CI/CD",
    "Ansible",
    "Terraform",
    "Linux",
    "Cloud Security",
    "Networking",
    "Monitoring & Logging",
    "Virtualization",

    // UI/UX Design
    "UI Design",
    "UX Design",
    "Figma",
    "Adobe XD",
    "Sketch",
    "Wireframing",
    "Prototyping",
    "User Research",
    "Visual Design",
    "Interaction Design",
    "Information Architecture",

    // Digital Marketing
    "SEO",
    "SEM",
    "Social Media Marketing",
    "Content Marketing",
    "Email Marketing",
    "Google Analytics",
    "PPC Advertising",
    "Conversion Rate Optimization",
    "Brand Strategy",
    "Influencer Marketing",
    "Affiliate Marketing",
    "Market Research",

    // Project Management
    "Agile",
    "Scrum",
    "Kanban",
    "Project Management",
    "JIRA",
    "Asana",
    "Trello",
    "Microsoft Project",
    "Risk Management",
    "Stakeholder Management",
    "Budgeting",

    // General Skills
    "Communication",
    "Problem Solving",
    "Time Management",
    "Teamwork",
    "Critical Thinking",
    "Leadership",
    "Customer Service",
    "Negotiation",
    "Public Speaking",
    "Conflict Resolution",
    "Adaptability",
    "Creativity",

    // Cybersecurity
    "Network Security",
    "Information Security",
    "Penetration Testing",
    "Incident Response",
    "Ethical Hacking",
    "Compliance (GDPR, HIPAA)",
    "Firewalls",
    "Intrusion Detection Systems (IDS)",

    // Graphic Design
    "Photoshop",
    "Illustrator",
    "InDesign",
    "Graphic Design",
    "Branding",
    "Print Design",
    "Digital Illustration",
    
    // Sales & Customer Support
    "Sales Strategy",
    "Lead Generation",
    "CRM Software (Salesforce, HubSpot)",
    "Customer Relationship Management",
    "B2B Sales",
    "B2C Sales",
    "Account Management"
];


function showSuggestions(value) {
    const suggestionsBox = document.getElementById("suggestions-box");
    suggestionsBox.innerHTML = ""; // Clear previous suggestions
    const skills = value.split(',').map(skill => skill.trim()); // Split the input into skills

    if (skills.length > 0) {
        const lastSkill = skills[skills.length - 1]; // Get the last entered skill
        if (lastSkill) {
            const filteredSuggestions = skillsSuggestions.filter(skill => skill.toLowerCase().startsWith(lastSkill.toLowerCase()));
            filteredSuggestions.forEach(skill => {
                const suggestionItem = document.createElement("div");
                suggestionItem.textContent = skill;
                suggestionItem.classList.add("suggestion-item");
                suggestionItem.onclick = function() {
                    // Append the selected skill and clear the suggestions
                    skills[skills.length - 1] = skill; // Replace the last skill with the selected one
                    document.getElementById("skills-input").value = skills.join(", ") + ", "; // Join the skills and add a comma
                    suggestionsBox.innerHTML = ""; // Clear suggestions
                    document.getElementById("skills-input").focus(); // Keep focus on the input field
                };
                suggestionsBox.appendChild(suggestionItem);
            });
        }
    }
}
</script>
<style>
.suggestions {
    border: 1px solid #ddd;
    border-radius: 4px;
    position: absolute;
    background-color: white;
    max-height: 150px;
    overflow-y: auto;
    z-index: 1000;
    width: calc(100% - 2px);
}

.suggestion-item {
    padding: 10px;
    cursor: pointer;
}

.suggestion-item:hover {
    background-color: #f0f0f0;
}
</style>
