<?php
// Configuration
define('DB_FILE', __DIR__ . '/data/database.sqlite');

// Connect to SQLite
function getDB()
{
    try {
        // Create the file if it doesn't exist
        if (!file_exists(DB_FILE)) {
            touch(DB_FILE);
        }

        $pdo = new PDO('sqlite:' . DB_FILE);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
}

// Helper: Get Setting
function get_setting($key, $default = '')
{
    $db = getDB();
    $stmt = $db->prepare("SELECT value FROM settings WHERE key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['value'] : $default;
}

// Helper: Set Setting
function set_setting($key, $value)
{
    $db = getDB();
    $stmt = $db->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES (?, ?)");
    return $stmt->execute([$key, $value]);
}

// Helper: Get Page
function get_page($slug)
{
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM pages WHERE slug = ?");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}
?>