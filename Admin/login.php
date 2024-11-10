<?php
// Include config.php for the database connection
include('../config.php'); // Adjust the path as needed

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
    $stmt->execute(['username' => $username, 'password' => md5($password)]); // Use a secure hashing method in production
    $user = $stmt->fetch();

    if ($user) {
        // Login successful
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php"); // Redirect to the admin dashboard
        exit;
    } else {
        // Login failed
        echo "Invalid username or password.";
    }
}
?>
