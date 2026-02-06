<?php
require_once __DIR__ . '/../includes/db.php';

try {
    // Create settings table
    $db->exec("CREATE TABLE IF NOT EXISTS settings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        key TEXT UNIQUE,
        value TEXT,
        label TEXT,
        type TEXT DEFAULT 'text' -- 'text', 'image', 'textarea', etc.
    )");

    // Insert default logo setting if it doesn't exist
    $stmt = $db->prepare("INSERT OR IGNORE INTO settings (key, value, label, type) VALUES (?, ?, ?, ?)");
    $stmt->execute(['site_logo', 'assets/logo.png', 'Logo del Sitio', 'image']);
    $stmt->execute(['site_name', 'Gráfica Emede', 'Nombre del Sitio', 'text']);

    echo "Tabla 'settings' creada y poblada correctamente.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
