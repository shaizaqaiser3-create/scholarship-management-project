<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

$user_id = $_SESSION['user_id'];
$edu_id = $_GET['id'] ?? 0;

// Fetch current record
$sql = "SELECT * FROM education WHERE edu_id = '$edu_id' AND user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
$education = mysqli_fetch_assoc($result);

if(!$education) {
    echo "Record not found or access denied!";
    exit();
}

$success = "";
$error = "";

// Update record
if(isset($_POST['update_education'])) {
    $degree = mysqli_real_escape_string($conn, $_POST['degree']);
    $institute = mysqli_real_escape_string($conn, $_POST['institute']);
    $marks = mysqli_real_escape_string($conn, $_POST['marks']);
    $grade = mysqli_real_escape_string($conn, $_POST['grade']);
    $percentage = mysqli_real_escape_string($conn, $_POST['percentage']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    
    $update_sql = "UPDATE education SET 
                   degree = '$degree',
                   institute = '$institute',
                   marks = '$marks',
                   grade = '$grade',
                   percentage = '$percentage',
                   year = '$year',
                   country = '$country'
                   WHERE edu_id = '$edu_id' AND user_id = '$user_id'";
    
    if(mysqli_query($conn, $update_sql)) {
        $success = "✅ Education record updated successfully!";
        // Refresh data
        $result = mysqli_query($conn, $sql);
        $education = mysqli_fetch_assoc($result);
    } else {
        $error = "❌ Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Education</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        input, select { padding: 8px; margin: 5px; width: 300px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>✏️ Edit Education Record</h2>
    <a href="view_education.php">← Back to Education List</a>
    
    <?php if($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" style="margin-top: 20px;">
        <strong>Degree Level:</strong><br>
        <select name="degree" required>
            <option value="Matric" <?php echo ($education['degree'] == 'Matric') ? 'selected' : ''; ?>>Matric</option>
            <option value="Intermediate" <?php echo ($education['degree'] == 'Intermediate') ? 'selected' : ''; ?>>Intermediate</option>
            <option value="Bachelor" <?php echo ($education['degree'] == 'Bachelor') ? 'selected' : ''; ?>>Bachelor</option>
            <option value="Master" <?php echo ($education['degree'] == 'Master') ? 'selected' : ''; ?>>Master</option>
            <option value="PhD" <?php echo ($education['degree'] == 'PhD') ? 'selected' : ''; ?>>PhD</option>
        </select><br><br>
        
        <strong>Institute Name:</strong><br>
        <input type="text" name="institute" value="<?php echo $education['institute']; ?>" required><br><br>
        
        <strong>Marks:</strong><br>
        <input type="text" name="marks" value="<?php echo $education['marks']; ?>" placeholder="e.g., 850/1100"><br><br>
        
        <strong>Grade:</strong><br>
        <input type="text" name="grade" value="<?php echo $education['grade']; ?>" placeholder="e.g., A+, B, C"><br><br>
        
        <strong>Percentage:</strong><br>
        <input type="number" name="percentage" value="<?php echo $education['percentage']; ?>" min="0" max="100" required><br><br>
        
        <strong>Year of Completion:</strong><br>
        <input type="number" name="year" value="<?php echo $education['year']; ?>" min="1900" max="2026" required><br><br>
        
        <strong>Country:</strong><br>
        <input type="text" name="country" value="<?php echo $education['country']; ?>" required><br><br>
        
        <button name="update_education" style="background: green; color: white; padding: 10px 20px;">💾 Save Changes</button>
        <a href="view_education.php" style="padding: 10px 20px; background: gray; color: white; text-decoration: none;">Cancel</a>
    </form>
</body>
</html>