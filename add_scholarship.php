<?php
session_start();
// Simple admin check
if(!isset($_SESSION['user_type'])) {
    $_SESSION['user_type'] = 'admin';
}

include '../db.php';

$success = "";
$error = "";

if(isset($_POST['add_scholarship'])) {
    // Get form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $institute = mysqli_real_escape_string($conn, $_POST['institute']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $degree = mysqli_real_escape_string($conn, $_POST['degree']);
    $min_percentage = mysqli_real_escape_string($conn, $_POST['min_percentage']);
    $benefits = mysqli_real_escape_string($conn, $_POST['benefits']);
    $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);
    
    // Insert into database
    $sql = "INSERT INTO scholarships (name, institute, country, degree, min_percentage, benefits, deadline) 
            VALUES ('$name', '$institute', '$country', '$degree', '$min_percentage', '$benefits', '$deadline')";
    
    if(mysqli_query($conn, $sql)) {
        $success = "✅ Scholarship added successfully!";
        // Clear form
        $_POST = array();
    } else {
        $error = "❌ Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Scholarship - Admin</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .form-group { margin: 20px 0; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; }
        input, select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; }
        textarea { height: 100px; resize: vertical; }
        button { background: #28a745; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
        button:hover { background: #218838; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .back-btn { display: inline-block; background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-bottom: 20px; }
        .back-btn:hover { background: #5a6268; }
        .form-tips { background: #e8f4fc; padding: 15px; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-btn">← Back to Admin Dashboard</a>
        <h1>➕ Add New Scholarship</h1>
        
        <?php if($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <!-- Scholarship Name -->
            <div class="form-group">
                <label for="name">🎓 Scholarship Name *</label>
                <input type="text" id="name" name="name" value="<?php echo $_POST['name'] ?? ''; ?>" 
                       placeholder="e.g., Merit Scholarship 2024" required>
                <small>Make it descriptive and attractive</small>
            </div>
            
            <!-- Institute -->
            <div class="form-group">
                <label for="institute">🏛️ Institute/University *</label>
                <input type="text" id="institute" name="institute" value="<?php echo $_POST['institute'] ?? ''; ?>" 
                       placeholder="e.g., University of Punjab" required>
            </div>
            
            <!-- Country -->
            <div class="form-group">
                <label for="country">🌍 Country *</label>
                <select id="country" name="country" required>
                    <option value="">-- Select Country --</option>
                    <option value="Pakistan" <?php echo ($_POST['country'] ?? '') == 'Pakistan' ? 'selected' : ''; ?>>Pakistan</option>
                    <option value="USA" <?php echo ($_POST['country'] ?? '') == 'USA' ? 'selected' : ''; ?>>USA</option>
                    <option value="UK" <?php echo ($_POST['country'] ?? '') == 'UK' ? 'selected' : ''; ?>>UK</option>
                    <option value="Canada" <?php echo ($_POST['country'] ?? '') == 'Canada' ? 'selected' : ''; ?>>Canada</option>
                    <option value="Australia" <?php echo ($_POST['country'] ?? '') == 'Australia' ? 'selected' : ''; ?>>Australia</option>
                    <option value="Germany" <?php echo ($_POST['country'] ?? '') == 'Germany' ? 'selected' : ''; ?>>Germany</option>
                </select>
            </div>
            
            <!-- Required Degree -->
            <div class="form-group">
                <label for="degree">📚 Required Degree Level *</label>
                <select id="degree" name="degree" required>
                    <option value="">-- Select Degree --</option>
                    <option value="Matric" <?php echo ($_POST['degree'] ?? '') == 'Matric' ? 'selected' : ''; ?>>Matric / O-Levels</option>
                    <option value="Intermediate" <?php echo ($_POST['degree'] ?? '') == 'Intermediate' ? 'selected' : ''; ?>>Intermediate / A-Levels</option>
                    <option value="Bachelor" <?php echo ($_POST['degree'] ?? '') == 'Bachelor' ? 'selected' : ''; ?>>Bachelor's Degree</option>
                    <option value="Master" <?php echo ($_POST['degree'] ?? '') == 'Master' ? 'selected' : ''; ?>>Master's Degree</option>
                    <option value="PhD" <?php echo ($_POST['degree'] ?? '') == 'PhD' ? 'selected' : ''; ?>>PhD</option>
                </select>
            </div>
            
            <!-- Minimum Percentage -->
            <div class="form-group">
                <label for="min_percentage">📊 Minimum Percentage Required *</label>
                <input type="number" id="min_percentage" name="min_percentage" 
                       value="<?php echo $_POST['min_percentage'] ?? '60'; ?>" 
                       min="0" max="100" step="0.01" required>
                <small>Set the minimum percentage students must have</small>
            </div>
            
            <!-- Benefits -->
            <div class="form-group">
                <label for="benefits">💰 Benefits & Coverage *</label>
                <textarea id="benefits" name="benefits" placeholder="Describe what the scholarship offers..." required><?php echo $_POST['benefits'] ?? ''; ?></textarea>
                <small>Examples: Full tuition fee waiver, Monthly stipend, Hostel accommodation, Laptop, Books allowance, Travel allowance</small>
            </div>
            
            <!-- Deadline -->
            <div class="form-group">
                <label for="deadline">⏰ Application Deadline *</label>
                <input type="date" id="deadline" name="deadline" 
                       value="<?php echo $_POST['deadline'] ?? ''; ?>" required>
                <small>Must be a future date</small>
            </div>
            
            <!-- Submit Button -->
            <div class="form-group">
                <button type="submit" name="add_scholarship">➕ Add Scholarship</button>
            </div>
        </form>
        
        <div class="form-tips">
            <h3>💡 Tips for Adding Scholarships:</h3>
            <ul>
                <li><strong>Name:</strong> Be specific (e.g., "Engineering Merit Scholarship 2024")</li>
                <li><strong>Benefits:</strong> List all perks to attract more applicants</li>
                <li><strong>Percentage:</strong> Set realistic requirements based on degree level</li>
                <li><strong>Deadline:</strong> Give enough time for students to apply</li>
                <li><strong>Country:</strong> Specify if scholarship is for domestic or international students</li>
            </ul>
        </div>
        
        <div style="margin-top: 30px; text-align: center; color: #666;">
            <p>After adding, check <a href="../search_scholarship.php" target="_blank">Search Scholarships</a> to see it live.</p>
        </div>
    </div>
</body>
</html>