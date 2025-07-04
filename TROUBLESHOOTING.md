# Strawe - Mobile Optimization Troubleshooting Guide

## Quick Error Diagnosis

### 🚀 **Quick Fix Steps**

1. **Run the Error Checker**: Visit `fix-errors.php` in your browser for automated diagnosis
2. **Check Upload Directories**: Ensure `assets/uploads/` folders exist and are writable
3. **Verify Database Connection**: Make sure MySQL is running and database exists
4. **Clear Browser Cache**: Force refresh with Ctrl+F5 (or Cmd+Shift+R on Mac)

### 📋 **Common Issues & Solutions**

#### **1. Blank Page or PHP Errors**
```
Solution:
- Check PHP error logs
- Ensure PHP 7.0+ is installed
- Verify all required PHP extensions are loaded
```

#### **2. CSS/JS Not Loading**
```
Solution:
- Check file paths in assets/ directory
- Verify web server has read permissions
- Clear browser cache and force refresh
```

#### **3. Images Not Displaying**
```
Solution:
- Create upload directories: assets/uploads/avatars/ and assets/uploads/posts/
- Set proper permissions (755 or 777)
- Ensure default_avatar.jpg exists
```

#### **4. Database Connection Errors**
```
Solution:
- Start MySQL/MariaDB service
- Create database 'strawe'
- Import database schema
- Check credentials in config/database.php
```

#### **5. Mobile Interface Issues**
```
Solution:
- Ensure viewport meta tag is present
- Check CSS media queries are loading
- Verify JavaScript is enabled
- Test on actual mobile device
```

### 🛠️ **File Structure Check**

Ensure these directories exist:
```
/
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── uploads/
│       ├── avatars/
│       │   └── default_avatar.jpg
│       └── posts/
├── config/
│   └── database.php
├── includes/
│   ├── auth.php
│   ├── functions.php
│   └── navbar.php
└── *.php files
```

### 📱 **Mobile Testing Checklist**

- [ ] Navigation icons are touch-friendly (44px minimum)
- [ ] Text is readable without zooming
- [ ] Forms work with on-screen keyboard
- [ ] Images scale properly
- [ ] Performance is smooth on mobile
- [ ] No horizontal scrolling

### 🔧 **Server Requirements**

- **PHP**: 7.0 or higher
- **MySQL**: 5.6 or higher (or MariaDB equivalent)
- **Extensions**: PDO, PDO_MySQL, GD, mbstring
- **Web Server**: Apache or Nginx with URL rewriting

### 🐛 **Debug Mode**

Add to the top of any PHP file for debugging:
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
```

### 📞 **Getting Help**

1. **Check Error Logs**: Look in PHP error logs and browser console
2. **Run Diagnostics**: Use the `fix-errors.php` script
3. **Test Incrementally**: Start with basic pages, then add features
4. **Mobile Testing**: Use browser dev tools mobile simulation

### 🔍 **Performance Monitoring**

Use browser developer tools to check:
- Network tab for loading issues
- Console for JavaScript errors
- Performance tab for mobile optimization
- Application tab for cache issues

### ⚡ **Quick Commands**

```bash
# Check PHP syntax
php -l filename.php

# Set directory permissions
chmod 755 assets/uploads/ -R

# Check web server status
systemctl status apache2  # or nginx

# Check MySQL status
systemctl status mysql
```

---

**Note**: The mobile optimizations include automatic error handling and graceful degradation for better user experience across all devices.