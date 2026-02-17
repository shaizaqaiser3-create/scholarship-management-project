<?php
session_start();
include 'db.php';

if(isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

$error = '';

if(isset($_POST['admin_login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    // Check in database
    $sql = "SELECT * FROM users WHERE email = '$email' AND user_type = 'admin'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        
        // Try password verification
        if(password_verify($password, $admin['password'])) {
            // Set session
            $_SESSION['user_id'] = $admin['user_id'];
            $_SESSION['user_name'] = $admin['name'];
            $_SESSION['user_email'] = $admin['email'];
            $_SESSION['user_type'] = $admin['user_type'];
            
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid password! Try 'admin123' or 'password123'";
        }
    } else {
        $error = "No admin account found with this email!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - Scholarship Portal</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        
        .login-container {
            background: white;
            border-radius: 10px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        h1 { 
            color: #dc3545; 
            text-align: center;
            margin-bottom: 30px;
        }
        
        .error {
            background: #ffe6e6;
            color: #dc3545;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .test-creds {
            background: #e8f4fc;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 14px;
            text-align: center;
        }
        
        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        
        button {
            background: #dc3545;
            color: white;
            padding: 14px;
            border: none;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.3s;
        }
        
        button:hover {
            background: #c82333;
        }
        
        .links {
            text-align: center;
            margin-top: 20px;
        }
        
        .links a {
            color: #007bff;
            text-decoration: none;
            margin: 0 10px;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>👨‍💼 Admin Login</h1>
        
        <?php if($error): ?>
            <div class="error">❌ <?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="test-creds">
            <strong>Test Credentials:</strong><br>
            Email: <strong>admin@test.com</strong><br>
            Password: <strong>admin123</strong>
        </div>
        
        <form method="POST">
            <input type="email" name="email" placeholder="Admin Email" required value="admin@test.com">
            <input type="password" name="password" placeholder="Password" required value="admin123">
            <button type="submit" name="admin_login">🔐 Login as Admin</button>
        </form>
        
        <div class="links">
            <a href="login.php">Student Login</a> | 
            <a href="register.php">Register</a> |
            <a href="go_admin.php">Quick Access</a>
        </div>
        
        <div style="text-align: center; margin-top: 20px; color: #666; font-size: 12px;">
            If login fails, use <a href="go_admin.php">Quick Access</a> or check <a href="debug_login.php">debug page</a>
        </div>
    </div>
</body>
</html>