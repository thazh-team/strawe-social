<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = sanitize($_POST['content']);
    $user_id = $_SESSION['user_id'];
    
    if (empty($content)) {
        $error = 'Vui lòng nhập nội dung bài viết';
    } else {
        $image = '';
        $audio = '';
        
        // Upload hình ảnh
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_image_types = ['jpg', 'jpeg', 'png', 'gif'];
            $image_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            
            if (in_array($image_extension, $allowed_image_types)) {
                if ($_FILES['image']['size'] <= 5000000) { // 5MB
                    $image = uploadFile($_FILES['image'], 'posts');
                    if (!$image) {
                        $error = 'Có lỗi khi upload hình ảnh';
                    }
                } else {
                    $error = 'Hình ảnh quá lớn (tối đa 5MB)';
                }
            } else {
                $error = 'Định dạng hình ảnh không được hỗ trợ';
            }
        }
        
        // Upload âm thanh
        if (empty($error) && isset($_FILES['audio']) && $_FILES['audio']['error'] == 0) {
            $allowed_audio_types = ['mp3', 'wav', 'ogg'];
            $audio_extension = strtolower(pathinfo($_FILES['audio']['name'], PATHINFO_EXTENSION));
            
            if (in_array($audio_extension, $allowed_audio_types)) {
                if ($_FILES['audio']['size'] <= 10000000) { // 10MB
                    $audio = uploadFile($_FILES['audio'], 'posts');
                    if (!$audio) {
                        $error = 'Có lỗi khi upload âm thanh';
                    }
                } else {
                    $error = 'File âm thanh quá lớn (tối đa 10MB)';
                }
            } else {
                $error = 'Định dạng âm thanh không được hỗ trợ';
            }
        }
        
        if (empty($error)) {
            $stmt = $pdo->prepare("INSERT INTO posts (user_id, content, image, audio) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$user_id, $content, $image, $audio])) {
                $success = 'Đăng bài viết thành công!';
                $_POST = []; // Clear form
            } else {
                $error = 'Có lỗi xảy ra, vui lòng thử lại';
            }
        }
    }
}

$current_user = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng bài viết - Strawe</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <div class="card" style="max-width: 600px; margin: 0 auto;">
                <h2>Đăng bài viết mới</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                        <br><a href="index.php">Quay về trang chủ</a>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                            <img src="assets/uploads/avatars/<?php echo $current_user['avatar']; ?>" alt="Avatar" class="post-avatar" onerror="this.src='assets/uploads/avatars/default_avatar.jpg'">
                            <div>
                                <strong><?php echo htmlspecialchars($current_user['full_name']); ?></strong>
                                <?php echo getVerifyTick($current_user); ?>
                                <br>
                                <small>@<?php echo htmlspecialchars($current_user['username']); ?></small>
                            </div>
                        </div>
                        
                        <textarea class="form-control" name="content" placeholder="Bạn đang nghĩ gì?" rows="4" style="resize: vertical;" required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Hình ảnh (tùy chọn):</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small style="color: #657786;">Hỗ trợ: JPG, PNG, GIF. Tối đa 5MB.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="audio">Âm thanh (tùy chọn):</label>
                        <input type="file" class="form-control" id="audio" name="audio" accept="audio/*">
                        <small style="color: #657786;">Hỗ trợ: MP3, WAV, OGG. Tối đa 10MB.</small>
                    </div>
                    
                    <div style="display: flex; gap: 15px;">
                        <button type="submit" class="btn btn-primary">📝 Đăng bài</button>
                        <a href="index.php" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
    <script>
        // Preview ảnh khi chọn file
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = document.getElementById('image-preview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.id = 'image-preview';
                        preview.style.maxWidth = '100%';
                        preview.style.marginTop = '10px';
                        preview.style.borderRadius = '8px';
                        document.getElementById('image').parentNode.appendChild(preview);
                    }
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
