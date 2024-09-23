<?php
require_once 'Database.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo "User not authenticated.";
    exit();
}

$user_id = $_SESSION['user_id'];
$listing_id = $_POST['listing_id'];

// Verify user_id exists in shop_owners table
try {
    $db = new Database();
    $pdo = $db->getConnection();

    // Check if the shop owner ID exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM listings WHERE id = :id AND shop_owner_id = :user_id");
    $stmt->execute([':id' => $listing_id, ':user_id' => $user_id]);
    if ($stmt->fetchColumn() == 0) {
        echo "Invalid listing ID or you do not have permission to edit this listing.";
        exit();
    }

    $offer_title = $_POST['offer_title'] ?? '';
    $offer_type = $_POST['offer_type'] ?? '';
    $offer_value = $_POST['offer_value'] ?? '';
    $custom_offer = $_POST['custom_offer'] ?? '';
    $expiry_date = $_POST['expiry_date'] ?? '';
    $status = $_POST['status'] ?? 'active';

    // Validate required fields
    if (empty($offer_title) || empty($offer_type)) {
        echo "Title and offer type are required.";
        exit();
    }

    // Optional validation for offer value based on offer type
    if ($offer_type !== 'custom_offer' && empty($offer_value)) {
        echo "Offer value is required.";
        exit();
    }

    // Prepare SQL statement to update listing
    $stmt = $pdo->prepare("
        UPDATE listings SET title = :title, offer_type = :offer_type, offer_value = :offer_value, custom_offer = :custom_offer, expiry_date = :expiry_date, status = :status, updated_at = NOW()
        WHERE id = :id AND shop_owner_id = :user_id
    ");

    $stmt->execute([
        ':title' => $offer_title,
        ':offer_type' => $offer_type,
        ':offer_value' => $offer_value,
        ':custom_offer' => $custom_offer,
        ':expiry_date' => $expiry_date,
        ':status' => $status,
        ':id' => $listing_id,
        ':user_id' => $user_id
    ]);

    echo "Listing updated successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
