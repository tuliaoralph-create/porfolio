<?php
/**
 * Database Connection & Data Access Layer
 * Portfolio: Ralph Tuliao
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ralph_portfolio');

$pdo = null;
$db_connected = false;
$db_error = null;

// Synchronize user uploaded portrait
$uploadedAvatar = 'C:/Users/Administrator/.gemini/antigravity-ide/brain/e3705c68-af6f-45f5-92d2-2f2da90d887d/.user_uploaded/media_1792760064283.png';
$destAvatarPng = __DIR__ . '/assets/images/ralph-tuliao.png';
$destAvatarJpg = __DIR__ . '/assets/images/ralph-tuliao.jpg';
if (file_exists($uploadedAvatar)) {
    @copy($uploadedAvatar, $destAvatarPng);
    @copy($uploadedAvatar, $destAvatarJpg);
}

// Synchronize user uploaded resume image
$uploadedResumes = [
    __DIR__ . '/ralph-tuliao-resume.png',
    'C:/Users/Administrator/.gemini/antigravity-ide/brain/cf0edad3-2f4e-48aa-99f5-528ad6d3f731/.user_uploaded/ralph-tuliao-resume.png',
    'C:/Users/Administrator/.gemini/antigravity-ide/brain/024d764f-136a-442e-b3ae-a3460560fbcc/.user_uploaded/media_1793194710668.png',
    'C:/Users/Administrator/.gemini/antigravity-ide/brain/024d764f-136a-442e-b3ae-a3460560fbcc/.user_uploaded/media_1793192130883.png',
    'C:/Users/Administrator/.gemini/antigravity-ide/brain/024d764f-136a-442e-b3ae-a3460560fbcc/.user_uploaded/media_1793189584564.png'
];
$destResumeImg  = __DIR__ . '/assets/images/ralph-tuliao-resume.png';
$destResumeImg2 = __DIR__ . '/assets/resume/ralph-tuliao-resume.png';
foreach ($uploadedResumes as $uResume) {
    if (file_exists($uResume)) {
        @copy($uResume, $destResumeImg);
        @copy($uResume, $destResumeImg2);
        break;
    }
}
// Keep assets/resume and assets/images copies in perfect lockstep
if (file_exists($destResumeImg2) && !file_exists($destResumeImg)) {
    @copy($destResumeImg2, $destResumeImg);
} elseif (file_exists($destResumeImg) && !file_exists($destResumeImg2)) {
    @copy($destResumeImg, $destResumeImg2);
}

// Ensure logo asset exists
$logoPath = __DIR__ . '/assets/images/rt-logo.jpg';
if (!file_exists($logoPath)) {
    $brainDir = 'C:/Users/Administrator/.gemini/antigravity-ide/brain/e3705c68-af6f-45f5-92d2-2f2da90d887d/';
    $found = glob($brainDir . 'rt_logo_icon_*.jpg');
    if (!empty($found)) {
        @copy($found[0], $logoPath);
    }
}

// Ensure Nexus Systems website mockup image exists
$webImgPath = __DIR__ . '/assets/images/nexus-website.jpg';
if (!file_exists($webImgPath)) {
    $brainDir = 'C:/Users/Administrator/.gemini/antigravity-ide/brain/94c62058-b058-4b0c-9b08-998223f158e9/';
    $foundWeb = glob($brainDir . 'modern_website_mockup_*.jpg');
    if (!empty($foundWeb)) {
        @copy($foundWeb[0], $webImgPath);
    }
}

/**
 * Initialize MySQL connection and ensure database & tables exist
 */
function initDatabaseConnection() {
    global $pdo, $db_connected, $db_error;

    if ($db_connected && $pdo instanceof PDO) {
        return $pdo;
    }

    $hosts = ['127.0.0.1', 'localhost'];
    $connected = false;

    foreach ($hosts as $host) {
        // Attempt 1: Direct connection to target database
        try {
            $dsn = "mysql:host={$host};port=3306;dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_TIMEOUT            => 3,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
            $connected = true;
            break;
        } catch (PDOException $e) {
            // Attempt 2: Connect to server root and initialize database
            try {
                $rootDsn = "mysql:host={$host};port=3306;charset=utf8mb4";
                $tempPdo = new PDO($rootDsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT            => 3,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]);
                $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                
                $pdo = new PDO("mysql:host={$host};port=3306;dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::ATTR_TIMEOUT            => 3,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]);
                $connected = true;
                break;
            } catch (PDOException $ex) {
                $db_error = $ex->getMessage();
            }
        }
    }

    if ($connected && $pdo) {
        $db_connected = true;
        // Ensure all required tables exist
        try {
            // 1. Users table
            $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `username` VARCHAR(50) NOT NULL UNIQUE,
              `email` VARCHAR(120) NOT NULL UNIQUE,
              `password_hash` VARCHAR(255) NOT NULL,
              `role` ENUM('visitor', 'admin') DEFAULT 'visitor',
              `is_active` TINYINT(1) DEFAULT 1,
              `last_login` DATETIME DEFAULT NULL,
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // Seed admin user if users table is empty
            $chk = $pdo->query("SELECT COUNT(*) FROM `users`")->fetchColumn();
            if ($chk == 0) {
                $adminHash = password_hash('Admin@1234', PASSWORD_BCRYPT);
                $ins = $pdo->prepare("INSERT INTO `users` (`username`, `email`, `password_hash`, `role`) VALUES ('admin', 'ralphtuliao.tuliao@gmail.com', :h, 'admin')");
                $ins->execute(['h' => $adminHash]);
            }

            // Sync from data/users.json if exists
            $jsonFile = __DIR__ . '/data/users.json';
            if (file_exists($jsonFile)) {
                $jsonUsers = json_decode(@file_get_contents($jsonFile), true);
                if (is_array($jsonUsers)) {
                    $chkStmt = $pdo->prepare("SELECT id FROM `users` WHERE `username` = :u OR `email` = :e LIMIT 1");
                    $syncStmt = $pdo->prepare("INSERT INTO `users` (`username`, `email`, `password_hash`, `role`) VALUES (:u, :e, :h, :r)");
                    foreach ($jsonUsers as $ju) {
                        if (!empty($ju['username']) && !empty($ju['email']) && !empty($ju['password_hash'])) {
                            $chkStmt->execute(['u' => $ju['username'], 'e' => $ju['email']]);
                            if (!$chkStmt->fetch()) {
                                $syncStmt->execute([
                                    'u' => $ju['username'],
                                    'e' => $ju['email'],
                                    'h' => $ju['password_hash'],
                                    'r' => $ju['role'] ?? 'visitor'
                                ]);
                            }
                        }
                    }
                }
            }

            // 2. Profile Info table
            $pdo->exec("CREATE TABLE IF NOT EXISTS `profile_info` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `full_name` VARCHAR(100) NOT NULL,
              `title` VARCHAR(150) NOT NULL,
              `headline` VARCHAR(255) NOT NULL,
              `bio` TEXT NOT NULL,
              `avatar` VARCHAR(255) DEFAULT 'assets/images/ralph-tuliao.jpg',
              `email` VARCHAR(100) DEFAULT 'ralphtuliao.tuliao@gmail.com',
              `location` VARCHAR(100) DEFAULT 'Philippines',
              `status` VARCHAR(100) DEFAULT 'Available for new projects',
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $pCount = $pdo->query("SELECT COUNT(*) FROM `profile_info`")->fetchColumn();
            if ($pCount == 0) {
                $fb = getFallbackProfile();
                $stmt = $pdo->prepare("INSERT INTO `profile_info` (`full_name`, `title`, `headline`, `bio`, `avatar`, `email`, `location`, `status`) VALUES (:fn, :t, :h, :b, :a, :e, :l, :s)");
                $stmt->execute([
                    'fn' => $fb['full_name'], 't' => $fb['title'], 'h' => $fb['headline'],
                    'b' => $fb['bio'], 'a' => $fb['avatar'], 'e' => $fb['email'],
                    'l' => $fb['location'], 's' => $fb['status']
                ]);
            }

            // 3. Projects table
            $pdo->exec("CREATE TABLE IF NOT EXISTS `projects` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `title` VARCHAR(150) NOT NULL,
              `subtitle` VARCHAR(255) NOT NULL,
              `category` ENUM('web', 'iot') NOT NULL,
              `description` TEXT NOT NULL,
              `image` VARCHAR(255) NOT NULL,
              `tags` VARCHAR(255) NOT NULL,
              `features` TEXT NOT NULL,
              `tech_stack` VARCHAR(255) NOT NULL,
              `demo_url` VARCHAR(255) DEFAULT '',
              `live_demo_url` VARCHAR(500) DEFAULT '',
              `github_url` VARCHAR(255) DEFAULT '',
              `featured` TINYINT(1) DEFAULT 1,
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            try {
                $pdo->exec("ALTER TABLE `projects` ADD COLUMN `live_demo_url` VARCHAR(500) DEFAULT '' AFTER `demo_url`");
            } catch (Exception $e) {}

            $prCount = $pdo->query("SELECT COUNT(*) FROM `projects`")->fetchColumn();
            if ($prCount == 0) {
                $fbProjects = getFallbackProjects();
                $pStmt = $pdo->prepare("INSERT INTO `projects` (`title`, `subtitle`, `category`, `description`, `image`, `tags`, `features`, `tech_stack`, `demo_url`, `live_demo_url`, `github_url`, `featured`) VALUES (:t, :st, :c, :d, :img, :tags, :feat, :tech, :du, :ldu, :gh, :f)");
                foreach ($fbProjects as $p) {
                    $pStmt->execute([
                        't' => $p['title'], 'st' => $p['subtitle'], 'c' => $p['category'],
                        'd' => $p['description'], 'img' => $p['image'], 'tags' => $p['tags'],
                        'feat' => $p['features'], 'tech' => $p['tech_stack'], 'du' => $p['demo_url'],
                        'ldu' => $p['live_demo_url'], 'gh' => $p['github_url'], 'f' => $p['featured']
                    ]);
                }
            }

            // Patch existing projects to set live_demo_url if still using old hash URLs
            try {
                $pdo->exec("UPDATE `projects` SET `demo_url`='demo-tapseafood.html', `live_demo_url`='demo-tapseafood.html' WHERE `title` LIKE '%Tapseafood%' AND (`demo_url`='#demo-tapseafood' OR `live_demo_url`='')");
                $pdo->exec("UPDATE `projects` SET `demo_url`='demo-hotel.html', `live_demo_url`='demo-hotel.html' WHERE `title` LIKE '%Hotel%' AND (`demo_url`='#demo-hotel' OR `live_demo_url`='')");
                $pdo->exec("UPDATE `projects` SET `demo_url`='demo-arduino.html', `live_demo_url`='demo-arduino.html' WHERE `title` LIKE '%Arduino%' AND (`demo_url`='#demo-arduino' OR `live_demo_url`='')");
                // Remove Nexus if previously created
                $pdo->exec("DELETE FROM `projects` WHERE `title` LIKE '%Nexus%'");
            } catch (Exception $e) {}

            // 4. Skills table
            $pdo->exec("CREATE TABLE IF NOT EXISTS `skills` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `category` VARCHAR(50) NOT NULL,
              `name` VARCHAR(100) NOT NULL,
              `proficiency` INT NOT NULL DEFAULT 80,
              `icon_code` VARCHAR(50) DEFAULT 'code',
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $skCount = $pdo->query("SELECT COUNT(*) FROM `skills`")->fetchColumn();
            if ($skCount == 0) {
                $fbSkills = getFallbackSkills();
                $skStmt = $pdo->prepare("INSERT INTO `skills` (`category`, `name`, `proficiency`, `icon_code`) VALUES (:c, :n, :p, :i)");
                foreach ($fbSkills as $sk) {
                    $skStmt->execute(['c' => $sk['category'], 'n' => $sk['name'], 'p' => $sk['proficiency'], 'i' => $sk['icon_code']]);
                }
            }

            // 5. Contact Messages table
            $pdo->exec("CREATE TABLE IF NOT EXISTS `contact_messages` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `name` VARCHAR(100) NOT NULL,
              `email` VARCHAR(120) NOT NULL,
              `subject` VARCHAR(150) NOT NULL,
              `message` TEXT NOT NULL,
              `ip_address` VARCHAR(45) DEFAULT NULL,
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              `status` ENUM('unread', 'read', 'archived') DEFAULT 'unread'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        } catch (Exception $e) {}
    } else {
        $db_connected = false;
    }

    return $pdo;
}

// Auto-run connection on require
initDatabaseConnection();


/**
 * Fallback static data in case MySQL is not running or pending setup
 */
function getFallbackProjects() {
    return [
        [
            'id' => 1,
            'title' => 'Tapseafood: Online Food Ordering',
            'subtitle' => 'Full-Featured Seafood E-Commerce & Instant Ordering Platform',
            'category' => 'web',
            'description' => 'Tapseafood is a dynamic online food ordering web platform tailored for seafood restaurants. Designed with a vibrant, modern UI, it offers seamless dish exploration, customized gourmet order selection, real-time shopping cart totals, instant checkout processing, and interactive order status tracking.',
            'image' => 'assets/images/tapseafood.jpg',
            'tags' => 'E-Commerce,Web Development,PHP,MySQL,Interactive UI',
            'features' => 'Interactive Digital Seafood Menu;Real-Time Cart Calculation;Customer Checkout Pipeline;Dynamic Pricing & Tax Computation;Admin Order Management Dashboard',
            'tech_stack' => 'PHP, MySQL, JavaScript, HTML5/CSS3, Responsive UI/UX',
            'demo_url' => 'demo-tapseafood.html',
            'live_demo_url' => 'demo-tapseafood.html',
            'github_url' => 'https://github.com/ralphtuliao/tapseafood-ordering',
            'featured' => 1
        ],
        [
            'id' => 2,
            'title' => 'Hotel Online Booking',
            'subtitle' => 'Comprehensive Luxury Hotel Room Reservation & Availability System',
            'category' => 'web',
            'description' => 'A high-end hotel booking portal that delivers an effortless guest reservation experience. Features interactive suite previews, dynamic calendar date-selection, occupancy and guest filters, instant rate calculations, room availability verification, and automated confirmation records.',
            'image' => 'assets/images/hotel-booking.jpg',
            'tags' => 'Hospitality,Booking Engine,Web App,PHP,Database System',
            'features' => 'Real-Time Suite Availability Engine;Flexible Check-In / Check-Out Date Picker;Multi-Guest & Room Type Selector;Automated Booking Confirmation;Room Gallery & Amenity Explorer',
            'tech_stack' => 'PHP, MySQL, Vanilla JavaScript, CSS3 Glassmorphism, AJAX',
            'demo_url' => 'demo-hotel.html',
            'live_demo_url' => 'demo-hotel.html',
            'github_url' => 'https://github.com/ralphtuliao/hotel-online-booking',
            'featured' => 1
        ],
        [
            'id' => 3,
            'title' => 'Arduino Water Dispenser',
            'subtitle' => 'Touchless Smart Liquid Dispensing System Powered by Arduino',
            'category' => 'iot',
            'description' => 'An intelligent contactless water dispensing station developed using an Arduino microcontroller and ultrasonic sensing. It accurately detects cup placement, automatically triggers a submerged 12V pump via relay, tracks dispensed milliliters on an I2C OLED display, and provides red/green status LED feedback for hygienic, hands-free operation.',
            'image' => 'assets/images/arduino-dispenser.jpg',
            'tags' => 'IoT,Arduino,Hardware,C++,Automation,Sensors',
            'features' => 'Touchless Proximity Detection via HC-SR04 Ultrasonic Sensor;Automated Relay-Driven Submersible Water Pump;Real-Time Milliliter & Water Level Tracking on OLED Display;Overfill Safety Cutoff & Status LED Indicators;Clean Acrylic Chamber & Breadboard Prototype Architecture',
            'tech_stack' => 'Arduino UNO, C/C++, HC-SR04 Sensor, 0.96 OLED (I2C), 5V Relay, 12V Pump',
            'demo_url' => 'demo-arduino.html',
            'live_demo_url' => 'demo-arduino.html',
            'github_url' => 'https://github.com/ralphtuliao/arduino-smart-water-dispenser',
            'featured' => 1
        ]
    ];
}

function getFallbackProfile() {
    return [
        'full_name' => 'Ralph Tuliao',
        'title' => 'Full-Stack Web Developer & IoT Engineer',
        'headline' => 'Engineering modern web experiences & smart connected hardware solutions.',
        'bio' => 'Hello! I am Ralph Tuliao, a passionate developer combining robust full-stack web engineering with embedded IoT systems. From building intuitive e-commerce platforms like Tapseafood to automated hardware like the Arduino Smart Water Dispenser, I bring ideas to life with clean code, sleek interfaces, and reliable hardware integration.',
        'avatar' => 'assets/images/ralph-tuliao.jpg',
        'email' => 'ralphtuliao.tuliao@gmail.com',
        'location' => 'Manila, Philippines',
        'status' => 'Available for new projects'
    ];
}

function getFallbackSkills() {
    return [
        ['category' => 'Web Frontend', 'name' => 'HTML5 & Modern CSS3', 'proficiency' => 95, 'icon_code' => 'layout'],
        ['category' => 'Web Frontend', 'name' => 'JavaScript (ES6+)', 'proficiency' => 90, 'icon_code' => 'code'],
        ['category' => 'Web Frontend', 'name' => 'Responsive UI & Glassmorphism', 'proficiency' => 92, 'icon_code' => 'palette'],
        ['category' => 'Backend & DB', 'name' => 'PHP (OOP & Procedural)', 'proficiency' => 88, 'icon_code' => 'server'],
        ['category' => 'Backend & DB', 'name' => 'MySQL / Database Architecture', 'proficiency' => 85, 'icon_code' => 'database'],
        ['category' => 'Backend & DB', 'name' => 'RESTful APIs & AJAX', 'proficiency' => 84, 'icon_code' => 'wifi'],
        ['category' => 'IoT & Hardware', 'name' => 'Arduino UNO & C/C++', 'proficiency' => 88, 'icon_code' => 'cpu'],
        ['category' => 'IoT & Hardware', 'name' => 'Sensors (Ultrasonic, IR, Flow)', 'proficiency' => 86, 'icon_code' => 'activity'],
        ['category' => 'IoT & Hardware', 'name' => 'Relays, Pumps & DC Actuators', 'proficiency' => 82, 'icon_code' => 'zap'],
        ['category' => 'Tools & Dev', 'name' => 'Git & GitHub Version Control', 'proficiency' => 85, 'icon_code' => 'git-branch'],
        ['category' => 'Tools & Dev', 'name' => 'XAMPP & Apache Stack', 'proficiency' => 90, 'icon_code' => 'terminal'],
        ['category' => 'Tools & Dev', 'name' => 'Hardware Prototyping & Circuits', 'proficiency' => 80, 'icon_code' => 'tool'],
    ];
}

/**
 * Data Retrieval Functions
 */
function fetchProfileInfo() {
    global $pdo, $db_connected;
    if ($db_connected && $pdo) {
        try {
            $stmt = $pdo->query("SELECT * FROM `profile_info` LIMIT 1");
            $data = $stmt->fetch();
            if ($data) return $data;
        } catch (Exception $e) {}
    }
    return getFallbackProfile();
}

function fetchProjects($category = 'all') {
    global $pdo, $db_connected;
    if ($db_connected && $pdo) {
        try {
            if ($category !== 'all') {
                $stmt = $pdo->prepare("SELECT * FROM `projects` WHERE `category` = :cat ORDER BY `id` ASC");
                $stmt->execute(['cat' => $category]);
            } else {
                $stmt = $pdo->query("SELECT * FROM `projects` ORDER BY `id` ASC");
            }
            $results = $stmt->fetchAll();
            if (!empty($results)) return $results;
        } catch (Exception $e) {}
    }
    
    $fallbacks = getFallbackProjects();
    if ($category !== 'all') {
        return array_values(array_filter($fallbacks, function($p) use ($category) {
            return $p['category'] === $category;
        }));
    }
    return $fallbacks;
}

function fetchSkills() {
    global $pdo, $db_connected;
    if ($db_connected && $pdo) {
        try {
            $stmt = $pdo->query("SELECT * FROM `skills` ORDER BY `category`, `id` ASC");
            $results = $stmt->fetchAll();
            if (!empty($results)) return $results;
        } catch (Exception $e) {}
    }
    return getFallbackSkills();
}

function saveContactInquiry($name, $email, $subject, $message) {
    global $pdo, $db_connected;
    
    $name = trim(strip_tags($name));
    $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    $subject = trim(strip_tags($subject));
    $message = trim(strip_tags($message));
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    if (empty($name) || empty($email) || empty($message)) {
        return ['success' => false, 'error' => 'Please fill out all required fields.'];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Please provide a valid email address.'];
    }

    if ($db_connected && $pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO `contact_messages` (`name`, `email`, `subject`, `message`, `ip_address`) VALUES (:name, :email, :subject, :message, :ip)");
            $stmt->execute([
                'name'    => $name,
                'email'   => $email,
                'subject' => $subject,
                'message' => $message,
                'ip'      => $ip
            ]);
            return ['success' => true, 'message' => 'Thank you, your message has been saved in the database! Ralph will get back to you soon.'];
        } catch (Exception $e) {
            // Log or fallback
        }
    }

    // Fallback: log to a local file if DB isn't currently reachable
    $logEntry = date('Y-m-d H:i:s') . " | {$name} ({$email}) | {$subject}: {$message}\n";
    @file_put_contents(__DIR__ . '/messages_log.txt', $logEntry, FILE_APPEND);
    
    return ['success' => true, 'message' => 'Thank you, your message has been received! Ralph will get back to you soon.'];
}
