<?php
session_start();
require_once 'config/database.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: signin.php');
        exit();
    }
}

function getCurrentUser() {
    global $pdo;
    if (!isLoggedIn()) return null;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function isAdmin() {
    $user = getCurrentUser();
    return $user && $user['is_admin'];
}
?>
