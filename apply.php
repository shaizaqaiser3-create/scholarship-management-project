<?php
// Get scholarship ID from URL
$scholarship_id = $_GET['scholarship_id'] ?? '1';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Apply for Scholarship</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .scholarship-card { background: #e8f4fc; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .form-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input, textarea { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        textarea { height: 100px; }
        button { background: #28a745; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
        .back-btn { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-bottom: 20px; }
        .eligibility-badge { padding: 10px 15px; border-radius: 20px; font-weight: bold; }
        .eligible { background: #d4edda; color: #155724; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <a href="../search_scholarship.php" class="back-btn">← Back to Scholarships</a>
        
        <h1>📝 Apply for Scholarship</h1>
        
        <!-- Scholarship Info -->
        <div class="scholarship-card">
            <h2>🎓 Merit Scholarship 2024</h2>
            <p><strong>🏛️ Institute:</strong> University of Punjab</p>
            <p><strong>🌍 Country:</strong> Pakistan</p>
            <p><strong>📚 Required Degree:</strong> Bachelor's</p>
            <p><strong>📊 Minimum Percentage:</strong> 75%</p>
            <p><strong>⏰ Deadline:</strong> 30 June 2024</p>
            <p><strong>💰 Benefits:</strong> Full tuition fee waiver + Monthly stipend</p>
        </div>
        
        <!-- Eligibility Check -->
        <div style="margin: 20px 0;">
            <div class="eligibility-badge eligible">
                🎉 You are eligible to apply!
            </div>
            <p>Your profile meets all requirements for this scholarship.</p>
        </div>
        
        <!-- Application Form -->
        <div class="form-card">
            <h3>📋 Application Form</h3>
            
            <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;">
                <h4>Your Information:</h4>
                <p><strong>Name:</strong> Test Student</p>
                <p><strong>Email:</strong> test@example.com</p>
                <p><strong>Highest Degree:</strong> Bachelor's (85%)</p>
            </div>
            
            <form method="POST" action="">
                <div style="margin: 20px 0;">
                    <label><strong>Additional Information (Optional):</strong></label>
                    <textarea name="additional_info" placeholder="Tell us why you deserve this scholarship, any achievements, extracurricular activities, etc."></textarea>
                    <p><small>This can strengthen your application.</small></p>
                </div>
                
                <div style="margin: 20px 0;">
                    <label>
                        <input type="checkbox" required> I certify that all information provided is accurate
                    </label><br>
                    <label>
                        <input type="checkbox" required> I agree to the terms and conditions
                    </label>
                </div>
                
                <button type="submit" name="submit_application">🚀 Submit Application</button>
            </form>
            
            <?php
            if(isset($_POST['submit_application'])) {
                echo '<div class="success">';
                echo '✅ Application submitted successfully!<br>';
                echo 'Your application ID: #' . rand(1000, 9999) . '<br>';
                echo '<a href="my_applications.php">View your applications →</a>';
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- Next Steps -->
        <div style="margin-top: 30px; background: #fff3cd; padding: 20px; border-radius: 10px;">
            <h3>📅 What Happens Next?</h3>
            <ol>
                <li>Application received</li>
                <li>Review by committee (2-3 weeks)</li>
                <li>Decision notification via email</li>
                <li>Document submission (if approved)</li>
                <li>Scholarship disbursement</li>
            </ol>
        </div>
    </div>
</body>
</html>