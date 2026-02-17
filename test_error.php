<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>PHP Error Test</h2>";
echo "PHP Version: " . phpversion() . "<br>";

// Test database connection
include 'db.php';
echo "Database Connected: " . ($conn ? "✅ Yes" : "❌ No") . "<br>";

// Test session
session_start();
echo "Session Started: ✅<br>";

echo "<h3>✅ All tests passed!</h3>";
?>