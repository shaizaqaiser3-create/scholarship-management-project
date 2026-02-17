<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

$user_id = $_SESSION['user_id'];
$matched_scholarships = [];
$student_profile = null;

// 1. Get student's education profile (highest degree)
$edu_sql = "SELECT * FROM education WHERE user_id = '$user_id' ORDER BY 
           CASE degree 
               WHEN 'PhD' THEN 1
               WHEN 'Master' THEN 2
               WHEN 'Bachelor' THEN 3
               WHEN 'Intermediate' THEN 4
               WHEN 'Matric' THEN 5
               ELSE 6
           END LIMIT 1";
$edu_result = mysqli_query($conn, $edu_sql);

if(mysqli_num_rows($edu_result) > 0) {
    $student_profile = mysqli_fetch_assoc($edu_result);
    $student_degree = $student_profile['degree'];
    $student_percentage = $student_profile['percentage'];
    
    // Degree ranking for comparison
    $degree_rank = [
        'PhD' => 5,
        'Master' => 4,
        'Bachelor' => 3,
        'Intermediate' => 2,
        'Matric' => 1
    ];
    
    $student_rank = $degree_rank[$student_degree] ?? 0;
    
    // 2. Get all scholarships
    $scholarships_sql = "SELECT * FROM scholarships ORDER BY deadline ASC";
    $scholarships_result = mysqli_query($conn, $scholarships_sql);
    
    // 3. Filter matching scholarships
    while($scholarship = mysqli_fetch_assoc($scholarships_result)) {
        $required_degree = $scholarship['degree'];
        $min_percentage = $scholarship['min_percentage'];
        $required_rank = $degree_rank[$required_degree] ?? 0;
        
        // Check if student qualifies
        $degree_ok = ($student_rank >= $required_rank);
        $percentage_ok = ($student_percentage >= $min_percentage);
        
        if($degree_ok && $percentage_ok) {
            $matched_scholarships[] = $scholarship;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Matching Scholarships</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .profile-box { background: #e8f4fc; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .scholarship-card { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; background: white; }
        .match-badge { background: green; color: white; padding: 5px 10px; border-radius: 20px; font-size: 12px; }
        .apply-btn { background: green; color: white; padding: 8px 15px; text-decoration: none; display: inline-block; margin-top: 10px; }
        .no-profile { background: #fff3cd; padding: 20px; border: 1px solid #ffeaa7; border-radius: 5px; }
    </style>
</head>
<body>
    <h2>🎯 Scholarships Matching Your Profile</h2>
    <a href="dashboard.php">← Back to Dashboard</a>
    
    <?php if($student_profile): ?>
        <div class="profile-box">
            <h3>📋 Your Education Profile</h3>
            <p><strong>Highest Degree:</strong> <?php echo $student_profile['degree']; ?></p>
            <p><strong>Percentage:</strong> <?php echo $student_profile['percentage']; ?>%</p>
            <p><strong>Institute:</strong> <?php echo $student_profile['institute']; ?></p>
            <p><a href="view_education.php">View/Edit Profile</a></p>
        </div>
        
        <h3>✅ Matching Scholarships (<?php echo count($matched_scholarships); ?> found)</h3>
        
        <?php if(count($matched_scholarships) > 0): ?>
            <?php foreach($matched_scholarships as $scholarship): ?>
                <div class="scholarship-card">
                    <span class="match-badge">🎯 Perfect Match</span>
                    <h3><?php echo $scholarship['name']; ?></h3>
                    <p><strong>🏛️ Institute:</strong> <?php echo $scholarship['institute']; ?></p>
                    <p><strong>🌍 Country:</strong> <?php echo $scholarship['country']; ?></p>
                    <p><strong>🎓 Required Degree:</strong> <?php echo $scholarship['degree']; ?></p>
                    <p><strong>📊 Minimum Percentage:</strong> <?php echo $scholarship['min_percentage']; ?>% 
                       (Your: <?php echo $student_profile['percentage']; ?>%) ✓</p>
                    <p><strong>💰 Benefits:</strong> <?php echo $scholarship['benefits']; ?></p>
                    <p><strong>⏰ Deadline:</strong> <?php echo date('d M, Y', strtotime($scholarship['deadline'])); ?></p>
                    
                    <a href="../search_scholarship.php?search=1&search_query=<?php echo urlencode($scholarship['name']); ?>" 
                       class="apply-btn">➕ Apply Now</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #666;">
                <h3>😔 No Perfect Matches Found</h3>
                <p>We couldn't find scholarships matching your current profile.</p>
                <p><a href="../search_scholarship.php">🔍 Try Advanced Search</a> or <a href="add_education.php">update your education profile</a>.</p>
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <div class="no-profile">
            <h3>📝 Complete Your Profile First</h3>
            <p>You haven't added your education history yet.</p>
            <p>The matching system needs to know your highest degree and percentage.</p>
            <p><a href="add_education.php" style="background: blue; color: white; padding: 10px 20px; text-decoration: none;">
                ➕ Add Your Education Now
            </a></p>
        </div>
    <?php endif; ?>
</body>
</html>