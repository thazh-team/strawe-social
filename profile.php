<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$username = isset($_GET['user']) ? sanitize($_GET['user']) : '';
$current_user = getCurrentUser();

// Nếu không có username, hiển thị profile của user hiện tại
if (empty($username)) {
    $username = $current_user['username'];
}

// Lấy thông tin user
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$profile_user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile_user) {
    header('Location: index.php');
    exit();
}

// Đếm followers
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM follows WHERE following_id = ?");
$stmt->execute([$profile_user['id']]);
$followers_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Đếm following
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM follows WHERE follower_id = ?");
$stmt->execute([$profile_user['id']]);
$following_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Đếm tổng likes
$stmt = $pdo->prepare("SELECT SUM(likes_count) as total_likes FROM posts WHERE user_id = ?");
$stmt->execute([$profile_user['id']]);
$total_likes = $stmt->fetch(PDO::FETCH_ASSOC)['total_likes'] ?: 0;

// Kiểm tra đã follow chưa
$is_following = false;
if ($profile_user['id'] != $current_user['id']) {
    $stmt = $pdo->prepare("SELECT id FROM follows WHERE follower_id = ? AND following_id = ?");
    $stmt->execute([$current_user['id'], $profile_user['id']]);
    $is_following = (bool)$stmt->fetch();
}

// Xử lý follow/unfollow
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_follow'])) {
    if ($profile_user['id'] != $current_user['id']) {
        if ($is_following) {
            $stmt = $pdo->prepare("DELETE FROM follows WHERE follower_id = ? AND following_id = ?");
            $stmt->execute([$current_user['id'], $profile_user['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO follows (follower_id, following_id) VALUES (?, ?)");
            $stmt->execute([$current_user['id'], $profile_user['id']]);
        }
        header("Location: profile.php?user=" . $username);
        exit();
    }
}

// Lấy bài viết của user
$stmt = $pdo->prepare("
    SELECT p.*, 
           (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) as user_liked
    FROM posts p 
    WHERE p.user_id = ? 
    ORDER BY p.created_at DESC
");
$stmt->execute([$current_user['id'], $profile_user['id']]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($profile_user['full_name']); ?> - Strawe</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <div class="profile-header">
                <img src="assets/uploads/avatars/<?php echo $profile_user['avatar']; ?>" alt="Avatar" class="profile-avatar" onerror="this.src='assets/uploads/avatars/default_avatar.jpg'">
                
                <div class="profile-name">
                    <?php echo htmlspecialchars($profile_user['full_name']); ?>
                    <?php echo getVerifyTick($profile_user); ?>
                </div>
                
                <div class="profile-username">
                    @<?php echo htmlspecialchars($profile_user['username']); ?>
                </div>
                
                <?php if (!empty($profile_user['bio'])): ?>
                    <div style="margin: 15px 0; max-width: 400px;">
                        <?php echo nl2br(htmlspecialchars($profile_user['bio'])); ?>
                    </div>
                <?php endif; ?>
                
                <div class="profile-stats">
                    <div class="stat">
                        <span class="stat-number"><?php echo $followers_count; ?></span>
                        <span class="stat-label">Người theo dõi</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number"><?php echo $following_count; ?></span>
                        <span class="stat-label">Đang theo dõi</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number"><?php echo $total_likes; ?></span>
                        <span class="stat-label">Lượt thích</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number"><?php echo count($posts); ?></span>
                        <span class="stat-label">Bài viết</span>
                    </div>
                </div>
                
                <?php if ($profile_user['id'] != $current_user['id']): ?>
                    <form method="POST" style="margin-top: 20px;">
                        <button type="submit" name="toggle_follow" class="btn <?php echo $is_following ? 'btn-secondary' : 'btn-primary'; ?>">
                            <?php echo $is_following ? '✓ Đang theo dõi' : '+ Theo dõi'; ?>
                        </button>
                    </form>
                <?php else: ?>
                    <div style="margin-top: 20px;">
                        <a href="settings.php" class="btn btn-secondary">⚙️ Chỉnh sửa trang cá nhân</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="card">
                <h3>Bài viết của <?php echo htmlspecialchars($profile_user['full_name']); ?></h3>
                
                <?php if (empty($posts)): ?>
                    <p style="text-align: center; color: #657786; margin: 40px 0;">
                        <?php if ($profile_user['id'] == $current_user['id']): ?>
                            Bạn chưa có bài viết nào. <a href="newpost.php">Đăng bài viết đầu tiên</a>!
                        <?php else: ?>
                            <?php echo htmlspecialchars($profile_user['full_name']); ?> chưa có bài viết nào.
                        <?php endif; ?>
                    </p>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="post">
                            <div class="post-header">
                                <img src="assets/uploads/avatars/<?php echo $profile_user['avatar']; ?>" alt="Avatar" class="post-avatar" onerror="this.src='assets/uploads/avatars/default_avatar.jpg'">
                                <div class="post-author">
                                    <span><?php echo htmlspecialchars($profile_user['full_name']); ?></span>
                                    <?php echo getVerifyTick($profile_user); ?>
                                    <small>@<?php echo htmlspecialchars($profile_user['username']); ?></small>
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
                                <form method="POST" action="index.php" style="display: inline;">
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
