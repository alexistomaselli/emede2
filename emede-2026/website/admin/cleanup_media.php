<?php
require_once __DIR__ . '/../includes/db.php';

// Get all media records
$media = get_all_media();

$deleted = 0;
$kept = 0;

foreach ($media as $item) {
    $full_path = __DIR__ . '/../' . $item['filename'];

    if (!file_exists($full_path)) {
        echo "❌ Eliminando registro huérfano: {$item['filename']} (ID: {$item['id']})\n";
        $stmt = $db->prepare("DELETE FROM media WHERE id = ?");
        $stmt->execute([$item['id']]);
        $deleted++;
    } else {
        echo "✅ Archivo existe: {$item['filename']}\n";
        $kept++;
    }
}

echo "\n";
echo "========================================\n";
echo "Total eliminados: $deleted\n";
echo "Total conservados: $kept\n";
echo "========================================\n";
