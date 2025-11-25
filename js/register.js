// /js/register.js

$(document).ready(function() {
  $('#register-form').on('submit', function(e) {
    e.preventDefault();

    // Clear previous errors
    $('#register-error').text('');

    // Gather user input
    var username = $('#register-username').val().trim();
    var email = $('#register-email').val().trim();
    var password = $('#register-password').val().trim();
    var age = $('#register-age').val().trim();
    var dob = $('#register-dob').val().trim();
    var contact = $('#register-contact').val().trim();

    // Simple validation
    if (!username || !email || !password || !age || !dob || !contact) {
      $('#register-error').text('Please fill all fields.');
      return;
    }

    $.ajax({
      url: '../php/register.php',      // Backend endpoint
      type: 'POST',
      dataType: 'json',
      data: {
        username: username,
        email: email,
        password: password,
        age: age,
        dob: dob,
        contact: contact
      },
      success: function(response) {
        if (response.success) {
          // Registration successful, prompt or redirect to login
          window.location.href = 'login.html';
        } else {
          $('#register-error').text(response.error || 'Registration failed.');
        }
      },
      error: function() {
        $('#register-error').text('Server error. Please try again later.');
      }
    });
  });
});
