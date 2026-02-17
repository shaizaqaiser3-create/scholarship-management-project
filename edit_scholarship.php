<?php
session_start();
if(!isset($_SESSION['user_type'])) {
    $_SESSION['user_type'] = 'admin';
}

include '../db.php';

$scholarship_id = $_GET['id'] ?? 0;
$error = "";
$success = "";

// Fetch scholarship data
$sql = "SELECT * FROM scholarships WHERE scholarship_id = $scholarship_id";
$result = mysqli_query($conn, $sql);
$scholarship = mysqli_fetch_assoc($result);

if(!$scholarship) {
    die("❌ Scholarship not found!");
}

// Update scholarship
if(isset($_POST['update_scholarship'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $institute = mysqli_real_escape_string($conn, $_POST['institute']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $degree = mysqli_real_escape_string($conn, $_POST['degree']);
    $min_percentage = mysqli_real_escape_string($conn, $_POST['min_percentage']);
    $benefits = mysqli_real_escape_string($conn, $_POST['benefits']);
    $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);
    
    $update_sql = "UPDATE scholarships SET 
                  name = '$name',
                  institute = '$institute',
                  country = '$country',
                  degree = '$degree',
                  min_percentage = '$min_percentage',
                  benefits = '$benefits',
                  deadline = '$deadline'
                  WHERE scholarship_id = $scholarship_id";
    
    if(mysqli_query($conn, $update_sql)) {
        $success = "✅ Scholarship updated successfully!";
        // Refresh data
        $result = mysqli_query($conn, $sql);
        $scholarship = mysqli_fetch_assoc($result);
    } else {
        $error = "❌ Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Scholarship - Admin</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #ffc107; padding-bottom: 10px; }
        .form-group { margin: 20px 0; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; }
        input, select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; }
        textarea { height: 100px; resize: vertical; }
        button { padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; margin-right: 10px; }
        .update-btn { background: #28a745; color: white; }
        .cancel-btn { background: #6c757d; color: white; text-decoration: none; padding: 15px 30px; display: inline-block; }
        .delete-btn { background: #dc3545; color: white; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .scholarship-info { background: #e8f4fc; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .info-label { color: #666; font-size: 14px; }
        .info-value { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✏️ Edit Scholarship</h1>
        
        <!-- Scholarship Info -->
        <div class="scholarship-info">
            <div style="display: flex; justify-content: space-between;">
                <div>
                    <span class="info-label">Scholarship ID:</span>
                    <span class="info-value">#<?php echo $scholarship['scholarship_id']; ?></span>
                </div>
                <div>
                    <span class="info-label">Created:</span>
                    <span class="info-value"><?php echo date('d M, Y', strtotime($scholarship['created_at'])); ?></span>
                </div>
                <div>
                    <span class="info-label">Last Updated:</span>
                    <span class="info-value"><?php echo date('d M, Y H:i', strtotime($scholarship['created_at'])); ?></span>
                </div>
            </div>
        </div>
        
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
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($scholarship['name']); ?>" required>
            </div>
            
            <!-- Institute -->
            <div class="form-group">
                <label for="institute">🏛️ Institute/University *</label>
                <input type="text" id="institute" name="institute" value="<?php echo htmlspecialchars($scholarship['institute']); ?>" required>
            </div>
            
            <!-- Country -->
            <div class="form-group">
                <label for="country">🌍 Country *</label>
                <select id="country" name="country" required>
                    <option value="Pakistan" <?php echo $scholarship['country'] == 'Pakistan' ? 'selected' : ''; ?>>Pakistan</option>
                    <option value="USA" <?php echo $scholarship['country'] == 'USA' ? 'selected' : ''; ?>>USA</option>
                    <option value="UK" <?php echo $scholarship['country'] == 'UK' ? 'selected' : ''; ?>>UK</option>
                    <option value="Canada" <?php echo $scholarship['country'] == 'Canada' ? 'selected' : ''; ?>>Canada</option>
                    <option value="Australia" <?php echo $scholarship['country'] == 'Australia' ? 'selected' : ''; ?>>Australia</option>
                    <option value="Germany" <?php echo $scholarship['country'] == 'Germany' ? 'selected' : ''; ?>>Germany</option>
                </select>
            </div>
            
            <!-- Required Degree -->
            <div class="form-group">
                <label for="degree">📚 Required Degree Level *</label>
                <select id="degree" name="degree" required>
                    <option value="Matric" <?php echo $scholarship['degree'] == 'Matric' ? 'selected' : ''; ?>>Matric / O-Levels</option>
                    <option value="Intermediate" <?php echo $scholarship['degree'] == 'Intermediate' ? 'selected' : ''; ?>>Intermediate / A-Levels</option>
                    <option value="Bachelor" <?php echo $scholarship['degree'] == 'Bachelor' ? 'selected' : ''; ?>>Bachelor's Degree</option>
                    <option value="Master" <?php echo $scholarship['degree'] == 'Master' ? 'selected' : ''; ?>>Master's Degree</option>
                    <option value="PhD" <?php echo $scholarship['degree'] == 'PhD' ? 'selected' : ''; ?>>PhD</option>
                </select>
            </div>
            
            <!-- Minimum Percentage -->
            <div class="form-group">
                <label for="min_percentage">📊 Minimum Percentage Required *</label>
                <input type="number" id="min_percentage" name="min_percentage" 
                       value="<?php echo $scholarship['min_percentage']; ?>" 
                       min="0" max="100" step="0.01" required>
            </div>
            
            <!-- Benefits -->
            <div class="form-group">
                <label for="benefits">💰 Benefits & Coverage *</label>
                <textarea id="benefits" name="benefits" required><?php echo htmlspecialchars($scholarship['benefits']); ?></textarea>
            </div>
            
            <!-- Deadline -->
            <div class="form-group">
                <label for="deadline">⏰ Application Deadline *</label>
                <input type="date" id="deadline" name="deadline" 
                       value="<?php echo $scholarship['deadline']; ?>" required>
                <?php
                $deadline_date = new DateTime($scholarship['deadline']);
                $today = new DateTime();
                if($deadline_date < $today) {
                    echo '<p style="color: red;">⚠️ This deadline has passed! Students cannot apply to expired scholarships.</p>';
                }
                ?>
            </div>
            
            <!-- Buttons -->
            <div class="form-group">
                <button type="submit" name="update_scholarship" class="update-btn">💾 Save Changes</button>
                <a href="manage_scholarships.php" class="cancel-btn">↩️ Cancel</a>
                <a href="?delete=<?php echo $scholarship_id; ?>&from=edit" 
                   class="delete-btn" 
                   onclick="return confirm('Are you sure you want to permanently delete this scholarship?\n\n<?php echo addslashes($scholarship['name']); ?>')"
                   style="background: #dc3545; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">
                   🗑️ Delete Scholarship
                </a>
            </div>
        </form>
        
        <div style="margin-top: 30px; background: #fff3cd; padding: 15px; border-radius: 5px;">
            <h3>📝 Edit Notes:</h3>
            <ul>
                <li>Updating scholarship details will affect all future applicants.</li>
                <li>Changing the deadline may affect ongoing applications.</li>
                <li>Consider creating a new scholarship instead of major edits if there are existing applications.</li>
                <li>Deleted scholarships cannot be recovered.</li>
            </ul>
        </div>
        
        <div style="margin-top: 20px;">
            <a href="../search_scholarship.php?search=1&search_query=<?php echo urlencode($scholarship['name']); ?>" 
               target="_blank" style="color: #17a2b8;">
               👁️ Preview how this scholarship appears to students
            </a>
        </div>
    </div>
</body>
</html>