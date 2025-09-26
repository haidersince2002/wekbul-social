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
$pageTitle = 'Home - ConnectHub';
?>
<?php include __DIR__ . '/includes/header.php'; ?>
<div class="home-container">
    <div class="home-layout">
        <!-- Left Sidebar -->
        <div class="home-sidebar">
            <div class="sidebar-profile">
                <?php
                $profilePic = isset($currentUser['profile_picture']) && $currentUser['profile_picture'] !== ''
                    ? 'uploads/profiles/' . $currentUser['profile_picture']
                    : 'assets/images/default.jpg';
                ?>
                <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Your Avatar" class="sidebar-avatar">
                <div class="sidebar-info">
                    <h3><?php echo htmlspecialchars($currentUser['full_name']); ?></h3>
                    <a href="profile.php" class="view-profile-link">View profile</a>
                </div>
            </div>

            <div class="sidebar-menu">
                <a href="home.php" class="sidebar-link active">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Home Feed
                </a>
                <a href="profile.php" class="sidebar-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    My Profile
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="home-content">
            <!-- Create Post Section -->
            <div class="create-post-section">
                <h3>Create Post</h3>
                <form id="createPostForm" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <div class="post-input-container">
                        <img src="<?php echo htmlspecialchars($profilePic); ?>"
                            alt="Your Avatar" class="user-avatar">
                        <textarea id="postDescription" name="description" class="post-textarea"
                            placeholder="What's on your mind, <?php echo htmlspecialchars($currentUser['full_name']); ?>?" required></textarea>
                    </div>

                    <!-- File Upload Area -->
                    <div class="file-upload-area" onclick="document.getElementById('postImage').click()">
                        <input type="file" id="postImage" name="post_image" accept="image/jpeg,image/jpg,image/png" style="display: none;">
                        <div id="uploadText">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            Add a photo to your post
                            <div class="upload-note">JPG, PNG up to 5MB</div>
                        </div>
                        <div id="imagePreview"></div>
                    </div>

                    <div class="post-actions-row">
                        <button type="submit" class="btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                            Share Post
                        </button>
                    </div>
                </form>
            </div>

            <!-- Posts Feed -->
            <div class="feed-section">
                <h2>Recent Posts</h2>
                <div id="postsContainer">
                    <?php if (empty($allPosts)): ?>
                        <div class="no-posts">
                            <div class="empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <h3>Welcome to ConnectHub!</h3>
                                <p>No posts yet. Be the first to share something!</p>
                                <a href="#" class="btn-primary" onclick="document.getElementById('postDescription').focus(); return false;">Create your first post</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($allPosts as $post): ?>
                            <div class="post-card" data-post-id="<?php echo $post['id']; ?>">
                                <!-- Post Header -->
                                <div class="post-header">
                                    <?php
                                    $postUserProfilePic = isset($post['user_profile_picture']) && $post['user_profile_picture'] !== ''
                                        ? 'uploads/profiles/' . $post['user_profile_picture']
                                        : 'assets/images/default.jpg';
                                    ?>
                                    <img src="<?php echo htmlspecialchars($postUserProfilePic); ?>"
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
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                                        </svg>
                                        <span class="like-count"><?php echo $post['likes']; ?></span>
                                    </button>
                                    <button class="action-btn btn-dislike" data-post-id="<?php echo $post['id']; ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zm7-13h2.67A2.31 2.31 0 0 1 22 4v7a2.31 2.31 0 0 1-2.33 2H17"></path>
                                        </svg>
                                        <span class="dislike-count"><?php echo $post['dislikes']; ?></span>
                                    </button>

                                    <?php if ($post['user_id'] == $_SESSION['user_id']): ?>
                                        <button class="action-btn btn-delete" data-post-id="<?php echo $post['id']; ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                            Delete
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="messageContainer" class="message-container"></div>
<script src="assets/js/home.js"></script>
<?php include __DIR__ . '/includes/footer.php'; ?>