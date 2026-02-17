<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔧 DEBUG: Apply System</h2>";
echo "<div style='background: #ffeb3b; padding: 20px;'>";

// 1. Check if files exist
echo "<h3>1. File Existence:</h3>";
$files = [
    'student/apply.php' => file_exists('student/apply.php'),
    'student/my_applications.php' => file_exists('student/my_applications.php'),
    'db.php' => file_exists('db.php')
];

foreach($files as $file => $exists) {
    echo $file . ": " . ($exists ? "✅ Exists" : "❌ MISSING") . "<br>";
}

// 2. Check database
include 'db.php';
echo "<h3>2. Database Connection:</h3>";
echo "Connected: " . ($conn ? "✅ Yes" : "❌ No") . "<br>";

// 3. Check tables
$tables = ['applications', 'scholarships', 'users', 'education'];
echo "<h3>3. Database Tables:</h3>";
foreach($tables as $table) {
    $check = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    echo $table . ": " . (mysqli_num_rows($check) > 0 ? "✅ Exists" : "❌ Missing") . "<br>";
}

// 4. Test URLs
echo "<h3>4. Test URLs:</h3>";
$base = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
echo "Base URL: $base<br>";
echo "<a href='student/apply.php?scholarship_id=1'>Test apply.php</a><br>";
echo "<a href='student/my_applications.php'>Test my_applications.php</a><br>";

echo "</div>";

// 5. Try to include apply.php directly
echo "<h3>5. Direct Include Test:</h3>";
try {
    include 'student/apply.php';
    echo "✅ apply.php included successfully";
} catch(Exception $e) {
    echo "❌ Error including apply.php: " . $e->getMessage();
}
?>