// /js/login.js

$(document).ready(function() {
  $('#login-form').on('submit', function(e) {
    e.preventDefault();

    // Clear old errors
    $('#login-error').text('');
    
    var email = $('#login-email').val().trim();
    var password = $('#login-password').val().trim();

    if (!email || !password) {
      $('#login-error').text('Please enter both email and password.');
      return;
    }

    $.ajax({
      url: '../php/login.php',      // Adjust path if needed
      type: 'POST',
      dataType: 'json',
      data: {
        email: email,
        password: password
      },
      success: function(response) {
        if (response.success && response.sessionKey) {
          // Store the session key in browser localStorage
          localStorage.setItem('sessionKey', response.sessionKey);

          // Redirect to profile page
          window.location.href = 'profile.html';
        } else {
          $('#login-error').text(response.error || 'Invalid login.');
        }
      },
      error: function() {
        $('#login-error').text('Server error. Please try again later.');
      }
    });
  });
});
