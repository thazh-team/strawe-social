<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

// Xử lý like/unlike
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_like'])) {
    $post_id = (int)$_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$user_id, $post_id]);
    
    if ($stmt->fetch()) {
        // Unlike
        $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$user_id, $post_id]);
        
        $stmt = $pdo->prepare("UPDATE posts SET likes_count = likes_count - 1 WHERE id = ?");
        $stmt->execute([$post_id]);
    } else {
        // Like
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $post_id]);
        
        $stmt = $pdo->prepare("UPDATE posts SET likes_count = likes_count + 1 WHERE id = ?");
        $stmt->execute([$post_id]);
    }
    
    header('Location: index.php');
    exit();
}

// Lấy danh sách bài viết
$stmt = $pdo->prepare("
    SELECT p.*, u.username, u.full_name, u.avatar, u.is_verified, u.is_admin,
           (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) as user_liked
    FROM posts p 
    JOIN users u ON p.user_id = u.id 
    ORDER BY p.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$current_user = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - Strawe</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <div class="card">
                <h2>Bảng tin mới nhất</h2>
                
                <?php if (empty($posts)): ?>
                    <p style="text-align: center; color: #657786; margin: 40px 0;">
                        Chưa có bài viết nào. <a href="newpost.php">Đăng bài viết đầu tiên</a>!
                    </p>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="post">
                            <div class="post-header">
                                <img src="assets/uploads/avatars/<?php echo $post['avatar']; ?>" alt="Avatar" class="post-avatar" onerror="this.src='assets/uploads/avatars/default_avatar.jpg'">
                                <div class="post-author">
                                    <span><?php echo htmlspecialchars($post['full_name']); ?></span>
                                    <?php echo getVerifyTick($post); ?>
                                    <small>@<?php echo htmlspecialchars($post['username']); ?></small>
                                </div>
                                <span class="post-time"><?php echo timeAgo($post['created_at']); ?></span>
                            </div>
                            
                            <div class="post-content">
                                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                            </div>
                            
                            <?php if ($post['image'] || $post['audio']): ?>
                                <div class="post-media">
                                    <?php if ($post['image']): ?>
                                        <img src="assets/uploads/posts/<?php echo $post['image']; ?>" alt="Hình ảnh bài viết">
                                    <?php endif; ?>
                                    
                                    <?php if ($post['audio']): ?>
                                        <audio controls>
                                            <source src="assets/uploads/posts/<?php echo $post['audio']; ?>" type="audio/mpeg">
                                            Trình duyệt của bạn không hỗ trợ phát âm thanh.
                                        </audio>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="post-actions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" name="toggle_like" class="post-action <?php echo $post['user_liked'] ? 'liked' : ''; ?>">
                                        <?php echo $post['user_liked'] ? '❤️' : '🤍'; ?>
                                        <span><?php echo $post['likes_count']; ?></span>
                                    </button>
                                </form>
                                
                                <button class="post-action">
                                    💬 <span>0</span>
                                </button>
                                
                                <button class="post-action">
                                    🔄 <span>0</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
