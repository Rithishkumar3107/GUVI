// /js/profile.js

$(document).ready(function() {
  // Fetch user profile information on load
  var sessionKey = localStorage.getItem('sessionKey');
  if (!sessionKey) {
    // No session, redirect to login
    window.location.href = 'login.html';
    return;
  }

  $.ajax({
    url: '../php/profile.php',
    type: 'POST',
    dataType: 'json',
    data: { sessionKey: sessionKey, action: 'fetch' },
    success: function(response) {
      if (response.success) {
        $('#profile-username').val(response.data.username);
        $('#profile-email').val(response.data.email);
        $('#profile-age').val(response.data.age);
        $('#profile-dob').val(response.data.dob);
        $('#profile-contact').val(response.data.contact);
        // Add other fields as needed
      } else {
        $('#profile-error').text(response.error || 'Could not load profile.');
      }
    },
    error: function() {
      $('#profile-error').text('Server error. Please try again later.');
    }
  });

  // Handle update button click
  $('#profile-form').on('submit', function(e) {
    e.preventDefault();
    var updatedData = {
      sessionKey: sessionKey,
      action: 'update',
      username: $('#profile-username').val().trim(),
      email: $('#profile-email').val().trim(),
      age: $('#profile-age').val().trim(),
      dob: $('#profile-dob').val().trim(),
      contact: $('#profile-contact').val().trim()
      // Add other fields as needed
    };

    $.ajax({
      url: '../php/profile.php',
      type: 'POST',
      dataType: 'json',
      data: updatedData,
      success: function(response) {
        if (response.success) {
          $('#profile-success').text('Profile updated successfully.');
        } else {
          $('#profile-error').text(response.error || 'Update failed.');
        }
      },
      error: function() {
        $('#profile-error').text('Server error. Please try again later.');
      }
    });
  });

  // Logout functionality
  $('#logout-btn').on('click', function() {
    localStorage.removeItem('sessionKey');
    window.location.href = 'login.html';
  });
});
