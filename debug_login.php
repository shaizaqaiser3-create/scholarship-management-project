<?php
include 'db.php';

echo "<h2>🔍 Login System Debug</h2>";
echo "<div style='background: #ffeb3b; padding: 20px;'>";

// 1. Check database connection
echo "<h3>1. Database Connection:</h3>";
echo "Connected: " . ($conn ? "✅ Yes" : "❌ No") . "<br>";

// 2. Check if admin@test.com exists
$sql = "SELECT * FROM users WHERE email = 'admin@test.com'";
$result = mysqli_query($conn, $sql);
echo "<h3>2. Check admin@test.com in database:</h3>";
echo "SQL: <code>$sql</code><br>";
echo "Rows found: " . mysqli_num_rows($result) . "<br>";

if(mysqli_num_rows($result) > 0) {
    $admin = mysqli_fetch_assoc($result);
    echo "<pre>";
    print_r($admin);
    echo "</pre>";
    
    // Test password verification
    echo "<h3>3. Test Password Verification:</h3>";
    $test_passwords = ['password123', 'admin123', '123456', 'password'];
    
    foreach($test_passwords as $test_pw) {
        $matches = password_verify($test_pw, $admin['password']);
        echo "password_verify('$test_pw', hash): " . ($matches ? "✅ MATCHES" : "❌ NO MATCH") . "<br>";
    }
    
    // Show hash details
    echo "<h3>4. Password Hash Analysis:</h3>";
    $hash = $admin['password'];
    echo "Hash: $hash<br>";
    echo "Length: " . strlen($hash) . " chars<br>";
    echo "Hash type: " . password_get_info($hash)['algoName'] . "<br>";
    
} else {
    echo "<p style='color: red;'>❌ admin@test.com not found in database!</p>";
}

echo "</div>";

// 3. Show all admin users
echo "<h3>5. All Admin Users in Database:</h3>";
$admins_sql = "SELECT user_id, name, email, user_type, LEFT(password, 20) as pass_start FROM users WHERE user_type = 'admin'";
$admins_result = mysqli_query($conn, $admins_sql);

if(mysqli_num_rows($admins_result) > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Type</th><th>Password Start</th></tr>";
    while($row = mysqli_fetch_assoc($admins_result)) {
        echo "<tr>";
        echo "<td>" . $row['user_id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['user_type'] . "</td>";
        echo "<td>" . $row['pass_start'] . "...</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No admin users found.</p>";
}
?>

<h3>🛠️ Quick Fix Buttons:</h3>
<form method="POST" style="margin: 10px;">
    <button name="fix1">Fix 1: Create Admin with password123</button>
</form>

<?php
if(isset($_POST['fix1'])) {
    // Delete existing
    mysqli_query($conn, "DELETE FROM users WHERE email = 'admin@test.com'");
    
    // Create new with KNOWN hash
    $hash = password_hash('password123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (name, email, cnic, password, user_type) 
            VALUES ('Admin User', 'admin@test.com', '99999-8888888-7', '$hash', 'admin')";
    
    if(mysqli_query($conn, $sql)) {
        echo "<p style='color: green;'>✅ Admin created with password: password123</p>";
        echo "<p>Hash generated: $hash</p>";
    }
}
?>