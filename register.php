<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php'); // Redirect to login page if not authenticated
    exit();
}

$page_title = 'Register | Go Offers';

?>
<?php include 'header.php'; ?>

<div class="container mt-5">
    <h2 class="text-center">Shop Owner Registration</h2>
    <form id="registrationForm" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="store_name" class="form-label">Store Name</label>
                    <input type="text" class="form-control" id="store_name" name="store_name" required>
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" required>
                </div>
                <div class="mb-3">
                    <label for="contact_number" class="form-label">Contact Number</label>
                    <input type="text" class="form-control" id="contact_number" name="contact_number" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="store_logo" class="form-label">Store Logo</label>
                    <input type="file" class="form-control" id="store_logo" name="store_logo" accept="image/*">
                </div>
                <div class="mb-3">
                    <label for="website_url" class="form-label">Website URL</label>
                    <input type="text" class="form-control" id="website_url" name="website_url">
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description"></textarea>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_premium" name="is_premium">
                    <label class="form-check-label" for="is_premium">Premium</label>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
                <div id="responseMessage" class="mt-3"></div>
            </div>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    $('#registrationForm').on('submit', function(event) {
        event.preventDefault(); // Prevent default form submission
        
        var formData = new FormData(this);
        
        $.ajax({
            url: 'include/api/register_process.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response === 'Registration successful!') {
                    window.location.href = 'dashboard.php'; // Redirect to listings page
                } else {
                    $('#responseMessage').html('<div class="alert alert-danger">' + response + '</div>');
                }
            },
            error: function() {
                $('#responseMessage').html('<div class="alert alert-danger">An error occurred while processing your request.</div>');
            }
        });
    });
});
</script>
</body>
</html>