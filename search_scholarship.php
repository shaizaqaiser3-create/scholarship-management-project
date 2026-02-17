<?php
session_start();
include 'db.php';

$search_results = [];
$search_query = "";

if(isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search_query'] ?? '');
    $country = mysqli_real_escape_string($conn, $_GET['country'] ?? '');
    $degree = mysqli_real_escape_string($conn, $_GET['degree'] ?? '');
    
    // Build SQL query (using correct column names)
    $sql = "SELECT * FROM scholarships WHERE 1=1";
    
    if(!empty($search_query)) {
        $sql .= " AND (name LIKE '%$search_query%' OR institute LIKE '%$search_query%')";
    }
    
    if(!empty($country) && $country != 'All Countries') {
        $sql .= " AND country = '$country'";
    }
    
    if(!empty($degree)) {
        $sql .= " AND degree = '$degree'";
    }
    
    $sql .= " ORDER BY deadline ASC";
    
    $result = mysqli_query($conn, $sql);
    if($result) {
        while($row = mysqli_fetch_assoc($result)) {
            $search_results[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Scholarships</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f8f9fa; }
        .search-box { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input, select { padding: 10px; margin: 5px; width: 250px; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px; }
        .clear-btn { background: #6c757d; }
        .scholarship-card { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; background: white; }
        .deadline { color: #dc3545; font-weight: bold; }
        .benefits { color: #28a745; }
        .apply-btn { background: #28a745; color: white; padding: 8px 15px; text-decoration: none; display: inline-block; margin-top: 10px; border-radius: 4px; }
    </style>
</head>
<body>
    <h2>🔍 Search Scholarships</h2>
    
    <?php if(isset($_SESSION['user_id'])): ?>
        <?php if($_SESSION['user_type'] == 'admin'): ?>
            <a href="admin_dashboard.php">← Admin Dashboard</a> |
        <?php else: ?>
            <a href="#" onclick="alert('Create student/dashboard.php')">← Student Dashboard</a> |
        <?php endif; ?>
    <?php endif; ?>
    <a href="login.php">Login</a> | 
    <a href="register.php">Register</a> |
    <a href="admin_login.php">Admin Login</a>
    
    <div class="search-box">
        <form method="GET">
            <h3>Search Filters:</h3>
            
            <input type="text" name="search_query" placeholder="Search by name or institute" 
                   value="<?php echo htmlspecialchars($_GET['search_query'] ?? ''); ?>">
            
            <select name="country">
                <option value="">All Countries</option>
                <option value="Pakistan" <?php echo ($_GET['country'] ?? '') == 'Pakistan' ? 'selected' : ''; ?>>Pakistan</option>
                <option value="USA" <?php echo ($_GET['country'] ?? '') == 'USA' ? 'selected' : ''; ?>>USA</option>
                <option value="UK" <?php echo ($_GET['country'] ?? '') == 'UK' ? 'selected' : ''; ?>>UK</option>
                <option value="Canada" <?php echo ($_GET['country'] ?? '') == 'Canada' ? 'selected' : ''; ?>>Canada</option>
            </select>
            
            <select name="degree">
                <option value="">All Degrees</option>
                <option value="Matric" <?php echo ($_GET['degree'] ?? '') == 'Matric' ? 'selected' : ''; ?>>Matric</option>
                <option value="Intermediate" <?php echo ($_GET['degree'] ?? '') == 'Intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                <option value="Bachelor" <?php echo ($_GET['degree'] ?? '') == 'Bachelor' ? 'selected' : ''; ?>>Bachelor</option>
                <option value="Master" <?php echo ($_GET['degree'] ?? '') == 'Master' ? 'selected' : ''; ?>>Master</option>
                <option value="PhD" <?php echo ($_GET['degree'] ?? '') == 'PhD' ? 'selected' : ''; ?>>PhD</option>
            </select>
            
            <button type="submit" name="search">🔍 Search</button>
            <button type="button" onclick="window.location.href='search_scholarship.php'" class="clear-btn">🔄 Clear</button>
        </form>
    </div>
    
    <?php if(isset($_GET['search'])): ?>
        <h3>Search Results (<?php echo count($search_results); ?> found)</h3>
        
        <?php if(count($search_results) > 0): ?>
            <?php foreach($search_results as $scholarship): ?>
                <div class="scholarship-card">
                    <h3><?php echo htmlspecialchars($scholarship['name']); ?></h3>
                    <p><strong>🏛️ Institute:</strong> <?php echo htmlspecialchars($scholarship['institute']); ?></p>
                    <p><strong>🌍 Country:</strong> <?php echo htmlspecialchars($scholarship['country']); ?></p>
                    <p><strong>🎓 Required Degree:</strong> <?php echo htmlspecialchars($scholarship['degree']); ?></p>
                    <p><strong>📊 Minimum Percentage:</strong> <?php echo $scholarship['min_percentage']; ?>%</p>
                    <p class="benefits"><strong>💰 Benefits:</strong> <?php echo htmlspecialchars($scholarship['benefits']); ?></p>
                    <p class="deadline"><strong>⏰ Deadline:</strong> <?php echo date('d M, Y', strtotime($scholarship['deadline'])); ?></p>
                    
                    <?php if(isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'student'): ?>
                        <a href="#" onclick="alert('Create student/apply.php?scholarship_id=<?php echo $scholarship['scholarship_id']; ?>')" class="apply-btn">
                            ➕ Apply Now
                        </a>
                    <?php elseif(!isset($_SESSION['user_id'])): ?>
                        <a href="login.php" style="color: #007bff;">🔑 Login to Apply</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; border: 2px dashed #ccc; color: #666;">
                <h3>🔍 No Scholarships Found</h3>
                <p>Try different search terms or filters.</p>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div style="text-align: center; padding: 40px; color: #666;">
            <h3>🔍 Start Your Scholarship Search</h3>
            <p>Use the filters above to find scholarships matching your profile.</p>
        </div>
    <?php endif; ?>
</body>
</html>