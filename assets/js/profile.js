// File: assets/js/profile.js
$(document).ready(function () {
  // Edit profile functionality
  $(".editable").on("click", function () {
    const field = $(this).data("field");
    const currentValue = $(this).text().trim().replace(/\s*$/, ""); // Remove trailing spaces and SVG icon

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

          // Update the display with SVG icon
          $(`[data-field="${field}"]`).html(
            newValue +
              '<svg class="edit-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>'
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
        submitBtn.prop("disabled", false).text("Save Changes");
      },
    });
  });

  // Change profile picture
  $("#newProfilePicture").on("change", function () {
    const file = this.files[0];
    if (file) {
      const formData = new FormData();
      formData.append("profile_picture", file);

      // Show loading indicator
      $("#profileImage").css("opacity", "0.5");

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
            // Update the image with a cache-busting parameter
            $("#profileImage")
              .attr(
                "src",
                "uploads/profiles/" +
                  response.filename +
                  "?t=" +
                  new Date().getTime()
              )
              .css("opacity", "1");
            showMessage(response.message, "success");
          } else {
            $("#profileImage").css("opacity", "1");
            showMessage(response.message, "error");
          }
        },
        error: function () {
          $("#profileImage").css("opacity", "1");
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
        submitBtn
          .prop("disabled", false)
          .html(
            '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg> Post'
          );
      },
    });
  });

  // Like/Dislike posts
  $(".btn-like, .btn-dislike").on("click", function () {
    const postId = $(this).data("post-id");
    const reactionType = $(this).hasClass("btn-like") ? "like" : "dislike";
    const button = $(this);

    button.addClass("active-reaction");

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

          // Toggle reaction buttons based on user's choice
          if (reactionType === "like") {
            button.addClass("reacted");
            button
              .closest(".post-actions")
              .find(".btn-dislike")
              .removeClass("reacted");
          } else {
            button.addClass("reacted");
            button
              .closest(".post-actions")
              .find(".btn-like")
              .removeClass("reacted");
          }
        } else {
          showMessage(response.message, "error");
        }
      },
      error: function () {
        showMessage("An error occurred. Please try again.", "error");
      },
      complete: function () {
        button.removeClass("active-reaction");
      },
    });
  });

  // Delete posts
  $(".btn-delete").on("click", function () {
    if (!confirm("Are you sure you want to delete this post?")) {
      return;
    }

    const postId = $(this).data("post-id");
    const postElement = $(this).closest(".post-card");

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

            // Check if there are no more posts and show empty state if needed
            if ($(".post-card").length === 0) {
              $("#posts-tab").html(`
                <div class="empty-state">
                  <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                  <h3>No Posts Yet</h3>
                  <p>You haven't created any posts yet. Start sharing with your network!</p>
                  <button class="btn-primary" onclick="document.querySelector('.profile-tab[data-tab=\\'info\\']').click();">Create Your First Post</button>
                </div>
              `);
            }
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
    $(".form-message").remove();

    const messageDiv = $(`<div class="form-message ${type}"></div>`).text(
      message
    );
    $(".profile-content").prepend(messageDiv);

    setTimeout(function () {
      messageDiv.fadeOut();
    }, 5000);
  }
});
