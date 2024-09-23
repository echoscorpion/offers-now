<?php
require_once 'Database.php';
require_once 'config.php';

$max_limit_non_premium = MAX_LISTING;

session_start();

if (!isset($_SESSION['user_id'])) {
    echo "User not authenticated.";
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $db = new Database();
    $pdo = $db->getConnection();

    // Check if the shop owner ID exists
    $stmt = $pdo->prepare("SELECT is_premium FROM shop_owners WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $shop_owner = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$shop_owner) {
        echo "<div class='alert alert-danger'>Invalid shop owner ID.</div>";
        exit();
    }

    if($shop_owner['is_premium'] != 1) {
        // Check current listings count if user is not premium
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM listings WHERE shop_owner_id = :id");
        $stmt->execute([':id' => $user_id]);
        $listing_count = $stmt->fetchColumn();

        if ($listing_count >= $max_limit_non_premium) {
            echo "<div class='alert alert-danger'>Non-premium users can only have 7 listings.</div>";
            exit();
        }
    }


    $offer_title = $_POST['offer_title'] ?? '';
    $offer_type = $_POST['offer_type'] ?? '';
    $offer_value = $_POST['offer_value'] ?? '';
    $custom_offer = $_POST['custom_offer'] ?? '';
    $description = $_POST['description'] ?? '';
    $expiry_date = $_POST['expiry_date'] ?? '';

    // Validate required fields
    if (empty($offer_title) || empty($offer_type)) {
        echo "<div class='alert alert-warning'>Title and offer type are required.</div>";
        exit();
    }

    // Optional validation for offer value based on offer type
    if ($offer_type !== 'custom_offer' && empty($offer_value)) {
        echo "<div class='alert alert-warning'>Offer value is required.</div>";
        exit();
    }

    // Prepare SQL statement to insert listing
    $stmt = $pdo->prepare("
        INSERT INTO listings (shop_owner_id, title, description, offer_type, offer_value, custom_offer, expiry_date, status, created_at, updated_at)
        VALUES (:shop_owner_id, :title, :description, :offer_type, :offer_value, :custom_offer, :expiry_date, :status, NOW(), NOW())
    ");

    $stmt->execute([
        ':shop_owner_id' => $user_id,
        ':title' => $offer_title,
        ':description' => $description,
        ':offer_type' => $offer_type,
        ':offer_value' => $offer_value,
        ':custom_offer' => $custom_offer,
        ':expiry_date' => $expiry_date,
        ':status' => 'active'
    ]);

    echo "<div class='alert alert-success'>Listing added successfully.</div>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
