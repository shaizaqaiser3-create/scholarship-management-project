<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

$message = '';

if(isset($_POST['register'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $cnic = mysqli_real_escape_string($conn, $_POST['cnic']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate passwords match
    if($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        // Check if email already exists
        $check = "SELECT user_id FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $check);
        
        if(mysqli_num_rows($result) > 0){
            $message = "Email already registered!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $query = "INSERT INTO users (name, email, cnic, password, user_type) 
                      VALUES ('$name', '$email', '$cnic', '$hashed_password', 'student')";
            
            if(mysqli_query($conn, $query)){
                $message = "✅ Registration Successful! <a href='login.php'>Login here</a>";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Scholarship Portal</title>
    <style>
        body { font-family: Arial; padding: 50px; max-width: 500px; margin: 0 auto; }
        h2 { color: #2c3e50; }
        form { background: #f8f9fa; padding: 20px; border-radius: 10px; }
        input { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #28a745; color: white; padding: 12px; border: none; width: 100%; border-radius: 5px; cursor: pointer; }
        .message { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h2>Register for Scholarship Portal</h2>
    
    <?php if($message): ?>
        <div class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="text" name="cnic" placeholder="CNIC (e.g., 12345-6789012-3)" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button name="register">Register</button>
    </form>
    
    <p style="text-align: center; margin-top: 20px;">
        Already have an account? <a href="login.php">Login here</a>
    </p>
    
    <div style="text-align: center; margin-top: 30px; color: #666;">
        <p>Test Accounts:</p>
        <p>Admin: admin@test.com / admin123</p>
        <p>Student: student@test.com / password123</p>
    </div>
</body>
</html>