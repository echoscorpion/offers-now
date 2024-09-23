// logout script
$(document).ready(function() {
    $('#logoutBtn').on('click', function(event) {
        event.preventDefault(); // Prevent default button action
        
        $.ajax({
            url: 'include/api/logout_process.php',
            type: 'POST',
            success: function(response) {
                if (response === 'Logout successful!') {
                    window.location.href = 'login.php'; // Redirect to login page after logout
                } else {
                    alert('Logout failed. Please try again.');
                }
            },
            error: function() {
                alert('An error occurred while processing your request.');
            }
        });
    });
});