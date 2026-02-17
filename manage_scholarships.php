<?php
session_start();
// Simple admin check
if(!isset($_SESSION['user_type'])) {
    $_SESSION['user_type'] = 'admin';
}

include '../db.php';

// Delete scholarship if requested
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete_sql = "DELETE FROM scholarships WHERE scholarship_id = $id";
    if(mysqli_query($conn, $delete_sql)) {
        $delete_msg = "✅ Scholarship deleted successfully!";
    }
}

// Get all scholarships
$sql = "SELECT * FROM scholarships ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$total_scholarships = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Scholarships - Admin</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #333; }
        .back-btn { display: inline-block; background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-bottom: 20px; }
        .add-btn { background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 15px 0; }
        table { width: 100%; background: white; border-collapse: collapse; margin-top: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        th { background: #007bff; color: white; padding: 15px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f8f9fa; }
        .action-btn { padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .edit-btn { background: #ffc107; color: #000; }
        .delete-btn { background: #dc3545; color: white; }
        .view-btn { background: #17a2b8; color: white; }
        .empty-state { text-align: center; padding: 50px; background: white; border-radius: 10px; margin-top: 20px; }
        .stats { display: flex; gap: 20px; margin: 20px 0; }
        .stat-box { flex: 1; background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .stat-number { font-size: 36px; font-weight: bold; color: #007bff; }
        .filter-bar { background: white; padding: 15px; border-radius: 10px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-btn">← Admin Dashboard</a>
        <a href="add_scholarship.php" class="add-btn">➕ Add New</a>
        
        <h1>📋 Manage Scholarships</h1>
        
        <?php if(isset($delete_msg)): ?>
            <div class="success"><?php echo $delete_msg; ?></div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="stats">
            <div class="stat-box">
                <div class="stat-number"><?php echo $total_scholarships; ?></div>
                <div>Total Scholarships</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">
                    <?php 
                    $today = date('Y-m-d');
                    $upcoming = mysqli_query($conn, "SELECT COUNT(*) as count FROM scholarships WHERE deadline >= '$today'");
                    echo mysqli_fetch_assoc($upcoming)['count'];
                    ?>
                </div>
                <div>Active (Not Expired)</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">
                    <?php
                    $bachelor = mysqli_query($conn, "SELECT COUNT(*) as count FROM scholarships WHERE degree = 'Bachelor'");
                    echo mysqli_fetch_assoc($bachelor)['count'];
                    ?>
                </div>
                <div>For Bachelor's</div>
            </div>
        </div>
        
        <?php if($total_scholarships > 0): ?>
            <div class="filter-bar">
                <strong>Filter:</strong>
                <select onchange="filterTable(this.value)">
                    <option value="all">All Scholarships</option>
                    <option value="active">Active (Not Expired)</option>
                    <option value="bachelor">For Bachelor's Degree</option>
                    <option value="pakistan">Pakistan Only</option>
                </select>
            </div>
            
            <table id="scholarshipsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Scholarship Name</th>
                        <th>Institute</th>
                        <th>Country</th>
                        <th>Degree</th>
                        <th>Min %</th>
                        <th>Deadline</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): 
                        $deadline_date = new DateTime($row['deadline']);
                        $today = new DateTime();
                        $is_expired = $deadline_date < $today;
                    ?>
                    <tr class="<?php echo $is_expired ? 'expired' : ''; ?>" 
                        data-country="<?php echo $row['country']; ?>"
                        data-degree="<?php echo $row['degree']; ?>"
                        data-expired="<?php echo $is_expired ? 'yes' : 'no'; ?>">
                        <td><?php echo $row['scholarship_id']; ?></td>
                        <td>
                            <strong><?php echo $row['name']; ?></strong>
                            <?php if($is_expired): ?>
                                <span style="color: red; font-size: 12px;">(Expired)</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['institute']; ?></td>
                        <td><?php echo $row['country']; ?></td>
                        <td><?php echo $row['degree']; ?></td>
                        <td><?php echo $row['min_percentage']; ?>%</td>
                        <td style="color: <?php echo $is_expired ? 'red' : 'green'; ?>;">
                            <?php echo date('d M, Y', strtotime($row['deadline'])); ?>
                        </td>
                        <td>
                            <a href="../search_scholarship.php?search=1&search_query=<?php echo urlencode($row['name']); ?>" 
                               class="action-btn view-btn" target="_blank">👁️ View</a>
                            <a href="edit_scholarship.php?id=<?php echo $row['scholarship_id']; ?>" 
                               class="action-btn edit-btn">✏️ Edit</a>
                            <a href="?delete=<?php echo $row['scholarship_id']; ?>" 
                               class="action-btn delete-btn" 
                               onclick="return confirm('Are you sure you want to delete this scholarship?\n\n<?php echo addslashes($row['name']); ?>')">🗑️ Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <div style="margin-top: 20px; color: #666;">
                <p><strong>Note:</strong> Red dates indicate expired scholarships. Students cannot apply to expired scholarships.</p>
            </div>
            
            <script>
            function filterTable(filter) {
                const rows = document.querySelectorAll('#scholarshipsTable tbody tr');
                const today = new Date().toISOString().split('T')[0];
                
                rows.forEach(row => {
                    let show = true;
                    
                    if(filter === 'active' && row.dataset.expired === 'yes') {
                        show = false;
                    } else if(filter === 'bachelor' && row.dataset.degree !== 'Bachelor') {
                        show = false;
                    } else if(filter === 'pakistan' && row.dataset.country !== 'Pakistan') {
                        show = false;
                    }
                    
                    row.style.display = show ? '' : 'none';
                });
            }
            </script>
            
        <?php else: ?>
            <div class="empty-state">
                <h2>📭 No Scholarships Found</h2>
                <p>You haven't added any scholarships yet.</p>
                <p><a href="add_scholarship.php" class="add-btn" style="display: inline-block; margin-top: 20px;">
                    ➕ Add Your First Scholarship
                </a></p>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px; background: #e8f4fc; padding: 20px; border-radius: 10px;">
            <h3>📊 Export Options:</h3>
            <a href="export_scholarships.php?type=csv" style="background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-right: 10px;">
                📥 Export as CSV
            </a>
            <a href="#" onclick="window.print()" style="background: #17a2b8; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
                🖨️ Print List
            </a>
        </div>
    </div>
</body>
</html>