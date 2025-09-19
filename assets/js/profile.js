// File: assets/js/profile.js
$(document).ready(function () {
  // Edit profile functionality
  $(".editable").on("click", function () {
    const field = $(this).data("field");
    const currentValue = $(this).text().trim().replace("✏️", "").trim();

    $("#editField").val(field);
    $("#editValue").val(currentValue);
    $("#editLabel").text(field === "full_name" ? "Full Name:" : "Age:");

    if (field === "age") {
      $("#editValue")
        .attr("type", "number")
        .attr("min", "13")
        .attr("max", "120");
    } else {
      $("#editValue").attr("type", "text").removeAttr("min").removeAttr("max");
    }

    $("#editModal").show();
  });

  // Close modal
  $(".close, .modal").on("click", function (e) {
    if (e.target === this) {
      $("#editModal").hide();
    }
  });

  // Save profile changes
  $("#editProfileForm").on("submit", function (e) {
    e.preventDefault();

    const formData =
      $(this).serialize() +
      "&csrf_token=" +
      encodeURIComponent(window.CSRF_TOKEN);
    const submitBtn = $(this).find('button[type="submit"]');

    submitBtn.prop("disabled", true).text("Saving...");

    $.ajax({
      url: "api/profile.php",
      type: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          const field = $("#editField").val();
          const newValue = $("#editValue").val();

          // Update the display
          $(`[data-field="${field}"]`).html(
            newValue + ' <i class="edit-icon">✏️</i>'
          );

          $("#editModal").hide();
          showMessage("Profile updated successfully!", "success");
        } else {
          showMessage(response.message, "error");
        }
      },
      error: function () {
        showMessage("An error occurred. Please try again.", "error");
      },
      complete: function () {
        submitBtn.prop("disabled", false).text("Save");
      },
    });
  });

  // Change profile picture
  $("#newProfilePicture").on("change", function () {
    const file = this.files[0];
    if (file) {
      const formData = new FormData();
      formData.append("profile_picture", file);

      $.ajax({
        url: "api/profile.php",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        headers: { "X-CSRF-Token": window.CSRF_TOKEN },
        dataType: "json",
        success: function (response) {
          if (response.success) {
            $("#profileImage").attr(
              "src",
              "uploads/profiles/" + response.filename
            );
            showMessage(response.message, "success");
          } else {
            showMessage(response.message, "error");
          }
        },
        error: function () {
          showMessage("An error occurred while uploading the image.", "error");
        },
      });
    }
  });

  // Add new post
  $("#addPostForm").on("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = $(this).find('button[type="submit"]');

    submitBtn
      .prop("disabled", true)
      .html('<span class="spinner"></span>Posting...');

    $.ajax({
      url: "api/posts.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showMessage(response.message, "success");
          $("#addPostForm")[0].reset();

          // Reload posts
          setTimeout(function () {
            location.reload();
          }, 1500);
        } else {
          showMessage(response.message, "error");
        }
      },
      error: function () {
        showMessage("An error occurred while adding the post.", "error");
      },
      complete: function () {
        submitBtn.prop("disabled", false).text("Post");
      },
    });
  });

  // Like/Dislike posts
  $(".btn-like, .btn-dislike").on("click", function () {
    const postId = $(this).data("post-id");
    const reactionType = $(this).hasClass("btn-like") ? "like" : "dislike";
    const button = $(this);

    $.ajax({
      url: "api/reactions.php",
      type: "POST",
      data: {
        post_id: postId,
        reaction_type: reactionType,
        csrf_token: window.CSRF_TOKEN,
      },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          // Update reaction counts
          button
            .closest(".post-actions")
            .find(".like-count")
            .text(response.reactions.like);
          button
            .closest(".post-actions")
            .find(".dislike-count")
            .text(response.reactions.dislike);
        } else {
          showMessage(response.message, "error");
        }
      },
      error: function () {
        showMessage("An error occurred. Please try again.", "error");
      },
    });
  });

  // Delete posts
  $(".btn-delete").on("click", function () {
    if (!confirm("Are you sure you want to delete this post?")) {
      return;
    }

    const postId = $(this).data("post-id");
    const postElement = $(this).closest(".post");

    $.ajax({
      url: "api/posts.php",
      type: "DELETE",
      data: { post_id: postId },
      headers: { "X-CSRF-Token": window.CSRF_TOKEN },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          postElement.fadeOut(300, function () {
            $(this).remove();
          });
          showMessage(response.message, "success");
        } else {
          showMessage(response.message, "error");
        }
      },
      error: function () {
        showMessage("An error occurred while deleting the post.", "error");
      },
    });
  });

  // Show message function
  function showMessage(message, type) {
    $(".message").remove();

    const messageDiv = $(`<div class="message ${type}"></div>`).text(message);
    $(".profile-content").prepend(messageDiv);

    setTimeout(function () {
      messageDiv.fadeOut();
    }, 5000);
  }
});
