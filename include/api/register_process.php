<?php

require_once 'Database.php';
require_once 'hash_password.php';

// Retrieve form data
$store_name = $_POST['store_name'];
$location = $_POST['location'];
$contact_number = $_POST['contact_number'];
$email = $_POST['email'];
$password = $_POST['password'];
$website_url = $_POST['website_url'];
$description = $_POST['description'];
$is_premium = isset($_POST['is_premium']) ? 1 : 0;

// Handle file upload and convert to base64
$store_logo_data = '';
if (isset($_FILES['store_logo']) && $_FILES['store_logo']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['store_logo']['tmp_name'];
    $file_data = file_get_contents($file_tmp);
    $store_logo_data = 'data:' . $_FILES['store_logo']['type'] . ';base64,' . base64_encode($file_data);
}

// Hash the password
$hashed_password = hashPassword($password);

// Database connection
$database = new Database();
$conn = $database->getConnection();

// Insert into users table
$sql_user = "
    INSERT INTO users (email, password)
    VALUES (:email, :password)
";

$stmt_user = $conn->prepare($sql_user);
$stmt_user->bindParam(':email', $email);
$stmt_user->bindParam(':password', $hashed_password);

if (!$stmt_user->execute()) {
    echo "Error inserting into users table.";
    exit;
}

// Get the user_id of the newly inserted user
$user_id = $conn->lastInsertId();

// Insert into shop_owners table
$sql_shop_owner = "
    INSERT INTO shop_owners (user_id, store_name, location, contact_number, store_logo, website_url, description, is_premium)
    VALUES (:user_id, :store_name, :location, :contact_number, :store_logo, :website_url, :description, :is_premium)
";

$stmt_shop_owner = $conn->prepare($sql_shop_owner);
$stmt_shop_owner->bindParam(':user_id', $user_id);
$stmt_shop_owner->bindParam(':store_name', $store_name);
$stmt_shop_owner->bindParam(':location', $location);
$stmt_shop_owner->bindParam(':contact_number', $contact_number);
$stmt_shop_owner->bindParam(':store_logo', $store_logo_data); // Store base64 data
$stmt_shop_owner->bindParam(':website_url', $website_url);
$stmt_shop_owner->bindParam(':description', $description);
$stmt_shop_owner->bindParam(':is_premium', $is_premium, PDO::PARAM_INT);

if ($stmt_shop_owner->execute()) {
    echo "Registration successful!";
} else {
    echo "Error during registration.";
}

?>
