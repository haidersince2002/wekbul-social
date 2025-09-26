<?php
// File: classes/Post.php (Enhanced with new method)
class Post
{
    private $db;
    private $table = 'posts';

    public function __construct($database)
    {
        $this->db = $database->getConnection();
    }

    // Existing methods...
    public function create($userId, $description, $image = null)
    {
        $sql = "INSERT INTO {$this->table} (user_id, description, image, created_at) 
                VALUES (:user_id, :description, :image, NOW())";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':description' => $description,
            ':image' => $image
        ]);
    }

    public function getUserPosts($userId)
    {
        $sql = "SELECT p.*, 
                (SELECT COUNT(*) FROM post_reactions WHERE post_id = p.id AND reaction_type = 'like') as likes,
                (SELECT COUNT(*) FROM post_reactions WHERE post_id = p.id AND reaction_type = 'dislike') as dislikes
                FROM {$this->table} p 
                WHERE p.user_id = :user_id 
                ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    // NEW METHOD: Get all posts with user information for home feed
    public function getAllPostsWithUsers()
    {
        $sql = "SELECT p.*, u.full_name as user_name, u.profile_picture as user_profile_picture,
                (SELECT COUNT(*) FROM post_reactions WHERE post_id = p.id AND reaction_type = 'like') as likes,
                (SELECT COUNT(*) FROM post_reactions WHERE post_id = p.id AND reaction_type = 'dislike') as dislikes
                FROM {$this->table} p 
                JOIN users u ON p.user_id = u.id 
                ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $posts = $stmt->fetchAll();

        // Set default profile picture for posts where the user has none
        foreach ($posts as $key => $post) {
            if (empty($post['user_profile_picture'])) {
                $posts[$key]['user_profile_picture'] = '';
            }
        }

        return $posts;
    }

    public function delete($postId, $userId)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $postId, ':user_id' => $userId]);
    }

    public function addReaction($postId, $userId, $reactionType)
    {
        // Remove existing reaction first
        $this->removeReaction($postId, $userId);

        $sql = "INSERT INTO post_reactions (post_id, user_id, reaction_type, created_at) 
                VALUES (:post_id, :user_id, :reaction_type, NOW())";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':post_id' => $postId,
            ':user_id' => $userId,
            ':reaction_type' => $reactionType
        ]);
    }

    public function removeReaction($postId, $userId)
    {
        $sql = "DELETE FROM post_reactions WHERE post_id = :post_id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':post_id' => $postId, ':user_id' => $userId]);
    }

    public function getReactionCounts($postId)
    {
        $sql = "SELECT reaction_type, COUNT(*) as count 
                FROM post_reactions 
                WHERE post_id = :post_id 
                GROUP BY reaction_type";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':post_id' => $postId]);

        $reactions = ['like' => 0, 'dislike' => 0];
        while ($row = $stmt->fetch()) {
            $reactions[$row['reaction_type']] = $row['count'];
        }

        return $reactions;
    }
}
