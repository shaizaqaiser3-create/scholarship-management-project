<?php
// VERY SIMPLE - NO DATABASE, NO SESSIONS
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Applications</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .app-card { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .status-pending { color: orange; }
        .status-approved { color: green; }
        .status-rejected { color: red; }
    </style>
</head>
<body>
    <h1>📋 My Scholarship Applications</h1>
    
    <a href="dashboard.php">← Back to Dashboard</a>
    <a href="../search_scholarship.php" style="margin-left: 15px; background: #28a745; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px;">🔍 Find More Scholarships</a>
    
    <h3>Your Applications (3)</h3>
    
    <!-- Application 1 -->
    <div class="app-card">
        <h3>🎓 Merit Scholarship 2024</h3>
        <p><strong>🏛️ Institute:</strong> University of Punjab</p>
        <p><strong>📅 Applied:</strong> 15 Jan 2024</p>
        <p><strong>📊 Your Percentage:</strong> 85%</p>
        <p><strong>📈 Status:</strong> <span class="status-pending">⏳ Pending Review</span></p>
        <p><small>Application ID: #1001</small></p>
    </div>
    
    <!-- Application 2 -->
    <div class="app-card">
        <h3>💻 Women in Tech Scholarship</h3>
        <p><strong>🏛️ Institute:</strong> FAST University</p>
        <p><strong>📅 Applied:</strong> 10 Jan 2024</p>
        <p><strong>📊 Your Percentage:</strong> 78%</p>
        <p><strong>📈 Status:</strong> <span class="status-approved">✅ Approved</span></p>
        <p><em>Congratulations! Your application has been approved.</em></p>
        <p><small>Application ID: #1002</small></p>
    </div>
    
    <!-- Application 3 -->
    <div class="app-card">
        <h3>⚽ Sports Scholarship</h3>
        <p><strong>🏛️ Institute:</strong> University of Karachi</p>
        <p><strong>📅 Applied:</strong> 5 Jan 2024</p>
        <p><strong>📊 Your Percentage:</strong> 70%</p>
        <p><strong>📈 Status:</strong> <span class="status-rejected">❌ Rejected</span></p>
        <p><em>Minimum percentage requirement not met (Required: 75%)</em></p>
        <p><small>Application ID: #1003</small></p>
    </div>
    
    <div style="margin-top: 30px; background: #e8f4fc; padding: 15px; border-radius: 5px;">
        <h3>📊 Application Statistics</h3>
        <p><strong>Total Applications:</strong> 3</p>
        <p><strong>Approved:</strong> 1 (33%)</p>
        <p><strong>Pending:</strong> 1 (33%)</p>
        <p><strong>Rejected:</strong> 1 (33%)</p>
    </div>
    
    <div style="margin-top: 20px;">
        <h3>💡 Next Steps:</h3>
        <ol>
            <li><strong>Pending:</strong> Wait for review (2-3 weeks)</li>
            <li><strong>Approved:</strong> Check email for documents submission</li>
            <li><strong>Rejected:</strong> Apply for other matching scholarships</li>
        </ol>
    </div>
</body>
</html>