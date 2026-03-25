<?php
session_start();

// If already logged in, redirect to admin panel
if (isset($_SESSION['admin_id'])) {
    header('Location: admin.php');
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config.php';
    
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'Username and password are required';
    } else {
        // Prepare and execute query
        $sql = "SELECT id, username, password FROM admin_users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    $_SESSION['admin_id'] = $row['id'];
                    $_SESSION['admin_username'] = $row['username'];
                    header('Location: admin.php');
                    exit;
                } else {
                    $error_message = 'Invalid credentials';
                }
            } else {
                $error_message = 'Invalid credentials';
            }
            $stmt->close();
        }
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - BIZCONNECT</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(rgba(14,26,43,0.88), rgba(14,26,43,0.88)),
                        url("https://images.unsplash.com/photo-1497215728101-856f4ea42174");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: white;
        }
        
        .login-container {
            background: rgba(17, 24, 39, 0.95);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
        }
        
        .login-container h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }
        
        .login-container p {
            text-align: center;
            color: #9ca3af;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #374151;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 14px;
        }
        
        .form-group input::placeholder {
            color: #9ca3af;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #60a5fa;
            background: rgba(255, 255, 255, 0.15);
        }
        
        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
        }
        
        .error-message {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #ef4444;
        }
        
        .info-box {
            background: rgba(59, 130, 246, 0.2);
            padding: 12px;
            border-radius: 6px;
            font-size: 12px;
            color: #93c5fd;
            margin-top: 20px;
            border-left: 4px solid #3b82f6;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>BIZCONNECT</h2>
        <p>Admin Panel Login</p>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <button type="submit" class="login-btn">Login</button>
        </form>
        
        <div class="info-box">
            <strong>Default Credentials:</strong><br>
            Username: admin<br>
            Password: admin123
        </div>
    </div>
</body>
</html>
