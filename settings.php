<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$current_user = getCurrentUser();
$error = '';
$success = '';

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = sanitize($_POST['full_name']);
        $bio = sanitize($_POST['bio']);
        $new_email = sanitize($_POST['email']);
        
        if (empty($full_name)) {
            $error = 'Vui lòng nhập họ và tên';
        } else {
            // Kiểm tra email đã tồn tại
            if ($new_email != $current_user['email']) {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$new_email, $current_user['id']]);
                if ($stmt->fetch()) {
                    $error = 'Email này đã được sử dụng';
                }
            }
            
            if (empty($error)) {
                // Upload avatar nếu có
                $avatar = $current_user['avatar'];
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                    $file_extension = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                    
                    if (in_array($file_extension, $allowed_types)) {
                        if ($_FILES['avatar']['size'] <= 2000000) { // 2MB
                            $new_avatar = uploadFile($_FILES['avatar'], 'avatars');
                            if ($new_avatar) {
                                // Xóa avatar cũ
                                if ($avatar != 'default_avatar.jpg' && file_exists("assets/uploads/avatars/$avatar")) {
                                    unlink("assets/uploads/avatars/$avatar");
                                }
                                $avatar = $new_avatar;
                            }
                        } else {
                            $error = 'Avatar quá lớn (tối đa 2MB)';
                        }
                    } else {
                        $error = 'Định dạng avatar không được hỗ trợ';
                    }
                }
                
                if (empty($error)) {
                    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, bio = ?, email = ?, avatar = ? WHERE id = ?");
                    if ($stmt->execute([$full_name, $bio, $new_email, $avatar, $current_user['id']])) {
                        $success = 'Cập nhật thông tin thành công!';
                        $current_user = getCurrentUser(); // Refresh user data
                    } else {
                        $error = 'Có lỗi xảy ra, vui lòng thử lại';
                    }
                }
            }
        }
    }
    
    // Xử lý đổi mật khẩu
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = 'Vui lòng nhập đầy đủ thông tin mật khẩu';
        } elseif (!password_verify($current_password, $current_user['password'])) {
            $error = 'Mật khẩu hiện tại không đúng';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Mật khẩu xác nhận không khớp';
        } elseif (strlen($new_password) < 6) {
            $error = 'Mật khẩu mới phải có ít nhất 6 ký tự';
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($stmt->execute([$hashed_password, $current_user['id']])) {
                $success = 'Đổi mật khẩu thành công!';
            } else {
                $error = 'Có lỗi xảy ra, vui lòng thử lại';
            }
        }
    }
    
    // Xử lý bật/tắt verify tick
    if (isset($_POST['toggle_verified']) && $current_user['is_verified']) {
        $new_status = $current_user['is_verified'] ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE users SET is_verified = ? WHERE id = ?");
        if ($stmt->execute([$new_status, $current_user['id']])) {
            $success = $new_status ? 'Đã bật verify tick' : 'Đã tắt verify tick';
            $current_user = getCurrentUser();
        }
    }
}

// Xử lý đăng xuất
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: signin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt - Strawe</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <div style="max-width: 800px; margin: 0 auto;">
                <h2>Cài đặt tài khoản</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <!-- Cài đặt tài khoản -->
                <div class="card">
                    <h3>Thông tin cá nhân</h3>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Avatar hiện tại:</label>
                            <div style="margin: 10px 0;">
                                <img src="assets/uploads/avatars/<?php echo $current_user['avatar']; ?>" alt="Avatar" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;" onerror="this.src='assets/uploads/avatars/default_avatar.jpg'">
                            </div>
                            <input type="file" class="form-control" name="avatar" accept="image/*">
                            <small style="color: #657786;">JPG, PNG, GIF. Tối đa 2MB.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="full_name">Họ và tên:</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($current_user['full_name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($current_user['email']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="bio">Giới thiệu bản thân:</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3" placeholder="Viết vài dòng về bản thân..."><?php echo htmlspecialchars($current_user['bio']); ?></textarea>
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn btn-primary">Cập nhật thông tin</button>
                    </form>
                </div>
                
                <!-- Đổi mật khẩu -->
                <div class="card">
                    <h3>Đổi mật khẩu</h3>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label for="current_password">Mật khẩu hiện tại:</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">Mật khẩu mới:</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Xác nhận mật khẩu mới:</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" name="change_password" class="btn btn-primary">Đổi mật khẩu</button>
                    </form>
                </div>
                
                <!-- Xác nhận tài khoản -->
                <div class="card">
                    <h3>Xác nhận tài khoản</h3>
                    
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                        <span>Trạng thái: </span>
                        <?php if ($current_user['is_verified']): ?>
                            <span style="color: #17bf63; font-weight: 600;">
                                ✓ Đã xác nhận <?php echo getVerifyTick($current_user); ?>
                            </span>
                        <?php else: ?>
                            <span style="color: #657786;">Chưa xác nhận</span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!$current_user['is_verified']): ?>
                        <a href="verify_accounts.php" class="btn btn-primary">Gửi yêu cầu xác nhận tài khoản</a>
                    <?php else: ?>
                        <p style="color: #657786;">Tài khoản của bạn đã được xác nhận. Cảm ơn bạn đã tin tưởng Strawe!</p>
                        
                        <?php if (!$current_user['is_admin']): ?>
                            <form method="POST" style="margin-top: 15px;">
                                <button type="submit" name="toggle_verified" class="btn btn-secondary" onclick="return confirm('Bạn có chắc muốn tắt verify tick không?')">
                                    Tắt verify tick
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Cài đặt khác -->
                <div class="card">
                    <h3>Cài đặt khác</h3>
                    
                    <div style="padding: 20px 0; border-bottom: 1px solid #e1e8ed;">
                        <strong>Thông tin tài khoản</strong>
                        <p style="color: #657786; margin: 5px 0;">Username: @<?php echo htmlspecialchars($current_user['username']); ?></p>
                        <p style="color: #657786; margin: 5px 0;">Tham gia: <?php echo date('d/m/Y', strtotime($current_user['created_at'])); ?></p>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <a href="?logout=1" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn đăng xuất không?')">
                            🚪 Đăng xuất
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
