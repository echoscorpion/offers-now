<?php
require_once 'include/api/Database.php';
require_once 'include/api/hash_password.php';

$page_title = 'Edit Listings | Go Offers';

session_start();

if (!isset($_SESSION['user_id'])) {
header('Location: login.php');
exit();
}

if (!isset($_GET['id'])) {
echo "No listing ID provided.";
exit();
}

$listing_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

try {
$db = new Database();
$pdo = $db->getConnection();

// Fetch listing details
$stmt = $pdo->prepare("SELECT * FROM listings WHERE id = :id AND shop_owner_id = :user_id");
$stmt->execute([':id' => $listing_id, ':user_id' => $user_id]);
$listing = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$listing) {
    echo "Listing not found or you do not have permission to edit this listing.";
    exit();
}

} catch (PDOException $e) {
echo "Error: " . $e->getMessage();
exit();
}
?>

<?php include 'header.php'; ?>

<div class="container mt-5">
    <h2>Edit Listing</h2>
    <form id="edit-listing-form">
        <input type="hidden" name="listing_id" value="<?php echo htmlspecialchars($listing['id']); ?>">

        <div class="form-group mb-3">
            <label for="offer_title">Title</label>
            <input type="text" class="form-control" id="offer_title" name="offer_title" value="<?php echo htmlspecialchars($listing['title']); ?>" required>
        </div>

        <div class="form-group mb-3">
            <label for="offer_type">Offer Type</label>
            <select class="form-control" id="offer_type" name="offer_type" required>
                <option value="Percentage Off" <?php echo ($listing['offer_type'] == 'Percentage Off') ? 'selected' : ''; ?>>Percent Off</option>
                <option value="Flat Off" <?php echo ($listing['offer_type'] == 'Flat Off') ? 'selected' : ''; ?>>Flat Off</option>
                <option value="Up To" <?php echo ($listing['offer_type'] == 'Up To') ? 'selected' : ''; ?>>Up to Off</option>
                <option value="Up To %" <?php echo ($listing['offer_type'] == 'Up To %') ? 'selected' : ''; ?>>Up to % Off</option>
                <option value="Custom Offer" <?php echo ($listing['offer_type'] == 'Custom Offer') ? 'selected' : ''; ?>>Custom Offer</option>
            </select>
        </div>

        <div class="form-group mb-3" id="offer_value_group">
            <label for="offer_value">Offer Value</label>
            <input type="text" class="form-control" id="offer_value" name="offer_value" value="<?php echo htmlspecialchars($listing['offer_value']); ?>">
        </div>

        <div class="form-group mb-3" id="custom_offer_group" style="display: <?php echo ($listing['offer_type'] == 'custom_offer') ? 'block' : 'none'; ?>">
            <label for="custom_offer">Custom Offer</label>
            <input type="text" class="form-control" id="custom_offer" name="custom_offer" value="<?php echo htmlspecialchars($listing['custom_offer']); ?>">
        </div>

        <div class="form-group mb-3">
            <label for="expiry_date">Expiry Date</label>
            <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="<?php echo htmlspecialchars($listing['expiry_date']); ?>">
        </div>

        <button type="submit" class="btn btn-primary">Update Listing</button>
    </form>
</div>

<?php include 'footer.php'; ?>
<script>
    // Show/hide custom offer field based on offer type
    $('#offer_type').change(function() {
        if ($(this).val() === 'custom_offer') {
            $('#custom_offer_group').show();
            $('#offer_value_group').hide();
        } else {
            $('#custom_offer_group').hide();
            $('#offer_value_group').show();
        }
    });

    // AJAX form submission
    $('#edit-listing-form').on('submit', function(event) {
        event.preventDefault();
        $.ajax({
            url: 'include/api/editlisting_process.php',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                alert(response);
                window.location.href = 'dashboard.php';
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + error);
            }
        });
    });
</script>

</body>
</html>