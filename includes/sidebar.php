<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="left-side-bar">
    <div class="brand-logo">
        <a href="index.php">
            <span>JAT</span>
        </a>
        <div class="close-sidebar" data-toggle="left-sidebar-close" role="button" aria-label="Close Sidebar">
            <i class="ion-close-round"></i>
        </div>
    </div>
    <nav class="menu-block customscroll">
        <div class="sidebar-menu">
            <ul id="accordion-menu">
                <li>
                    <a href="index.php" class="dropdown-toggle no-arrow <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" aria-label="Dashboard">
                        <span class="micon bi bi-house-fill"></span>
                        <span class="mtext">Dashboard</span>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle" 
                       aria-expanded="<?php echo isset($dropdown_active) && $dropdown_active == 'applications' ? 'true' : 'false'; ?>" 
                       aria-label="Applications">
                        <span class="micon bi bi-briefcase-fill"></span>
                        <span class="mtext">Applications</span>
                    </a>
                    <ul class="submenu" style="<?php echo isset($dropdown_active) && $dropdown_active == 'applications' ? 'display:block;' : 'display:none;'; ?>">
                        <li><a href="add_application.php" class="<?php echo $current_page == 'add_application.php' ? 'active' : ''; ?>">Add Application</a></li>
                        <li><a href="applications.php" class="<?php echo $current_page == 'applications.php' ? 'active' : ''; ?>">Manage Applications</a></li>
                    </ul>
                </li>
                <li class="dropdown">
    <a href="javascript:;" class="dropdown-toggle" 
       aria-expanded="<?php echo isset($dropdown_active) && $dropdown_active == 'documents' ? 'true' : 'false'; ?>" 
       aria-label="Documents">
        <span class="micon bi bi-file-earmark-text"></span>
        <span class="mtext">Documents</span>
    </a>
    <ul class="submenu" style="<?php echo isset($dropdown_active) && $dropdown_active == 'documents' ? 'display:block;' : 'display:none;'; ?>">
        <li><a href="upload_document.php" class="<?php echo $current_page == 'upload_document.php' ? 'active' : ''; ?>">Upload Document</a></li>
        <li><a href="manage_documents.php" class="<?php echo $current_page == 'manage_documents.php' ? 'active' : ''; ?>">Manage Documents</a></li>
    </ul>
</li>

            </ul>
        </div>
    </nav>
</div>
