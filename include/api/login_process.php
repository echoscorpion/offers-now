<?php

require_once 'Database.php';
require_once 'hash_password.php';

// Retrieve form data
$email = $_POST['email'];
$password = $_POST['password'];

// Database connection
$database = new Database();
$conn = $database->getConnection();

// Fetch the stored hash for the user
$sql = "SELECT id, password FROM users WHERE email = :email"; // Changed 'user_id' to 'id'
$stmt = $conn->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $storedHash = $user['password'];
    if (verifyPassword($password, $storedHash)) {
        // Start session and set session variables
        session_start();
        $_SESSION['user_id'] = $user['id']; // Changed 'user_id' to 'id'
        $_SESSION['email'] = $email;
        
        echo "Login successful!";
    } else {
        echo "Invalid email or password.";
    }
} else {
    echo "Invalid email or password.";
}

?>
