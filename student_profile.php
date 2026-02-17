<?php
session_start();
if(!isset($_SESSION['user_type'])) {
    $_SESSION['user_type'] = 'admin';
}

include '../db.php';

$student_id = $_GET['id'] ?? 0;

// Get student info
$student_sql = "SELECT * FROM users WHERE user_id = $student_id AND user_type = 'student'";
$student_result = mysqli_query($conn, $student_sql);
$student = mysqli_fetch_assoc($student_result);

if(!$student) {
    die("❌ Student not found!");
}

// Get education records
$edu_sql = "SELECT * FROM education WHERE user_id = $student_id ORDER BY 
           CASE degree 
               WHEN 'PhD' THEN 1
               WHEN 'Master' THEN 2
               WHEN 'Bachelor' THEN 3
               WHEN 'Intermediate' THEN 4
               WHEN 'Matric' THEN 5
               ELSE 6
           END";
$edu_result = mysqli_query($conn, $edu_sql);
$edu_count = mysqli_num_rows($edu_result);

// Get matching scholarships count
$match_sql = "SELECT COUNT(*) as match_count FROM scholarships";
$match_result = mysqli_query($conn, $match_sql);
$match_data = mysqli_fetch_assoc($match_result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Profile - Admin</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; }
        .back-btn { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-bottom: 20px; display: inline-block; }
        .profile-card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .profile-header { display: flex; align-items: center; gap: 20px; margin-bottom: 20px; }
        .avatar { width: 80px; height: 80px; background: #007bff; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 36px; font-weight: bold; }
        .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .info-item { padding: 10px 0; border-bottom: 1px solid #eee; }
        .info-label { color: #666; font-size: 14px; }
        .info-value { font-weight: bold; }
        .section-title { color: #333; border-left: 4px solid #007bff; padding-left: 10px; margin: 30px 0 15px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .no-data { text-align: center; padding: 40px; color: #666; background: #f8f9fa; border-radius: 5px; }
        .action-buttons { display: flex; gap: 10px; margin-top: 20px; }
        .action-btn { padding: 10px 20px; text-decoration: none; border-radius: 5px; }
        .edit-btn { background: #ffc107; color: #000; }
        .email-btn { background: #6f42c1; color: white; }
        .print-btn { background: #17a2b8; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <a href="view_students.php" class="back-btn">← Back to Students</a>
        
        <div class="profile-card">
            <div class="profile-header">
                <div class="avatar">
                    <?php echo strtoupper(substr($student['name'], 0, 1)); ?>
                </div>
                <div>
                    <h1 style="margin: 0;"><?php echo htmlspecialchars($student['name']); ?></h1>
                    <p style="color: #666; margin: 5px 0;">Student ID: #<?php echo $student['user_id']; ?></p>
                    <p style="color: #666;">Member since: <?php echo date('d M, Y', strtotime($student['created_at'])); ?></p>
                </div>
            </div>
            
            <div class="action-buttons">
                <a href="mailto:<?php echo $student['email']; ?>" class="action-btn email-btn">📧 Email Student</a>
                <a href="#" onclick="window.print()" class="action-btn print-btn">🖨️ Print Profile</a>
            </div>
        </div>
        
        <div class="profile-card">
            <h2 class="section-title">📋 Personal Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Full Name</div>
                    <div class="info-value"><?php echo htmlspecialchars($student['name']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email Address</div>
                    <div class="info-value"><?php echo htmlspecialchars($student['email']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">CNIC Number</div>
                    <div class="info-value"><?php echo htmlspecialchars($student['cnic']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Account Type</div>
                    <div class="info-value"><?php echo ucfirst($student['user_type']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Registration Date</div>
                    <div class="info-value"><?php echo date('d M, Y H:i', strtotime($student['created_at'])); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Profile Status</div>
                    <div class="info-value">
                        <?php 
                        if($edu_count > 0) {
                            echo "<span style='color: green;'>✓ Profile Complete</span>";
                        } else {
                            echo "<span style='color: orange;'>⚠️ Education Missing</span>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="profile-card">
            <h2 class="section-title">📚 Education History (<?php echo $edu_count; ?> records)</h2>
            
            <?php if($edu_count > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Degree</th>
                            <th>Institute</th>
                            <th>Marks/Grade</th>
                            <th>Percentage</th>
                            <th>Year</th>
                            <th>Country</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($edu = mysqli_fetch_assoc($edu_result)): ?>
                        <tr>
                            <td><strong><?php echo $edu['degree']; ?></strong></td>
                            <td><?php echo $edu['institute']; ?></td>
                            <td>
                                <?php 
                                if($edu['marks']) echo $edu['marks'];
                                if($edu['grade']) echo " (" . $edu['grade'] . ")";
                                ?>
                            </td>
                            <td><?php echo $edu['percentage']; ?>%</td>
                            <td><?php echo $edu['year']; ?></td>
                            <td><?php echo $edu['country']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <p>This student hasn't added any education history yet.</p>
                    <p>They need to complete their profile to get scholarship matches.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="profile-card">
            <h2 class="section-title">🎯 Scholarship Matching</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Available Scholarships</div>
                    <div class="info-value"><?php echo $match_data['match_count']; ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Education Records</div>
                    <div class="info-value"><?php echo $edu_count; ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Profile Completion</div>
                    <div class="info-value">
                        <?php 
                        $completion = $edu_count > 0 ? "100%" : "50%";
                        echo $completion;
                        ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Matching Scholarships</div>
                    <div class="info-value">
                        <a href="../student/matching.php?user_id=<?php echo $student_id; ?>" target="_blank">
                            View Matching Scholarships
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 30px; color: #666;">
            <p>Last updated: <?php echo date('d M, Y H:i'); ?></p>
        </div>
    </div>
</body>
</html>