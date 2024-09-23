<?php
$page_title = 'Go Offers';
require_once 'include/api/Database.php';

session_start();

try {
    $db = new Database();
    $pdo = $db->getConnection();

    // Fetch all distinct locations and offer types for filter
    $locations = $pdo->query("SELECT DISTINCT location FROM shop_owners")->fetchAll(PDO::FETCH_ASSOC);
    $offer_types = $pdo->query("SELECT DISTINCT offer_type FROM listings")->fetchAll(PDO::FETCH_ASSOC);

    // Fetch the maximum offer value for the range input
    $max_offer_value = $pdo->query("SELECT MAX(offer_value) as max_offer_value FROM listings")->fetchColumn();

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<?php include 'header.php'; ?>

<section class="main">
    <div class="container-fluid offersListings">
        <div class="row">
            <div class="col-md-3">
                <div class="filter-sidebar">
                    <h4>Filter Listings</h4>

                    <div class="form-group mb-3">
                        <label for="filter_location">Location</label>
                        <select id="filter_location" class="form-control select2">
                            <option value="">All Locations</option>
                            <?php foreach ($locations as $location): ?>
                                <option value="<?php echo htmlspecialchars($location['location']); ?>"><?php echo htmlspecialchars($location['location']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="filter_offer_type">Offer Type</label>
                        <select id="filter_offer_type" class="form-control select2">
                            <option value="">All Offer Types</option>
                            <?php foreach ($offer_types as $offer_type): ?>
                                <option value="<?php echo htmlspecialchars($offer_type['offer_type']); ?>"><?php echo htmlspecialchars($offer_type['offer_type']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3 form-control">
                        <label for="filter_offer_value">Offer Value</label>
                        <input type="range" id="filter_offer_value" class="" min="0" max="<?php echo htmlspecialchars($max_offer_value); ?>" step="1" value="0" style="width:100%">
                        <span id="offer_value_display"></span>
                    </div>

                    <div class="form-group mb-3">
                        <label for="filter_expiry_date">Expiry Date</label>
                        <input type="date" id="filter_expiry_date" class="form-control">
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="row topToolBar">
                    <div class="col-md-8">
                        <div id="filter-toolbar"></div>
                    </div>
                    <div class="col-md-4">
                        <select id="sort_by" class="form-select">
                            <option value="">Sort By</option>
                            <option value="low_to_high">Offer Value: Low to High</option>
                            <option value="high_to_low">Offer Value: High to Low</option>
                            <option value="new_to_old">New to Old</option>
                            <option value="old_to_new">Old to New</option>


                        </select>
                    </div>
                </div>
                <div id="listings-container" class="row"></div>
                <nav id="pagination" class="py-5" aria-label="Page navigation">
                    <ul class="pagination">
                        <!-- Pagination buttons will be dynamically generated here -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

<script>
    function loadListings(page = 1, filters = {}) {
        var sort_by = $('#sort_by').val(); // Get selected sort value

        $.ajax({
            url: 'include/api/get_listings.php',
            method: 'GET',
            data: { ...filters, page: page, sort_by: sort_by },
            success: function(response) {
                // Parse the response if it's JSON
                var listing = typeof response === "string" ? JSON.parse(response) : response;

                // Check if the status is 'success'
                if (listing.status === 'success' && Array.isArray(listing.data.listings)) {
                    $('#listings-container').html('');  // Clear previous listings
                    $('#pagination .pagination').html('');  // Clear previous pagination

                    $.each(listing.data.listings, function(index, listing) {
                        // Dynamically create and append the listing HTML using JS
                        var listingHtml = `
                            <div class="col-md-3">
                                <div class="card h-100 mb-3">
                                    <div class="card-body">
                                        <div class="offer-badge">
                                        <p class="card-text offer-value">${listing.offer_value}</p>
                                        <p class="card-text offer-type">${listing.offer_type}</p>
                                        </div>
                                        <p class="card-title">${listing.title} / ${listing.store_name} / ${listing.location}</p>
                                        <p class="card-text"><strong>Expiry:</strong> ${listing.expiry_date}</p>
                                    </div>
                                </div>
                            </div>`;
                        $('#listings-container').append(listingHtml);
                    });

                    // Generate pagination controls
                    generatePaginationControls(page, listing.data.total_pages);
                } else {
                    $('#listings-container').html('<p>No listings found.</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error loading listings:", error);
                $('#listings-container').html('<p>Error loading listings. Please try again.</p>');
            }
        });
    }

    function generatePaginationControls(currentPage, totalPages) {
        const pagination = $('#pagination .pagination');
        pagination.html('');

        // Previous button
        pagination.append(`<li class="page-item${currentPage === 1 ? ' disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a></li>`);

        // First page
        if (totalPages > 1) {
            pagination.append(`<li class="page-item${currentPage === 1 ? ' active' : ''}"><a class="page-link" href="#" data-page="1">1</a></li>`);
        }

        // Ellipsis and page numbers
        if (currentPage > 3) {
            pagination.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
        }

        for (let i = Math.max(2, currentPage - 2); i <= Math.min(totalPages - 1, currentPage + 2); i++) {
            pagination.append(`<li class="page-item${i === currentPage ? ' active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`);
        }

        if (currentPage < totalPages - 2) {
            pagination.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
        }

        // Last page
        if (totalPages > 1) {
            pagination.append(`<li class="page-item${currentPage === totalPages ? ' active' : ''}"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`);
        }

        // Next button
        pagination.append(`<li class="page-item${currentPage === totalPages ? ' disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage + 1}">Next</a></li>`);
    }

    function updateFilterToolbar(filters) {
        $('#filter-toolbar').html('');
        $.each(filters, function(key, value) {
            if (value) {
                let filterLabel = '';
                if (key === 'location') filterLabel = 'Location: ' + value;
                if (key === 'offer_type') filterLabel = 'Offer Type: ' + value;
                if (key === 'offer_value') filterLabel = 'Offer Value: ' + value;
                if (key === 'expiry_date') filterLabel = 'Expiry Date: ' + value;

                $('#filter-toolbar').append(
                    '<span class="badge filter-tag" data-filter="' + key + '"><span>' + filterLabel + ' </span><span class="remove-filter fs-4 ms-4" role="button"><i class="bi bi-x"></i></span></span> '
                );
            }
        });
    }

    $(document).ready(function() {
        var filters = {};

        // Load initial listings when the document is ready
        loadListings();

        // Add change events to trigger filter changes automatically
        $('#filter_location, #filter_offer_type, #filter_offer_value, #filter_expiry_date').on('change', function() {
            filters = {
                location: $('#filter_location').val(),
                offer_type: $('#filter_offer_type').val(),
                offer_value: $('#filter_offer_value').val(),
                expiry_date: $('#filter_expiry_date').val()
            };

            updateFilterToolbar(filters);
            loadListings(1, filters); // Reset to the first page
        });

        // Remove specific filters when clicking on the "×"
        $(document).on('click', '.remove-filter', function() {
            let filterType = $(this).parent().data('filter');
            $('#' + 'filter_' + filterType).val('');  // Clear the filter input
            
            // Reset filter values and range slider
            if (filterType === 'offer_value') {
                $('#filter_offer_value').val(0);  // Reset range input to 0
                $('#offer_value_display').text('');  // Update the displayed value
            }

            filters[filterType] = '';  // Remove the filter from the filters object

            // Update the filter toolbar and reload listings
            updateFilterToolbar(filters);
            loadListings(1, filters); // Reset to the first page
        });

        // Update the displayed offer value percentage
        $('#filter_offer_value').on('input', function() {
            $('#offer_value_display').text($(this).val());
        });

        // Handle pagination click
        $(document).on('click', '#pagination .page-link', function(e) {
            e.preventDefault();
            var page = $(this).data('page');
            loadListings(page, filters);
        });

        $('#sort_by').on('change', function() {
            loadListings(1, filters);  // Reload listings from the first page with sorting applied
        });
    });

    $(document).ready(function() {
        $('.select2').select2(
            {
                placeholder: 'Select an option'

            }
        );
        
    });
</script>

</body>
</html>
