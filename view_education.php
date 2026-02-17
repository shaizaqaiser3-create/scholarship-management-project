<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

$user_id = $_SESSION['user_id'];

// Delete record if requested
if(isset($_GET['delete'])) {
    $edu_id = $_GET['delete'];
    $sql = "DELETE FROM education WHERE edu_id = '$edu_id' AND user_id = '$user_id'";
    mysqli_query($conn, $sql);
    header("Location: view_education.php?msg=deleted");
    exit();
}

// Get all education records for this student
$sql = "SELECT * FROM education WHERE user_id = '$user_id' ORDER BY 
        CASE degree 
            WHEN 'PhD' THEN 1
            WHEN 'Master' THEN 2
            WHEN 'Bachelor' THEN 3
            WHEN 'Intermediate' THEN 4
            WHEN 'Matric' THEN 5
            ELSE 6
        END, year DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Education</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f2f2f2; }
        .success { color: green; }
        .empty { text-align: center; padding: 40px; border: 2px dashed #ccc; margin: 20px; }
    </style>
</head>
<body>
    <h2>📚 My Education History</h2>
    <a href="dashboard.php">← Back to Dashboard</a>
    <a href="add_education.php" style="margin-left: 20px; background: blue; color: white; padding: 8px 15px;">➕ Add New</a>
    
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="success">✅ Record deleted successfully!</div>
    <?php endif; ?>
    
    <?php if(mysqli_num_rows($result) > 0): ?>
        <table>
            <tr>
                <th>Degree</th>
                <th>Institute</th>
                <th>Marks/Grade</th>
                <th>Percentage</th>
                <th>Year</th>
                <th>Country</th>
                <th>Actions</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><strong><?php echo $row['degree']; ?></strong></td>
                <td><?php echo $row['institute']; ?></td>
                <td>
                    <?php 
                    if(!empty($row['marks'])) echo $row['marks'];
                    if(!empty($row['grade'])) echo " (" . $row['grade'] . ")";
                    if(empty($row['marks']) && empty($row['grade'])) echo "-";
                    ?>
                </td>
                <td><?php echo $row['percentage']; ?>%</td>
                <td><?php echo $row['year']; ?></td>
                <td><?php echo $row['country']; ?></td>
                <td>
                    <a href="edit_education.php?id=<?php echo $row['edu_id']; ?>">✏️ Edit</a> | 
                    <a href="?delete=<?php echo $row['edu_id']; ?>" 
                       onclick="return confirm('Are you sure you want to delete this record?')"
                       style="color: red;">🗑️ Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <p><strong>Total Records:</strong> <?php echo mysqli_num_rows($result); ?></p>
    <?php else: ?>
        <div class="empty">
            <h3>📝 No Education Records Found</h3>
            <p>You haven't added any education history yet.</p>
            <a href="add_education.php" style="background: green; color: white; padding: 10px 20px; text-decoration: none;">
                ➕ Add Your First Education Record
            </a>
        </div>
    <?php endif; ?>
</body>
</html>