<?php
session_start();
require_once 'config/database.php';
require_once 'classes/User.php';
require_once 'classes/Post.php';
require_once __DIR__ . '/includes/csrf.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

$database = new Database();
$user = new User($database);
$post = new Post($database);

$userData = $user->getUserById($_SESSION['user_id']);
$userPosts = $post->getUserPosts($_SESSION['user_id']);
$pageTitle = 'Profile - ConnectHub';
?>

<?php include __DIR__ . '/includes/header.php'; ?>
<div class="profile-container">
    <div class="profile-header-section">
        <div class="profile-cover"></div>
        <div class="profile-header-content">
            <?php
            $profilePic = isset($userData['profile_picture']) && $userData['profile_picture'] !== ''
                ? 'uploads/profiles/' . $userData['profile_picture']
                : 'assets/images/default.jpg';
            ?>
            <div class="profile-picture-container">
                <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" id="profileImage">
                <div class="profile-picture-edit" onclick="document.getElementById('newProfilePicture').click();">
                    <input type="file" id="newProfilePicture" accept="image/jpeg,image/jpg,image/png" style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 16l-3-10 7.104 4 3.896-6 3.896 6 7.104-4-3 10H3zm11 2H8"></path>
                    </svg>
                </div>
            </div>
            <div class="profile-header-info">
                <h1><?php echo htmlspecialchars($userData['full_name']); ?></h1>
                <div class="profile-stats">
                    <div class="profile-stat">
                        <span class="stat-value"><?php echo count($userPosts); ?></span>
                        <span class="stat-label">Posts</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="profile-tabs-container">
        <div class="profile-tabs">
            <button class="profile-tab active" data-tab="info">Profile Info</button>
            <button class="profile-tab" data-tab="posts">Your Posts</button>
        </div>
    </div>

    <div class="profile-content">
        <div class="profile-tab-content active" id="info-tab">
            <div class="profile-card">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    Personal Information
                </h3>

                <div class="profile-info-list">
                    <div class="profile-info-item">
                        <div class="info-label">Full Name</div>
                        <div class="info-value editable" data-field="full_name">
                            <?php echo htmlspecialchars($userData['full_name']); ?>
                            <svg class="edit-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="profile-info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?php echo htmlspecialchars($userData['email']); ?></div>
                    </div>

                    <div class="profile-info-item">
                        <div class="info-label">Age</div>
                        <div class="info-value editable" data-field="age">
                            <?php echo htmlspecialchars($userData['age']); ?>
                            <svg class="edit-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="profile-info-item">
                        <div class="info-label">Member Since</div>
                        <div class="info-value"><?php echo date('F j, Y', strtotime($userData['created_at'])); ?></div>
                    </div>
                </div>
            </div>

            <div class="profile-card">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Create New Post
                </h3>

                <form id="addPostForm" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <div class="form-group">
                        <textarea id="postDescription" name="description" placeholder="What's on your mind?" required></textarea>
                    </div>

                    <div class="form-group file-upload-wrapper">
                        <input type="file" id="postImage" name="post_image" accept="image/jpeg,image/jpg,image/png">
                        <small>Optional. Add an image to your post (JPG or PNG up to 5MB)</small>
                    </div>

                    <button type="submit" class="btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                        Post
                    </button>
                </form>
            </div>
        </div>

        <div class="profile-tab-content" id="posts-tab">
            <div class="profile-posts">
                <?php if (empty($userPosts)): ?>
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        <h3>No Posts Yet</h3>
                        <p>You haven't created any posts yet. Start sharing with your network!</p>
                        <button class="btn-primary" onclick="document.querySelector('.profile-tab[data-tab=\'info\']').click();">Create Your First Post</button>
                    </div>
                <?php else: ?>
                    <div id="postsWrapper">
                        <?php foreach ($userPosts as $userPost): ?>
                            <div class="post-card" data-post-id="<?php echo $userPost['id']; ?>">
                                <div class="post-header">
                                    <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" class="post-author-avatar">
                                    <div class="post-author-info">
                                        <h4><?php echo htmlspecialchars($userData['full_name']); ?></h4>
                                        <p class="post-date"><?php echo date('M j, Y \a\t g:i A', strtotime($userPost['created_at'])); ?></p>
                                    </div>
                                </div>

                                <div class="post-content">
                                    <div class="post-description">
                                        <?php echo nl2br(htmlspecialchars($userPost['description'])); ?>
                                    </div>
                                    <?php if (!empty($userPost['image'])): ?>
                                        <img src="uploads/posts/<?php echo htmlspecialchars($userPost['image']); ?>" alt="Post Image" class="post-image">
                                    <?php endif; ?>
                                </div>

                                <div class="post-actions">
                                    <button class="action-btn btn-like" data-post-id="<?php echo $userPost['id']; ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                                        </svg>
                                        <span class="like-count"><?php echo $userPost['likes']; ?></span>
                                    </button>
                                    <button class="action-btn btn-dislike" data-post-id="<?php echo $userPost['id']; ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zm7-13h2.67A2.31 2.31 0 0 1 22 4v7a2.31 2.31 0 0 1-2.33 2H17"></path>
                                        </svg>
                                        <span class="dislike-count"><?php echo $userPost['dislikes']; ?></span>
                                    </button>
                                    <button class="action-btn btn-delete" data-post-id="<?php echo $userPost['id']; ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Edit Profile</h3>
        <form id="editProfileForm">
            <input type="hidden" id="editField" name="field">
            <div class="form-group">
                <label id="editLabel"></label>
                <input type="text" id="editValue" name="value" required>
            </div>
            <button type="submit" class="btn-primary">Save Changes</button>
        </form>
    </div>
</div>

<script>
    // JavaScript for profile tabs
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.profile-tab');
        const tabContents = document.querySelectorAll('.profile-tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                tabs.forEach(t => t.classList.remove('active'));
                // Add active class to clicked tab
                this.classList.add('active');

                // Hide all tab contents
                tabContents.forEach(content => content.classList.remove('active'));

                // Show the selected tab content
                const tabName = this.getAttribute('data-tab');
                document.getElementById(tabName + '-tab').classList.add('active');
            });
        });
    });
</script>
<script src="assets/js/profile.js"></script>
<?php include __DIR__ . '/includes/footer.php'; ?>