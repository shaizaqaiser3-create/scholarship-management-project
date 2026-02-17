<?php
session_start();
if(!isset($_SESSION['user_type'])) {
    $_SESSION['user_type'] = 'admin';
}

include '../db.php';

$student_id = $_GET['id'] ?? 0;

// Get student info
$student_sql = "SELECT name FROM users WHERE user_id = $student_id";
$student_result = mysqli_query($conn, $student_sql);
$student_name = mysqli_fetch_assoc($student_result)['name'];

// Get education records
$edu_sql = "SELECT * FROM education WHERE user_id = $student_id ORDER BY year DESC";
$edu_result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Education - Admin</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .back-btn { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
        h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <a href="view_students.php" class="back-btn">← Back to Students</a>
    <a href="student_profile.php?id=<?php echo $student_id; ?>" class="back-btn" style="background: #17a2b8;">👤 Full Profile</a>
    
    <h2>📚 Education History for <?php echo htmlspecialchars($student_name); ?></h2>
    
    <?php if(mysqli_num_rows($edu_result) > 0): ?>
        <table>
            <tr>
                <th>Degree</th>
                <th>Institute</th>
                <th>Marks/Grade</th>
                <th>Percentage</th>
                <th>Year</th>
                <th>Country</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($edu_result)): ?>
            <tr>
                <td><?php echo $row['degree']; ?></td>
                <td><?php echo $row['institute']; ?></td>
                <td>
                    <?php 
                    if($row['marks']) echo $row['marks'];
                    if($row['grade']) echo " (" . $row['grade'] . ")";
                    ?>
                </td>
                <td><?php echo $row['percentage']; ?>%</td>
                <td><?php echo $row['year']; ?></td>
                <td><?php echo $row['country']; ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No education records found.</p>
    <?php endif; ?>
</body>
</html>