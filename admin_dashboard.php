<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if admin is logged in (FIXED: changed 'role' to 'user_type')
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php?error=access_denied");
    exit();
}

// Include database connection
require_once('db.php');

// Check connection
if (!$conn) {
    die("Database connection failed. Please check your connection settings.");
}

// Get statistics
$stats = [];

// Total scholarships
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM scholarships");
$row = mysqli_fetch_assoc($result);
$stats['total_scholarships'] = $row['total'];

// Total applications
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM applications");
$row = mysqli_fetch_assoc($result);
$stats['total_applications'] = $row['total'];

// Pending applications
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM applications WHERE status = 'pending'");
$row = mysqli_fetch_assoc($result);
$stats['pending_applications'] = $row['total'];

// Total users
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
$row = mysqli_fetch_assoc($result);
$stats['total_users'] = $row['total'];

// Total students
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE user_type = 'student'");
$row = mysqli_fetch_assoc($result);
$stats['total_students'] = $row['total'];

// Get recent applications
$recent_applications_query = "
    SELECT a.*, u.name, u.email, s.name as scholarship_name 
    FROM applications a 
    JOIN users u ON a.user_id = u.user_id 
    JOIN scholarships s ON a.scholarship_id = s.scholarship_id 
    ORDER BY a.applied_date DESC 
    LIMIT 10
";
$recent_applications = mysqli_query($conn, $recent_applications_query);

// Get recent scholarships
$recent_scholarships_query = "
    SELECT * FROM scholarships 
    ORDER BY created_at DESC 
    LIMIT 5
";
$recent_scholarships = mysqli_query($conn, $recent_scholarships_query);

// Get recent users
$recent_users_query = "
    SELECT user_id, email, name, user_type, created_at 
    FROM users 
    ORDER BY created_at DESC 
    LIMIT 5
";
$recent_users = mysqli_query($conn, $recent_users_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Scholarship Portal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50, #4a6491);
            color: white;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 1.8rem;
            font-weight: 600;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logout-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #c0392b;
        }

        .container {
            display: flex;
            min-height: calc(100vh - 80px);
        }

        .sidebar {
            width: 250px;
            background-color: white;
            padding: 2rem 1rem;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
        }

        .nav-menu {
            list-style: none;
        }

        .nav-menu li {
            margin-bottom: 0.5rem;
        }

        .nav-menu a {
            display: block;
            padding: 0.8rem 1rem;
            color: #2c3e50;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .nav-menu a:hover, .nav-menu a.active {
            background-color: #3498db;
            color: white;
        }

        .nav-menu a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
        }

        .dashboard-title {
            margin-bottom: 2rem;
            color: #2c3e50;
            font-size: 1.8rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.8rem;
            color: white;
        }

        .scholarship-icon { background: linear-gradient(135deg, #3498db, #2980b9); }
        .application-icon { background: linear-gradient(135deg, #2ecc71, #27ae60); }
        .pending-icon { background: linear-gradient(135deg, #f39c12, #d35400); }
        .user-icon { background: linear-gradient(135deg, #9b59b6, #8e44ad); }

        .stat-info h3 {
            font-size: 1.8rem;
            color: #2c3e50;
            margin-bottom: 0.2rem;
        }

        .stat-info p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .section {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid #f1f1f1;
        }

        .section-title {
            font-size: 1.3rem;
            color: #2c3e50;
        }

        .view-all {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }

        .view-all:hover {
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background-color: #f8f9fa;
            padding: 1rem;
            text-align: left;
            color: #2c3e50;
            font-weight: 600;
            border-bottom: 2px solid #eee;
        }

        table td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            color: #555;
        }

        table tr:hover {
            background-color: #f9f9f9;
        }

        .status {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-btn {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85rem;
            margin-right: 0.5rem;
            transition: opacity 0.3s;
        }

        .action-btn:hover {
            opacity: 0.9;
        }

        .btn-view {
            background-color: #3498db;
            color: white;
        }

        .btn-edit {
            background-color: #f39c12;
            color: white;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }

        .no-data {
            text-align: center;
            padding: 2rem;
            color: #95a5a6;
            font-style: italic;
        }

        @media (max-width: 1024px) {
            .container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                padding: 1rem;
            }
            
            .nav-menu {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .nav-menu li {
                flex: 1;
                min-width: 150px;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-graduation-cap"></i> Scholarship Portal Admin</h1>
        <div class="user-info">
            <!-- FIXED: Changed session variable names -->
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?> (<?php echo htmlspecialchars($_SESSION['user_email'] ?? 'admin@test.com'); ?>)</span>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="container">
        <nav class="sidebar">
            <ul class="nav-menu">
                <li><a href="admin_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="#" onclick="alert('Create scholarships.php file')"><i class="fas fa-award"></i> Scholarships</a></li>
                <li><a href="#" onclick="alert('Create applications.php file')"><i class="fas fa-file-alt"></i> Applications</a></li>
                <li><a href="#" onclick="alert('Create users.php file')"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="#" onclick="alert('Create reports.php file')"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="#" onclick="alert('Create settings.php file')"><i class="fas fa-cog"></i> Settings</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <h2 class="dashboard-title">Admin Dashboard</h2>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon scholarship-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_scholarships']; ?></h3>
                        <p>Total Scholarships</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon application-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_applications']; ?></h3>
                        <p>Total Applications</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon pending-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['pending_applications']; ?></h3>
                        <p>Pending Applications</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon user-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_users']; ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>
            </div>

            <!-- Recent Applications -->
            <div class="section">
                <div class="section-header">
                    <h3 class="section-title">Recent Applications</h3>
                    <a href="#" onclick="alert('Create applications.php file')" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <?php if(mysqli_num_rows($recent_applications) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Applicant</th>
                                <th>Scholarship</th>
                                <th>Applied Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($recent_applications)): ?>
                                <tr>
                                    <td>#<?php echo $row['application_id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?><br><small><?php echo htmlspecialchars($row['email']); ?></small></td>
                                    <td><?php echo htmlspecialchars($row['scholarship_name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['applied_date'])); ?></td>
                                    <td>
                                        <span class="status status-<?php echo $row['status']; ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="action-btn btn-view" onclick="viewApplication(<?php echo $row['application_id']; ?>)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button class="action-btn btn-edit" onclick="editApplication(<?php echo $row['application_id']; ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-file-alt fa-3x"></i>
                        <p>No applications found</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Scholarships -->
            <div class="section">
                <div class="section-header">
                    <h3 class="section-title">Recent Scholarships</h3>
                    <a href="#" onclick="alert('Create scholarships.php file')" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <?php if(mysqli_num_rows($recent_scholarships) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Deadline</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($recent_scholarships)): ?>
                                <tr>
                                    <td>#<?php echo $row['scholarship_id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['deadline'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['benefits']); ?></td>
                                    <td>
                                        <button class="action-btn btn-view" onclick="viewScholarship(<?php echo $row['scholarship_id']; ?>)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button class="action-btn btn-edit" onclick="editScholarship(<?php echo $row['scholarship_id']; ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-award fa-3x"></i>
                        <p>No scholarships found</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Users -->
            <div class="section">
                <div class="section-header">
                    <h3 class="section-title">Recent Users</h3>
                    <a href="#" onclick="alert('Create users.php file')" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <?php if(mysqli_num_rows($recent_users) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($recent_users)): ?>
                                <tr>
                                    <td>#<?php echo $row['user_id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td>
                                        <span class="status <?php echo 'status-' . $row['user_type']; ?>">
                                            <?php echo ucfirst($row['user_type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <button class="action-btn btn-view" onclick="viewUser(<?php echo $row['user_id']; ?>)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button class="action-btn btn-edit" onclick="editUser(<?php echo $row['user_id']; ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-users fa-3x"></i>
                        <p>No users found</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        // JavaScript functions for actions
        function viewApplication(id) {
            alert('Create view_application.php?id=' + id);
        }

        function editApplication(id) {
            alert('Create edit_application.php?id=' + id);
        }

        function viewScholarship(id) {
            alert('Create view_scholarship.php?id=' + id);
        }

        function editScholarship(id) {
            alert('Create edit_scholarship.php?id=' + id);
        }

        function viewUser(id) {
            alert('Create view_user.php?id=' + id);
        }

        function editUser(id) {
            alert('Create edit_user.php?id=' + id);
        }
    </script>
</body>
</html>
<?php
// Close database connection
mysqli_close($conn);
?>