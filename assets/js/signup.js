// File: assets/js/signup.js
$(document).ready(function () {
  // Client-side validation patterns
  const patterns = {
    email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    password: /^(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/,
    name: /^[a-zA-Z\s]{2,}$/,
  };

  // Real-time validation
  $("#full_name").on("blur", function () {
    const value = $(this).val().trim();
    const errorSpan = $(this).siblings(".error-message");

    if (!patterns.name.test(value)) {
      errorSpan.text(
        "Name must be at least 2 characters and contain only letters"
      );
      $(this).addClass("error");
    } else {
      errorSpan.text("");
      $(this).removeClass("error");
    }
  });

  $("#email").on("blur", function () {
    const value = $(this).val().trim();
    const errorSpan = $(this).siblings(".error-message");

    if (!patterns.email.test(value)) {
      errorSpan.text("Please enter a valid email address");
      $(this).addClass("error");
    } else {
      errorSpan.text("");
      $(this).removeClass("error");
    }
  });

  $("#password").on("input", function () {
    const value = $(this).val();
    const errorSpan = $(this).siblings(".error-message");

    if (!patterns.password.test(value)) {
      errorSpan.text(
        "Password must be at least 8 characters with 1 special character"
      );
      $(this).addClass("error");
    } else {
      errorSpan.text("");
      $(this).removeClass("error");
    }
  });

  $("#age").on("blur", function () {
    const value = parseInt($(this).val());
    const errorSpan = $(this).siblings(".error-message");

    if (isNaN(value) || value < 13 || value > 120) {
      errorSpan.text("Age must be between 13 and 120");
      $(this).addClass("error");
    } else {
      errorSpan.text("");
      $(this).removeClass("error");
    }
  });

  $("#profile_picture").on("change", function () {
    const file = this.files[0];
    const errorSpan = $(this).siblings(".error-message");

    if (file) {
      // Check file type
      const allowedTypes = ["image/jpeg", "image/jpg", "image/png"];
      if (!allowedTypes.includes(file.type)) {
        errorSpan.text("Only JPG and PNG files are allowed");
        $(this).addClass("error");
        return;
      }

      // Check file size (5MB)
      if (file.size > 5 * 1024 * 1024) {
        errorSpan.text("File size must be less than 5MB");
        $(this).addClass("error");
        return;
      }

      errorSpan.text("");
      $(this).removeClass("error");
    }
  });

  // Form submission
  $("#signupForm").on("submit", function (e) {
    e.preventDefault();

    // Clear previous messages
    $(".error-message").text("");
    $(".form-group input").removeClass("error");
    $(".form-message").remove();

    const formData = new FormData(this);
    const submitBtn = $(this).find('button[type="submit"]');

    // Disable submit button and show loading
    submitBtn
      .prop("disabled", true)
      .html('<span class="spinner"></span>Creating Account...');

    $.ajax({
      url: "signup.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          $('<div class="form-message success"></div>')
            .text(response.message)
            .insertBefore("#signupForm");

          setTimeout(function () {
            window.location.href = "login.php";
          }, 2000);
        } else {
          $('<div class="form-message error"></div>')
            .text(response.message)
            .insertBefore("#signupForm");
        }
      },
      error: function () {
        $('<div class="form-message error"></div>')
          .text("An error occurred. Please try again.")
          .insertBefore("#signupForm");
      },
      complete: function () {
        submitBtn.prop("disabled", false).text("Sign Up");
      },
    });
  });
});
