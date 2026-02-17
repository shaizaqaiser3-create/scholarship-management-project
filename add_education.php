<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'student') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

$success = "";
$error = "";

if(isset($_POST['add_education'])) {
    $user_id = $_SESSION['user_id'];
    $degree = mysqli_real_escape_string($conn, $_POST['degree']);
    $institute = mysqli_real_escape_string($conn, $_POST['institute']);
    $marks = isset($_POST['marks']) ? mysqli_real_escape_string($conn, $_POST['marks']) : "";
    $grade = isset($_POST['grade']) ? mysqli_real_escape_string($conn, $_POST['grade']) : "";
    $percentage = mysqli_real_escape_string($conn, $_POST['percentage']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    
    // FIXED SQL WITH ALL COLUMNS:
    $sql = "INSERT INTO education (user_id, degree, institute, marks, grade, percentage, year, country) 
            VALUES ('$user_id', '$degree', '$institute', '$marks', '$grade', '$percentage', '$year', '$country')";
    
    if(mysqli_query($conn, $sql)) {
        $success = "✅ Education record added successfully!";
    } else {
        $error = "❌ Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Education</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        input, select { padding: 8px; margin: 5px; width: 300px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>➕ Add Education History</h2>
    <a href="dashboard.php">← Back to Dashboard</a>
    
    <?php if($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" style="margin-top: 20px;">
        <strong>Degree Level:</strong><br>
        <select name="degree" required>
            <option value="">-- Select Degree --</option>
            <option value="Matric">Matric / O-Levels</option>
            <option value="Intermediate">Intermediate / A-Levels</option>
            <option value="Bachelor">Bachelor's Degree</option>
            <option value="Master">Master's Degree</option>
            <option value="PhD">PhD</option>
        </select><br><br>
        
        <strong>Institute Name:</strong><br>
        <input type="text" name="institute" placeholder="e.g., ABC University" required><br><br>
        
        <strong>Marks (Optional):</strong><br>
        <input type="text" name="marks" placeholder="e.g., 850/1100"><br><br>
        
        <strong>Grade (Optional):</strong><br>
        <input type="text" name="grade" placeholder="e.g., A+, B, C"><br><br>
        
        <strong>Percentage:</strong><br>
        <input type="number" name="percentage" min="0" max="100" placeholder="e.g., 85" required><br><br>
        
        <strong>Year of Completion:</strong><br>
        <input type="number" name="year" min="1900" max="2026" value="2024" required><br><br>
        
        <strong>Country:</strong><br>
        <input type="text" name="country" value="Pakistan" required><br><br>
        
        <button name="add_education" style="background: blue; color: white; padding: 10px 20px;">➕ Add Record</button>
    </form>
</body>
</html>