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
$pageTitle = 'Profile - Social Network';
?>

<?php include __DIR__ . '/includes/header.php'; ?>
<div class="container">
    <header class="profile-header">
        <h1>Welcome, <?php echo htmlspecialchars($userData['full_name']); ?>!</h1>
        <a href="auth/logout.php" class="btn-secondary">Logout</a>
    </header>

    <div class="profile-content">
        <!-- Profile Information -->
        <div class="profile-info">
            <div class="profile-picture">
                <?php
                $profilePic = isset($userData['profile_picture']) && $userData['profile_picture'] !== ''
                    ? $userData['profile_picture']
                    : 'default.jpg';
                ?>
                <img src="uploads/profiles/<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" id="profileImage">
            </div>

            <div class="profile-details">
                <div class="detail-item">
                    <label>Full Name:</label>
                    <span class="editable" data-field="full_name">
                        <?php echo htmlspecialchars($userData['full_name']); ?>
                        <i class="edit-icon">✏️</i>
                    </span>
                </div>

                <div class="detail-item">
                    <label>Email:</label>
                    <span><?php echo htmlspecialchars($userData['email']); ?></span>
                </div>

                <div class="detail-item">
                    <label>Age:</label>
                    <span class="editable" data-field="age">
                        <?php echo htmlspecialchars($userData['age']); ?>
                        <i class="edit-icon">✏️</i>
                    </span>
                </div>

                <div class="detail-item">
                    <label>Profile Picture:</label>
                    <input type="file" id="newProfilePicture" accept="image/jpeg,image/jpg,image/png" style="display: none;">
                    <button class="btn-secondary" onclick="document.getElementById('newProfilePicture').click();">
                        Change Picture
                    </button>
                </div>
            </div>
        </div>

        <!-- Add New Post -->
        <div class="add-post">
            <h3>Add New Post</h3>
            <form id="addPostForm" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <div class="form-group">
                    <textarea id="postDescription" name="description" placeholder="What's on your mind?" required></textarea>
                </div>
                <div class="form-group">
                    <input type="file" id="postImage" name="post_image" accept="image/jpeg,image/jpg,image/png">
                    <label for="postImage" class="file-label">Add Image</label>
                </div>
                <button type="submit" class="btn-primary">Post</button>
            </form>
        </div>

        <!-- Posts Display -->
        <div class="posts-container">
            <h3>Your Posts</h3>
            <div id="postsWrapper">
                <?php foreach ($userPosts as $userPost): ?>
                    <div class="post" data-post-id="<?php echo $userPost['id']; ?>">
                        <?php if (!empty($userPost['image'])): ?>
                            <div class="post-image">
                                <img src="uploads/posts/<?php echo htmlspecialchars($userPost['image']); ?>" alt="Post Image">
                            </div>
                        <?php endif; ?>

                        <div class="post-content">
                            <p><?php echo htmlspecialchars($userPost['description']); ?></p>
                            <small class="post-date"><?php echo date('M j, Y g:i A', strtotime($userPost['created_at'])); ?></small>
                        </div>

                        <div class="post-actions">
                            <button class="btn-like" data-post-id="<?php echo $userPost['id']; ?>">
                                👍 Like (<span class="like-count"><?php echo $userPost['likes']; ?></span>)
                            </button>
                            <button class="btn-dislike" data-post-id="<?php echo $userPost['id']; ?>">
                                👎 Dislike (<span class="dislike-count"><?php echo $userPost['dislikes']; ?></span>)
                            </button>
                            <button class="btn-delete" data-post-id="<?php echo $userPost['id']; ?>">
                                🗑️ Delete
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
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
            <button type="submit" class="btn-primary">Save</button>
        </form>
    </div>
</div>

<script src="assets/js/profile.js"></script>
<?php include __DIR__ . '/includes/footer.php'; ?>