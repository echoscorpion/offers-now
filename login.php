<?php 
$page_title = 'Owner Login | Go Offers';

?>
<?php include 'header.php'; ?>

<div class="container mt-5">
    <h2 class="text-center">User Login</h2>
    <form id="loginForm">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
                <div id="responseMessage" class="mt-3"></div>
            </div>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
<script>
$(document).ready(function() {
    $('#loginForm').on('submit', function(event) {
        event.preventDefault(); // Prevent default form submission
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: 'include/api/login_process.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response === 'Login successful!') {
                    window.location.href = 'dashboard.php'; // Redirect to the user's dashboard or listings page
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