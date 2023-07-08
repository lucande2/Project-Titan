$(document).ready(function() {
    $('#reset-btn').click(function(e) {
        e.preventDefault();

        var userId = $(this).data('userId');

        $.ajax({
            url: '../../engine/processes/analysis_preset.php',
            type: 'post',
            data: {
                'reset': true,
                'userId': userId,
            },
            success: function(response) {
                location.reload();  // reload the page to see the updated values
            },
            error: function(xhr, status, error) {
                console.error('Error resetting values:', error);
            }
        });
    });
});
