// Home page interactions
$(function () {
  // File upload preview
  $("#postImage").on("change", function () {
    const file = this.files[0];
    if (!file) {
      $("#imagePreview").empty();
      $("#uploadText").show();
      return;
    }
    const allowedTypes = ["image/jpeg", "image/jpg", "image/png"];
    if (!allowedTypes.includes(file.type)) {
      showToast("Only JPG and PNG files are allowed", "error");
      $(this).val("");
      return;
    }
    if (file.size > 5 * 1024 * 1024) {
      showToast("File size must be less than 5MB", "error");
      $(this).val("");
      return;
    }
    const reader = new FileReader();
    reader.onload = function (e) {
      $("#uploadText").hide();
      $("#imagePreview").html(
        '<img src="' +
          e.target.result +
          '" class="preview-img">' +
          '<div class="preview-note">Ready to share! ✨</div>'
      );
    };
    reader.readAsDataURL(file);
  });

  // Create post
  $("#createPostForm").on("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const $submitBtn = $(this).find('button[type="submit"]');
    $submitBtn.prop("disabled", true).html("🔄 Sharing...");

    $.ajax({
      url: "api/posts.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          $("#createPostForm")[0].reset();
          $("#imagePreview").empty();
          $("#uploadText").show();
          showToast("Post shared successfully! 🎉", "success");
          setTimeout(function () {
            window.location.reload();
          }, 1200);
        } else {
          showToast(response.message || "Failed to share post", "error");
        }
      },
      error: function () {
        showToast("An error occurred while sharing your post", "error");
      },
      complete: function () {
        $submitBtn.prop("disabled", false).html("🚀 Share Post");
      },
    });
  });

  // React buttons
  $(".btn-like, .btn-dislike").on("click", function () {
    const postId = $(this).data("post-id");
    const reactionType = $(this).hasClass("btn-like") ? "like" : "dislike";
    const $button = $(this);
    $.ajax({
      url: "api/reactions.php",
      type: "POST",
      data: { post_id: postId, reaction_type: reactionType },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          $button
            .closest(".post-actions")
            .find(".like-count")
            .text(response.reactions.like);
          $button
            .closest(".post-actions")
            .find(".dislike-count")
            .text(response.reactions.dislike);
        } else {
          showToast(response.message || "Failed to update reaction", "error");
        }
      },
      error: function () {
        showToast("An error occurred", "error");
      },
    });
  });

  // Delete post
  $(".btn-delete").on("click", function () {
    if (
      !confirm(
        "🗑️ Are you sure you want to delete this post? This action cannot be undone."
      )
    )
      return;
    const postId = $(this).data("post-id");
    const $postCard = $(this).closest(".post-card");

    $.ajax({
      url: "api/posts.php?csrf_token=" + encodeURIComponent(window.CSRF_TOKEN),
      type: "DELETE",
      data: { post_id: postId },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          $postCard.fadeOut(300, function () {
            $(this).remove();
          });
          showToast("Post deleted successfully", "success");
        } else {
          showToast(response.message || "Failed to delete post", "error");
        }
      },
      error: function () {
        showToast("An error occurred while deleting the post", "error");
      },
    });
  });

  // Drag and drop area
  const uploadArea = $(".file-upload-area");
  uploadArea.on("dragover dragenter", function (e) {
    e.preventDefault();
    $(this).addClass("dragover");
  });
  uploadArea.on("dragleave dragend drop", function (e) {
    e.preventDefault();
    $(this).removeClass("dragover");
  });
  uploadArea.on("drop", function (e) {
    const files = e.originalEvent.dataTransfer.files;
    if (files.length > 0) {
      $("#postImage")[0].files = files;
      $("#postImage").trigger("change");
    }
  });
});
