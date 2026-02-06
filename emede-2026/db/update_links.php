<?php
require_once __DIR__ . '/../includes/db.php';

try {
    $updates = [
        ['url' => 'index.php', 'new' => './'],
        ['url' => 'trayectoria.php', 'new' => 'trayectoria'],
        ['url' => 'packaging.php', 'new' => 'packaging'],
        ['url' => 'posavasos.php', 'new' => 'posavasos'],
        ['url' => 'comercial.php', 'new' => 'comercial'],
        ['url' => 'galeria.php', 'new' => 'galeria']
    ];

    $stmt = $db->prepare("UPDATE menus SET url = ? WHERE url = ?");
    foreach ($updates as $u) {
        $stmt->execute([$u['new'], $u['url']]);
    }

    echo "Menu URLs updated to clean format.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
