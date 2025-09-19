<?php
// File: home.php (Main Feed Page - Shows All Posts)
session_start();
require_once 'config/database.php';
require_once 'classes/User.php';
require_once 'classes/Post.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

$database = new Database();
$user = new User($database);
$post = new Post($database);

$currentUser = $user->getUserById($_SESSION['user_id']);
$allPosts = $post->getAllPostsWithUsers();
$pageTitle = 'Home - Social Network';
?>
<?php include __DIR__ . '/includes/header.php'; ?>
<div class="main-container">
    <nav class="header-nav">
        <div class="nav-left">
            <h1 class="brand-title">Social Network</h1>
            <a href="home.php" class="nav-link active">Home</a>
            <a href="profile.php" class="nav-link">My Profile</a>
        </div>
        <div class="nav-right">
            <?php
            $currentPic = $currentUser['profile_picture'] ?? '';
            $currentPicSrc = (empty($currentPic) || $currentPic === 'default.jpg')
                ? 'assets/images/default.jpg'
                : 'uploads/profiles/' . htmlspecialchars($currentPic);
            ?>
            <img src="<?php echo $currentPicSrc; ?>" alt="Your Avatar" class="user-avatar">
            <span class="user-greet">Hi, <?php echo htmlspecialchars($currentUser['full_name']); ?>!</span>
            <a href="auth/logout.php" class="nav-link">Logout</a>
        </div>
    </nav>

    <!-- Create Post Section -->
    <div class="create-post-section">
        <h3 class="section-title">Share something</h3>
        <form id="createPostForm" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="post-input-container">
                <img src="<?php echo $currentPicSrc; ?>" alt="Your Avatar" class="user-avatar">
                <textarea id="postDescription" name="description" class="post-textarea"
                    placeholder="What's happening, <?php echo htmlspecialchars($currentUser['full_name']); ?>?" required></textarea>
            </div>

            <!-- File Upload Area -->
            <div class="file-upload-area" onclick="document.getElementById('postImage').click()">
                <input type="file" id="postImage" name="post_image" accept="image/jpeg,image/jpg,image/png" class="hidden-input" required>
                <div id="uploadText">
                    Click to add a photo to your post
                    <div class="muted-note">JPG/PNG up to 5MB</div>
                </div>
                <div id="imagePreview" class="image-preview"></div>
            </div>

            <div class="form-actions">
                <button type="submit" class="action-btn btn-primary-solid">Share Post</button>
            </div>
        </form>
    </div>

    <!-- Posts Feed -->
    <div class="feed-section">
        <h2>Recent Posts</h2>
        <div id="postsContainer">
            <?php if (empty($allPosts)): ?>
                <div class="no-posts">
                    <h3>Welcome to Social Network</h3>
                    <p>No posts yet. Be the first to share something!</p>
                    <p class="muted-note"><a href="profile.php" class="nav-link">Go to your profile</a> to get started.</p>
                </div>
            <?php else: ?>
                <?php foreach ($allPosts as $post): ?>
                    <div class="post-card" data-post-id="<?php echo $post['id']; ?>">
                        <!-- Post Header -->
                        <div class="post-header">
                            <?php
                            $authorPic = $post['user_profile_picture'] ?? '';
                            $authorPicSrc = (empty($authorPic) || $authorPic === 'default.jpg')
                                ? 'assets/images/default.jpg'
                                : 'uploads/profiles/' . htmlspecialchars($authorPic);
                            ?>
                            <img src="<?php echo $authorPicSrc; ?>"
                                alt="<?php echo htmlspecialchars($post['user_name']); ?>" class="post-author-avatar">
                            <div class="post-author-info">
                                <h4><?php echo htmlspecialchars($post['user_name']); ?></h4>
                                <p class="post-date"><?php echo date('M j, Y \a\t g:i A', strtotime($post['created_at'])); ?></p>
                            </div>
                        </div>

                        <!-- Post Content -->
                        <div class="post-content">
                            <div class="post-description">
                                <?php echo nl2br(htmlspecialchars($post['description'])); ?>
                            </div>
                            <?php if (!empty($post['image'])): ?>
                                <img src="uploads/posts/<?php echo htmlspecialchars($post['image']); ?>"
                                    alt="Post Image" class="post-image">
                            <?php endif; ?>
                        </div>

                        <!-- Post Actions -->
                        <div class="post-actions">
                            <button class="action-btn btn-like" data-post-id="<?php echo $post['id']; ?>">
                                👍 <span class="like-count"><?php echo $post['likes']; ?></span>
                            </button>
                            <button class="action-btn btn-dislike" data-post-id="<?php echo $post['id']; ?>">
                                👎 <span class="dislike-count"><?php echo $post['dislikes']; ?></span>
                            </button>

                            <?php if ($post['user_id'] == $_SESSION['user_id']): ?>
                                <button class="action-btn btn-delete" data-post-id="<?php echo $post['id']; ?>">
                                    🗑️ Delete
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="messageContainer" class="message-container"></div>
<script src="assets/js/home.js"></script>
<?php include __DIR__ . '/includes/footer.php'; ?>