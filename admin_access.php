<?php
session_start();

// Set admin session manually (FIXED VERSION)
$_SESSION['user_id'] = 1;  // Use actual user_id from database
$_SESSION['user_name'] = 'Admin User';
$_SESSION['user_email'] = 'admin@test.com';
$_SESSION['user_type'] = 'admin';

echo "<h1>✅ Admin Access Granted!</h1>";
echo "<p>Redirecting to admin dashboard...</p>";
echo "<script>setTimeout(function(){ window.location.href = 'admin_dashboard.php'; }, 1000);</script>";
?>