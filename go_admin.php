<?php
session_start();
include 'db.php';

// Check if admin exists in database, create if not
$check_sql = "SELECT * FROM users WHERE email = 'admin@test.com' AND user_type = 'admin'";
$result = mysqli_query($conn, $check_sql);

if(mysqli_num_rows($result) == 0) {
    // Create admin user if doesn't exist
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $insert_sql = "INSERT INTO users (name, email, cnic, password, user_type) 
                   VALUES ('Admin User', 'admin@test.com', '99999-8888888-7', '$hash', 'admin')";
    if(mysqli_query($conn, $insert_sql)) {
        echo "<p style='color:green;'>✅ Admin user created successfully!</p>";
    }
    
    // Re-fetch the admin
    $result = mysqli_query($conn, $check_sql);
}

if(mysqli_num_rows($result) == 1) {
    $admin = mysqli_fetch_assoc($result);
    
    // Set session with REAL database user_id
    $_SESSION['user_id'] = $admin['user_id'];
    $_SESSION['user_name'] = $admin['name'];
    $_SESSION['user_email'] = $admin['email'];
    $_SESSION['user_type'] = 'admin';
    
    echo "<h1>✅ Admin Access Granted!</h1>";
    echo "<p>Session set for user_id: " . $_SESSION['user_id'] . "</p>";
    echo "<p>Redirecting to admin dashboard...</p>";
    
    // Redirect after 2 seconds
    echo "<script>setTimeout(function(){ window.location.href = 'admin_dashboard.php'; }, 1000);</script>";
} else {
    echo "<h1 style='color:red;'>❌ Error: Cannot create/admin user!</h1>";
    echo "<p>Please check database connection.</p>";
    echo "<p><a href='debug_login.php'>Debug Database</a></p>";
}
?>