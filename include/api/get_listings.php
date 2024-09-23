<?php
require_once 'Database.php';
require_once 'utils.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();

    // Default values for pagination
    $limit = 20;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Prepare the base SQL query
    $sql = "SELECT listings.*, shop_owners.location, shop_owners.store_name 
            FROM listings 
            INNER JOIN shop_owners ON listings.shop_owner_id = shop_owners.id 
            WHERE 1=1";

    // Apply filters if provided
    if (!empty($_GET['location'])) {
        $sql .= " AND shop_owners.location = :location";
    }
    if (!empty($_GET['offer_type'])) {
        $sql .= " AND listings.offer_type = :offer_type";
    }
    if (!empty($_GET['offer_value'])) {
        $sql .= " AND listings.offer_value >= :offer_value";
    }
    if (!empty($_GET['expiry_date'])) {
        $sql .= " AND listings.expiry_date <= :expiry_date";
    }

    $sql .= " AND listings.status = 'active'";

    $sql_order = " ORDER BY listings.created_at DESC";


    // Apply sorting if provided
    if (!empty($_GET['sort_by'])) {
        if ($_GET['sort_by'] == 'low_to_high') {
            $sql_order = " ORDER BY listings.offer_value ASC";
        } elseif ($_GET['sort_by'] == 'high_to_low') {
            $sql_order = " ORDER BY listings.offer_value DESC";
        } elseif ($_GET['sort_by'] == 'new_to_old') {
            $sql_order = " ORDER BY listings.created_at DESC";
        } elseif ($_GET['sort_by'] == 'old_to_new') {
            $sql_order = " ORDER BY listings.created_at ASC";
        }
    }

    $sql.= $sql_order;

    // Get total count of listings for pagination
    $countSql = "SELECT COUNT(*) FROM (" . $sql . ") as count_query";
    $stmt = $pdo->prepare($countSql);

    // Bind parameters for filters
    if (!empty($_GET['location'])) {
        $stmt->bindParam(':location', $_GET['location']);
    }
    if (!empty($_GET['offer_type'])) {
        $stmt->bindParam(':offer_type', $_GET['offer_type']);
    }
    if (!empty($_GET['offer_value'])) {
        $stmt->bindParam(':offer_value', $_GET['offer_value']);
    }
    if (!empty($_GET['expiry_date'])) {
        $stmt->bindParam(':expiry_date', $_GET['expiry_date']);
    }

    $stmt->execute();
    $totalCount = $stmt->fetchColumn();
    $totalPages = ceil($totalCount / $limit);

    // Add pagination to the query
    $sql .= " LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($sql);

    // Bind parameters for filters
    if (!empty($_GET['location'])) {
        $stmt->bindParam(':location', $_GET['location']);
    }
    if (!empty($_GET['offer_type'])) {
        $stmt->bindParam(':offer_type', $_GET['offer_type']);
    }
    if (!empty($_GET['offer_value'])) {
        $stmt->bindParam(':offer_value', $_GET['offer_value']);
    }
    if (!empty($_GET['expiry_date'])) {
        $stmt->bindParam(':expiry_date', $_GET['expiry_date']);
    }


    // Bind pagination parameters
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

    $stmt->execute();
    $listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if listings are available
    if ($listings) {
        success(['listings' => $listings, 'total_pages' => $totalPages], 'Listings retrieved successfully');
    } else {
        success(['listings' => [], 'total_pages' => 0], 'No listings found');
    }

} catch (PDOException $e) {
    error("Database error: " . $e->getMessage(), 500);
}
