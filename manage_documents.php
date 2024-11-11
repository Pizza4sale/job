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

// Fetch documents for the logged-in user
$query = "SELECT * FROM Documents WHERE user_id = ? ORDER BY upload_date DESC";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$documents = $result->fetch_all(MYSQLI_ASSOC);
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
                            <h4>Document Management</h4>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 text-right">
                        <a href="upload_document.php" class="btn btn-primary">Upload New Document</a>
                    </div>
                </div>
            </div>

            <!-- Documents Table -->
            <div class="card-box mb-30">
                <div class="pd-ltr-20">
                    <div class="card-box pd-20 box-shadow">
                        <h4 class="text-center">Your Documents</h4>
                        <?php if (empty($documents)): ?>
                            <p class="text-center">No documents found.</p>
                        <?php else: ?>
                        <table class="table hover data-table-export nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Document Name</th>
                                    <th>Upload Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documents as $document): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($document['id']); ?></td>
                                        <td><?php echo htmlspecialchars($document['document_name']); ?></td>
                                        <td><?php echo date('F j, Y', strtotime($document['upload_date'])); ?></td>
                                        <td>
    <a href="view_pdf.php?id=<?php echo htmlspecialchars($document['id']); ?>" class="btn btn-sm btn-info">View</a>
    <a href="edit_document.php?id=<?php echo htmlspecialchars($document['id']); ?>" class="btn btn-sm btn-warning">Edit</a>
    <a href="delete_document.php?id=<?php echo htmlspecialchars($document['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this document?');">Delete</a>
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

<?php
include("includes/script.php");
?>
