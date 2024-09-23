<?php

session_start();

require_once 'Database.php';
require_once 'utils.php'; // Include the utility functions

if (!isset($_SESSION['user_id'])) {
    error('You must be logged in to view your listings.', 403);
}

$user_id = $_SESSION['user_id'];
$database = new Database();
$conn = $database->getConnection();

// SQL query to join listings with shop_owners to get location and store_name
$sql = "
    SELECT 
        l.id, 
        l.title, 
        l.offer_type, 
        l.offer_value, 
        l.custom_offer, 
        l.expiry_date, 
        l.status, 
        l.created_at, 
        l.updated_at, 
        s.location, 
        s.store_name
    FROM 
        listings l
    INNER JOIN 
        shop_owners s 
    ON 
        l.shop_owner_id = s.id
    WHERE 
        l.shop_owner_id = :user_id
";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convert dates to d-m-Y format for consistency
foreach ($listings as &$listing) {
    $listing['created_at'] = (new DateTime($listing['created_at']))->format('d-m-Y');
    $listing['expiry_date'] = (new DateTime($listing['expiry_date']))->format('d-m-Y');
}

// Return JSON response
if (count($listings) > 0) {
    success($listings, 'Listings retrieved successfully.');
} else {
    success([], 'No listings found.');
}
?>
