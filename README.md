# Strawe - Mạng xã hội PHP đơn giản

**Strawe** là một mạng xã hội nhỏ gọn được phát triển bằng PHP thuần, dành cho mục đích học tập và phát triển cá nhân. Dự án bao gồm các tính năng cơ bản như đăng ký, đăng nhập, đăng bài, trang cá nhân, tìm kiếm, xác minh tài khoản, và bảng điều khiển admin.

---

## 📂 Cấu trúc dự án

strawe-social/ ├── assets/               # Tài nguyên: ảnh, CSS, JS │   ├── css/style.css     # Giao diện chính │   ├── js/main.js        # JavaScript chính │   └── uploads/          # Ảnh đại diện và bài viết ├── config/ │   └── database.php      # Kết nối cơ sở dữ liệu ├── includes/ │   ├── auth.php          # Xử lý xác thực │   ├── functions.php     # Hàm tiện ích chung │   └── navbar.php        # Thanh điều hướng ├── index.php             # Trang chủ (New Feeds) ├── signup.php            # Trang đăng ký ├── signin.php            # Trang đăng nhập ├── logout.php            # Đăng xuất ├── newpost.php           # Đăng bài mới ├── profile.php           # Hồ sơ người dùng ├── search.php            # Tìm kiếm người dùng/bài viết ├── settings.php          # Cài đặt tài khoản ├── verify_accounts.php   # Xác minh người dùng (tick xanh) ├── admin_dashboard.php   # Bảng điều khiển Admin └── .htaccess             # Cấu hình rewrite URL

---

## ✨ Tính năng

- Đăng ký & đăng nhập tài khoản
- Trang chủ hiển thị bài viết mới
- Đăng bài với hình ảnh
- Trang cá nhân người dùng
- Tìm kiếm người dùng & bài viết
- Cài đặt tài khoản
- Xác minh tài khoản (tick xanh hoặc admin)
- Trang quản trị dành cho admin

---

## ⚙️ Yêu cầu hệ thống

- PHP 7.0 trở lên
- MySQL hoặc MariaDB
- Apache (khuyến nghị có hỗ trợ `.htaccess`)

---

## 🚀 Cài đặt

1. **Clone hoặc tải về mã nguồn**
   ```bash
   git clone https://github.com/your-username/strawe-social.git

2. Tạo cơ sở dữ liệu và import file SQL (nếu có)


3. Cấu hình kết nối database Mở config/database.php và cập nhật thông tin:

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "strawe";


4. Chạy project Trỏ trình duyệt đến thư mục chứa mã nguồn, ví dụ: http://localhost/strawe-social




---

📄 Giấy phép

Dự án được phát hành theo giấy phép MIT License.


---

📬 Liên hệ

Nếu bạn cần hỗ trợ hoặc muốn đóng góp, hãy liên hệ qua Zalo: https://zalo.me/0839365622


---

> © 2025 - Dự án Strawe. Tự do học hỏi, tự do phát triển.