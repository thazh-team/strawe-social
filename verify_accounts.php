<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$current_user = getCurrentUser();
$error = '';
$success = '';

// Kiểm tra đã có yêu cầu pending chưa
$stmt = $pdo->prepare("SELECT * FROM verify_requests WHERE user_id = ? AND status = 'pending'");
$stmt->execute([$current_user['id']]);
$pending_request = $stmt->fetch(PDO::FETCH_ASSOC);

// Kiểm tra đã verified chưa
if ($current_user['is_verified']) {
    header('Location: settings.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $social_link = sanitize($_POST['social_link']);
    $email_proof = sanitize($_POST['email_proof']);
    $reason = sanitize($_POST['reason']);
    
    if (empty($reason)) {
        $error = 'Vui lòng nhập lý do xác nhận tài khoản';
    } elseif (empty($social_link) && empty($email_proof)) {
        $error = 'Vui lòng cung cấp ít nhất một liên kết mạng xã hội hoặc bài báo';
    } else {
        // Kiểm tra đã có yêu cầu pending chưa
        if ($pending_request) {
            $error = 'Bạn đã có yêu cầu đang chờ xử lý';
        } else {
            $stmt = $pdo->prepare("INSERT INTO verify_requests (user_id, social_link, email_proof, reason) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$current_user['id'], $social_link, $email_proof, $reason])) {
                $success = 'Gửi yêu cầu xác nhận thành công! Chúng tôi sẽ xem xét và phản hồi trong vòng 24-48 giờ.';
                $_POST = []; // Clear form
                
                // Refresh pending request
                $stmt = $pdo->prepare("SELECT * FROM verify_requests WHERE user_id = ? AND status = 'pending'");
                $stmt->execute([$current_user['id']]);
                $pending_request = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = 'Có lỗi xảy ra, vui lòng thử lại';
            }
        }
    }
}

// Lấy lịch sử yêu cầu
$stmt = $pdo->prepare("SELECT * FROM verify_requests WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$current_user['id']]);
$request_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận tài khoản - Strawe</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <div style="max-width: 700px; margin: 0 auto;">
                <h2>🔰 Xác nhận tài khoản</h2>
                
                <div class="card">
                    <h3>Tại sao nên xác nhận tài khoản?</h3>
                    <ul style="color: #657786; line-height: 1.8;">
                        <li>Nhận dấu tick xanh xác thực bên cạnh tên</li>
                        <li>Tăng độ tin cậy và uy tín trên nền tảng</li>
                        <li>Được ưu tiên hiển thị trong kết quả tìm kiếm</li>
                        <li>Bảo vệ tài khoản khỏi việc mạo danh</li>
                    </ul>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if (!$pending_request): ?>
                    <div class="card">
                        <h3>Gửi yêu cầu xác nhận</h3>
                        
                        <form method="POST">
                            <div class="form-group">
                                <label for="social_link">Liên kết mạng xã hội hoặc bài báo về bạn:</label>
                                <input type="url" class="form-control" id="social_link" name="social_link" placeholder="https://facebook.com/yourprofile hoặc link bài báo" value="<?php echo isset($_POST['social_link']) ? htmlspecialchars($_POST['social_link']) : ''; ?>">
                                <small style="color: #657786;">Ví dụ: Facebook, Instagram, LinkedIn, Wikipedia, bài báo trên báo chí...</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="email_proof">Email liên hệ để xác nhận:</label>
                                <input type="email" class="form-control" id="email_proof" name="email_proof" placeholder="email@example.com" value="<?php echo isset($_POST['email_proof']) ? htmlspecialchars($_POST['email_proof']) : ''; ?>">
                                <small style="color: #657786;">Email công việc hoặc email có thể liên hệ trực tiếp</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="reason">Lý do xác nhận tài khoản: *</label>
                                <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="Ví dụ: Tôi là người nổi tiếng, nhà báo, doanh nhân, hoặc có ảnh hưởng trong lĩnh vực..." required><?php echo isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : ''; ?></textarea>
                                <small style="color: #657786;">Hãy mô tả rõ ràng lý do tại sao bạn cần được xác nhận tài khoản</small>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">📤 Gửi yêu cầu</button>
                            <a href="settings.php" class="btn btn-secondary">Quay lại</a>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <h3>⏳ Yêu cầu đang chờ xử lý</h3>
                        <p>Bạn đã gửi yêu cầu xác nhận tài khoản vào <strong><?php echo date('d/m/Y H:i', strtotime($pending_request['created_at'])); ?></strong></p>
                        <p style="color: #657786;">Chúng tôi sẽ xem xét và phản hồi trong vòng 24-48 giờ. Vui lòng kiên nhẫn chờ đợi.</p>
                        
                        <div style="background: #f7f9fa; padding: 15px; border-radius: 8px; margin: 15px 0;">
                            <strong>Thông tin yêu cầu:</strong><br>
                            <?php if ($pending_request['social_link']): ?>
                                <strong>Liên kết:</strong> <a href="<?php echo htmlspecialchars($pending_request['social_link']); ?>" target="_blank"><?php echo htmlspecialchars($pending_request['social_link']); ?></a><br>
                            <?php endif; ?>
                            <?php if ($pending_request['email_proof']): ?>
                                <strong>Email:</strong> <?php echo htmlspecialchars($pending_request['email_proof']); ?><br>
                            <?php endif; ?>
                            <strong>Lý do:</strong> <?php echo nl2br(htmlspecialchars($pending_request['reason'])); ?>
                        </div>
                        
                        <a href="settings.php" class="btn btn-secondary">Quay lại cài đặt</a>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($request_history)): ?>
                    <div class="card">
                        <h3>Lịch sử yêu cầu</h3>
                        
                        <?php foreach ($request_history as $request): ?>
                            <div class="request-item">
                                <div class="request-header">
                                    <span>Gửi vào: <?php echo date('d/m/Y H:i', strtotime($request['created_at'])); ?></span>
                                    <span class="request-status status-<?php echo $request['status']; ?>">
                                        <?php 
                                        switch($request['status']) {
                                            case 'pending': echo 'Đang chờ'; break;
                                            case 'approved': echo 'Đã duyệt'; break;
                                            case 'rejected': echo 'Từ chối'; break;
                                        }
                                        ?>
                                    </span>
                                </div>
                                
                                <div style="margin: 10px 0; color: #657786;">
                                    <?php echo nl2br(htmlspecialchars($request['reason'])); ?>
                                </div>
                                
                                <?php if ($request['social_link']): ?>
                                    <div style="font-size: 14px; color: #657786;">
                                        Liên kết: <a href="<?php echo htmlspecialchars($request['social_link']); ?>" target="_blank"><?php echo htmlspecialchars($request['social_link']); ?></a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
