<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not authenticated
    exit();
}

require_once 'include/api/Database.php';

$page_title = 'Dashboard | Go Offers';

?>

<?php include 'header.php'; ?>

<section class="main">
<div class="container-fluid ownerDashboard">
    <div class="row">
        <div class="col-md-12">
            <div class="tabsNav">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="listings-tab" data-bs-toggle="tab" href="#listings" role="tab" aria-controls="listings" aria-selected="true">My Listings</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="addListing-tab" data-bs-toggle="tab" href="#addListing" role="tab" aria-controls="addListing" aria-selected="false">Add Listing</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content px-2 py-5" id="myTabContent">
                <!-- Listings Tab Content -->
                <div class="tab-pane fade show active" id="listings" role="tabpanel" aria-labelledby="listings-tab">
                    <div id="listingsTable">
                        <!-- Listings will be loaded here via AJAX -->
                    </div>
                </div>

                <!-- Add Listing Tab Content -->
                <div class="tab-pane fade" id="addListing" role="tabpanel" aria-labelledby="addListing-tab">
                    <div class="row">
                        <div class="col-md-7 border-end border-secondary-subtle p-md-5">
                            <form id="addListingForm">
                                <div class="mb-3">
                                    <label for="offer_title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="offer_title" name="offer_title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="offer_type" class="form-label">Offer Type</label>
                                    <select class="form-select" id="offer_type" name="offer_type" required>
                                        <option value="">Select Offer Type</option>
                                        <option value="Percentage Off">Percentage Off</option>
                                        <option value="Flat Off">Flat Off</option>
                                        <option value="Up To">Up To</option>
                                        <option value="Up To %">Up To % Off</option>
                                        <option value="Custom Offer">Custom Offer</option>
                                    </select>
                                </div>
                                <div class="mb-3" id="offer_value_container">
                                    <label for="offer_value" class="form-label">Offer Value</label>
                                    <input type="number" class="form-control" id="offer_value" name="offer_value" required>
                                </div>
                                <div class="mb-3 d-none" id="custom_offer_container">
                                    <label for="custom_offer" class="form-label">Custom Offer</label>
                                    <input type="text" class="form-control" id="custom_offer" name="custom_offer">
                                </div>
                                <div class="mb-3" id="">
                                    <label for="description" class="form-label">Description</label>
                                    <input type="textarea" class="form-control" id="description" name="description" required>
                                </div>
                                <div class="mb-3" id="">
                                    <label for="expiry_date" class="form-label">Expires On</label>
                                    <input type="date" class="form-control" id="expiry_date" name="expiry_date" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Listing</button>
                                <div id="addListingResponse" class="mt-3"></div>
                            </form>
                        </div>
                        <div class="col-md-5">
                            <div class=" h-100 row justify-content-center align-items-center">
                                <div class="col-md-6" id="dashBoard-preview-listing" style="display:none">
                                    <h3 class="text-center fs-5 my-4">Preview</h3>
                                    <div class="card h-100 mb-3">
                                        <div class="card-body">
                                            <div class="offer-badge">
                                                <p class="card-text offer-value" id="preview_offer_value"></p>
                                                <p class="card-text offer-type" id="preview_offer_type"></p>
                                            </div>
                                            <p class="card-title" id="preview_title"></p>
                                            <p class="card-text"><strong>Expiry:</strong> <span id="preview_expiry"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>

<?php include 'footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
     // Load Listings
     function loadListings() {
        fetch('include/api/listings.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    if (data.data.length > 0) {
                        let html = '<table class="table table-striped">';
                        html += '<thead><tr><th>Title</th><th>Store Name</th><th>Offer Type</th><th>Offer Value</th><th>Expiry Date</th><th>Status</th><th>Created At</th><th>Update</th></tr></thead>';
                        html += '<tbody>';
                        data.data.forEach(listing => {
                            html += '<tr>';
                            // html += '<td><img src="' + listing.store_logo + '"></td>';
                            html += '<td>' + listing.title + '</td>';
                            html += '<td>' + listing.store_name + '</td>';
                            html += '<td>' + listing.offer_type + '</td>';
                            html += '<td>' + listing.offer_value + '</td>';
                            html += '<td>' + listing.expiry_date + '</td>';
                            html += '<td>' + listing.status + '</td>';
                            html += '<td>' + listing.created_at + '</td>';
                            html += '<td class="d-flex">';
                            html += '<button class="btn btn-primary btn-sm me-2 edit-listing" data-id="' + listing.id + '">Edit</button>';
                            html += '<button class="btn btn-danger btn-sm delete-listing" data-id="' + listing.id + '">Delete</button>';
                            html += '</td>';
                            html += '</tr>';
                        });
                        html += '</tbody></table>';
                        document.getElementById('listingsTable').innerHTML = html;
                    } else {
                        document.getElementById('listingsTable').innerHTML = '<div class="alert alert-info">No listings found.</div>';
                    }
                } else {
                    document.getElementById('listingsTable').innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                }
            })
            .catch(() => {
                document.getElementById('listingsTable').innerHTML = '<div class="alert alert-danger">An error occurred while loading listings.</div>';
            });
    }

    loadListings(); // Initial load

    // Add Listing Form submission
    document.getElementById('addListingForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission
        
        fetch('include/api/addlisting_process.php', {
            method: 'POST',
            body: new URLSearchParams(new FormData(this)),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('addListingResponse').innerHTML = data;
            loadListings();
            this.reset(); // Reset the form
        })
        .catch(() => {
            document.getElementById('addListingResponse').innerHTML = '<div class="alert alert-danger">An error occurred while adding the listing.</div>';
        });
    });
});

$(document).ready(function() {
    // Attach event listener to the document for elements with class 'edit-listing'
    $(document).on('click', '.edit-listing', function() {
        var listingId = $(this).data('id');
        // Redirect to the edit page or open a modal for editing
        window.location.href = 'edit_listing.php?id=' + listingId;
    });

    // Attach event listener to the document for elements with class 'delete-listing'
    $(document).on('click', '.delete-listing', function() {
        var listingId = $(this).data('id');
        if (confirm('Are you sure you want to delete this listing?')) {
            // Make an AJAX call to delete the listing
            $.ajax({
                url: 'include/api/delete_listing_process.php',
                method: 'POST',
                data: { id: listingId },
                success: function(response) {
                    alert(response);
                    location.reload();
                }
            });
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Grab form elements
    const offerTitle = document.getElementById('offer_title');
    const offerType = document.getElementById('offer_type');
    const offerValue = document.getElementById('offer_value');
    const expiryDate = document.getElementById('expiry_date');

    // Grab preview elements
    const previewOfferValue = document.getElementById('preview_offer_value');
    const previewOfferType = document.getElementById('preview_offer_type');
    const previewTitle = document.getElementById('preview_title');
    const previewExpiry = document.getElementById('preview_expiry');
    const previewCard = document.getElementById('dashBoard-preview-listing');


    // Event listeners for input changes
    if (offerTitle) {
        offerTitle.addEventListener('input', function() {
            previewTitle.textContent = offerTitle.value || 'Your Offer Title';
            previewCard.style.display = "block";

        });
    }

    if (offerType) {
        offerType.addEventListener('change', function() {
            previewOfferType.textContent = offerType.value || 'Offer Type';
            previewCard.style.display = "block";

        });
    }

    if (offerValue) {
        offerValue.addEventListener('input', function() {
            previewOfferValue.textContent = offerValue.value || '0';
            previewCard.style.display = "block";

        });
    }

    if (expiryDate) {
        expiryDate.addEventListener('input', function() {
            previewExpiry.textContent = expiryDate.value || 'YYYY-MM-DD';
            previewCard.style.display = "block";

        });
    }
});
</script>
</body>
</html>