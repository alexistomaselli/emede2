<?php
$db_path = __DIR__ . '/../db/database.sqlite';

try {
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

function get_page_data($slug)
{
    global $db;
    $stmt = $db->prepare("SELECT * FROM pages WHERE slug = ?");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

function get_menu_items()
{
    global $db;
    return $db->query("SELECT * FROM menus WHERE is_active = 1 ORDER BY menu_order ASC")->fetchAll();
}

/**
 * Fetch banners for a specific page.
 * If no page_id is provided, tries to find 'home' banners.
 */
function get_banners($page_id = null, $active_only = false)
{
    global $db;
    if ($page_id === null) {
        $stmt = $db->prepare("SELECT id FROM pages WHERE slug = 'home'");
        $stmt->execute();
        $page_id = $stmt->fetchColumn();
    }

    $sql = "SELECT * FROM banners WHERE page_id = ?";
    if ($active_only) {
        $sql .= " AND status = 1";
    }
    $sql .= " ORDER BY item_order ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute([$page_id]);
    return $stmt->fetchAll();
}

function get_all_media()
{
    global $db;
    return $db->query("SELECT * FROM media ORDER BY created_at DESC")->fetchAll();
}

function get_page_sections($page_id, $active_only = false)
{
    global $db;
    $sql = "SELECT * FROM page_sections WHERE page_id = ?";
    if ($active_only) {
        $sql .= " AND status = 1";
    }
    $sql .= " ORDER BY item_order ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute([$page_id]);
    return $stmt->fetchAll();
}

function get_section_items($section_id)
{
    global $db;
    $stmt = $db->prepare("SELECT * FROM section_items WHERE section_id = ? ORDER BY item_order ASC");
    $stmt->execute([$section_id]);
    return $stmt->fetchAll();
}

function save_media_meta($filename, $original_name, $mime, $size)
{
    global $db;
    $stmt = $db->prepare("INSERT INTO media (filename, original_name, mime_type, file_size) VALUES (?, ?, ?, ?)");
    $stmt->execute([$filename, $original_name, $mime, $size]);
    return $db->lastInsertId();
}

function get_setting($key, $default = '')
{
    global $db;
    try {
        $stmt = $db->prepare("SELECT value FROM settings WHERE key = ?");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return $val !== false ? $val : $default;
    } catch (Exception $e) {
        return $default;
    }
}

function get_all_settings()
{
    global $db;
    return $db->query("SELECT * FROM settings")->fetchAll();
}
?>