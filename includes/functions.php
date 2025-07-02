<?php
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'Vừa xong';
    if ($time < 3600) return floor($time/60) . ' phút trước';
    if ($time < 86400) return floor($time/3600) . ' giờ trước';
    if ($time < 2592000) return floor($time/86400) . ' ngày trước';
    if ($time < 31536000) return floor($time/2592000) . ' tháng trước';
    return floor($time/31536000) . ' năm trước';
}

function getVerifyTick($user) {
    if (!$user['is_verified']) return '';
    
    if ($user['is_admin']) {
        return '<svg class="verify-tick admin-tick" width="16" height="16" viewBox="0 0 24 24">
                <path fill="#ff6600" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>';
    } else {
        return '<svg class="verify-tick" width="16" height="16" viewBox="0 0 24 24">
                <path fill="#1da1f2" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>';
    }
}

function uploadFile($file, $folder) {
    $target_dir = "assets/uploads/$folder/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $new_filename;
    }
    return false;
}
?>
