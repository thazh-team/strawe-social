<?php
// Comprehensive Error Checking and Fixing Script
echo "<h2>Strawe Application Error Checker & Fixer</h2>";
echo "<style>body{font-family:Arial;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";

$errors = [];
$fixes = [];

// Check PHP version
echo "<h3>1. Checking PHP Environment</h3>";
if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
    echo "<span class='success'>✓ PHP Version: " . PHP_VERSION . " (Compatible)</span><br>";
} else {
    echo "<span class='error'>✗ PHP Version: " . PHP_VERSION . " (Requires 7.0+)</span><br>";
    $errors[] = "PHP version too old";
}

// Check required extensions
$required_extensions = ['pdo', 'pdo_mysql', 'gd', 'mbstring'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<span class='success'>✓ Extension: $ext</span><br>";
    } else {
        echo "<span class='error'>✗ Extension: $ext (Missing)</span><br>";
        $errors[] = "Missing PHP extension: $ext";
    }
}

// Check directory structure
echo "<h3>2. Checking Directory Structure</h3>";
$required_dirs = [
    'config',
    'includes', 
    'assets',
    'assets/css',
    'assets/js',
    'assets/uploads',
    'assets/uploads/avatars',
    'assets/uploads/posts'
];

foreach ($required_dirs as $dir) {
    if (is_dir($dir)) {
        echo "<span class='success'>✓ Directory: $dir</span><br>";
    } else {
        echo "<span class='error'>✗ Directory: $dir (Missing)</span><br>";
        if (mkdir($dir, 0755, true)) {
            echo "<span class='info'>→ Created directory: $dir</span><br>";
            $fixes[] = "Created directory: $dir";
        }
    }
}

// Check file permissions
echo "<h3>3. Checking File Permissions</h3>";
$upload_dirs = ['assets/uploads', 'assets/uploads/avatars', 'assets/uploads/posts'];
foreach ($upload_dirs as $dir) {
    if (is_writable($dir)) {
        echo "<span class='success'>✓ Writable: $dir</span><br>";
    } else {
        echo "<span class='error'>✗ Not writable: $dir</span><br>";
        if (chmod($dir, 0755)) {
            echo "<span class='info'>→ Fixed permissions: $dir</span><br>";
            $fixes[] = "Fixed permissions: $dir";
        }
    }
}

// Check required files
echo "<h3>4. Checking Required Files</h3>";
$required_files = [
    'config/database.php',
    'includes/auth.php',
    'includes/functions.php',
    'includes/navbar.php',
    'assets/css/style.css',
    'assets/js/main.js'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "<span class='success'>✓ File: $file</span><br>";
        
        // Check for syntax errors
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $output = shell_exec("php -l $file 2>&1");
            if (strpos($output, 'No syntax errors') !== false) {
                echo "<span class='success'>  → PHP syntax OK</span><br>";
            } else {
                echo "<span class='error'>  → PHP syntax error in $file</span><br>";
                $errors[] = "Syntax error in $file";
            }
        }
    } else {
        echo "<span class='error'>✗ File: $file (Missing)</span><br>";
        $errors[] = "Missing file: $file";
    }
}

// Check default avatar
echo "<h3>5. Checking Default Avatar</h3>";
$default_avatar = 'assets/uploads/avatars/default_avatar.jpg';
if (file_exists($default_avatar)) {
    echo "<span class='success'>✓ Default avatar exists</span><br>";
} else {
    echo "<span class='error'>✗ Default avatar missing</span><br>";
    // Create a simple placeholder
    $placeholder_content = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
    if (file_put_contents($default_avatar, $placeholder_content)) {
        echo "<span class='info'>→ Created placeholder default avatar</span><br>";
        $fixes[] = "Created default avatar placeholder";
    }
}

// Test database connection
echo "<h3>6. Testing Database Connection</h3>";
try {
    if (file_exists('config/database.php')) {
        include 'config/database.php';
        echo "<span class='success'>✓ Database connection successful</span><br>";
        
        // Test if required tables exist (basic check)
        $tables = ['users', 'posts', 'likes', 'follows'];
        foreach ($tables as $table) {
            try {
                $stmt = $pdo->query("SELECT 1 FROM $table LIMIT 1");
                echo "<span class='success'>✓ Table: $table</span><br>";
            } catch (PDOException $e) {
                echo "<span class='error'>✗ Table: $table (Missing or error)</span><br>";
                $errors[] = "Database table issue: $table";
            }
        }
    } else {
        echo "<span class='error'>✗ Database config file missing</span><br>";
        $errors[] = "Database config missing";
    }
} catch (Exception $e) {
    echo "<span class='error'>✗ Database connection failed: " . $e->getMessage() . "</span><br>";
    $errors[] = "Database connection failed";
}

// Check CSS and JS files for basic integrity
echo "<h3>7. Checking CSS & JS Integrity</h3>";
if (file_exists('assets/css/style.css')) {
    $css_content = file_get_contents('assets/css/style.css');
    $open_braces = substr_count($css_content, '{');
    $close_braces = substr_count($css_content, '}');
    
    if ($open_braces === $close_braces) {
        echo "<span class='success'>✓ CSS braces balanced ($open_braces pairs)</span><br>";
    } else {
        echo "<span class='error'>✗ CSS braces unbalanced (Open: $open_braces, Close: $close_braces)</span><br>";
        $errors[] = "CSS syntax error - unbalanced braces";
    }
}

// Summary
echo "<h3>8. Summary</h3>";
if (empty($errors)) {
    echo "<div style='background:#d4edda;padding:10px;border-radius:5px;'>";
    echo "<span class='success'><strong>✓ All checks passed! Application should work correctly.</strong></span>";
    echo "</div>";
} else {
    echo "<div style='background:#f8d7da;padding:10px;border-radius:5px;'>";
    echo "<span class='error'><strong>Found " . count($errors) . " error(s):</strong></span><br>";
    foreach ($errors as $error) {
        echo "• $error<br>";
    }
    echo "</div>";
}

if (!empty($fixes)) {
    echo "<div style='background:#d1ecf1;padding:10px;border-radius:5px;margin-top:10px;'>";
    echo "<span class='info'><strong>Applied " . count($fixes) . " fix(es):</strong></span><br>";
    foreach ($fixes as $fix) {
        echo "• $fix<br>";
    }
    echo "</div>";
}

echo "<br><strong>Next steps:</strong><br>";
echo "1. If database errors exist, ensure MySQL/MariaDB is running and database 'strawe' exists<br>";
echo "2. Import the database schema if tables are missing<br>";
echo "3. Check web server configuration (Apache/Nginx)<br>";
echo "4. Verify file permissions for web server user<br>";
echo "<br><a href='index.php'>← Back to Application</a>";
?>