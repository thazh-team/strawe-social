<?php
session_start();

// Hủy tất cả session variables
$_SESSION = array();

// Hủy session cookie nếu có
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hủy session
session_destroy();

// Redirect về trang đăng nhập
header('Location: signin.php?message=logout_success');
exit();
?>