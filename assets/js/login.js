$(document).ready(function () {
  // Form submission
  $("#loginForm").on("submit", function (e) {
    e.preventDefault();

    // Clear previous messages
    $(".form-message").remove();
    $(".error-message").text("");

    const formData = $(this).serialize();
    const submitBtn = $(this).find('button[type="submit"]');

    // Disable submit button and show loading
    submitBtn
      .prop("disabled", true)
      .html('<span class="spinner"></span>Logging in...');

    $.ajax({
      url: "login.php",
      type: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          $('<div class="form-message success"></div>')
            .text(response.message)
            .insertBefore("#loginForm");

          setTimeout(function () {
            window.location.href = response.redirect;
          }, 1500);
        } else {
          $('<div class="form-message error"></div>')
            .text(response.message)
            .insertBefore("#loginForm");
        }
      },
      error: function () {
        $('<div class="form-message error"></div>')
          .text("An error occurred. Please try again.")
          .insertBefore("#loginForm");
      },
      complete: function () {
        submitBtn.prop("disabled", false).text("Login");
      },
    });
  });

  // Real-time validation
  $("#email").on("blur", function () {
    const email = $(this).val().trim();
    const errorSpan = $(this).siblings(".error-message");

    if (!email) {
      errorSpan.text("Email is required");
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      errorSpan.text("Please enter a valid email address");
    } else {
      errorSpan.text("");
    }
  });

  $("#password").on("blur", function () {
    const password = $(this).val();
    const errorSpan = $(this).siblings(".error-message");

    if (!password) {
      errorSpan.text("Password is required");
    } else {
      errorSpan.text("");
    }
  });
});
