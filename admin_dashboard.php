<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

if (!isAdmin()) {
    header('Location: index.php');
    exit();
}

$current_user = getCurrentUser();
$success = '';
$error = '';

// Xử lý duyệt/từ chối yêu cầu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve_request'])) {
        $request_id = (int)$_POST['request_id'];
        
        // Lấy thông tin yêu cầu
        $stmt = $pdo->prepare("SELECT * FROM verify_requests WHERE id = ?");
        $stmt->execute([$request_id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($request) {
            // Cập nhật trạng thái yêu cầu
            $stmt = $pdo->prepare("UPDATE verify_requests SET status = 'approved' WHERE id = ?");
            $stmt->execute([$request_id]);
            
            // Cập nhật trạng thái user
            $stmt = $pdo->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
            $stmt->execute([$request['user_id']]);
            
            $success = 'Đã duyệt yêu cầu xác nhận tài khoản';
        }
    }
    
    if (isset($_POST['reject_request'])) {
        $request_id = (int)$_POST['request_id'];
        
        $stmt = $pdo->prepare("UPDATE verify_requests SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$request_id]);
        
        $success = 'Đã từ chối yêu cầu xác nhận tài khoản';
    }
    
    if (isset($_POST['revoke_verification'])) {
        $user_id = (int)$_POST['user_id'];
        
        $stmt = $pdo->prepare("UPDATE users SET is_verified = 0 WHERE id = ? AND is_admin = 0");
        $stmt->execute([$user_id]);
        
        $success = 'Đã thu hồi xác nhận tài khoản';
    }
}

// Lấy thống kê
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
$total_users = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM posts");
$total_posts = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM verify_requests WHERE status = 'pending'");
$pending_requests = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE is_verified = 1");
$verified_users = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Lấy yêu cầu xác nhận
$stmt = $pdo->prepare("
    SELECT vr.*, u.username, u.full_name, u.avatar, u.is_verified, u.is_admin
    FROM verify_requests vr
    JOIN users u ON vr.user_id = u.id
    ORDER BY 
        CASE WHEN vr.status = 'pending' THEN 1 ELSE 2 END,
        vr.created_at DESC
    LIMIT 50
");
$stmt->execute();
$verify_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách user đã xác nhận
$stmt = $pdo->prepare("
    SELECT * FROM users 
    WHERE is_verified = 1 
    ORDER BY is_admin DESC, created_at DESC 
    LIMIT 20
");
$stmt->execute();
$verified_users_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị viên - Thazh Social</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <h2>🛡️ Bảng điều khiển quản trị</h2>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <!-- Thống kê -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
                <div class="admin-card">
                    <h3>👥 Tổng người dùng</h3>
                    <div style="font-size: 24px; font-weight: bold; color: #1da1f2;"><?php echo number_format($total_users); ?></div>
                </div>
                
                <div class="admin-card">
                    <h3>📝 Tổng bài viết</h3>
                    <div style="font-size: 24px; font-weight: bold; color: #17bf63;"><?php echo number_format($total_posts); ?></div>
                </div>
                
                <div class="admin-card">
                    <h3>⏳ Yêu cầu chờ duyệt</h3>
                    <div style="font-size: 24px; font-weight: bold; color: #ff6600;"><?php echo number_format($pending_requests); ?></div>
                </div>
                
                <div class="admin-card">
                    <h3>✅ Đã xác nhận</h3>
                    <div style="font-size: 24px; font-weight: bold; color: #1da1f2;"><?php echo number_format($verified_users); ?></div>
                </div>
            </div>
            
            <!-- Yêu cầu xác nhận -->
            <div class="admin-card">
                <h3>📋 Yêu cầu xác nhận tài khoản</h3>
                
                <?php if (empty($verify_requests)): ?>
                    <p style="text-align: center; color: #657786; margin: 30px 0;">Không có yêu cầu nào.</p>
                <?php else: ?>
                    <?php foreach ($verify_requests as $request): ?>
                        <div class="request-item">
                            <div class="request-header">
                                <div class="request-user">
                                    <img src="assets/uploads/avatars/<?php echo $request['avatar']; ?>" alt="Avatar" class="post-avatar" onerror="this.src='assets/uploads/avatars/default_avatar.jpg'">
                                    <div>
                                        <span><?php echo htmlspecialchars($request['full_name']); ?></span>
                                        <?php echo getVerifyTick($request); ?>
                                        <br>
                                        <small>@<?php echo htmlspecialchars($request['username']); ?></small>
                                    </div>
                                </div>
                                
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <span class="request-status status-<?php echo $request['status']; ?>">
                                        <?php 
                                        switch($request['status']) {
                                            case 'pending': echo 'Đang chờ'; break;
                                            case 'approved': echo 'Đã duyệt'; break;
                                            case 'rejected': echo 'Từ chối'; break;
                                        }
                                        ?>
                                    </span>
                                    <small style="color: #657786;"><?php echo timeAgo($request['created_at']); ?></small>
                                </div>
                            </div>
                            
                            <div style="margin: 15px 0;">
                                <strong>Lý do:</strong><br>
                                <div style="background: #f7f9fa; padding: 10px; border-radius: 6px; margin: 5px 0;">
                                    <?php echo nl2br(htmlspecialchars($request['reason'])); ?>
                                </div>
                            </div>
                            
                            <?php if ($request['social_link']): ?>
                                <div style="margin: 10px 0;">
                                    <strong>Liên kết:</strong> 
                                    <a href="<?php echo htmlspecialchars($request['social_link']); ?>" target="_blank" style="color: #1da1f2;">
                                        <?php echo htmlspecialchars($request['social_link']); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($request['email_proof']): ?>
                                <div style="margin: 10px 0;">
                                    <strong>Email:</strong> <?php echo htmlspecialchars($request['email_proof']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($request['status'] == 'pending'): ?>
                                <div class="request-actions">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                        <button type="submit" name="approve_request" class="btn btn-success" onclick="return confirm('Xác nhận duyệt yêu cầu này?')">
                                            ✅ Duyệt
                                        </button>
                                    </form>
                                    
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                        <button type="submit" name="reject_request" class="btn btn-danger" onclick="return confirm('Xác nhận từ chối yêu cầu này?')">
                                            ❌ Từ chối
                                        </button>
                                    </form>
                                    
                                    <a href="profile.php?user=<?php echo $request['username']; ?>" class="btn btn-secondary" target="_blank">
                                        👁️ Xem trang cá nhân
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Danh sách user đã xác nhận -->
            <div class="admin-card">
                <h3>✅ Tài khoản đã xác nhận</h3>
                
                <?php if (empty($verified_users_list)): ?>
                    <p style="text-align: center; color: #657786; margin: 30px 0;">Chưa có tài khoản nào được xác nhận.</p>
                <?php else: ?>
                    <?php foreach ($verified_users_list as $user): ?>
                        <div class="request-item">
                            <div class="request-header">
                                <div class="request-user">
                                    <img src="assets/uploads/avatars/<?php echo $user['avatar']; ?>" alt="Avatar" class="post-avatar" onerror="this.src='assets/uploads/avatars/default_avatar.jpg'">
                                    <div>
                                        <span><?php echo htmlspecialchars($user['full_name']); ?></span>
                                        <?php echo getVerifyTick($user); ?>
                                        <br>
                                        <small>@<?php echo htmlspecialchars($user['username']); ?></small>
                                        <?php if ($user['is_admin']): ?>
                                            <br><small style="color: #ff6600; font-weight: 600;">👑 Quản trị viên</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <small style="color: #657786;">Tham gia: <?php echo date('d/m/Y', strtotime($user['created_at'])); ?></small>
                                </div>
                            </div>
                            
                            <?php if (!empty($user['bio'])): ?>
                                <div style="margin: 10px 0; color: #657786;">
                                    <?php echo nl2br(htmlspecialchars($user['bio'])); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="request-actions">
                                <a href="profile.php?user=<?php echo $user['username']; ?>" class="btn btn-secondary" target="_blank">
                                    👁️ Xem trang cá nhân
                                </a>
                                
                                <?php if (!$user['is_admin']): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="revoke_verification" class="btn btn-danger" onclick="return confirm('Xác nhận thu hồi xác nhận tài khoản này?')">
                                            🚫 Thu hồi xác nhận
                                        </button>
                                    </form>
                                <?php endif; ?>
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
