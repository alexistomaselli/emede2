<?php
$db_path = __DIR__ . '/../db/database.sqlite';

try {
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if column exists
    $stmt = $db->query("PRAGMA table_info(section_items)");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN, 1);

    if (!in_array('rating', $columns)) {
        echo "Agregando columna rating...\n";
        $db->exec("ALTER TABLE section_items ADD COLUMN rating INTEGER DEFAULT 5");
        echo "Columna rating agregada.\n";
    } else {
        echo "La columna rating ya existe.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
