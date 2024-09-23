<?php
require_once 'Database.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo "User not authenticated.";
    exit();
}

$user_id = $_SESSION['user_id'];
$listing_id = $_POST['id'] ?? '';

if (empty($listing_id)) {
    echo "No listing ID provided.";
    exit();
}

try {
    $db = new Database();
    $pdo = $db->getConnection();

    // Verify that the listing belongs to the logged-in user
    $stmt = $pdo->prepare("SELECT * FROM listings WHERE id = :listing_id AND shop_owner_id = :shop_owner_id");
    $stmt->execute([':listing_id' => $listing_id, ':shop_owner_id' => $user_id]);
    $listing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$listing) {
        echo "Listing not found or you do not have permission to delete this listing.";
        exit();
    }

    // Delete the listing
    $stmt = $pdo->prepare("DELETE FROM listings WHERE id = :listing_id");
    $stmt->execute([':listing_id' => $listing_id]);

    echo "Listing deleted successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
