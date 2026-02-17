<?php
session_start();
include 'db.php';

// If already logged in, redirect to dashboard
if(isset($_SESSION['user_id'])) {
    if($_SESSION['user_type'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: student/dashboard.php");
    }
    exit();
}

$error = "";

if(isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    // Check user
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verify password
        if(password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_type'] = $user['user_type'];
            
            // Redirect
            if($_SESSION['user_type'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: student/dashboard.php");
            }
            exit();
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "No account found with this email!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Scholarship Portal</title>
</head>
<body>
    <h2>Login</h2>
    
    <?php if($error): ?>
        <div style="color: red;"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        Email: <input type="email" name="email" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <button name="login">Login</button>
    </form>
    
    <p>Don't have an account? <a href="register.php">Register here</a></p>
    
    <!-- DEMO LOGIN (for class project) -->
    <div style="margin-top: 20px; border-top: 1px solid #ccc; padding-top: 20px;">
        <h3>Demo Login:</h3>
        <p>Use these credentials for testing:</p>
        <p><strong>Student:</strong> test@example.com / password123</p>
        <p><strong>Admin:</strong> (register admin account first)</p>
    </div>
</body>
</html>