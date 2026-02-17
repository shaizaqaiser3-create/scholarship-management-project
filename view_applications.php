<?php
session_start();
if(!isset($_SESSION['user_type'])) {
    $_SESSION['user_type'] = 'admin';
}

include '../db.php';

// Handle actions: approve, reject, delete
if(isset($_GET['action'])) {
    $app_id = $_GET['id'];
    $action = $_GET['action'];
    
    if($action == 'approve') {
        $sql = "UPDATE applications SET status = 'approved' WHERE application_id = $app_id";
        $message = "✅ Application approved!";
    } elseif($action == 'reject') {
        $sql = "UPDATE applications SET status = 'rejected' WHERE application_id = $app_id";
        $message = "❌ Application rejected.";
    } elseif($action == 'delete') {
        $sql = "DELETE FROM applications WHERE application_id = $app_id";
        $message = "🗑️ Application deleted.";
    }
    
    if(isset($sql) && mysqli_query($conn, $sql)) {
        $action_msg = $message;
    }
}

// Handle admin notes
if(isset($_POST['save_notes'])) {
    $app_id = $_POST['app_id'];
    $notes = mysqli_real_escape_string($conn, $_POST['admin_notes']);
    $update_sql = "UPDATE applications SET admin_notes = '$notes' WHERE application_id = $app_id";
    mysqli_query($conn, $update_sql);
}

// Get all applications with filters
$status_filter = $_GET['status'] ?? 'all';
$where = "1=1";
if($status_filter != 'all') {
    $where .= " AND a.status = '$status_filter'";
}

$search = $_GET['search'] ?? '';
if(!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $where .= " AND (s.name LIKE '%$search%' OR u.name LIKE '%$search%' OR u.email LIKE '%$search%')";
}

$sql = "SELECT a.*, s.name as scholarship_name, s.institute, s.country, 
               u.name as student_name, u.email as student_email, u.user_id as student_id
        FROM applications a
        JOIN scholarships s ON a.scholarship_id = s.scholarship_id
        JOIN users u ON a.student_id = u.user_id
        WHERE $where
        ORDER BY a.application_date DESC";
$result = mysqli_query($conn, $sql);
$total_applications = mysqli_num_rows($result);

// Statistics
$stats_sql = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
    FROM applications";
$stats_result = mysqli_query($conn, $stats_sql);
$stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Applications - Admin</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; }
        h1 { color: #333; }
        .back-btn { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-bottom: 20px; display: inline-block; }
        .stats { display: flex; gap: 20px; margin: 20px 0; }
        .stat-box { flex: 1; background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .stat-number { font-size: 36px; font-weight: bold; }
        .filter-bar { background: white; padding: 20px; border-radius: 10px; margin: 20px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .application-card { background: white; padding: 20px; border-radius: 10px; margin: 15px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .application-header { display: flex; justify-content: space-between; align-items: center; }
        .status-badge { padding: 5px 15px; border-radius: 20px; font-weight: bold; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .status-review { background: #cce5ff; color: #004085; }
        .action-btn { padding: 8px 15px; text-decoration: none; border-radius: 5px; margin-right: 5px; font-size: 14px; }
        .approve-btn { background: #28a745; color: white; }
        .reject-btn { background: #dc3545; color: white; }
        .view-btn { background: #17a2b8; color: white; }
        .notes-btn { background: #ffc107; color: #000; }
        .empty-state { text-align: center; padding: 50px; background: white; border-radius: 10px; margin-top: 20px; }
        .notes-panel { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 15px; }
        .notes-panel textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; height: 80px; }
        .application-details { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 15px; }
        .student-info { background: #e8f4fc; padding: 15px; border-radius: 5px; }
        .scholarship-info { background: #f8f9fa; padding: 15px; border-radius: 5px; }
        .success-msg { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-btn">← Admin Dashboard</a>
        
        <h1>📋 Manage Scholarship Applications</h1>
        
        <?php if(isset($action_msg)): ?>
            <div class="success-msg"><?php echo $action_msg; ?></div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="stats">
            <div class="stat-box">
                <div class="stat-number" style="color: #007bff;"><?php echo $stats['total']; ?></div>
                <div>Total Applications</div>
            </div>
            <div class="stat-box">
                <div class="stat-number" style="color: #ffc107;"><?php echo $stats['pending']; ?></div>
                <div>Pending Review</div>
            </div>
            <div class="stat-box">
                <div class="stat-number" style="color: #28a745;"><?php echo $stats['approved']; ?></div>
                <div>Approved</div>
            </div>
            <div class="stat-box">
                <div class="stat-number" style="color: #dc3545;"><?php echo $stats['rejected']; ?></div>
                <div>Rejected</div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filter-bar">
            <form method="GET">
                <strong>Filter by Status:</strong>
                <select name="status" onchange="this.form.submit()">
                    <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Applications</option>
                    <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?php echo $status_filter == 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?php echo $status_filter == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
                
                <input type="text" name="search" placeholder="Search by student or scholarship..." 
                       value="<?php echo htmlspecialchars($search); ?>" style="margin-left: 20px; padding: 8px; width: 300px;">
                <button type="submit" style="background: #007bff; color: white; padding: 8px 15px; border: none; border-radius: 5px;">🔍 Search</button>
                <?php if(!empty($search) || $status_filter != 'all'): ?>
                    <a href="view_applications.php" style="margin-left: 10px;">Clear Filters</a>
                <?php endif; ?>
            </form>
        </div>
        
        <?php if($total_applications > 0): ?>
            
            <h3>Applications (<?php echo $total_applications; ?> found)</h3>
            
            <?php while($app = mysqli_fetch_assoc($result)): 
                $status_class = "status-" . $app['status'];
            ?>
            <div class="application-card" id="app-<?php echo $app['application_id']; ?>">
                <div class="application-header">
                    <div>
                        <h3 style="margin: 0;">Application #<?php echo $app['application_id']; ?></h3>
                        <p style="margin: 5px 0; color: #666;">
                            <strong>📅 Applied:</strong> <?php echo date('d M, Y H:i', strtotime($app['application_date'])); ?>
                        </p>
                    </div>
                    <div>
                        <span class="status-badge <?php echo $status_class; ?>">
                            <?php echo strtoupper($app['status']); ?>
                        </span>
                    </div>
                </div>
                
                <div class="application-details">
                    <div class="student-info">
                        <h4>👤 Student Information</h4>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($app['student_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($app['student_email']); ?></p>
                        <p><strong>Student ID:</strong> #<?php echo $app['student_id']; ?></p>
                        <p><strong>Applied with:</strong> <?php echo $app['applied_degree']; ?> (<?php echo $app['applied_percentage']; ?>%)</p>
                        
                        <div style="margin-top: 10px;">
                            <a href="student_profile.php?id=<?php echo $app['student_id']; ?>" class="action-btn view-btn" target="_blank">👤 View Profile</a>
                            <a href="mailto:<?php echo $app['student_email']; ?>" class="action-btn" style="background: #6f42c1; color: white;">📧 Email</a>
                        </div>
                    </div>
                    
                    <div class="scholarship-info">
                        <h4>🎓 Scholarship</h4>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($app['scholarship_name']); ?></p>
                        <p><strong>Institute:</strong> <?php echo htmlspecialchars($app['institute']); ?></p>
                        <p><strong>Country:</strong> <?php echo $app['country']; ?></p>
                        <p><strong>Application ID:</strong> #<?php echo $app['application_id']; ?></p>
                    </div>
                </div>
                
                <?php if($app['additional_info']): ?>
                    <div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                        <strong>Student's Additional Notes:</strong>
                        <p><?php echo nl2br(htmlspecialchars($app['additional_info'])); ?></p>
                    </div>
                <?php endif; ?>
                
                <!-- Admin Notes -->
                <div class="notes-panel">
                    <form method="POST">
                        <input type="hidden" name="app_id" value="<?php echo $app['application_id']; ?>">
                        <strong>📝 Admin Notes:</strong>
                        <textarea name="admin_notes" placeholder="Add internal notes here..."><?php echo htmlspecialchars($app['admin_notes'] ?? ''); ?></textarea>
                        <button type="submit" name="save_notes" style="background: #6c757d; color: white; padding: 8px 15px; border: none; border-radius: 5px; margin-top: 5px;">Save Notes</button>
                    </form>
                </div>
                
                <!-- Action Buttons -->
                <div style="margin-top: 15px;">
                    <?php if($app['status'] == 'pending'): ?>
                        <a href="?action=approve&id=<?php echo $app['application_id']; ?>" 
                           class="action-btn approve-btn"
                           onclick="return confirm('Approve this application?')">✅ Approve</a>
                        <a href="?action=reject&id=<?php echo $app['application_id']; ?>" 
                           class="action-btn reject-btn"
                           onclick="return confirm('Reject this application?')">❌ Reject</a>
                    <?php endif; ?>
                    
                    <a href="?action=delete&id=<?php echo $app['application_id']; ?>" 
                       class="action-btn" style="background: #dc3545; color: white;"
                       onclick="return confirm('Delete this application permanently?')">🗑️ Delete</a>
                    
                    <a href="../student/apply.php?scholarship_id=<?php echo $app['scholarship_id']; ?>" 
                       class="action-btn view-btn" target="_blank">👁️ View Application Form</a>
                </div>
            </div>
            <?php endwhile; ?>
            
        <?php else: ?>
            <div class="empty-state">
                <h2>📭 No Applications Found</h2>
                <p><?php echo !empty($search) ? 'No applications match your search criteria.' : 'No applications have been submitted yet.'; ?></p>
                <?php if(!empty($search) || $status_filter != 'all'): ?>
                    <p><a href="view_applications.php">View all applications</a></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- Export Options -->
        <div style="margin-top: 30px; background: #e8f4fc; padding: 20px; border-radius: 10px;">
            <h3>📊 Export & Reports</h3>
            <div style="display: flex; gap: 15px; margin-top: 10px;">
                <a href="export_applications.php?type=csv&status=<?php echo $status_filter; ?>" 
                   style="background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
                   📥 Export as CSV
                </a>
                <a href="#" onclick="window.print()" 
                   style="background: #17a2b8; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
                   🖨️ Print Report
                </a>
                <a href="dashboard.php" 
                   style="background: #6c757d; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
                   📊 Dashboard
                </a>
            </div>
        </div>
    </div>
    
    <script>
    function filterApplications() {
        const status = document.getElementById('statusFilter').value;
        window.location.href = 'view_applications.php?status=' + status;
    }
    </script>
</body>
</html>