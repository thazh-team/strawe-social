<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$search_query = '';
$search_results = [];
$search_type = 'all';

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search_query = sanitize($_GET['q']);
    $search_type = isset($_GET['type']) ? sanitize($_GET['type']) : 'all';
    
    // Cập nhật hot search
    $stmt = $pdo->prepare("SELECT id FROM hot_searches WHERE keyword = ?");
    $stmt->execute([$search_query]);
    
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("UPDATE hot_searches SET search_count = search_count + 1, updated_at = NOW() WHERE keyword = ?");
        $stmt->execute([$search_query]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO hot_searches (keyword) VALUES (?)");
        $stmt->execute([$search_query]);
    }
    
    // Tìm kiếm theo loại
    switch ($search_type) {
        case 'users':
            $stmt = $pdo->prepare("SELECT * FROM users WHERE (username LIKE ? OR full_name LIKE ?) AND id != ? ORDER BY is_verified DESC, full_name ASC LIMIT 20");
            $stmt->execute(["%$search_query%", "%$search_query%", $_SESSION['user_id']]);
            $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'posts':
            $stmt = $pdo->prepare("
                SELECT p.*, u.username, u.full_name, u.avatar, u.is_verified, u.is_admin
                FROM posts p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.content LIKE ? 
                ORDER BY p.created_at DESC 
                LIMIT 20
            ");
            $stmt->execute(["%$search_query%"]);
            $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        default: // all
            // Tìm kiếm users
            $stmt = $pdo->prepare("SELECT *, 'user' as type FROM users WHERE (username LIKE ? OR full_name LIKE ?) AND id != ? ORDER BY is_verified DESC LIMIT 10");
            $stmt->execute(["%$search_query%", "%$search_query%", $_SESSION['user_id']]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Tìm kiếm posts
            $stmt = $pdo->prepare("
                SELECT p.*, u.username, u.full_name, u.avatar, u.is_verified, u.is_admin, 'post' as type
                FROM posts p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.content LIKE ? 
                ORDER BY p.created_at DESC 
                LIMIT 10
            ");
            $stmt->execute(["%$search_query%"]);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $search_results = array_merge($users, $posts);
            break;
    }
}

// Lấy hot searches
$stmt = $pdo->prepare("SELECT keyword, search_count FROM hot_searches ORDER BY search_count DESC, updated_at DESC LIMIT 10");
$stmt->execute();
$hot_searches = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm - Thazh Social</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <div class="search-container">
                <div class="search-box">
                    <form method="GET" action="search.php">
                        <input type="text" class="search-input" name="q" placeholder="Tìm kiếm bạn bè, bài viết, hashtag..." value="<?php echo htmlspecialchars($search_query); ?>">
                        <input type="hidden" name="type" value="<?php echo $search_type; ?>">
                    </form>
                </div>
                
                <?php if (!empty($search_query)): ?>
                    <div class="card">
                        <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                            <a href="?q=<?php echo urlencode($search_query); ?>&type=all" class="btn <?php echo $search_type == 'all' ? 'btn-primary' : 'btn-secondary'; ?>">Tất cả</a>
                            <a href="?q=<?php echo urlencode($search_query); ?>&type=users" class="btn <?php echo $search_type == 'users' ? 'btn-primary' : 'btn-secondary'; ?>">Người dùng</a>
                            <a href="?q=<?php echo urlencode($search_query); ?>&type=posts" class="btn <?php echo $search_type == 'posts' ? 'btn-primary' : 'btn-secondary'; ?>">Bài viết</a>
                        </div>
                        
                        <h3>Kết quả tìm kiếm cho: "<?php echo htmlspecialchars($search_query); ?>"</h3>
                        
                        <?php if (empty($search_results)): ?>
                            <p style="text-align: center; color: #657786; margin: 30px 0;">Không tìm thấy kết quả phù hợp.</p>
                        <?php else: ?>
                            <?php foreach ($search_results as $result): ?>
                                <?php if ($result['type'] == 'user' || isset($result['username'])): ?>
                                    <!-- User result -->
                                    <div class="post">
                                        <div class="post-header">
                                            <img src="assets/uploads/avatars/<?php echo $result['avatar']; ?>" alt="Avatar" class="post-avatar" onerror="this.src='assets/uploads/avatars/default_avatar.jpg'">
                                            <div class="post-author">
                                                <span><?php echo htmlspecialchars($result['full_name']); ?></span>
                                                <?php echo getVerifyTick($result); ?>
                                                <small>@<?php echo htmlspecialchars($result['username']); ?></small>
                                            </div>
                                            <div style="margin-left: auto;">
                                                <a href="profile.php?user=<?php echo $result['username']; ?>" class="btn btn-primary" style="padding: 8px 16px; font-size: 14px;">Xem trang</a>
                                            </div>
                                        </div>
                                        <?php if (!empty($result['bio'])): ?>
                                            <div class="post-content" style="margin-left: 50px;">
                                                <?php echo nl2br(htmlspecialchars($result['bio'])); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <!-- Post result -->
                                    <div class="post">
                                        <div class="post-header">
                                            <img src="assets/uploads/avatars/<?php echo $result['avatar']; ?>" alt="Avatar" class="post-avatar" onerror="this.src='assets/uploads/avatars/default_avatar.jpg'">
                                            <div class="post-author">
                                                <span><?php echo htmlspecialchars($result['full_name']); ?></span>
                                                <?php echo getVerifyTick($result); ?>
                                                <small>@<?php echo htmlspecialchars($result['username']); ?></small>
                                            </div>
                                            <span class="post-time"><?php echo timeAgo($result['created_at']); ?></span>
                                        </div>
                                        
                                        <div class="post-content">
                                            <?php echo nl2br(htmlspecialchars($result['content'])); ?>
                                        </div>
                                        
                                        <?php if ($result['image'] || $result['audio']): ?>
                                            <div class="post-media">
                                                <?php if ($result['image']): ?>
                                                    <img src="assets/uploads/posts/<?php echo $result['image']; ?>" alt="Hình ảnh bài viết">
                                                <?php endif; ?>
                                                
                                                <?php if ($result['audio']): ?>
                                                    <audio controls>
                                                        <source src="assets/uploads/posts/<?php echo $result['audio']; ?>" type="audio/mpeg">
                                                    </audio>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($hot_searches)): ?>
                    <div class="hot-searches">
                        <h3>🔥 Tìm kiếm phổ biến</h3>
                        <?php foreach ($hot_searches as $hot): ?>
                            <a href="?q=<?php echo urlencode($hot['keyword']); ?>" class="hot-search-item">
                                <?php echo htmlspecialchars($hot['keyword']); ?>
                                <small style="color: #657786; margin-left: 10px;"><?php echo $hot['search_count']; ?> lượt tìm</small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
