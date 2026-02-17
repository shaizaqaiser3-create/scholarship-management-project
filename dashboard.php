<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if($_SESSION['user_type'] != 'student') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .welcome { background: #e8f4fc; padding: 20px; border-radius: 10px; }
        ul { list-style: none; padding: 0; }
        li { margin: 10px 0; }
        a { display: block; padding: 12px; background: #007bff; color: white; 
            text-decoration: none; border-radius: 5px; }
        a:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="welcome">
        <h1>Welcome, <?php echo $_SESSION['user_name']; ?>! 👋</h1>
        <p>Email: <?php echo $_SESSION['user_email']; ?></p>
        <p>User Type: <strong><?php echo $_SESSION['user_type']; ?></strong></p>
    </div>
    
    <h2>📋 Quick Actions</h2>
    <ul>
        <li><a href="add_education.php">➕ Add Education History</a></li>
        <li><a href="view_education.php">📚 View My Education</a></li>
        <li><a href="matching.php">🎯 Matching Scholarships</a></li>
        <li><a href="../search_scholarship.php">🔍 Search Scholarships</a></li>
        <li><a href="../logout.php">🚪 Logout</a></li>
    </ul>
    
    <h3>📊 Your Statistics</h3>
    <?php
    include '../db.php';
    $user_id = $_SESSION['user_id'];
    
    // Count education records
    $edu_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM education WHERE user_id = '$user_id'");
    $edu_row = mysqli_fetch_assoc($edu_count);
    
    // Count matching scholarships (simplified)
    $match_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM scholarships");
    $match_row = mysqli_fetch_assoc($match_count);
    ?>
    <p>📝 Education Records: <strong><?php echo $edu_row['total']; ?></strong></p>
    <p>🎯 Available Scholarships: <strong><?php echo $match_row['total']; ?></strong></p>
</body>
</html>