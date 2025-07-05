<?php
require_once 'includes/auth.php';
$current_user = getCurrentUser();
?>

<nav class="navbar">
    <div class="nav-container">
        <div class="nav-brand">
            <a href="index.php">
                <svg class="brand-logo" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 2L29 9V23L16 30L3 23V9L16 2Z" stroke="currentColor" stroke-width="2" fill="none"/>
                    <path d="M16 10L23 14V22L16 26L9 22V14L16 10Z" fill="currentColor"/>
                </svg>
                <h2>Thazh Social</h2>
            </a>
        </div>
        
        <div class="nav-menu">
            <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" 
               title="Trang chủ" aria-label="Trang chủ">
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 22V12H15V22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="desktop-only">Trang chủ</span>
            </a>
            
            <a href="search.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'search.php' ? 'active' : ''; ?>" 
               title="Tìm kiếm" aria-label="Tìm kiếm">
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                    <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="desktop-only">Tìm kiếm</span>
            </a>
            
            <a href="newpost.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'newpost.php' ? 'active' : ''; ?>" 
               title="Đăng bài" aria-label="Đăng bài">
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18.5 2.49998C18.8978 2.10216 19.4374 1.87866 20 1.87866C20.5626 1.87866 21.1022 2.10216 21.5 2.49998C21.8978 2.89781 22.1213 3.43737 22.1213 3.99998C22.1213 4.56259 21.8978 5.10216 21.5 5.49998L12 15L8 16L9 12L18.5 2.49998Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="desktop-only">Đăng bài</span>
            </a>
            
            <a href="profile.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>" 
               title="Trang cá nhân" aria-label="Trang cá nhân">
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="desktop-only">Trang cá nhân</span>
            </a>
            
            <a href="settings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" 
               title="Cài đặt" aria-label="Cài đặt">
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                    <path d="M19.4 15C19.2669 15.3016 19.2272 15.6362 19.286 15.9606C19.3448 16.285 19.4995 16.5843 19.73 16.82L19.79 16.88C19.976 17.0657 20.1235 17.2863 20.2241 17.5291C20.3248 17.7719 20.3766 18.0322 20.3766 18.295C20.3766 18.5578 20.3248 18.8181 20.2241 19.0609C20.1235 19.3037 19.976 19.5243 19.79 19.71C19.6043 19.896 19.3837 20.0435 19.1409 20.1441C18.8981 20.2448 18.6378 20.2966 18.375 20.2966C18.1122 20.2966 17.8519 20.2448 17.6091 20.1441C17.3663 20.0435 17.1457 19.896 16.96 19.71L16.9 19.65C16.6643 19.4195 16.365 19.2648 16.0406 19.206C15.7162 19.1472 15.3816 19.1869 15.08 19.32C14.7842 19.4468 14.532 19.6572 14.3543 19.9255C14.1766 20.1938 14.0813 20.5082 14.08 20.83V21C14.08 21.5304 13.8693 22.0391 13.4942 22.4142C13.1191 22.7893 12.6104 23 12.08 23C11.5496 23 11.0409 22.7893 10.6658 22.4142C10.2907 22.0391 10.08 21.5304 10.08 21V20.91C10.0723 20.579 9.96512 20.2573 9.77251 19.9887C9.5799 19.7201 9.31074 19.5176 9 19.405C8.69838 19.2719 8.36381 19.2322 8.03941 19.291C7.71502 19.3498 7.41568 19.5045 7.18 19.735L7.12 19.795C6.93425 19.981 6.71368 20.1285 6.47088 20.2291C6.22808 20.3298 5.96783 20.3816 5.705 20.3816C5.44217 20.3816 5.18192 20.3298 4.93912 20.2291C4.69632 20.1285 4.47575 19.981 4.29 19.795C4.10405 19.6093 3.95653 19.3887 3.85588 19.1459C3.75523 18.9031 3.70343 18.6428 3.70343 18.38C3.70343 18.1172 3.75523 17.8569 3.85588 17.6141C3.95653 17.3713 4.10405 17.1507 4.29 16.965L4.35 16.905C4.58054 16.6693 4.73519 16.37 4.794 16.0456C4.85282 15.7212 4.81312 15.3866 4.68 15.085C4.55324 14.7892 4.34276 14.537 4.07447 14.3593C3.80618 14.1816 3.49179 14.0863 3.17 14.085H3C2.46957 14.085 1.96086 13.8743 1.58579 13.4992C1.21071 13.1241 1 12.6154 1 12.085C1 11.5546 1.21071 11.0459 1.58579 10.6708C1.96086 10.2957 2.46957 10.085 3 10.085H3.09C3.42099 10.0773 3.742716 9.97012 4.01131 9.77751C4.27989 9.5849 4.48244 9.31574 4.595 9.005C4.72812 8.70338 4.76782 8.36881 4.709 8.04442C4.65019 7.72002 4.49554 7.42068 4.265 7.185L4.205 7.125C4.01905 6.93925 3.87153 6.71868 3.77088 6.47588C3.67023 6.23308 3.61843 5.97283 3.61843 5.71C3.61843 5.44717 3.67023 5.18692 3.77088 4.94412C3.87153 4.70132 4.01905 4.48075 4.205 4.295C4.39075 4.10905 4.61132 3.96153 4.85412 3.86088C5.09692 3.76023 5.35717 3.70843 5.62 3.70843C5.88283 3.70843 6.14308 3.76023 6.38588 3.86088C6.62868 3.96153 6.84925 4.10905 7.035 4.295L7.095 4.355C7.33068 4.58554 7.63002 4.74019 7.95442 4.799C8.27881 4.85782 8.61338 4.81812 8.915 4.685H9C9.29577 4.55824 9.54802 4.34776 9.72569 4.07947C9.90337 3.81118 9.99872 3.49679 10 3.175V3C10 2.46957 10.2107 1.96086 10.5858 1.58579C10.9609 1.21071 11.4696 1 12 1C12.5304 1 13.0391 1.21071 13.4142 1.58579C13.7893 1.96086 14 2.46957 14 3V3.09C14.0013 3.41179 14.0966 3.72618 14.2743 3.99447C14.452 4.26276 14.7042 4.47324 15 4.6C15.3016 4.73312 15.6362 4.77282 15.9606 4.714C16.285 4.65519 16.5843 4.50054 16.82 4.27L16.88 4.21C17.0657 4.02405 17.2863 3.87653 17.5291 3.77588C17.7719 3.67523 18.0322 3.62343 18.295 3.62343C18.5578 3.62343 18.8181 3.67523 19.0609 3.77588C19.3037 3.87653 19.5243 4.02405 19.71 4.21C19.896 4.39575 20.0435 4.61632 20.1441 4.85912C20.2448 5.10192 20.2966 5.36217 20.2966 5.625C20.2966 5.88783 20.2448 6.14808 20.1441 6.39088C20.0435 6.63368 19.896 6.85425 19.71 7.04L19.65 7.1C19.4195 7.33568 19.2648 7.63502 19.206 7.95942C19.1472 8.28381 19.1869 8.61838 19.32 8.92C19.4468 9.21577 19.6572 9.46802 19.9255 9.64569C20.1938 9.82337 20.5082 9.91872 20.83 9.92H21C21.5304 9.92 22.0391 10.1307 22.4142 10.5058C22.7893 10.8809 23 11.3896 23 11.92C23 12.4504 22.7893 12.9591 22.4142 13.3342C22.0391 13.7093 21.5304 13.92 21 13.92H20.91C20.5882 13.9213 20.2738 14.0166 20.0055 14.1943C19.7372 14.372 19.5267 14.6242 19.4 14.92V15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="desktop-only">Cài đặt</span>
            </a>
            
            <?php if (isAdmin()): ?>
            <a href="admin_dashboard.php" class="nav-link admin-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : ''; ?>" 
               title="Quản trị" aria-label="Quản trị">
                <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9.09 9C9.3251 8.33167 9.78915 7.76811 10.4 7.40913C11.0108 7.05016 11.7289 6.91894 12.4272 7.03871C13.1255 7.15849 13.7588 7.52152 14.2151 8.06353C14.6713 8.60553 14.9211 9.29152 14.92 10C14.92 12 11.92 13 11.92 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 17H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="desktop-only">Quản trị</span>
            </a>
            <?php endif; ?>
        </div>
        
        <div class="nav-user">
            <?php if ($current_user): ?>
                <div class="user-info desktop-only">
                    <span class="username">
                        <?php echo getVerifyTick($current_user); ?>
                        <span><?php echo htmlspecialchars($current_user['full_name']); ?></span>
                    </span>
                </div>
                <div class="mobile-only">
                    <a href="profile.php" class="nav-link" title="<?php echo htmlspecialchars($current_user['full_name']); ?>">
                        <img src="assets/uploads/avatars/<?php echo $current_user['avatar']; ?>" 
                             alt="Avatar" 
                             class="nav-avatar" 
                             onerror="this.src='assets/uploads/avatars/default_avatar.jpg'">
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
.nav-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #1da1f2;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.user-info .username {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 14px;
    color: #0f1419;
}

@media (max-width: 768px) {
    .nav-user .desktop-only {
        display: none;
    }
    
    .nav-user .mobile-only {
        display: block;
    }
}

@media (min-width: 769px) {
    .nav-user .mobile-only {
        display: none;
    }
    
    .nav-user .desktop-only {
        display: block;
    }
}
</style>
