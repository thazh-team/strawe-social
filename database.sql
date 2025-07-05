-- Database: thazh_social
-- Mạng xã hội Thazh Social

CREATE DATABASE IF NOT EXISTS thazh_social CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE thazh_social;

-- Bảng users (người dùng)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    avatar VARCHAR(255) DEFAULT 'default_avatar.jpg',
    bio TEXT,
    followers_count INT DEFAULT 0,
    following_count INT DEFAULT 0,
    likes_count INT DEFAULT 0,
    is_verified BOOLEAN DEFAULT FALSE,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng posts (bài viết)
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255),
    audio VARCHAR(255),
    likes_count INT DEFAULT 0,
    comments_count INT DEFAULT 0,
    shares_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Bảng likes (lượt thích)
CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    UNIQUE KEY unique_like (user_id, post_id)
);

-- Bảng follows (theo dõi)
CREATE TABLE follows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    follower_id INT NOT NULL,
    following_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_follow (follower_id, following_id)
);

-- Bảng comments (bình luận)
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- Bảng verify_requests (yêu cầu xác minh tài khoản)
CREATE TABLE verify_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email VARCHAR(100) NOT NULL,
    social_links TEXT,
    reason TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Bảng hot_searches (tìm kiếm hot)
CREATE TABLE hot_searches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    keyword VARCHAR(100) NOT NULL,
    search_count INT DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng hashtags
CREATE TABLE hashtags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    usage_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng post_hashtags (liên kết bài viết với hashtag)
CREATE TABLE post_hashtags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    hashtag_id INT NOT NULL,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (hashtag_id) REFERENCES hashtags(id) ON DELETE CASCADE,
    UNIQUE KEY unique_post_hashtag (post_id, hashtag_id)
);

-- Tạo admin mặc định
INSERT INTO users (username, email, password, full_name, is_admin, is_verified) 
VALUES ('admin', 'admin@thazh.social', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Thazh Admin', TRUE, TRUE);

-- Tạo một số từ khóa hot search mặc định
INSERT INTO hot_searches (keyword, search_count) VALUES 
('Thazh Social', 100),
('mạng xã hội', 85),
('bạn bè', 70),
('chia sẻ', 65),
('kết nối', 60),
('hashtag', 55),
('trending', 50);

-- Tạo indexes để tối ưu performance
CREATE INDEX idx_posts_user_id ON posts(user_id);
CREATE INDEX idx_posts_created_at ON posts(created_at);
CREATE INDEX idx_likes_user_post ON likes(user_id, post_id);
CREATE INDEX idx_follows_follower ON follows(follower_id);
CREATE INDEX idx_follows_following ON follows(following_id);
CREATE INDEX idx_comments_post_id ON comments(post_id);
CREATE INDEX idx_verify_requests_status ON verify_requests(status);
CREATE INDEX idx_hot_searches_count ON hot_searches(search_count DESC);

-- Tạo triggers để tự động cập nhật counts
DELIMITER //

CREATE TRIGGER update_likes_count_insert 
AFTER INSERT ON likes 
FOR EACH ROW 
BEGIN
    UPDATE posts SET likes_count = likes_count + 1 WHERE id = NEW.post_id;
    UPDATE users SET likes_count = likes_count + 1 WHERE id = (SELECT user_id FROM posts WHERE id = NEW.post_id);
END//

CREATE TRIGGER update_likes_count_delete 
AFTER DELETE ON likes 
FOR EACH ROW 
BEGIN
    UPDATE posts SET likes_count = likes_count - 1 WHERE id = OLD.post_id;
    UPDATE users SET likes_count = likes_count - 1 WHERE id = (SELECT user_id FROM posts WHERE id = OLD.post_id);
END//

CREATE TRIGGER update_followers_count_insert 
AFTER INSERT ON follows 
FOR EACH ROW 
BEGIN
    UPDATE users SET followers_count = followers_count + 1 WHERE id = NEW.following_id;
    UPDATE users SET following_count = following_count + 1 WHERE id = NEW.follower_id;
END//

CREATE TRIGGER update_followers_count_delete 
AFTER DELETE ON follows 
FOR EACH ROW 
BEGIN
    UPDATE users SET followers_count = followers_count - 1 WHERE id = OLD.following_id;
    UPDATE users SET following_count = following_count - 1 WHERE id = OLD.follower_id;
END//

DELIMITER ;