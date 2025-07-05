# Hướng dẫn triển khai Thazh Social trên InfinityFree

## 📋 Chuẩn bị

### 1. Tạo tài khoản InfinityFree
- Truy cập [InfinityFree](https://infinityfree.net/)
- Đăng ký tài khoản miễn phí
- Tạo hosting account mới

### 2. Thiết lập domain
- Sử dụng subdomain miễn phí (ví dụ: yoursite.infinityfreeapp.com)
- Hoặc kết nối domain riêng của bạn

## 🚀 Triển khai

### Bước 1: Upload files
1. Mở **File Manager** trong control panel InfinityFree
2. Vào thư mục `htdocs`
3. Upload tất cả files của dự án Thazh Social vào thư mục `htdocs`

### Bước 2: Tạo database
1. Trong control panel, vào **MySQL Databases**
2. Tạo database mới (tên gợi ý: `epiz_xxxxx_thazh_social`)
3. Tạo user cho database
4. Ghi nhớ thông tin: 
   - Database name
   - Username
   - Password
   - Host (thường là sql000.infinityfree.com)

### Bước 3: Import database
1. Vào **phpMyAdmin** từ control panel
2. Chọn database vừa tạo
3. Click tab **Import**
4. Upload file `database.sql`
5. Click **Go** để thực hiện import

### Bước 4: Cấu hình database
Sửa file `config/database.php`:

```php
<?php
// Thay đổi thông tin kết nối database
$host = 'sql000.infinityfree.com'; // Hoặc host của bạn
$dbname = 'epiz_xxxxx_thazh_social'; // Tên database thực tế
$username = 'epiz_xxxxx'; // Username database
$password = 'your_password'; // Password database

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Lỗi kết nối database: " . $e->getMessage());
}
?>
```

### Bước 5: Cấu hình permissions
1. Đảm bảo thư mục `assets/uploads/` có quyền ghi
2. Tạo thư mục con: 
   - `assets/uploads/avatars/`
   - `assets/uploads/posts/`

### Bước 6: Kiểm tra
1. Truy cập website của bạn
2. Thử đăng ký tài khoản mới
3. Test upload avatar và đăng bài

## ⚙️ Cấu hình InfinityFree đặc bit

### File .htaccess
Đảm bảo file `.htaccess` có nội dung phù hợp:

```apache
RewriteEngine On

# Redirect to HTTPS (nếu có SSL)
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"

# File upload limits
php_value upload_max_filesize 5M
php_value post_max_size 5M
php_value max_execution_time 300

# Error handling
ErrorDocument 404 /404.php
```

### Cấu hình PHP
InfinityFree có một số hạn chế:
- Max file upload: 5MB
- Max execution time: 300 seconds
- Không hỗ trợ mail() function (cần dùng SMTP)

## 🔧 Tối ưu hóa

### 1. Nén hình ảnh
- Nén avatar và hình ảnh bài viết trước khi upload
- Sử dụng format WebP nếu có thể

### 2. Cache
- Sử dụng browser caching trong `.htaccess`
- Cache static files (CSS, JS, images)

### 3. Database
- Tối ưu hóa queries
- Sử dụng indexes hiệu quả

## 📱 SSL Certificate (HTTPS)
1. Trong control panel InfinityFree
2. Vào **SSL Certificates**  
3. Tạo Let's Encrypt SSL certificate miễn phí
4. Đợi 1-2 giờ để SSL active

## 🎯 Tài khoản Admin mặc định

Sau khi import database, bạn có thể đăng nhập admin:
- **Email**: admin@thazh.social
- **Password**: password (nên đổi ngay)

## 🚨 Lưu ý quan trọng

1. **Backup thường xuyên**: InfinityFree có thể xóa account không hoạt động
2. **Giới hạn bandwidth**: 20GB/tháng
3. **CPU usage**: Tránh script chạy quá lâu
4. **Database size**: Giới hạn 1GB
5. **Email**: Cần cấu hình SMTP thay vì mail() function

## 🔍 Troubleshooting

### Website không load
- Kiểm tra thông tin database trong `config/database.php`
- Xem error logs trong control panel

### Upload file lỗi
- Kiểm tra permissions thư mục `assets/uploads/`
- Đảm bảo file size < 5MB

### Database connection lỗi
- Kiểm tra host, username, password database
- Đảm bảo database đã được tạo

## 📞 Hỗ trợ

Nếu gặp vấn đề:
1. Kiểm tra [InfinityFree Forum](https://forum.infinityfree.net/)
2. Xem error logs trong control panel
3. Liên hệ support InfinityFree

---

**Chúc bạn triển khai thành công! 🎉**