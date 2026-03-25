<?php
// Database Setup Script - Run once to create database and tables

$servername = "localhost";
$username = "root";
$password = "";

// Create connection without database
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS bizconnect_db";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists.\n";
} else {
    echo "Error creating database: " . $conn->error . "\n";
}

// Select database
$conn->select_db("bizconnect_db");

// Create messages table
$createTable = "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('unread', 'read') DEFAULT 'unread',
    INDEX idx_created_at (created_at),
    INDEX idx_status (status)
)";

if ($conn->query($createTable) === TRUE) {
    echo "Table 'contact_messages' created successfully or already exists.\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}

// Create admin users table
$createAdminTable = "CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($createAdminTable) === TRUE) {
    echo "Table 'admin_users' created successfully or already exists.\n";
} else {
    echo "Error creating admin table: " . $conn->error . "\n";
}

// Check if default admin exists, if not create one
$checkAdmin = "SELECT * FROM admin_users WHERE username = 'admin'";
$result = $conn->query($checkAdmin);

if ($result->num_rows == 0) {
    // Default admin credentials: username=admin, password=admin123
    $adminPassword = password_hash("admin123", PASSWORD_DEFAULT);
    $insertAdmin = "INSERT INTO admin_users (username, password, email) VALUES ('admin', '$adminPassword', 'admin@bizconnect.com')";
    
    if ($conn->query($insertAdmin) === TRUE) {
        echo "Default admin user created successfully.\n";
        echo "Username: admin\n";
        echo "Password: admin123\n";
    } else {
        echo "Error creating admin user: " . $conn->error . "\n";
    }
}

echo "\nDatabase setup completed successfully!\n";
echo "Visit: admin.php to access the admin panel.\n";

$conn->close();
?>
